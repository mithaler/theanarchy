<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use const BGA\Games\theanarchy\Boxes\SECTIONS;

class Michaelmas extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::MICHAELMAS,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must fill in Michaelmas numbers'),
            descriptionMyTurn: clienttranslate('${you} must fill in Michaelmas numbers'),
            transitions: [
                'checkboxes' => StateConstants::CHECK_BOXES,
            ]
        );
    }

    public function getArgs(int $currentPlayerId) {
        $michaelmasBox = SECTIONS["MICHAELMAS"];
        $nums = array_map(
            fn($card) => $card->card->michaelmas,
            $this->game->playerHandCards($currentPlayerId),
        );
        $currBoxes = $this->game->allCheckedBoxes($currentPlayerId, section: "MICHAELMAS");
        $checked = $michaelmasBox->validBoxesByNum($nums, $currentPlayerId, $currBoxes);
        /*
        $avail = array_values(array_unique(
            array_reduce($checked, fn ($out, $boxes) => [...$out, ...$boxes], [])
        ));*/

        return ["availableBoxesByNum" => $checked];
    }

    #[PossibleAction]
    function actCheckBox(
        int $currentPlayerId,
        #[IntParam(name: "boxId")] int $boxId,
        #[StringParam(name: "choice")] string $choice,
    ) {
        // uniquely, does its own validation! (to avoid multiple DB lookups)
        $reward = SECTIONS["MICHAELMAS"]->check($this->game, $currentPlayerId, $boxId, false, $choice);
        $reward->grant($this->game, $currentPlayerId);
    }

    function zombie() {
        // TODO player ID? just pass
    }
}
