<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\NotificationMessage;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Resource;

use const BGA\Games\theanarchy\Boxes\SECTIONS;

class AttacksDone extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::ATTACKS_DONE,
            type: StateType::GAME,
            transitions: ['newRound' => StateConstants::NEW_ROUND],
        );
    }

    private function determination(int $round): int {
        // 1, 2, 2, 3, 4
        return $round <= 2 ? $round : $round - 1;
    }

    private function playerRoundCleanup(int $round) {
        $determination = $this->determination($round);
        $players = $this->game->loadPlayersBasicInfos();
        foreach ($players as $playerId => $player) {
            $playerNotifArgs = ['player_id' => $playerId, 'player_name' => $player['player_name']];

            // transform remaining defenders into serfs at 3:1 strength:serfs
            $defenders = $this->game->getCastleDefenders($playerId);
            $totalStrength = $this->game->totalStrength(array_merge([], ...array_values($defenders)));
            $serfs = $totalStrength / 3;
            if ($serfs > 0) {
                $this->game->resources(Resource::SERFS)->inc($playerId, $serfs);
                $this->game->notify->all(
                    'defendersToSerfs',
                    clienttranslate('${player_name} gets ${serfs} serf(s) from remaining defenders'),
                    [...$playerNotifArgs, 'serfs' => $serfs],
                );
            }

            // zero out unused muster tokens
            $this->game->resources(Resource::MUSTER_TOKENS)->set($playerId, 0);

            // gain loyalty/discontent based on determination
            // loyalty = determination - failed attack count
            // discontent = determination - loyalty
            $attacks = $this->game->playerAttacks($playerId);
            $loyalty = $determination - count(array_filter($attacks, fn ($atk) => $atk->state === 'failed'));
            $discontent = $determination - $loyalty;
            for ($i = 0; $i < $loyalty; $i++) {
                $reward = SECTIONS["LOYALTY"]->check($this->game, $playerId, null, false);
                $reward->grant($this->game, $playerId);
            }
            for ($i = 0; $i < $discontent; $i++) {
                SECTIONS["DISCONTENT"]->check($this->game, $playerId, null, false);
            }
        }

        // remove all defenders
        Game::DbQuery('DELETE FROM castle_defender');

        // discard all attack/FE cards and reshuffle
        $this->game->attackCards->moveAllCardsInLocationKeepOrder('hand', 'deck');
        $this->game->finalEscaladeCards->moveAllCardsInLocationKeepOrder('hand', 'deck');
        $this->game->attackCards->shuffle('deck');
        $this->game->finalEscaladeCards->shuffle('deck');

        // tell the frontend to do clean up too
        $this->game->notify->all(
            'roundCleanup', clienttranslate('All players discard all defenders and attack cards')
        );
    }

    public function onEnteringState() {
        //$this->game->gamestate->unsetPrivateStateForAllPlayers();

        // clean up all players' defenders/attacks (next state will deal new attacks)
        $round = $this->game->round->get();
        $this->playerRoundCleanup($round);

        if ($round === 5) {
            // TODO: the game is over!
        } else {
            $newRound = $round + 1;
            $this->game->round->inc(1, new NotificationMessage(
                clienttranslate('Entering round ${round}'),
                ['round' => $newRound],
            ));
            $this->gamestate->nextState('newRound');
        }
    }
}