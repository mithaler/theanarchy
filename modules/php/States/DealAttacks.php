<?php

declare(strict_types=1);

namespace Bga\Games\theanarchy\States;

use Bga\GameFramework\States\GameState;
use Bga\GameFramework\StateType;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

class DealAttacks extends GameState {
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: StateConstants::DEAL_ATTACKS,
            type: StateType::GAME,
            transitions: [
                'choosePathCards' => StateConstants::CHOOSE_PATH_CARDS,
            ]
        );
    }

    public function getArgs(): array {
        return [];
    }

    public function onEnteringState() {
        // shuffle both attack decks, and deal attacks to all players
        $this->game->finalEscaladeCards->shuffle('deck');
        $this->game->attackCards->shuffle('deck');

        $players = $this->game->loadPlayersBasicInfos();
        foreach ($players as $player) {
            $playerId = (int) $player['player_id'];
            $feCard = $this->game->finalEscaladeCards->pickCard('deck', $playerId);

            $attackCardCount = $this->game->round->get();
            $attackCards = $this->game->attackCards->pickCards($attackCardCount, 'deck', $playerId);
            $this->game->notify->all(
                'newAttackCards',
                clienttranslate('${player_name} draws attack cards for the round'),
                [
                    'player_id' => $playerId,
                    'player_name' => $player['player_name'],
                    'cards' => $this->game->attackCardRepr($feCard, $attackCards),
                ],
            );
        }

        // draw path cards to the table
        $pathCards = $this->game->pathCards->pickCardsForLocation(\count($players), 'deck', 'table');
        $this->game->notify->all(
            'newPathCards',
            clienttranslate('${count} new Path cards are available'),
            [
                'cards' => array_values(array_map(fn ($card) => $card['type'], $pathCards)),
                'count' => \count($pathCards)
            ],
        );

        $this->gamestate->nextState('choosePathCards');
        // the previous state should have changed to the correct active player!
    }

}