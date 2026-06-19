<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use BGA\Games\theanarchy\Boxes\Reward;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\StateConstants;

class KnightsTraining extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::KNIGHTS_TRAINING,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must select a Knight\'s Training reward'),
            descriptionMyTurn: clienttranslate('${you} must select a Knight\'s Training reward'),
            transitions: [
                'checkboxes' => StateConstants::CHECK_BOXES,
            ]
        );
    }

    public function getArgs() {
        // if we're in this state, there are only two possibilities
        return [];
    }

    #[PossibleAction]
    function actMakeChoice(
        int $currentPlayerId,
        #[StringParam(enum: ["soldiers", "KNIGHTS"])] string $choice,
    ) {
        // use a reward for this, even though we don't know (or care) what box ID it came from?
        if ($choice == "soldiers") {
            $reward = Reward::resources("KNIGHTS TRAINING", 0, [Resource::SOLDIERS]);
        } else if ($choice == "KNIGHTS") {
            $reward = Reward::boxes("KNIGHTS TRAINING", 0, ["KNIGHTS"]);
        }

        // we don't need the other parts of checking a box
        $reward->grant($this->game, $currentPlayerId);

        // TODO notify?
        $this->game->gamestate->nextPrivateState($currentPlayerId, "checkboxes");
    }

    function zombie() {
        // TODO player ID? just choose soldiers I guess
    }
}
