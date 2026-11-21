<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

class Box {

    public function __construct(
        /** Must be "ALL UPPERCASE", as it appears on the sheet. */
        public string $section,

        /** The number of the box. If the scheme for converting to a number is not obvious, it will be explained in the class' doc. */
        public int $boxId,

        /** The player who checked this box. */
        public int $playerId,

        /** The written value, if there is one. */
        public ?string $writtenValue = null,
    ) {}

    function toDbFields(): array {
        return [
            "player_id" => $this->playerId,
            "section" => $this->section,
            "box_id" => $this->boxId,
            "written_value" => $this->boxId,
        ];
    }

    public static function fromDb(array $fields): Box {
        return new Box(
            (string) $fields["section"],
            (int) $fields["box_id"],
            (int) $fields["player_id"],
            $fields["written_value"] ? (string) $fields["written_value"] : null,
        );
    }
}

class Reward {

    private ?int $id;
    public string $fromSection;
    public int $fromId;
    public bool $undoable;

    /**
     * @var array<string, int>
     */
    public array $resources;

    /**
     * @var array<string, Reward[]|null>
     */
    public array $boxes;

    public function __construct(
        string $fromSection,
        int $fromId,
        array $resources,
        array $boxesToCheck,
        bool $undoable = true,
    ) {
        $this->fromSection = $fromSection;
        $this->fromId = $fromId;
        $this->undoable = $undoable;

        /*
            this can be either:
                [Resource::SOME, Resource::ANOTHER]
            or:
                [Resource::SOME, Resource::ANOTHER->value => 2]
            we always transform it to the resource => count form
        */
        $this->resources = [];
        foreach ($resources as $idx => $value) {
            if (\is_string($idx) && \is_numeric($value)) {
                // it's enum(string) => count
                $this->resources[$idx] = $value;
            } else {
                // it's an enum variant
                $this->resources[$value->value] = 1;
            }
        }

        /*
            this can be either:
                ["SOMETHING", "ANOTHER"]
            in which case it becomes ["SOMETHING" => null, "ANOTHER" => null], or:
                ["SOMETHING" => [10]]
            in which case it will treat the numbers as IDs of boxes to grant
        */
        if (\count($boxesToCheck) > 0) {
            $boxes = [];
            foreach ($boxesToCheck as $idx => $box) {
                if (\is_string($idx)) {
                    $boxes[$idx] = $box;
                } else {
                    $boxes[$box] = null;
                }
            }
            $this->boxes = $boxes;
        } else {
            $this->boxes = [];
        }
    }

    public static function resources(string $fromSection, int $fromId, array $resources, bool $undoable = true): Reward {
        return new Reward($fromSection, $fromId, $resources, [], $undoable);
    }

    public static function boxes(string $fromSection, int $fromId, array $boxes, bool $undoable = true): Reward {
        return new Reward($fromSection, $fromId, [], $boxes, $undoable);
    }

    public static function none(string $fromSection, int $fromId, bool $undoable = true): Reward {
        return new Reward($fromSection, $fromId, [], [], $undoable);
    }

    /**
     * Gives the player the reward (if there is one), and notifies the frontend.
     * Must be called AFTER boxes are written to checked_box, or the reward won't be
     * associated with boxes correctly.
     */
    public function grant(Game $game, int $playerId, ?int $parentRewardId = null) {
        if ($this->resources) {
            foreach ($this->resources as $resource => $count) {
                $game->resources(Resource::from($resource))->inc($playerId, $count);
            }
        }

        $rewardId = $this->writeToDb($game, $parentRewardId);

        // notify this happened
        $this->notify($game, $playerId, topLevel: $parentRewardId === null);

        if ($this->boxes) {
            foreach ($this->boxes as $box => $boxIds) {
                $subrewards = [];
                if (\is_array($boxIds)) {
                    foreach ($boxIds as $boxId) {
                        $subr = SECTIONS[$box]->check($game, $playerId, $boxId, false);
                        if ($subr !== null) {
                            $subrewards[] = $subr;
                        }
                    }
                } else {
                    $subr = SECTIONS[$box]->check($game, $playerId, null, false);
                    if ($subr !== null) {
                        $subrewards[] = $subr;
                    }
                }
                $this->boxes[$box] = $subrewards;
                foreach ($subrewards as $subreward) {
                    $subreward->grant($game, $playerId, $rewardId);
                }
            }
        }
    }

