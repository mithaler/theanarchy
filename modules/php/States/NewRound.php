<?php

declare(strict_types=1);

namespace Bga\Games\theanarchy\States;

use Bga\GameFramework\States\GameState;
use Bga\GameFramework\StateType;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Resource;

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
                    'cards' => $this->game->attackSetRepr($this->game->playerAttacks($playerId), $playerId),
                ],
            );
        }

        $incomes = $this->game->getObjectListFromDB(<<<SQL
            SELECT player_id, LCASE(section) AS section, MAX(box_id) AS gain
            FROM checked_box
            WHERE section IN (
                'SERFS', 'CRAFTSMEN', 'MATERIALS', 'PATRONS', 'SILVER',
                'FOOD', 'SOLDIERS', 'KNIGHTS', 'MUSTER', 'RAMPARTS'
            )
            GROUP BY player_id, section;
            SQL
        );
        foreach ($incomes as $income) {
            if ($income["section"] === 'ramparts' && $income['gain'] >= 2) {
                $this->game->resources(Resource::MUSTER_TOKENS)->inc(
                    (int) $income['player_id'], (int) floor($income['gain'] / 2)
                );
            } else {
                $this->game->resources(Resource::from($income['section']))->inc(
                    (int) $income['player_id'], (int) $income['gain']
                );
            }
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