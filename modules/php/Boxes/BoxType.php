<?php

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

class Box {

    private function __construct(
        /** Must be "ALL UPPERCASE", as it appears on the sheet. */
        public string $section,

        /** Only for sections with multiple subsections, must be "all lowercase". */
        public ?string $subsection,

        /** The number of the box. If the scheme for converting to a number is not obvious, it will be explained in the class' doc. */
        public int $boxId,

        /** The player who checked this box. */
        public int $playerId,
    ) {}

    function toDbFields(): array {
        return [
            "player_id" => $this->playerId,
            "section" => $this->subsection != null ? "{$this->section}_{$this->subsection}" : $this->section,
            "box_id" => $this->boxId,
        ];
    }

    public static function fromDb(array $fields): Box {
        $sectionParts = explode("_", $fields["section"]);
        return new Box(
            $sectionParts[0],
            count($sectionParts) == 2 ? $sectionParts[1] : null,
            (int) $fields["box_id"],
            (int) $fields["player_id"],
        );
    }
}

class Reward {
    public function __construct(
        /** Resource -> int */
        public array | null $resources,
        /** Plain strings, naming sections */
        public array | null $boxes
    ) {}

    public static function resources(array $resources): Reward {
        return new Reward($resources, null);
    }

    public static function boxes(array $boxes): Reward {
        return new Reward(null, $boxes);
    }

    public static function none(): Reward {
        return new Reward(null, null);
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
     * @param int $boxId
     */
    abstract public function check(Game $game, int $playerId, int $boxId, bool $pay = true);
}
