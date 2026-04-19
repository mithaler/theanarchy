<?php

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

class Box {
    /** Must be "ALL UPPERCASE", as it appears on the sheet. */
    public string $section;

    /** Only for sections with multiple subsections, must be "all lowercase". */
    public ?string $subsection;

    /** The number of the box. If the scheme for converting to a number is not obvious, it will be explained in the class' doc. */
    public int $boxId;

    function toDbFields(int $playerId): array {
        return [
            "player_id" => $playerId,
            "section" => $this->subsection != null ? "$this->section_$this->subsection" : $this->section,
            "box_id" => $this->boxId,
        ];
    }


    static function fromDb(string $fields): static {
        $sectionParts = explode("_", $fields["section"]);
        $out = new static();
        $out->section = $sectionParts[0];
        $out->subsection = count($sectionParts) == 2 ? $sectionParts[1] : null;
        $out->boxId = (int) $fields["box_id"];
        return $out;
    }
}

abstract class BoxType {
    abstract public string $section { get; }

    /**
     * Returns the list of box IDs in this section that can be checked right now.
     * @param Box[] $currBoxes
     * @return int[] A list of checkable box IDs in this section.
     */
    abstract public function validBoxes(Game $game, int $playerId, array $currBoxes): array;

    /**
     * Returns the reward for a given box ID, which can be multiple things:
     * resources (instances of Resource), or sections to check the next box of (strings).
     * @return array<Resource, int>
     */
    abstract public function reward(Game $game, int $boxId): array;
}
