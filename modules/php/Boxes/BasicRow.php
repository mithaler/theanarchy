<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

require_once(__DIR__ . "/BoxType.php");

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

abstract class BasicRow extends BoxType {
    abstract public int $boxCount { get; }
    abstract protected array $cost { get; }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        if (!$this->canPay($game, $playerId, $this->cost)) {
            return [];
        }

        $highestFilled = $this->highestCheckedBox($playerId, $currBoxes);
        if ($highestFilled == $this->boxCount) {
            return [];
        }
        return [$highestFilled + 1];
    }
    abstract protected function reward(int $boxId): Reward;

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        if (!$pay && $boxId == null) {
            // look up current value
            $currValue = (int) Game::getUniqueValueFromDB(
                "SELECT MAX(box_id) FROM checked_box WHERE player_id = $playerId AND section = '$this->name'"
            );
            $boxId = $currValue + 1;
        }
        $reward = $this->reward($boxId);
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $this->cost, $reward);
    }
}

abstract class ResourceRow extends BasicRow {
    public int $boxCount = 13;

    abstract protected Resource $resourceReward { get; }
    abstract protected string $incomeUpgrade { get; }

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 5, 9 => Reward::resources($this->name, $boxId, [$this->resourceReward]),
            3, 7, 11 => new Reward($this->name, $boxId, [$this->resourceReward], [$this->incomeUpgrade]),
            13 => new Reward($this->name, $boxId, [$this->resourceReward], [$this->incomeUpgrade, "LOYALTY"]),
            default => Reward::none($this->name, $boxId),
        };
    }
}

class QuarryForest extends ResourceRow {
    public string $name = "QUARRY & FOREST";
    protected array $cost = [Resource::SERFS];
    protected Resource $resourceReward = Resource::MATERIALS;
    protected string $incomeUpgrade = "MATERIALS";
}

class Farms extends ResourceRow {
    public string $name = "FARMS";
    protected array $cost = [Resource::SERFS];
    protected Resource $resourceReward = Resource::FOOD;
    protected string $incomeUpgrade = "FOOD";
}

class TrainingGrounds extends ResourceRow {
    public string $name = "TRAINING GROUNDS";
    protected array $cost = [Resource::SERFS];
    protected Resource $resourceReward = Resource::SOLDIERS;
    protected string $incomeUpgrade = "SOLDIERS";
}

abstract class LeadershipTrack extends BasicRow {
    public int $boxCount = 9;
    public array $cost = [Resource::PATRONS];
}

class Governance extends LeadershipTrack {
    public string $name = "GOVERNANCE";

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1 => Reward::resources($this->name, $boxId, [Resource::SILVER]),
            3 => Reward::boxes($this->name, $boxId, ["LOYALTY"]),
            5 => new Reward($this->name, $boxId, [Resource::SERFS], ["SERFS"]),
            6 => Reward::resources($this->name, $boxId, [Resource::MATERIALS]),
            7 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS]),
            8 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            9 => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN]),
            default => Reward::none($this->name, $boxId),
        };
    }
}

class Warcraft extends LeadershipTrack {
    public string $name = "WARCRAFT";

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1 => Reward::resources($this->name, $boxId, [Resource::SOLDIERS]),
            3 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            5 => Reward::resources($this->name, $boxId, [Resource::MATERIALS]),
            7 => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN]),
            8 => Reward::boxes($this->name, $boxId, ["INFLUENCE"]),
            9 => new Reward($this->name, $boxId, [Resource::KNIGHTS], ["KNIGHTS"]),
            default => Reward::none($this->name, $boxId),
        };
    }
}

class Worship extends LeadershipTrack {
    public string $name = "WORSHIP";

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1 => Reward::resources($this->name, $boxId, [Resource::SILVER]),
            3 => Reward::boxes($this->name, $boxId, ["LOYALTY"]),
            5 => Reward::resources($this->name, $boxId, [Resource::FOOD]),
            7 => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN]),
            9 => new Reward($this->name, $boxId, [Resource::SILVER], ["LOYALTY"]),
            default => Reward::none($this->name, $boxId),
        };
    }
}

class Entertainment extends LeadershipTrack {
    public string $name = "ENTERTAINMENT";

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1 => Reward::resources($this->name, $boxId, [Resource::MATERIALS]),
            2 => Reward::boxes($this->name, $boxId, ["BREWHOUSE"]),
            3 => new Reward($this->name, $boxId, [Resource::FOOD], ["LOYALTY"]),
            4 => Reward::boxes($this->name, $boxId, ["BREWHOUSE"]),
            5 => Reward::resources($this->name, $boxId, [Resource::SILVER]),
            6 => Reward::boxes($this->name, $boxId, ["BREWHOUSE"]),
            7 => Reward::resources($this->name, $boxId, [Resource::CRAFTSMEN]),
            8 => Reward::boxes($this->name, $boxId, ["BREWHOUSE", "BREWHOUSE", "LOYALTY"]),
            9 => new Reward($this->name, $boxId, [Resource::SERFS], ["BREWHOUSE", "SERFS"]),
            default => Reward::none($this->name, $boxId),
        };
    }
}

class Ramparts extends BasicRow {
    public string $name = "RAMPARTS";
    public int $boxCount = 5;
    public array $cost = [Resource::CRAFTSMEN];

    const array REQUIRED_WARCRAFT = [1 => 1, 2 => 2, 3 => 4, 4 => 6, 5 => 7, 6 => 9];

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $next = parent::validBoxes($game, $playerId, $currBoxes);
        if (\count($next) > 0) {
            $warcraft = $this->highestCheckedBox($playerId, $currBoxes, section: "WARCRAFT");
            if ($warcraft >= self::REQUIRED_WARCRAFT[$next[0]]) {
                return $next;
            }
        }
        return [];
    }

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 5 => Reward::resources($this->name, $boxId, [Resource::SOLDIERS]),
            3 => new Reward($this->name, $boxId, [Resource::SOLDIERS], ["SOLDIERS"]),
            2, 4 => Reward::resources($this->name, $boxId, [Resource::MUSTER_TOKENS]),
            6 => new Reward($this->name, $boxId, [Resource::MUSTER_TOKENS], ["INFLUENCE"]),
        };
    }
}
