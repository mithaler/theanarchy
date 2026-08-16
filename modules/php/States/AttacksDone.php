<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

class AttacksDone extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::ATTACKS_DONE,
            type: StateType::PRIVATE,
        );
    }

    public function getArgs() {
        // like CheckBoxesDone, immediately unsets private state on entry
        return ['_no_notify' => true];
    }

    public function onEnteringState(int $currentPlayerId) {
        $this->game->gamestate->unsetPrivateState($currentPlayerId);
        $this->game->gamestate->setPlayerNonMultiactive($currentPlayerId, 'newRound');
    }

    public function zombie() {
        // TODO: just take them
    }
}