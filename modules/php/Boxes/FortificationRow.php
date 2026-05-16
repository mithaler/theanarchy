<?php

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BasicRow;
use BGA\Games\theanarchy\Boxes\Reward;

abstract class FortificationRow extends BasicRow {
    public array $cost = [Resource::CRAFTSMEN, Resource::MATERIALS];

    abstract protected int $smallCraneThreshold {get;}
    abstract protected int $largeCraneThreshold {get;}

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $nextBox = parent::validBoxes($game, $playerId, $currBoxes);

        // this will always have exactly 0 or 1 in it
        if (\count($nextBox) == 1) {
            if ($nextBox[0] == $this->smallCraneThreshold) {
                return $this->hasSmallCrane($playerId, $currBoxes) ? $nextBox : [];
            } else if ($nextBox[0] == $this->largeCraneThreshold) {
                return $this->hasLargeCrane($playerId, $currBoxes) ? $nextBox : [];
            }
        }
        return $nextBox;
    }
}

class Gate extends FortificationRow {
    public string $name = "GATE";
    public int $boxCount = 6;
    protected int $smallCraneThreshold = 3;
    protected int $largeCraneThreshold = 5;
    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 3, 5 => Reward::resources("GATE", $boxId, [Resource::PATRONS->value => 1, Resource::GATE->value => 1]),
            2, 4, 6 => new Reward("GATE", $boxId, [Resource::GATE->value => 1], ["MIGHT"]),
        };
    }
}