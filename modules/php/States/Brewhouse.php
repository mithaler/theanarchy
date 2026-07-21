<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

class Brewhouse extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::BREWHOUSE,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must fill in beer ingredients'),
            descriptionMyTurn: clienttranslate('${you} must fill in beer ingredients'),
            transitions: [
                'checkboxes' => StateConstants::CHECK_BOXES,
            ]
        );
    }

    public function getArgs(int $currentPlayerId) {
        return ["availableBoxes" => [
            "BREWHOUSE" => SECTIONS["BREWHOUSE"]->validBoxes(
                $this->game,
                $currentPlayerId,
                $this->game->allCheckedBoxes($currentPlayerId),
            )
        ]];
    }

    #[PossibleAction]
    function actCheckBox(int $currentPlayerId, #[IntParam(name: "boxId")] int $boxId) {
        // uniquely, does its own validation! (to avoid multiple DB lookups)
        $reward = SECTIONS["BREWHOUSE"]->check($this->game, $currentPlayerId, $boxId, false);
        $reward->grant($this->game, $currentPlayerId);
    }

    function zombie() {
        // TODO player ID? just choose soldiers I guess
    }
}
