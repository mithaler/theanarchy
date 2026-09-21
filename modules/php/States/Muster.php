<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\UserException;
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
            $this->game->resources(Resource::MUSTER_TOKENS)->get($playerId) === 0 ||
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
        #[StringParam(name: 'toSide', enum: ['top', 'bottom', 'left', 'right'])] string $toSide,
    ) {
        $defenders = $this->game->getCastleDefenders($currentPlayerId);
        $targetDefender = null;
        foreach ($defenders as $defenderGroup) {
            foreach ($defenderGroup as $def) {
                if ($def->id === $id) {
                    $targetDefender = $def;
                    break 2;
                }
            }
        }
        if (!$targetDefender) {
            throw new UserException("Invalid defender ID");
        }
        if ($targetDefender->side->value === $toSide) {
            throw new UserException("Defender is already on that side");
        }
        if ($this->game->resources(Resource::MUSTER_TOKENS)->get($currentPlayerId) < 1) {
            throw new UserException("You don't have any Muster tokens");
        }

        $this->game->resources(Resource::MUSTER_TOKENS)->inc($currentPlayerId, -1);
        Game::DbQuery("UPDATE castle_defender SET side = '$toSide' WHERE id = $id");
        $this->game->notify->all(
            'useMuster',
            '${player_name} uses a Muster token to move a ${workerType} from ${fromSide} to ${toSide}',
            [
                ...$this->game->playerNotifArgs($currentPlayerId),
                'workerType' => $targetDefender->type,
                'fromSide' => $targetDefender->side->value,
                'toSide' => $toSide,
                'id' => $id,
            ],
        );
    }

    #[PossibleAction]
    public function actPass(int $currentPlayerId) {
        $this->game->gamestate->nextPrivateState($currentPlayerId, 'useTactics');
    }

    public function zombie() {
        // TODO: just take them
    }
}