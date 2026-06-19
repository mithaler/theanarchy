<?php

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

class Chapel extends BoxType {
    public string $name = "CHAPEL";

    private const array WORSHIP_THRESHOLDS = [1 => 2, 6 => 5, 11 => 8];

    private function cost($boxId): array {
        return match ($boxId) {
            1, 6, 11 => [Resource::CRAFTSMEN, Resource::MATERIALS],
            2, 7, 12 => [Resource::FOOD],
            3, 8, 13 => [Resource::MATERIALS],
            4, 9, 14 => [Resource::SILVER],
            default => [],
        };
    }

    private function available(Game $game, int $playerId, int $boxId, array $currBoxes): bool {
        // did I fill it already?
        if ($this->idFilled($playerId, $this->name, $boxId, $currBoxes)) {
            return false;
        }

        // can I afford it?
        if (!$this->canPay($game, $playerId, $this->cost($boxId))) {
            return false;
        }

        // do I have enough Worship?
        if (array_key_exists($boxId, self::WORSHIP_THRESHOLDS)) {
            $worship = $this->highestCheckedBox($playerId, $currBoxes, section: "WORSHIP");
            if ($worship < self::WORSHIP_THRESHOLDS[$boxId]) {
                return false;
            }
        }

        // do I have the crane?
        if ($boxId == 6 && !$this->hasSmallCrane($playerId, $currBoxes)) {
            return false;
        } else if ($boxId == 11 && !$this->hasLargeCrane($playerId, $currBoxes)) {
            return false;
        }
        return true;
    }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $highest = $this->highestCheckedBox($playerId, $currBoxes);
        $choices = match (true) {
            $highest == 0 => [1],
            $highest < 5 => [2, 3, 4],
            $highest == 5 => [6],
            $highest < 10 => [7, 8, 9],
            $highest == 10 => [11],
            $highest < 14 => [12, 13, 14]
        };
        return array_values(array_filter($choices, fn ($boxId) =>
            $this->available($game, $playerId, $boxId, $currBoxes)
        ));
    }

    private function checkChapelComplete(int $playerId, int $boxId, array $currBoxes): Reward {
        $chapelSet = match ($boxId) {
            2, 3, 4 => [2, 3, 4],
            7, 8, 9 => [7, 8, 9],
            12, 13, 14 => [12, 13, 14],
            default => throw new UserException("Chapel broken")
        };
        if (array_all($chapelSet, fn ($chapelBox) => 
            $chapelBox == $boxId ||
            $this->idFilled($playerId, $this->name, $chapelBox, $currBoxes)
        )) {
            return Reward::boxes($this->name, $boxId, [$this->name => [$chapelSet[2] + 1]]);
        } else {
            return Reward::none($this->name, $boxId);
        }
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $reward = match ($boxId) {
            1, 6, 11 => Reward::none($this->name, $boxId),
            2, 3, 4, 7, 8, 9, 12, 13, 14 => $this->checkChapelComplete($playerId, $boxId, $game->allCheckedBoxes($playerId)),
            5, 10, 15 => new Reward($this->name, $boxId, [Resource::PATRONS->value => 1], ["SILVER", "INFLUENCE"]),
        };
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, $this->cost($boxId), $reward);
    }
}

class KnightsTraining extends BoxType {
    public string $name = "KNIGHTS TRAINING";

    private const array WORSHIP_THRESHOLDS = [1 => 2, 2 => 4, 3 => 6, 4 => 7, 5 => 8, 6 => 9]; 

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $highest = $this->highestCheckedBox($playerId, $currBoxes);
        if (
            $highest == 6 ||
            ($highest == 0 && !$this->idFilled($playerId, "CHAPEL", 1, $currBoxes))
        ) {
            return [];
        }

        $worship = $this->highestCheckedBox($playerId, $currBoxes, section: "WORSHIP");
        if (
            $worship >= self::WORSHIP_THRESHOLDS[$highest + 1] &&
            $this->canPay($game, $playerId, [Resource::PATRONS])
        ) {
            return [$highest + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $count = 2 + intdiv($boxId - 1, 2);
        $cards = $game->drawDomainCards($playerId, $count, false);
        $uniqSymbols = array_reduce($cards, function ($set, $card) {
            $set[$card->card->symbol->value] = true;
            return $set;
        }, []);

        // TODO: make this notification way better
        $game->notify->all("knightstraining", \clienttranslate('${player_name} sees symbols: ${symbols}'), [
            "player_id" => $playerId,
            "player_name" => $game->getPlayerNameById($playerId),
            "symbols" => implode(", ", array_keys($uniqSymbols)),
            "boxSection" => $this->name,
            "boxId" => $boxId,
        ]);

        $symbolCount = count($uniqSymbols);
        if ($symbolCount < 2) {
            $reward = Reward::none($this->name, $boxId);
        } else if ($symbolCount == 2) {
            $reward = Reward::resources($this->name, $boxId, [Resource::SOLDIERS]);
        } else if ($symbolCount == 3) {
            // no reward immediately; we have to change states to get a choice
            $reward = Reward::none($this->name, $boxId);
            $game->gamestate->nextPrivateState($playerId, "knightstraining");
        } else if ($symbolCount == 4) {
            $reward = new Reward($this->name, $boxId, [Resource::SOLDIERS->value => 1], ["KNIGHTS", "MIGHT"]);
        }
        return $this->basicCheckBox($game, $playerId, $boxId, $pay, [Resource::PATRONS], $reward);
    }
}