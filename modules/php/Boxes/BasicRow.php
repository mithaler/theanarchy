<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

require_once(__DIR__ . "/BoxType.php");

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

abstract class BasicRow extends BoxType {
    abstract public int $boxCount { get; }

    abstract protected function canPay(Game $game, int $playerId): bool;

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        if (!$this->canPay($game, $playerId)) {
            return [];
        }

        $highestFilled = array_reduce(
            $currBoxes,
            function ($max, $box) use ($playerId) {
                if ($box->playerId == $playerId && $box->section == $this->name && $box->boxId > $max) {
                    return $box->boxId;
                }
                return $max;
            },
            0
        );
        if ($highestFilled == $this->boxCount) {
            return [];
        }
        return [$highestFilled + 1];
    }
}

abstract class ResourceRow extends BasicRow {
    public int $boxCount = 13;

    abstract protected Resource $cost { get; }
    abstract protected Resource $resourceReward { get; }
    abstract protected string $incomeUpgrade { get; }

    function canPay(Game $game, int $playerId): bool {
        return $game->resources($this->cost)->get($playerId) > 0;
    }

    protected function reward(int $boxId): Reward {
        return match ($boxId) {
            1, 5, 9 => Reward::resources($this->name, $boxId, [$this->resourceReward->value => 1]),
            3, 7, 11 => new Reward($this->name, $boxId, [$this->resourceReward->value => 1], [$this->incomeUpgrade]),
            13 => new Reward($this->name, $boxId, [$this->resourceReward->value => 1], [$this->incomeUpgrade, "LOYALTY"]),
            default => Reward::none($this->name, $boxId),
        };
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true): Reward {
        Game::checkBox($playerId, $this->name, $boxId);
        if ($pay) {
            $game->resources($this->cost)->inc($playerId, -1);
        }
        $reward = $this->reward($boxId);
        $reward->grant($game, $playerId);
        return $reward;
    }
}

class QuarryForest extends ResourceRow {
    public string $name = "QUARRY & FOREST";
    protected Resource $cost = Resource::SERFS;
    protected Resource $resourceReward = Resource::MATERIALS;
    protected string $incomeUpgrade = "MATERIALS";
}

class Farms extends ResourceRow {
    public string $name = "FARMS";
    protected Resource $cost = Resource::SERFS;
    protected Resource $resourceReward = Resource::FOOD;
    protected string $incomeUpgrade = "FOOD";
}

class TrainingGrounds extends ResourceRow {
    public string $name = "TRAINING GROUNDS";
    protected Resource $cost = Resource::SERFS;
    protected Resource $resourceReward = Resource::SOLDIERS;
    protected string $incomeUpgrade = "SOLDIERS";
}