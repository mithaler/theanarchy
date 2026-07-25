<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Constants.php");

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

/**
 * The outer, initial check boxes state. (The real heavy lifting is in CheckBoxes,
 * the initialPrivate state this immediately transitions everyone to.)
 */
class CheckBoxesDone extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::CHECK_BOXES_DONE,
            type: StateType::PRIVATE,
            description: clienttranslate('Waiting for other players to check boxes'),
            descriptionMyTurn: clienttranslate('Other players may check boxes'),
            updateGameProgression: true,
            initialPrivate: StateConstants::CHECK_BOXES,
        );
    }

    function onEnteringState(int $currentPlayerId) {
        $this->gamestate->setPlayerNonMultiactive($currentPlayerId, "attacks");
    }

    function zombie() {}
}
