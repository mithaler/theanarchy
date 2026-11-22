<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\Boxes;

require_once(__DIR__ . "/../Constants.php");
require_once(__DIR__ . "/BoxType.php");

use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\ScenarioCard;
use BGA\Games\theanarchy\SiegeRequirement;

class CastleAttack extends BoxType {
    public string $name = "CASTLE ATTACK";

    /**
     * @param ScenarioCard[] $scenario
     * @param int $playerPos
     * @param Game $game
     * @param int $round
     * @param int $playerId
     */
    private function hasStrength(array $scenario, int $playerPos, Game $game, int $round, int $playerId) {
        $soldiers = $game->resources(Resource::SOLDIERS)->get($playerId);
        $knights = $game->resources(Resource::KNIGHTS)->get($playerId);
        $str = $soldiers + ($knights * 2);

        $targetStr = $scenario[$round]->requiredStrength;
        for ($i = $playerPos + 1; $i < $round; $i++) {
            $targetStr += $scenario[$i]->strOnBack;
        }
        return $str >= $targetStr;
    }

    /**
     * Returns whether the player meets the non-worker requirements for taking this castle.
     * @param ScenarioCard $card
     * @param Box[] $currBoxes
     * @param int $food
     */
    private function meetsReqs(Game $game, ScenarioCard $card, array $currBoxes, int $playerId): bool {
        $food = $game->resources(Resource::FOOD)->get($playerId);
        return array_all($card->siegeRequirements, fn ($req) => match ($req) {
                SiegeRequirement::FOOD => $food >= 1,
                SiegeRequirement::TWO_FOOD => $food >= 2,
                SiegeRequirement::LADDER => self::idFilled($playerId, "SIEGECRAFT_construction", 1, $currBoxes),
                SiegeRequirement::BALLISTA => self::idFilled($playerId, "SIEGECRAFT_construction", 2, $currBoxes),
                SiegeRequirement::TREBUCHET => self::idFilled($playerId, "SIEGECRAFT_construction", 4, $currBoxes),
                SiegeRequirement::BATTERING_RAM => self::idFilled($playerId, "SIEGECRAFT_construction", 5, $currBoxes),
                SiegeRequirement::TOWER => self::idFilled($playerId, "SIEGECRAFT_construction", 7, $currBoxes),
                SiegeRequirement::CRANE => self::idFilled($playerId, "SIEGECRAFT_construction", 8, $currBoxes),
                SiegeRequirement::BLACK_HORSE => self::idFilled($playerId, "STABLES", 4, $currBoxes),
                SiegeRequirement::WHITE_HORSE => self::idFilled($playerId, "STABLES", 8, $currBoxes),
            }
        );
    }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        // skip this entirely in siege mode
        if ($game->mode() === 'siege') {
            return [];
        }

        $round = $game->round->get();

        // did they already do it this round?
        $highest = $this->highestCheckedBox($playerId, $currBoxes);
        if ($highest >= $round) {
            return [];
        }

        $scenario = ScenarioCard::setForGame($game->mode(), $game->difficulty());
        $currScenario = $scenario[$round];
        if (
            $this->meetsReqs($game, $currScenario, $currBoxes, $playerId) &&
            $this->hasStrength($scenario, $highest, $game, $round, $playerId)
        ) {
            return [$round];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): ?Reward {
        throw new \Exception('Not implemented');
    }

}
