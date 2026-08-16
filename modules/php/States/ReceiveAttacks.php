<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

/**
 * There are two types of defenses:
 * * static (walls/towers/moat/gates)
 * * choice-based (workers/tactics)
 * We compute static ones automatically, taking them into account when computing attack strength.
 *
 * The choices the player must make when receiving an attack, in order:
 * * Muster? (Class: Muster)
 *   * Ask if: there's available muster + at least one worker anywhere
 *   * Do not try to be clever about when to ask! Just always do it
 * * Tactics? (Class: UseTactics)
 *   * Ask if: there's an available tactic, and attack strength not blocked by static defenses
 *   * Ask on all sides simultaneously
 * * Which workers take hits, and do they get beer?
 *   * Ask which workers if: defense strength > attack strength after tactics
 *   * Ask about beer if: any unexhausted soldiers/craftsmen are taking hits
 *   * Implement as a multi-plus/minus button, with knights, soldiers/craftsmen, and beer
 */

/**
 * The outer state for dealing with attacks; the actual work is done by private states, as
 * described above.
 */
class ReceiveAttacks extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::RECEIVE_ATTACKS,
            type: StateType::MULTIPLE_ACTIVE_PLAYER,
            description: clienttranslate('Players are receiving attacks'),
            initialPrivate: StateConstants::MUSTER,
        );
    }

    public function onEnteringState() {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->initializePrivateStateForAllActivePlayers();
    }
}