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

    public string $fromSection;
    public int $fromId;

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
    ) {
        $this->fromSection = $fromSection;
        $this->fromId = $fromId;

        /*
            this can be either:
                [Resource::SOME, Resource::ANOTHER]
            or:
                [Resource::SOME, Resource::ANOTHER->value => 2]
            we always transform it to the second form
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

    public static function resources(string $fromSection, int $fromId, array $resources): Reward {
        return new Reward($fromSection, $fromId, $resources, []);
    }

    public static function boxes(string $fromSection, int $fromId, array $boxes): Reward {
        return new Reward($fromSection, $fromId, [], $boxes);
    }

    public static function none(string $fromSection, int $fromId): Reward {
        return new Reward($fromSection, $fromId, [], []);
    }

    public function grant(Game $game, int $playerId) {
        if ($this->resources) {
            foreach ($this->resources as $resource => $count) {
                $game->resources(Resource::from($resource))->inc($playerId, $count);
            }
        }

        // notify this happened
        $this->notify($game, $playerId);

        if ($this->boxes) {
            foreach ($this->boxes as $box => $ids) {
                $subrewards = [];
                if (\is_array($ids)) {
                    foreach ($ids as $id) {
                        $subr = SECTIONS[$box]->check($game, $playerId, $id, false);
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
                    $subreward->grant($game, $playerId);
                }
            }
        }
    }

    private function notify(Game $game, int $playerId) {
        if (\count($this->resources) == 0 && \count($this->boxes) == 0) {
            $game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection}'), [
                "player_id" => $playerId,
                "player_name" => $game->getPlayerNameById($playerId),
                "boxSection" => $this->fromSection,
                "boxId" => $this->fromId,
            ]);
        } else {
            $resourceRewards = [];
            foreach ($this->resources as $resource => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $resourceRewards[] = $resource;
                }
            }
            $game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection} and earns ${rewards}'), [
                "player_id" => $playerId,
                "player_name" => $game->getPlayerNameById($playerId),
                "boxSection" => $this->fromSection,
                "boxId" => $this->fromId,
                // TODO make this pretty!
                "rewards" => implode(" ", [...$resourceRewards, ...array_keys($this->boxes)]),
                // we don't include resources here, the framework auto-notifies setPlayerCounter for that
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
     * Pays the cost, updates the DB. Might look up and recursively call this on downstream
     * BoxTypes, and recursively combine rewards. Assumes that the caller has already validated
     * that the box is checkable and the cost is payable.
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

    protected function basicCheckBox(
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
        $reward->grant($game, $playerId);
        return $reward;
    }
}
