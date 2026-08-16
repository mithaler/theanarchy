<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Constants.php");

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

/**
 * Ends a player's checkboxing.
 * TODO: Add a way to "undo pass" and let the player do more stuff?
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

    public function getArgs() {
        // the frontend does not need to see this state because it immediately gets unset
        // ...and if there's an immediate transition, it would otherwise get confused by
        // the reverse notif order
        return ['_no_notify' => true];
    }

    public function onEnteringState(int $currentPlayerId) {
        $this->gamestate->unsetPrivateState($currentPlayerId);
        $this->gamestate->setPlayerNonMultiactive($currentPlayerId, 'beginDefense');
    }

    function zombie() {}
}
