<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Resource;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

class BeginDefense extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::BEGIN_DEFENSE,
            type: StateType::GAME,
            transitions: ['receiveAttacks' => StateConstants::RECEIVE_ATTACKS],
        );
    }

    public function getArgs() {}

    public function onEnteringState() {
        $round = $this->game->round->get();
        $playerIds = $this->game->allPlayerIds();

        foreach ($playerIds as $playerId) {
            $playerNotifArgs = $this->game->playerNotifArgs($playerId);

            // pay food
            $food = $this->game->resources(Resource::FOOD)->get($playerId);
            $pay = $food - $round;
            if ($pay < 0) {
                $discontent = -$pay;
                $this->game->notify->all(
                    "foodDiscontent",
                    clienttranslate('${player_name} is unable to pay ${round} Food and suffers ${discontent} Discontent'),
                    [
                        ...$playerNotifArgs,
                        'round' => $round,
                        'discontent' => $discontent,
                    ],
                );
                for ($i = 0; $i < $discontent; $i++) {
                    $disc = SECTIONS["DISCONTENT"]->check($this->game, $playerId, pay: false);
                    $disc->grant($this->game, $playerId);
                }
            } else {
                $this->game->notify->all(
                    "foodDiscontent",
                    clienttranslate('${player_name} pays ${round} Food'),
                    [
                        ...$playerNotifArgs,
                        'round' => $round,
                    ],
                );
            }

            // discard remaining (the frontend warns about enrolling defenders first)
            foreach ([
                Resource::SERFS, Resource::CRAFTSMEN, Resource::PATRONS, Resource::SOLDIERS,
                Resource::KNIGHTS, Resource::SILVER, Resource::FOOD, Resource::MATERIALS,
            ] as $res) {
                $this->game->resources($res)->set($playerId, 0);
            }
            $this->game->notify->all(
                "discardRemaining",
                clienttranslate('${player_name} discards his remaining resources'),
                $playerNotifArgs,
            );

            // TODO: flip over hidden attack cards!
        }

        // proceed to getting attacked
        $this->gamestate->nextState('receiveAttacks');
    }
}