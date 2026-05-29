<?php

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BasicRow;
use BGA\Games\theanarchy\Boxes\Reward;

const WALLS = [Resource::WALL_LEFT, Resource::WALL_TOP, Resource::WALL_RIGHT, Resource::WALL_BOTTOM];

/**
 * Returns which walls are available to the player (taking into account the
 * wall difference rule).
 * @param Game $game
 * @param int $playerId
 * @return Resource[] A list of available walls.
 */
function availableWalls(Game $game, int $playerId): array {
    $currWalls = [];
    // preload them so we don't hammer the DB
    foreach (WALLS as $wall) {
        $currWalls[$wall->value] = $game->resources($wall)->get($playerId);
    }

    $out = [];
    foreach (WALLS as $idx => $wall) {
        $curr = $currWalls[$wall->value];
        if ($curr >= 4) {
            continue;
        }
        $prev = $currWalls[WALLS[$idx == 0 ? 3 : $idx - 1]->value];
        $next = $currWalls[WALLS[(($idx + 1) % 4)]->value];
        if ($curr <= $prev && $curr <= $next) {
            $out[] = $wall;
        }
    }
    return $out;
}

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

class Tower extends FortificationRow {
    public string $name = "TOWER";
    public int $boxCount = 8;
    protected int $smallCraneThreshold = 5;
    protected int $largeCraneThreshold = 7;

    // excludes the tower part! must be added by the caller.
    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 4, 5, 7 => Reward::resources("TOWER", $boxId, [Resource::PATRONS->value => 1]),
            3, 6, 8 => Reward::boxes("TOWER", $boxId, ["MIGHT"]),
            default => Reward::none("TOWER", $boxId),
        };
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        if (!\in_array($choice, ["towerLeftBottom", "towerLeftTop", "towerRightBottom", "towerRightTop"])) {
            throw new UserException("Tower choice must be set");
        }

        $tower = Resource::from($choice);
        if ($game->resources($tower)->get($playerId) >= 2) {
            throw new UserException("That tower is already maxed out");
        }

        $reward = $this->reward($boxId);
        $reward->resources[$tower->value] = 1;
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $this->cost, $reward);
    }
}

class Wall extends FortificationRow {
    public string $name = "WALL";
    public int $boxCount = 16;
    protected int $smallCraneThreshold = 7;
    protected int $largeCraneThreshold = 12;

    // excludes the wall part! must be added by the caller.
    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 4, 7, 10, 12, 15 => Reward::resources("WALL", $boxId, [Resource::PATRONS->value => 1]),
            3, 6, 8, 11, 13, 16 => Reward::boxes("WALL", $boxId, ["MIGHT"]),
            default => Reward::none("WALL", $boxId),
        };
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        if (!\in_array($choice, ["wallTop", "wallLeft", "wallBottom", "wallRight"])) {
            throw new UserException("Wall choice must be set");
        }

        $wall = Resource::from($choice);
        if (!\in_array($wall, availableWalls($game, $playerId))) {
            throw new UserException("That wall is unavailable");
        }

        $reward = $this->reward($boxId);
        $reward->resources[$wall->value] = 1;
        $this->basicCheckBox($game, $playerId, $boxId, $pay, $this->cost, $reward);

        // special notification: new available walls
        $newAvailWalls = availableWalls($game, $playerId);
        $game->notify->player($playerId, "newAvailableWalls", "", $newAvailWalls);

        return $reward;
    }
}

class Moat extends FortificationRow {
    public string $name = "MOAT";
    public int $boxCount = 12;
    protected int $smallCraneThreshold = 5;
    protected int $largeCraneThreshold = 9;

    // overridden, because checking cost is an OR
    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        if (
            !$this->canPay($game, $playerId, [Resource::SERFS]) &&
            !$this->canPay($game, $playerId, [Resource::SOLDIERS])
        ) {
            return [];
        }

        $highestFilled = $this->highestCheckedBox($playerId, $currBoxes);
        if ($highestFilled == $this->boxCount) {
            return [];
        }
        return [$highestFilled + 1];
    }

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 3, 5, 7, 9, 11 => Reward::none("MOAT", $boxId),
            2, 4, 8, 10 => Reward::resources("MOAT", $boxId, [Resource::MATERIALS->value => 1, Resource::MOAT->value => 1]),
            6, 12 => new Reward("MOAT", $boxId, [Resource::MATERIALS->value => 1, Resource::MOAT->value => 1], ["MIGHT"]),
        };
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward {
        if ($choice != Resource::SERFS->value && $choice != Resource::SOLDIERS->value) {
            throw new UserException("Cost choice for Moat must be either serfs or soldiers, got $choice");
        }
        $reward = $this->reward($boxId);
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, [Resource::from($choice)], $reward);
    }
}