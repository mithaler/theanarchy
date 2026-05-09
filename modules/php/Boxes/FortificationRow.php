<?php

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BasicRow;

abstract class FortificationRow extends BasicRow {
    public array $cost = [Resource::CRAFTSMEN, Resource::MATERIALS];

    abstract protected int $smallCraneThreshold {get;}
    abstract protected int $largeCraneThreshold {get;}

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $nextBox = parent::validBoxes($game, $playerId, $currBoxes);

        // this will always have exactly 0 or 1 in it
        if (\count($nextBox) == 1) {
            if ($nextBox == $this->smallCraneThreshold) {
                // check if player has a small crane
            } else if ($nextBox == $this->largeCraneThreshold) {
                // check if player has a large crane
            }
        }
        return $nextBox;
    }
}