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
                if ($box->playerId == $playerId && $box->section == $this->section && $box->boxId > $max) {
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

    abstract public Resource $cost { get; }
    abstract public Resource $resourceReward { get; }
    abstract public string $incomeUpgrade { get; }
    abstract public string $pointUpgrade { get; }

    function canPay(Game $game, int $playerId): bool {
        return $game->resources($this->cost)->get($playerId) > 0;
    }

    public function reward(Game $game, int $boxId): Reward {
        return match ($boxId) {
            1, 5, 9 => Reward::resources([$this->resourceReward => 1]),
            3, 7, 11 => new Reward([$this->resourceReward => 1], [$this->incomeUpgrade]),
            13 => new Reward([$this->resourceReward => 1], [$this->incomeUpgrade, $this->pointUpgrade]),
            default => Reward::none(),
        };
    }
}

class QuarryForest extends ResourceRow {
    public string $name = "QUARRY & FOREST";
    public Resource $cost = Resource::SERFS;
    public Resource $resourceReward = Resource::MATERIALS;
    public string $incomeUpgrade = "MATERIALS";
    public string $pointUpgrade = "LOYALTY";
}

class Farms extends ResourceRow {
    public string $name = "FARMS";
    public Resource $cost = Resource::SERFS;
    public Resource $resourceReward = Resource::FOOD;
    public string $incomeUpgrade = "FOOD";
    public string $pointUpgrade = "LOYALTY";  // TODO ???
}

class TrainingGrounds extends ResourceRow {
    public string $name = "TRAINING GROUNDS";
    public Resource $cost = Resource::SERFS;
    public Resource $resourceReward = Resource::SOLDIERS;
    public string $incomeUpgrade = "SOLDIERS";
    public string $pointUpgrade = "LOYALTY";  // TODO ???
}