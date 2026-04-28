<?php

namespace BGA\Games\theanarchy\States;

require_once(__DIR__ . "/../Boxes/Sections.php");
require_once(__DIR__ . "/../Constants.php");

use const BGA\Games\theanarchy\Boxes\SECTIONS;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;

class CheckBoxes extends GameState {

    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::CHECK_BOXES,
            type: StateType::MULTIPLE_ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} may check boxes'),
            descriptionMyTurn: clienttranslate('${you} may check boxes'),
            updateGameProgression: true,
        );
    }

    function onEnteringState(int $activePlayerId) {
        $this->gamestate->setAllPlayersMultiactive();
    }

    /**
     * Returns all boxes currently checkable by the player.
     * @param int $playerId The player to check.
     * @param array $allCheckedBoxes All boxes checked by the player.
     * @return array<string, int[]>
     */
    private function getAvailableBoxes(int $playerId, array $allCheckedBoxes): array {
        return array_reduce(
            SECTIONS,
            function($acc, $section) use ($allCheckedBoxes, $playerId) {
                $acc[$section->name] = $section->validBoxes($this->game, $playerId, $allCheckedBoxes);
                return $acc;
            },
            [],
        );
    }

    function getArgs() {
        $playerIds = $this->game->allPlayerIds();
        $boxes = $this->game->allCheckedBoxes();
        $out = [];
        foreach ($playerIds as $playerId) {
            $out[$playerId] = $this->getAvailableBoxes($playerId, $boxes);
        }
        return ["availableBoxes" => $out];
    }

    #[PossibleAction]
    function actCheckBox(int $currentPlayerId, string $section, string $boxId, string | null $writtenValue = null) {
        $currBoxes = $this->game->allCheckedBoxes($currentPlayerId);
        $validBoxes = $this->getAvailableBoxes($currentPlayerId, $currBoxes);
        if (!\array_key_exists($section, $validBoxes) || !\in_array($boxId, $validBoxes[$section])) {
            throw new UserException("Box not available");
        }

        SECTIONS[$section]->check($this->game, $currentPlayerId, $boxId, true);
    }

    #[PossibleAction]
    function actPass() {

    }

    function zombie() {
        $this->actPass();
    }

}