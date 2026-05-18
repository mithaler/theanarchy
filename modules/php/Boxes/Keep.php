<?php

use Bga\GameFramework\UserException;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

class Keep extends BoxType {
    public string $name = "KEEP";

    private function canBuild(Game $game, int $playerId): bool {
        return $game->resources(Resource::CRAFTSMEN)->get($playerId) > 0 && $game->resources(Resource::MATERIALS)->get($playerId);
    }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $last = $this->highestCheckedBox($playerId, $currBoxes);
        $governance = SECTIONS["GOVERNANCE"]->highestCheckedBox($playerId, $currBoxes);

        if ($last == 0 && $this->canBuild($game, $playerId) && $governance >= 1) {
            return [1];
        } else if ($last == 4 && $this->canBuild($game, $playerId) && $governance >= 4 && $this->hasSmallCrane($playerId, $currBoxes)) {
            return [5];
        } else if ($last == 8 && $this->canBuild($game, $playerId) && $governance >= 7 && $this->hasLargeCrane($playerId, $currBoxes)) {
            return [9];
        } else if ($last < 12 && $game->resources(Resource::PATRONS)->get($playerId) > 0) {
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

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        $cost = \in_array($boxId, [1, 5, 9])
            ? [Resource::CRAFTSMEN, Resource::MATERIALS]
            : [Resource::PATRONS];

        $reward = match ($boxId) {
            1, 5, 9 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            2, 6, 10 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS->value => 1]),
            3, 7, 11 => $this->materialReward($boxId, $choice),
            4 => $this->fourReward($choice),
            8 => Reward::boxes($this->name, $boxId, ["WORSHIP", "ENTERTAINMENT", "INFLUENCE"]),
            12 => Reward::boxes($this->name, $boxId, ["WARCRAFT", "WORSHIP", "ENTERTAINMENT", "INFLUENCE"]),
        };
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $cost, $reward);
    }
}