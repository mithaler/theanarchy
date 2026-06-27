<?php

use BGA\Games\theanarchy\Boxes\BoxType;
use Bga\Games\theanarchy\Boxes\Reward;
use Bga\Games\theanarchy\Game;
use Bga\Games\theanarchy\Resource;

class Tournaments extends BoxType {
    public string $name = "TOURNAMENTS";

    const array THRESHOLDS = [
        1 => 1,
        2 => 2,
        3 => 4,
        4 => 5,
        5 => 8,
        6 => 9,
    ];

    const array COSTS = [
        1 => [Resource::FOOD],
        2 => [Resource::FOOD],
        3 => [Resource::FOOD, Resource::SILVER],
        4 => [Resource::FOOD, Resource::SILVER],
        5 => [Resource::FOOD, Resource::FOOD, Resource::SILVER],
        6 => [Resource::FOOD, Resource::FOOD, Resource::SILVER],
    ];

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $highest = $this->highestCheckedBox($playerId, $currBoxes, ignoreAbove: 6);
        if ($highest == 6) {
            return [];
        }
        $next = $highest + 1;
        $entertainment = $this->highestCheckedBox($playerId, $currBoxes, section: "ENTERTAINMENT");
        if ($entertainment >= self::THRESHOLDS[$next] && $this->canPay($game, $playerId, self::COSTS[$next])) {
            return [$next];
        }
        return [];
    }

    private static function doTourney(Game $game, int $playerId, int $remainingTries = 2): bool {
        $cards = $game->drawDomainCards($playerId, 2, false);
        $self = $cards[0]->card;
        $opponent = $cards[1]->card;

        // who hit?
        $selfHit = $self->lance != $opponent->shield;
        $opponentHit = $opponent->lance != $self->shield;

        if (($selfHit && $opponentHit) || (!$selfHit && !$opponentHit)) {
            // no one hit, are there remaining tries?
            if ($remainingTries == 0) {
                // I win on a default
                $game->notify->all(
                    "tourneyThirdDraw",
                    \clienttranslate('${player_name} wins the tournament by default after a third draw'),
                    ["player_id" => $playerId, "player_name" => $game->getPlayerNameById($playerId)],
                );
                return true;
            }

            // try again
            $game->notify->all(
                "tourneyDraw",
                \clienttranslate('${player_name} DRAWS the tournament and tries again'),
                ["player_id" => $playerId, "player_name" => $game->getPlayerNameById($playerId)],
            );
            return self::doTourney($game, $playerId, $remainingTries - 1);
        } else if ($opponentHit && !$selfHit) {
            // I lose!
            $game->notify->all(
                "tourneyLoss",
                \clienttranslate('${player_name} LOSES the tournament!'),
                ["player_id" => $playerId, "player_name" => $game->getPlayerNameById($playerId)],
            );
            return false;
        } else if (!$opponentHit && $selfHit) {
            // I win!
            $game->notify->all(
                "tourneyWin",
                \clienttranslate('${player_name} WINS the tournament!'),
                ["player_id" => $playerId, "player_name" => $game->getPlayerNameById($playerId)],
            );
            return true;
        } else {
            // this should not happen because the branches above cover every possible case?
            throw new RuntimeException("WTF?");
        }
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): ?Reward {
        if ($boxId <= 6) {
            // you get this reward either way
            $reward = match ($boxId) {
                1, 2 => Reward::resources($this->name, $boxId, [Resource::KNIGHTS]),
                3, 4 => new Reward($this->name, $boxId, [Resource::KNIGHTS], ["BRAVERY"]),
                5, 6 => new Reward($this->name, $boxId, [Resource::KNIGHTS], ["BRAVERY", "INFLUENCE"]),
            };

            // do I win?
            $victory = self::doTourney($game, $playerId);
            if ($victory) {
                $reward->boxes["TOURNAMENTS"] = [$boxId + 6];
            }
            return $this->basicCheckBox($game, $playerId, $boxId, $pay, self::COSTS[$boxId], $reward);
        } else if ($boxId > 6 && !$pay) {
            $reward = match ($boxId) {
                7, 8, 9, 10 => new Reward("TOURNAMENTS", $boxId, [Resource::SOLDIERS], ["INFLUENCE"]),
                11, 12 => new Reward("TOURNAMENTS", $boxId, [Resource::SOLDIERS], ["MIGHT", "JOY"]),
            };
            return $this->basicCheckBox($game, $playerId, $boxId, false, [], $reward);
        }
    }
}