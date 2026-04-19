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
            function ($max, $box) {
                if ($box->section == $this->section && $box->boxId > $max) {
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

class QuarryForest extends BasicRow {
    public string $section = "QUARRY & FOREST";
    public int $boxCount = 13;

    function canPay(Game $game, int $playerId): bool {
        return $game->resources(Resource::SERFS)->get($playerId) > 0;
    }

    public function reward(Game $game, int $boxId): array {
        return match ($boxId) {
            1, 5, 9 => [Resource::MATERIALS],
            3, 7, 11 => [Resource::MATERIALS, "MATERIALS"],
            13 => [Resource::MATERIALS, "MATERIALS", "LOYALTY"],
            default => [],
        };
    }
}