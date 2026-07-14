<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

class Lammas extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::LAMMAS,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must fill in Lammas flags'),
            descriptionMyTurn: clienttranslate('${you} must fill in Lammas flags'),
            transitions: [
                'checkboxes' => StateConstants::CHECK_BOXES,
            ]
        );
    }

    public function getArgs(int $currentPlayerId) {
        return ["availableBoxes" => [
            "LAMMAS" => SECTIONS["LAMMAS"]->validBoxes(
                $this->game,
                $currentPlayerId,
                $this->game->allCheckedBoxes($currentPlayerId),
            )
        ]];
    }

    #[PossibleAction]
    function actCheckBox(int $currentPlayerId, #[IntParam(name: "boxId")] int $boxId) {
        // does its own validation! (to avoid multiple DB lookups)
        SECTIONS["LAMMAS"]->check($this->game, $currentPlayerId, $boxId, false);
    }

    function zombie() {
        // TODO player ID? just choose soldiers I guess
    }
}
