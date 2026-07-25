<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Boxes/StValentinesFestival.php");

use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;

use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\GameFramework\Actions\Types\IntParam;

use Bga\GameFramework\StateType;
use BGA\Games\theanarchy\StateConstants;
use const BGA\Games\theanarchy\DOMAIN_CARDS;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

const NAME = "ST VALENTINES FESTIVAL";

class StValentinesFestival extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::ST_VALENTINES_FESTIVAL,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must select St. Valentine\'s Festival boxes'),
            descriptionMyTurn: clienttranslate('${you} must select St. Valentine\'s Festival boxes'),
            transitions: [
                'checkboxes' => StateConstants::CHECK_BOXES,
            ]
        );
    }

    private function getAvailableBoxesByGender(int $playerId): array {
        // what cards are in the player's hand?
        $checkedBoxIds = $this->game->getObjectListFromDb(
            "SELECT box_id FROM checked_box WHERE player_id = $playerId AND section = '" . NAME . "'",
            true,
        );
        $cards = $this->game->domainCards->getCardsInLocation("{$playerId}_hand");

        $availBoxes = [];
        foreach ($cards as $card) {
            $valentine = DOMAIN_CARDS[(int) $card["type"]]->valentine;
            $left = $valentine == 1 ? 6 : $valentine - 1;
            $right = $valentine == 6 ? 1 : $valentine + 1;

            $idx = $card["location_arg"];
            $leftBoxId = valentineBoxId($left, $idx);
            $rightBoxId = valentineBoxId($right, $idx);
            foreach ([$leftBoxId, $rightBoxId] as $boxId) {
                if (!\in_array($boxId, $checkedBoxIds)) {
                    $availBoxes[$idx == 0 ? "female" : "male"][] = $boxId;
                }
            }
        }
        return $availBoxes;
    }

    public function getArgs(int $playerId): array {
        return ["availableBoxes" => $this->getAvailableBoxesByGender($playerId)];
    }

    #[PossibleAction]
    function actCheckBox(int $currentPlayerId, #[IntParam(name: "boxId")] int $boxId) {
        $avail = $this->getAvailableBoxesByGender($currentPlayerId);
        foreach ($avail as $gender => $choices) {
            if (\in_array($boxId, $choices)) {
                $reward = SECTIONS[NAME]->check($this->game, $currentPlayerId, $boxId, false);
                $reward->grant($this->game, $currentPlayerId);

                // discard the card for that gender
                $this->game->domainCards->moveAllCardsInLocation(
                    "{$currentPlayerId}_hand",
                    "{$currentPlayerId}_discard",
                    $gender == "female" ? 0 : 1,
                );

                // if that's the last card, return to main checkboxing
                if ($this->game->domainCards->countCardInLocation("{$currentPlayerId}_hand") == 0) {
                    $this->game->gamestate->nextPrivateState($currentPlayerId, "checkboxes");
                } else {
                    $this->game->notify->player(
                        $currentPlayerId, "newValentinesAvailable", "", $this->getArgs($currentPlayerId)
                    );
                }

                return;
            }
        }
        throw new UserException("Not a valid box");
    }

    function zombie() {
        // TODO
    }
}