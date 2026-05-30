<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Boxes/Sections.php");
require_once(__DIR__ . "/../Constants.php");

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

/**
 * The outer, initial check boxes state. (The real heavy lifting is in CheckBoxes,
 * the initialPrivate state this immediately transitions everyone to.)
 */
class CheckBoxesLoop extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::CHECK_BOXES_LOOP,
            type: StateType::MULTIPLE_ACTIVE_PLAYER,
            description: clienttranslate('Players may check boxes'),
            descriptionMyTurn: clienttranslate('${you} may check boxes'),
            updateGameProgression: true,
            initialPrivate: StateConstants::CHECK_BOXES,
        );
    }

    function onEnteringState() {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->initializePrivateStateForAllActivePlayers();
    }
}
