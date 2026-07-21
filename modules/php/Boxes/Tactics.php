<?php

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

class Tactics extends BoxType {

    public string $name = "TACTICS";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        if (
            !$this->canPay($game, $playerId, [Resource::PATRONS]) &&
            !$this->canPay($game, $playerId, [Resource::KNIGHTS])
        ) {
            return [];
        }

        $warcraft = $this->highestCheckedBox($playerId, $currBoxes, section: "WARCRAFT");
        $highestFilled = $this->highestCheckedBox($playerId, $currBoxes);

        if ($highestFilled < 5 && $warcraft >= $highestFilled * 2 + 1) {
            return [$highestFilled + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        // $choice is dash-separated: which resource the player paid, and which tactic the player wants
        $choices = explode("-", $choice);
        if (count($choices) != 2) {
            throw new UserException("Invalid choice");
        }
        $cost = [Resource::from($choices[0])];

        if (!in_array($choices[1], ["COVERS", "ROCKS", "HOT OIL", "LOGS", "BOLTS"])) {
            throw new UserException("Invalid tactic");
        }
        if (!$this->canPay($game, $playerId, $cost)) {
            throw new UserException("You can't afford that");
        }
        if ($this->highestCheckedBox($playerId, $game->allCheckedBoxes($playerId), section: $choices[1]) != 0) {
            throw new UserException("You already have that tactic");
        }

        $reward = Reward::boxes($this->name, $boxId, [$choices[1]]);
        return $this->checkAndPay($game, $playerId, $boxId, true, $cost, $reward);
    }
}

abstract class TacticsUse extends BoxType {
    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $highest = $this->highestCheckedBox($playerId, $currBoxes);
        if ($highest >= 1 && $highest < 16 && $this->canPay($game, $playerId, [Resource::MATERIALS])) {
            return [$highest + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        if (!$pay && $boxId == null) {
            $boxId = 1;
            $reward = new Reward($this->name, 1, [Resource::SOLDIERS], [$this->name => [2]]);
        } else if ($boxId > 1 && $boxId < 16) {
            $reward = Reward::none($this->name, $boxId);
        } else {
            throw new UserException("Invalid tactics request");
        }
        return $this->checkAndPay($game, $playerId, $boxId, $pay, [Resource::MATERIALS], $reward);
    }
}

class Covers extends TacticsUse {
    public string $name = "COVERS";
}

class Rocks extends TacticsUse {
    public string $name = "ROCKS";
}

class HotOil extends TacticsUse {
    public string $name = "HOT OIL";
}

class Logs extends TacticsUse {
    public string $name = "LOGS";
}

class Bolts extends TacticsUse {
    public string $name = "BOLTS";
}
