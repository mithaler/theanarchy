<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Boxes\BoxType;

require_once(__DIR__ . "/BoxType.php");

abstract class ProductionRow extends BoxType {
    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        return []; // production rows can only be checked by other boxes
    }

    public function check(Game $game, int $playerId, int|null $boxId = null, bool $pay = false): Reward {
        $currValue = (int) Game::getUniqueValueFromDB(
            "SELECT MAX(box_id) FROM checked_box WHERE player_id = $playerId AND section = '$this->name'"
        );
        $boxId = $currValue + 1;
        Game::checkBox($playerId, $this->name, $boxId);
        return Reward::none($this->name, $boxId);
    }
}

class Serfs extends ProductionRow {
    public string $name = "SERFS";
}

class Craftsmen extends ProductionRow {
    public string $name = "CRAFTSMEN";
}

class Materials extends ProductionRow {
    public string $name = "MATERIALS";
}

class Patrons extends ProductionRow {
    public string $name = "PATRONS";
}

class Silver extends ProductionRow {
    public string $name = "SILVER";
}

class Food extends ProductionRow {
    public string $name = "FOOD";
}

class Soldiers extends ProductionRow {
    public string $name = "SOLDIERS";
}

class Knights extends ProductionRow {
    public string $name = "KNIGHTS";
}
