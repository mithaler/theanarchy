<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\PlayerAttackCard;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Resource;

class Muster extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::MUSTER,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} may use Muster tokens'),
            descriptionMyTurn: clienttranslate('${you} may use Muster tokens'),
            transitions: [
                'useTactics' => StateConstants::USE_TACTICS,
                'attacksDone' => StateConstants::ATTACKS_DONE,
            ],
        );
    }

    public function getArgs(int $currentPlayerId) {
        // the frontend already knows how many defenders/muster tokens the player has

        // what's the next attack?
        $attack = $this->game->nextPlayerAttack($currentPlayerId);
        $nextState = $this->nextState($currentPlayerId, $attack);
        if ($nextState) {
            return ['nextState' => $nextState, '_no_notify' => true];
        }
        return ['attack' => $this->game->attackRepr($attack, $currentPlayerId)];
    }

    public function nextState(int $playerId, ?PlayerAttackCard $attack): ?string {
        // if there's no attack, we shouldn't be here
        if (!$attack) {
            return 'attacksDone';
        }

        // skip if:
        // * the attack doesn't allow defending with workers
        // * the player has no muster tokens
        // * the player has no defenders
        // * the player isn't actually taking any damage
        if (
            !$attack->card->defenderWorkers ||
            $this->game->resources(Resource::MUSTER_TOKENS) === 0 ||
            \count($this->game->getCastleDefenders($playerId)) === 0 ||
            !$this->game->receivingDamage($playerId, $attack)
        ) {
            return 'useTactics';
        }
        return null;
    }

    public function onEnteringState(int $currentPlayerId, array $args) {
        if (\array_key_exists('nextState', $args)) {
            $this->game->gamestate->nextPrivateState($currentPlayerId, $args['nextState']);
        }
    }

    #[PossibleAction]
    public function actMuster(
        int $currentPlayerId,
        #[IntParam(name: 'id')] int $id,
        #[StringParam(enum: ['top', 'bottom', 'left', 'right'])] string $target,
    ) {
        // TODO: check validity, move defender, notify
    }

    #[PossibleAction]
    public function actPass(int $currentPlayerId) {
        $this->game->gamestate->nextPrivateState($currentPlayerId, 'useTactics');
    }

    public function zombie() {
        // TODO: just take them
    }
}