<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

class Discontent extends BoxType {
    public string $name = "DISCONTENT";

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        return []; // never directly checkable
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = true, string|null $choice = null): Reward|null {
        $boxId = $this->highestCheckedBox($playerId, $game->allCheckedBoxes($playerId, $this->name)) + 1;
        Game::checkBox($playerId, $this->name, $boxId);
        return Reward::none($this->name, $boxId);
    }
}

class Joy extends Discontent {
    public string $name = "JOY";
}
