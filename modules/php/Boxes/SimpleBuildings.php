<?php

use Bga\GameFramework\UserException;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

abstract class SimpleBuilding extends BoxType {
    function canBuild(Game $game, int $playerId): bool {
        return $this->canPay($game, $playerId, [Resource::CRAFTSMEN, Resource::MATERIALS]);
    }
}

class Keep extends SimpleBuilding {
    public string $name = "KEEP";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $last = $this->highestCheckedBox($playerId, $currBoxes);
        $governance = SECTIONS["GOVERNANCE"]->highestCheckedBox($playerId, $currBoxes);
        if ($last == 0) {
            if ($this->canBuild($game, $playerId) && $governance >= 1) {
                return [1];
            }
        } else if ($last == 4) {
            if ($this->canBuild($game, $playerId) && $governance >= 4 && $this->hasSmallCrane($playerId, $currBoxes)) {
                return [5];
            }
        } else if ($last == 8) {
            if ($this->canBuild($game, $playerId) && $governance >= 7 && $this->hasLargeCrane($playerId, $currBoxes)) {
                return [9];
            }
        } else if ($last < 12 && $this->canPay($game, $playerId, [Resource::PATRONS])) {
            return [$last + 1];
        }
        return [];
    }

    private function materialReward(int $boxId, string $choice): Reward {
        if (!\in_array($choice, ["food", "silver", "materials"])) {
            throw new UserException("Invalid reward");
        }
        return Reward::resources($this->name, $boxId, [$choice => 1]);
    }

    private function fourReward(string $choice): Reward {
        if (!\in_array($choice, ["WARCRAFT", "WORSHIP", "ENTERTAINMENT"])) {
            throw new UserException("Invalid reward");
        }
        return Reward::boxes($this->name, 4, [$choice, "INFLUENCE"]);
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $cost = \in_array($boxId, [1, 5, 9])
            ? [Resource::CRAFTSMEN, Resource::MATERIALS]
            : [Resource::PATRONS];

        $reward = match ($boxId) {
            1, 5, 9 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            2, 6, 10 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS]),
            3, 7, 11 => $this->materialReward($boxId, $choice),
            4 => $this->fourReward($choice),
            8 => Reward::boxes($this->name, $boxId, ["WORSHIP", "ENTERTAINMENT", "INFLUENCE"]),
            12 => Reward::boxes($this->name, $boxId, ["WARCRAFT", "WORSHIP", "ENTERTAINMENT", "INFLUENCE"]),
        };
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $cost, $reward);
    }
}

class Mint extends SimpleBuilding {
    public string $name = "MINT";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $last = $this->highestCheckedBox($playerId, $currBoxes);
        $governance = SECTIONS["GOVERNANCE"]->highestCheckedBox($playerId, $currBoxes);

        if ($last == 0) {
            if ($this->canBuild($game, $playerId) && $governance >= 2) {
                return [1];
            }
        } else if ($last == 3) {
            if ($this->canBuild($game, $playerId) && $governance >= 5) {
                return [4];
            }
        } else if ($last == 6) {
            if ($this->canBuild($game, $playerId) && $governance >= 8 && $this->hasSmallCrane($playerId, $currBoxes)) {
                return [7];
            }
        } else if ($last < 9 && $this->canPay($game, $playerId, [Resource::SERFS])) {
            return [$last + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $cost = \in_array($boxId, [1, 4, 7])
            ? [Resource::CRAFTSMEN, Resource::MATERIALS]
            : [Resource::SERFS];

        $reward = match ($boxId) {
            1, 2, 4, 5, 7, 8 => Reward::none($this->name, $boxId),
            3, 6, 9 => new Reward($this->name, $boxId, [Resource::SILVER], ["SILVER", "INFLUENCE"]),
        };
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $cost, $reward);
    }
}

class Stables extends SimpleBuilding {
    public string $name = "STABLES";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $last = $this->highestCheckedBox($playerId, $currBoxes);
        $warcraft = SECTIONS["WARCRAFT"]->highestCheckedBox($playerId, $currBoxes);

        if ($last == 0) {
            if ($this->canBuild($game, $playerId) && $warcraft >= 3) {
                return [1];
            }
        } else if ($last == 4) {
            if ($this->canBuild($game, $playerId) && $warcraft >= 6) {
                return [5];
            }
        } else if ($last < 8 && (
            $this->canPay($game, $playerId, [Resource::SERFS]) ||
            $this->canPay($game, $playerId, [Resource::SOLDIERS]))
        ) {
            return [$last + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $building = \in_array($boxId, [1, 5]);
        if (!$building && $choice != Resource::SERFS->value && $choice != Resource::SOLDIERS->value) {
            throw new UserException("Cost choice for Stables must be either serfs or soldiers, got $choice");
        }

        $cost = $building ? [Resource::CRAFTSMEN, Resource::MATERIALS] : [Resource::from($choice)];
        $reward = match ($boxId) {
            1, 5 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            2, 6 => Reward::resources($this->name, $boxId, [Resource::FOOD]),
            3, 7 => Reward::resources($this->name, $boxId, [Resource::SILVER]),
            4, 8 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS]),
        };
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $cost, $reward);
    }
}