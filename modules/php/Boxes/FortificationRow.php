<?php

use Bga\GameFramework\UserException;
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

class Moat extends FortificationRow {
    public string $name = "MOAT";
    public int $boxCount = 12;
    protected int $smallCraneThreshold = 5;
    protected int $largeCraneThreshold = 9;

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 3, 5, 7, 9, 11 => Reward::none("MOAT", $boxId),
            2, 4, 8, 10 => Reward::resources("MOAT", $boxId, [Resource::MATERIALS->value => 1, Resource::MOAT->value => 1]),
            6, 12 => new Reward("MOAT", $boxId, [Resource::MATERIALS->value => 1, Resource::MOAT->value => 1], ["MIGHT"]),
        };
    }

    public function canPay(Game $game, int $playerId): bool {
        return (
            $game->resources(Resource::SERFS)->get($playerId) > 0 ||
            $game->resources(Resource::SOLDIERS)->get($playerId) > 0
        );
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        if ($choice != Resource::SERFS->value && $choice != Resource::SOLDIERS->value) {
            throw new UserException("Cost choice for Moat must be either serfs or soldiers, got $choice");
        }
        $reward = $this->reward($boxId);
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, [Resource::from($choice)], $reward);
    }
}