<?php

declare(strict_types=1);

namespace Bga\Games\theanarchy\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

class InitialSetup extends GameState {
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: 10,
            type: StateType::GAME,
            transitions: [
                "checkBoxes" => StateConstants::CHECK_BOXES,
            ]
        );
    }

    public function getArgs(): array {
        return [];
    }

    public function onEnteringState() {
        $this->gamestate->nextState("checkBoxes");
    }

}