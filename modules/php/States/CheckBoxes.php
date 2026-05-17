<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use BGA\Games\theanarchy\Resource;

require_once(__DIR__ . "/../Boxes/Sections.php");
require_once(__DIR__ . "/../Constants.php");

use const BGA\Games\theanarchy\Boxes\SECTIONS;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\GameFramework\Actions\Types\StringParam;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Boxes\Reward;

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
        return ["checkedBoxes" => $this->game->boxesByPlayer($boxes), "availableBoxes" => $out];
    }

    #[PossibleAction]
    function actCheckBox(
        int $currentPlayerId,
        // spaces prevent alphanum validation; don't ever put this directly in SQL!
        #[StringParam(name: "section")] string $section,
        #[IntParam(name: "boxId")] int $boxId,
        #[StringParam(name: "costChoice", alphanum: true)] string | null $costChoice,
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

        if ($costChoice != null) {
            $costChoice = Resource::tryFrom($costChoice);
            if ($costChoice == null) {
                throw new UserException("Invalid cost choice");
            }
        }

        // notify rewards
        $reward = SECTIONS[$section]->check($this->game, $currentPlayerId, $boxId, true, $costChoice);
        $this->notifyReward($reward, $currentPlayerId);

        // notify new available boxes
        $newAllCheckedBoxes = $this->game->allCheckedBoxes($currentPlayerId);
        $this->notify->player(
            $currentPlayerId, "newAvailable", "",
            $this->getAvailableBoxes($currentPlayerId, $newAllCheckedBoxes)
        );
    }

    private function notifyReward(Reward &$reward, int $playerId) {
        if (\count($reward->resources) == 0 && \count($reward->boxes) == 0) {
            $this->game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection}'), [
                "player_id" => $playerId,
                "player_name" => $this->game->getPlayerNameById($playerId),
                "boxSection" => $reward->fromSection,
                "boxId" => $reward->fromId,
            ]);
        } else {
            $resourceRewards = [];
            foreach ($reward->resources as $resource => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $resourceRewards[] = $resource;
                }
            }
            $this->game->notify->all("boxReward", \clienttranslate('${player_name} checks ${boxSection} and earns ${rewards}'), [
                "player_id" => $playerId,
                "player_name" => $this->game->getPlayerNameById($playerId),
                "boxSection" => $reward->fromSection,
                "boxId" => $reward->fromId,
                // TODO make this pretty!
                "rewards" => implode(" ", [...$resourceRewards, ...array_keys($reward->boxes)]),
                // we don't include resources here, the framework auto-notifies setPlayerCounter for that
            ]);
        }

        // DFS into the boxes and notify those too
        foreach ($reward->boxes as $boxReward) {
            $this->notifyReward($boxReward, $playerId);
        }
    }

    #[PossibleAction]
    function actPass() {

    }

    function zombie() {
        $this->actPass();
    }

}