    /**
     * Writes this Reward and its resources to the DB, and returns its ID.
     * The returned ID is meant to be used during box checking so the checked_box
     * table keeps a record of what Reward checked it.
     * Does not write subrewards; that's are done by recursive logic that calls this.
     */
    private function writeToDb(Game $game, ?int $parentRewardId): int {
        // insert the Reward and get its ID
        $game->DbQuery(\sprintf(
            "INSERT INTO reward (player_id, undoable, parent_reward_id, move_id) VALUES (%s, %s, %s, %s)",
            $game->getCurrentPlayerId(),
            $this->undoable ? 1 : 0,
            $parentRewardId ?? 'null',
            $parentRewardId === null ? $game->bga->logs->getCurrentMoveId() : 'null',
        ));
        $rewardId = $game->DbGetLastId();

        // update the checked_box entry with this reward ID now that we have it
        $game->DbQuery(
            "UPDATE checked_box
            SET reward_id = $rewardId
            WHERE box_id = $this->fromId AND section = '$this->fromSection'"
        );

        // write out the resource rewards
        if (\count($this->resources) > 0) {
            $values = [];
            foreach ($this->resources as $resource => $count) {
                $values[] = ("($rewardId, '$resource', $count)");
            }
            $game->DbQuery(
                "INSERT INTO reward_resource (reward_id, resource, count) VALUES " .
                implode(',', $values)
            );
        }

        return $rewardId;
    }

    private static function fromDb(array $data, array $allResources, array $allBoxes): Reward {
        $rewardId = (int) $data['id'];
        $box = $allBoxes[$rewardId];
        $resourceSet = $allResources[$rewardId] ?? [];
        $resources = \count($resourceSet) > 0 ? array_reduce(
            array_map(
                null,
                explode(',', $resourceSet['resources']),
                explode(',', $resourceSet['counts'])
            ),
            fn ($out, $item) => [...$out, $item[0] => $item[1]],
            [],
        ) : [];

        $out = new Reward(
            $box['section'],
            (int) $box['box_id'],
            $resources,
            [],
            $data['undoable'] === '1' ? true : false
        );
        $out->id = $rewardId;
        return $out;
    }

    private static function insertIntoTree(Reward &$top, Reward $toInsert, int $parentRewardId): bool {
        if ($top->id === $parentRewardId) {
            $top->boxes[$toInsert->fromSection][] = $toInsert;
            return true;
        }
        // it's not this one; search deeper
        foreach ($top->boxes as $childSet) {
            foreach ($childSet as $child) {
                if (self::insertIntoTree($child, $toInsert, $parentRewardId)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function latestFromPlayer(int $playerId): ?Reward {
        $rewardTree = Game::getObjectListFromDB(
            "WITH RECURSIVE reward_tree AS (
                SELECT * FROM `reward`
                WHERE id = (
                    SELECT id FROM `reward`
                    WHERE player_id = $playerId
                    ORDER BY move_id DESC
                    LIMIT 1
                )

                UNION ALL

                SELECT r.* FROM `reward` r
                INNER JOIN `reward_tree` rt ON rt.id = r.parent_reward_id
            )
            SELECT * FROM `reward_tree`
            ORDER BY parent_reward_id ASC"
        );
        if (\count($rewardTree) === 0) {
            return null;
        }

        // gather all the ids and get their resources and boxes
        $rewardIds = implode(',', array_map(fn ($r) => $r['id'], $rewardTree));
        $resources = Game::getCollectionFromDB(
            "SELECT
                `reward_id`,
                GROUP_CONCAT(`resource` ORDER BY `id` SEPARATOR ',') AS `resources`,
                GROUP_CONCAT(`count` ORDER BY `id` SEPARATOR ',') AS `counts`
            FROM `reward_resource`
            WHERE `reward_id` IN ($rewardIds)
            GROUP BY `reward_id`",
        );
        // ...there's only one per reward, right?
        $boxes = Game::getCollectionFromDB(
            "SELECT `reward_id`, `section`, `box_id`
            FROM `checked_box`
            WHERE `reward_id` IN ($rewardIds)",
        );

        // first one is top-level
        $top = self::fromDb($rewardTree[0], $resources, $boxes);

        // repeatedly search the list and materialize them
        $idsToFind = [$top->id];
        while (\count($idsToFind) > 0) {
            $subrewardRows = array_filter($rewardTree, fn ($r) => \in_array((int) $r['parent_reward_id'], $idsToFind));

            $idsToFind = [];
            foreach ($subrewardRows as $subRow) {
                $subreward = self::fromDb($subRow, $resources, $boxes);
                self::insertIntoTree($top, $subreward, (int) $subRow['parent_reward_id']);
                $idsToFind[] = $subreward->id;
            }
        }

        return $top;
    }

    private function notify(Game $game, int $playerId, bool $topLevel = false) {
        if (\count($this->resources) == 0 && \count($this->boxes) == 0) {
            $game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection}'), [
                ...$game->playerNotifArgs($playerId),
                "boxSection" => $this->fromSection,
                "boxId" => $this->fromId,
                "canUndo" => $topLevel ? $this->undoable : null,
            ]);
        } else {
            $resourceRewards = [];
            foreach ($this->resources as $resource => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $resourceRewards[] = $resource;
                }
            }
            $game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection} and earns ${rewards}'), [
                ...$game->playerNotifArgs($playerId),
                "boxSection" => $this->fromSection,
                "boxId" => $this->fromId,
                // TODO make this pretty!
                "rewards" => implode(" ", [...$resourceRewards, ...array_keys($this->boxes)]),
                // we don't include resources here, the framework auto-notifies setPlayerCounter for that
                "canUndo" => $topLevel ? $this->undoable : null,
            ]);
        }
    }
}

