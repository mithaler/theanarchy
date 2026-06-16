<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Resource;

require_once(__DIR__ . "/BoxType.php");

abstract class UnclickableRow extends BoxType {
    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        return []; // production rows can only be checked by other boxes
    }

    abstract protected function reward(string $section, int $boxId): Reward;

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = false, ?string $choice = null): Reward {
        $currValue = (int) Game::getUniqueValueFromDB(
            "SELECT MAX(box_id) FROM checked_box WHERE player_id = $playerId AND section = '$this->name'"
        );
        $boxId = $currValue + 1;
        Game::checkBox($playerId, $this->name, $boxId);
        return $this->reward($this->name, $boxId);
    }
}

abstract class ProductionRow extends UnclickableRow {
    protected function reward(string $section, int $boxId): Reward {
        return Reward::none($section, $boxId);
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

abstract class PointRow extends UnclickableRow {
    abstract protected Resource $firstWorkerReward { get; }
    abstract protected Resource $secondWorkerReward { get; }
    abstract protected string $firstPointReward { get; }
    abstract protected string $secondPointReward { get; }
    abstract protected string $thirdPointReward { get; }

    protected function reward(string $section, int $boxId): Reward {
        return match ($boxId) {
            2, 8, 14, 22 => Reward::resources($section, $boxId, [$this->firstWorkerReward->value => 1]),
            5, 11, 18 => Reward::resources($section, $boxId, [$this->secondWorkerReward->value => 1]),
            // there's a pattern here, but it's not worth proving how clever I am by not just hard-coding it
            16 => Reward::boxes($section, $boxId, [$this->firstPointReward]),
            20 => Reward::boxes($section, $boxId, [$this->secondPointReward]),
            24 => Reward::boxes($section, $boxId, [$this->thirdPointReward]),
            default => Reward::none($section, $boxId),
        };
    }
}

class Bravery extends PointRow {
    public string $name = "BRAVERY";
    protected Resource $firstWorkerReward = Resource::SERFS;
    protected Resource $secondWorkerReward = Resource::SOLDIERS;
    protected string $firstPointReward = "LOYALTY";
    protected string $secondPointReward = "INFLUENCE";
    protected string $thirdPointReward = "MIGHT";
}

class Loyalty extends PointRow {
    public string $name = "LOYALTY";
    protected Resource $firstWorkerReward = Resource::SOLDIERS;
    protected Resource $secondWorkerReward = Resource::SERFS;
    protected string $firstPointReward = "INFLUENCE";
    protected string $secondPointReward = "MIGHT";
    protected string $thirdPointReward = "BRAVERY";
}

class Influence extends PointRow {
    public string $name = "INFLUENCE";
    protected Resource $firstWorkerReward = Resource::CRAFTSMEN;
    protected Resource $secondWorkerReward = Resource::PATRONS;
    protected string $firstPointReward = "MIGHT";
    protected string $secondPointReward = "BRAVERY";
    protected string $thirdPointReward = "LOYALTY";
}

class Might extends PointRow {
    public string $name = "MIGHT";
    protected Resource $firstWorkerReward = Resource::PATRONS;
    protected Resource $secondWorkerReward = Resource::CRAFTSMEN;
    protected string $firstPointReward = "BRAVERY";
    protected string $secondPointReward = "LOYALTY";
    protected string $thirdPointReward = "INFLUENCE";
}