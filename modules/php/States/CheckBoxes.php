<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Boxes/Sections.php");
require_once(__DIR__ . "/../Constants.php");

use const BGA\Games\theanarchy\Boxes\SECTIONS;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\Actions\Types\IntParam;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

/**
 * The basic "checking boxes" state (private, coming from CheckBoxesLoop).
 * Other private states are for when the player has to make a choice based
 * on something they previously did (such as allocating domain cards).
 */
class CheckBoxes extends GameState {

    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::CHECK_BOXES,
            type: StateType::PRIVATE,
            descriptionMyTurn: clienttranslate('${you} may check boxes'),
            transitions: [
                'stvalentinesfestival' => StateConstants::ST_VALENTINES_FESTIVAL,
            ]
        );
    }

    function onEnteringState(int $activePlayerId) {
        $this->gamestate->setAllPlayersMultiactive();
    }

    function getArgs(int $playerId) {
        $boxes = $this->game->allCheckedBoxes($playerId);
        $availboxes = [$playerId => $this->game->getAvailableBoxes($playerId, $boxes)];
        $walls = [$playerId => availableWalls($this->game, $playerId)];
        return ["availableBoxes" => $availboxes, "availableWalls" => $walls];
    }

    #[PossibleAction]
    function actCheckBox(
        int $currentPlayerId,
        // spaces prevent alphanum validation; don't ever put this directly in SQL!
        #[StringParam(name: "section")] string $section,
        #[IntParam(name: "boxId")] int $boxId,
        #[StringParam(name: "choice", alphanum_dash: true)] string | null $choice,
        #[IntParam(name: "writtenValue")] int | null $writtenValue = null
    ) {
        if (!\array_key_exists($section, SECTIONS)) {
            throw new UserException("Section $section does not exist");
        }

        $currBoxes = $this->game->allCheckedBoxes($currentPlayerId);
        $validBoxes = SECTIONS[$section]->validBoxes($this->game, $currentPlayerId, $currBoxes);
        if (!\in_array($boxId, $validBoxes)) {
            throw new UserException("Box not available");
        }

        // notify rewards
        SECTIONS[$section]->check($this->game, $currentPlayerId, $boxId, true, $choice);

        // notify new available boxes
        $newAllCheckedBoxes = $this->game->allCheckedBoxes($currentPlayerId);
        $this->game->notifyNewAvailable(
            $currentPlayerId,
            $this->game->getAvailableBoxes($currentPlayerId, $newAllCheckedBoxes),
        );
    }

    #[PossibleAction]
    function actPass() {

    }

    function zombie() {
        $this->actPass();
    }

}