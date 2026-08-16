<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\PlayerAttackCard;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Tactic;

/**
 * Tactics uses are stored in the DB as "castle defenders".
 * // TODO: represent this on the frontend!
 */
class UseTactics extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::USE_TACTICS,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} may use Tactics'),
            descriptionMyTurn: clienttranslate('${you} may use Tactics'),
            transitions: [
                'allocateDefenders' => StateConstants::ALLOCATE_DEFENDERS,
                'attacksDone' => StateConstants::ATTACKS_DONE,
            ],
        );
    }

    public function getArgs(int $currentPlayerId) {
        // the frontend already knows how many tactics uses the player has

        // what's the next attack?
        $attack = $this->game->nextPlayerAttack($currentPlayerId);
        $nextState = $this->nextState($currentPlayerId, $attack);
        if ($nextState) {
            return ['nextState' => $nextState, '_no_notify' => true];
        }
        return ['attack' => $attack];
    }

    private static function availableUses(int $playerId, Tactic $tactic): int {
        return (int) Game::getUniqueValueFromDB(
            "SELECT COUNT(box_id) FROM checked_box
            WHERE player_id = $playerId
            AND section = '{$tactic->value}'
            AND written_value != 1"
        );
    }

    private function nextState(int $playerId, ?PlayerAttackCard $attack): ?string {
        // if there's no attack, we shouldn't be here
        // this shouldn't actually happen, Muster should check this! but just in case
        if (!$attack) {
            return 'attacksDone';
        }

        // skip if:
        // * the attack doesn't allow tactics
        // * the player has no uses of the attack's tactic
        // * the player isn't actually receiving any damage
        if (
            !$attack->card->tactic ||
            $this->availableUses($playerId, $attack->card->tactic) == 0 ||
            !$this->game->receivingDamage($playerId, $attack)
        ) {
            return 'allocateDefenders';
        }
        return null;
    }

    public function onEnteringState(int $currentPlayerId, array $args) {
        if (\array_key_exists('nextState', $args)) {
            $this->game->gamestate->nextPrivateState($currentPlayerId, $args['nextState']);
        }
    }

    #[PossibleAction]
    public function actDone(int $currentPlayerId) {
        $this->game->gamestate->nextPrivateState($currentPlayerId, 'allocateDefenders');
    }

    public function zombie() {
        // TODO: just take them
    }
}