abstract class BoxType {
    abstract public string $name { get; }

    /**
     * Returns the list of box IDs in this section that can be checked right now.
     * @param Box[] $currBoxes
     * @return int[] A list of checkable box IDs in this section.
     */
    abstract public function validBoxes(Game $game, int $playerId, array $currBoxes): array;

    /**
     * Pays the cost, checks the box, and returns an UNGRANTED `Reward`. Might look up and
     * recursively call this on downstream BoxTypes, and recursively combine rewards (all
     * of which will be returned as part of the returned Reward). Assumes that the caller
     * has already validated that the box is checkable and the cost is payable.
     *
     * It is always the responsibility of the caller of this function to call `grant()` on
     * the returned `Reward`.
     * @param Game $game
     * @param int $playerId The player to check it for.
     * @param int $boxId The box to check. If null, the implicit "next" box, which might be an error if such a thing is not defined for this section.
     * @param bool $pay Whether to pay the cost in the process.
     * @return Reward The full reward.
     */
    abstract public function check(
        Game $game,
        int $playerId,
        ?int $boxId = null,
        bool $pay = true,
        ?string $choice = null,
    ): ?Reward;

    /**
     * Returns whether the player can pay the specified resources. Can take a resources
     * array of one of two forms:
     *
     *   [RESOURCE::Something, RESOURCE::Another]
     *
     * or
     *
     *   [RESOURCE::Something, RESOURCE::Another->value => 2]
     */
    protected function canPay(Game &$game, int $playerId, array $resources): bool {
        if (\array_is_list($resources)) {
            foreach ($resources as $res) {
                if ($game->resources($res)->get($playerId) < 1) {
                    return false;
                }
            }
        } else {
            foreach ($resources as $res => $count) {
                if ($game->resources($res)->get($playerId) < $count) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Returns the ID of the highest checked box in this row.
     * (This is not meaningful for some box types, it's pretty obvious which ones.)
     * @param int $playerId The player to check.
     * @param Box[] $currBoxes The player's checked boxes (or all players', doesn't matter).
     * @param ?int $ignoreAbove If passed, ignores boxes above this.
     * @param ?string $section If passed, returns the highest for the specified section; if not, this type's section.
     */
    public function highestCheckedBox(int $playerId, array $currBoxes, ?int $ignoreAbove = null, ?string $section = null): int {
        $section = $section == null ? $this->name : $section;
        return array_reduce(
            $currBoxes,
            function (int $max, Box $box) use ($playerId, $ignoreAbove, $section) {
                if ($ignoreAbove != null && $box->boxId > $ignoreAbove) {
                    return $max;
                }
                if ($box->playerId == $playerId && $box->section == $section && $box->boxId > $max) {
                    return $box->boxId;
                }
                return $max;
            },
            0
        );
    }

    /**
     * Returns whether a specific ID is filled.
     * @param int $playerId The player to check.
     * @param string $section The section of the box to check.
     * @param int $boxId The ID of the box to check.
     * @param Box[] $currBoxes The player's checked boxes (or all players', doesn't matter).
     * @return bool True if the specified box is filled, false if not.
     */
    public static function idFilled(int $playerId, string $section, int $boxId, array &$currBoxes): bool {
        return array_find(
            $currBoxes,
            fn (Box $box) => (
                $box->playerId == $playerId &&
                $box->section == $section &&
                $box->boxId == $boxId
            )
        ) != null;
    }

    /** Returns whether the player has built the small crane. */
    protected function hasSmallCrane(int $playerId, array &$currBoxes) {
        return $this->idFilled($playerId, "SIEGECRAFT_construction", 3, $currBoxes);
    }

    /** Returns whether the player has built the large crane. */
    protected function hasLargeCrane(int $playerId, array &$currBoxes) {
        return $this->idFilled($playerId, "SIEGECRAFT_construction", 6, $currBoxes);
    }

    protected function checkAndPay(
        Game $game,
        int $playerId,
        int $boxId,
        bool $pay = true,
        array $cost,
        Reward &$reward,
        ?string $writtenValue = null
    ): Reward {
        Game::checkBox($playerId, $this->name, $boxId, $writtenValue);
        if ($pay) {
            foreach ($cost as $resourceCost) {
                $game->resources($resourceCost)->inc($playerId, -1);
            }
        }
        return $reward;
    }
}
