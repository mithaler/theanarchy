<?php

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

class Box {

    private function __construct(
        /** Must be "ALL UPPERCASE", as it appears on the sheet. */
        public string $section,

        /** The number of the box. If the scheme for converting to a number is not obvious, it will be explained in the class' doc. */
        public int $boxId,

        /** The player who checked this box. */
        public int $playerId,
    ) {}

    function toDbFields(): array {
        return [
            "player_id" => $this->playerId,
            "section" => $this->section,
            "box_id" => $this->boxId,
        ];
    }

    public static function fromDb(array $fields): Box {
        return new Box(
            (string) $fields["section"],
            (int) $fields["box_id"],
            (int) $fields["player_id"],
        );
    }
}

class Reward {

    public string $fromSection;
    public int $fromId;

    /**
     * @var array<Resource, int>
     */
    public array $resources;

    /**
     * @var array<string, Reward|null>
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
        $this->resources = $resources;

        if (\count($boxesToCheck) > 0) {
            $boxes = [];
            foreach ($boxesToCheck as $box) {
                // not yet resolved! call grant() to resolve them
                $boxes[$box] = null;
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
        if ($this->boxes) {
            foreach ($this->boxes as $box => $unused) {
                $subreward = SECTIONS[$box]->check($game, $playerId, null, false);
                $this->boxes[$box] = $subreward;
            }
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
    abstract public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true): Reward;

    /**
     * Returns the ID of the highest checked box in this row.
     * (This is not meaningful for some box types, it's pretty obvious which ones.)
     * @var int $playerId The player to check.
     * @var Box[] $currBoxes The player's checked boxes (or all players', doesn't matter).
     */
    public function highestCheckedBox(int $playerId, array &$currBoxes): int {
        return array_reduce(
            $currBoxes,
            function (int $max, Box $box) use ($playerId) {
                if ($box->playerId == $playerId && $box->section == $this->name && $box->boxId > $max) {
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

    protected function basicCheckBox(Game &$game, int $playerId, int $boxId, bool $pay = true, array $cost, Reward &$reward): Reward {
        Game::checkBox($playerId, $this->name, $boxId);
        if ($pay) {
            foreach ($cost as $resourceCost) {
                $game->resources($resourceCost)->inc($playerId, -1);
            }
        }
        $reward->grant($game, $playerId);
        return $reward;
    }
}
