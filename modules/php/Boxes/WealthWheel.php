<?php

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Resource;

abstract class WealthWheel extends BoxType {
    abstract protected function reward($boxId): Reward;

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true): Reward {
        $reward = $this->reward($boxId);
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, Resource::SILVER, $reward);
    }

    protected function sectionBoxIds(int $playerId, array &$currBoxes): array {
        return array_map(
            fn ($box) => $box->boxId,
            array_filter(
                $currBoxes,
                fn ($box) => (
                    $box->playerId == $playerId &&
                    $box->section == $this->name
                )
            )
        );
    }
}

/**
 * ID scheme is, in order:
 * * Inside to outside
 * * Top to bottom, or left to right if vertical
 */
abstract class WealthWheelSide extends WealthWheel {
    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        // can the player pay?
        if (!$game->resources(Resource::SILVER)->get($playerId) < 1) {
            return [];
        }

        $currBoxIds = $this->sectionBoxIds($playerId, $currBoxes);
        $outSet = [];
        foreach ($currBoxIds as $boxId) {
            $outSet = [...$outSet, ...match ($boxId) {
                1 => [2 => true, 3 => true],
                2 => [4 => true, 5 => true],
                3 => [5 => true, 6 => true],
                4 => [7 => true],
                5 => [8 => true, 9 => true],
                6 => [10 => true],
                8, 9 => [11 => true],
                default => [],
            }];
        }
        return array_keys($outSet);
    }
}

class Guildsmen extends WealthWheelSide {
    public string $name = "GUILDSMEN";
    protected function reward($boxId): Reward {
        return match ($boxId) {
            7, 10, 11 => new Reward($this->name, $boxId, [Resource::CRAFTSMEN->value => 1], ["CRAFTSMEN"]),
            default => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN->value => 1]),
        };
    }
}

class Allies extends WealthWheelSide {
    public string $name = "ALLIES";
    protected function reward($boxId): Reward {
        return match ($boxId) {
            1, 4, 6 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS->value => 1]),
            2, 3, 8, 9 => Reward::resources($this->name, $boxId, [Resource::PATRONS->value => 1]),
            5 => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN->value => 1]),
            7, 10 => new Reward($this->name, $boxId, [Resource::KNIGHTS->value => 1], ["LOYALTY"]),
            11 => new Reward($this->name, $boxId, [Resource::KNIGHTS->value => 1], ["KNIGHTS"]),
        };
    }
}

class Mercenaries extends WealthWheelSide {
    public string $name = "MERCENARIES";

    protected function reward($boxId): Reward {
        return match ($boxId) {
            1 => Reward::resources($this->name, $boxId, [Resource::SOLDIERS->value => 1]),
            2, 3, 7, 8, 9, 10 => Reward::resources($this->name, $boxId, [Resource::SOLDIERS->value => 2]),
            4, 5, 6 => Reward::resources($this->name, $boxId, [Resource::PATRONS->value => 1]),
            11 => new Reward($this->name, $boxId, [Resource::PATRONS->value => 1], ["PATRONS"]),
        };
    }
}

/**
 * This is only the technology researching portion!
 * ID scheme is:
 *   1 is the initial serf.
 *   2-9 are the lines.
 *   10 is the serf in the middle.
 * (This allows finding the relevant ID for the bulding portion with simple subtraction.)
 */
class Siegecraft extends WealthWheel {
    public string $name = "SIEGECRAFT";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        // can the player pay?
        if (!$game->resources(Resource::SILVER)->get($playerId) < 1) {
            return [];
        }

        $currBoxIds = $this->sectionBoxIds($playerId, $currBoxes);
        $outSet = [];
        foreach ($currBoxIds as $boxId) {
            $outSet = [...$outSet, ...match ($boxId) {
                1 => [2 => true, 3 => true, 4 => true],
                4 => [5 => true, 6 => true, 10 => true],
                5 => [8 => true, 9 => true],
                10 => [7 => true],
                7 => [8 => true, 9 => true],
                default => [],
            }];
        }
        return array_keys($outSet);
    }

    protected function reward($boxId): Reward {
        return match ($boxId) {
            1, 10 => Reward::resources($this->name, $boxId, [Resource::SERFS->value => 1]),
            default => Reward::none($this->name, $boxId),
        };
    }
}