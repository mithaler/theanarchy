<?php

declare(strict_types=1);

namespace Bga\Games\theanarchy\States;

use Bga\GameFramework\States\GameState;
use Bga\GameFramework\StateType;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

class NewRound extends GameState {
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: StateConstants::NEW_ROUND,
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

            // deal Final Escalade card; no drama here
            $feCard = $this->game->finalEscaladeCards->pickCard('deck', $playerId);

            // deal Attack cards and set their positions and visibility
            $attackCardCount = $this->game->round->get();
            $attackCards = $this->game->attackCards->pickCards($attackCardCount, 'deck', $playerId);
            foreach ($attackCards as $idx => $attack) {
                $position = $idx + 2;
                $state = $position === 3 || $position == 5 ? "hidden" : "visible";
                Game::DbQuery(
                    "UPDATE attack_card
                    SET position = $position, state = '$state'
                    WHERE card_id = {$attack['id']}"
                );
            }

            $this->game->notify->all(
                'newAttackCards',
                clienttranslate('${player_name} draws attack cards for the round'),
                [
                    ...$this->game->playerNotifArgs($playerId),
                    'cards' => $this->game->attackCardRepr($feCard, $attackCards),
                ],
            );
        }

        // TODO: board resource income!
        // that includes Muster tokens!

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