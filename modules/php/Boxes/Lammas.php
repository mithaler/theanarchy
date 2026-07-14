<?php
declare(strict_types=1);

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\Boxes\Box;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\PlayerDomainCard;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Lammas as Color;

const LAMMAS_NAME = "LAMMAS";

/**
 * IDs are 1-6 for the activation boxes, and 7-27 for the flags.
 */
class Lammas extends BoxType {
    public string $name = LAMMAS_NAME;

    private const array THRESHOLDS = [2, 3, 4, 5, 7, 9];

    const array COLORS = [
        7 => Color::YELLOW,
        9 => Color::RED,
        11 => Color::PURPLE,
        13 => Color::GREEN,

        14 => Color::RED,
        16 => Color::GREEN,
        18 => Color::YELLOW,
        20 => Color::PURPLE,

        21 => Color::GREEN,
        23 => Color::PURPLE,
        25 => Color::RED,
        27 => Color::YELLOW,
    ];

    // First is past 7;
    const array REWARDS = [
        8 => "LOYALTY",
        10 => "JOY",
        12 => "LOYALTY",
        15 => "JOY",
        17 => "LOYALTY",
        19 => "JOY",
        22 => "JOY",
        24 => "LOYALTY",
        26 => "JOY",
    ];

    /**
     * Returns valid flag placements (box IDs between 7 and 27 inclusive), based on
     * the cards the player is holding.
     * @param Game $game
     * @param int $playerId
     * @param Box[] $currBoxes
     * @param PlayerDomainCard[] $cards
     * @return void
     */
    private function validFlagPlacements(Game $game, int $playerId, array $currBoxes, array $cards): array {
        $availColors = array_map(fn ($card) => $card->card->lammas, $cards);
        return array_values(
            array_filter(
                range(7, 27),
                fn ($id) => array_key_exists($id, self::COLORS) && in_array(self::COLORS[$id], $availColors)
            )
        );
    }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        if ($game->gamestate->getCurrentStateId($playerId) == StateConstants::LAMMAS) {
            return $this->validFlagPlacements($game, $playerId, $currBoxes, $game->playerHandCards($playerId));
        }

        $curr = $this->highestCheckedBox($playerId, $currBoxes, 6);
        if (
            $curr < 6 &&
            $this->idFilled($playerId, "ENTERTAINMENT", self::THRESHOLDS[$curr], $currBoxes) &&
            $this->canPay($game, $playerId, [Resource::SERFS])
        ) {
            return [$curr + 1];
        }
        return [];
    }

    private function rewardBoxIds(int $boxId) {
        return array_filter(
            [$boxId - 1, $boxId + 1],
            fn ($rewardBoxId) => array_key_exists($rewardBoxId, self::REWARDS),
        );
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): ?Reward {
        $checkedBoxes = $game->allCheckedBoxes($playerId, LAMMAS_NAME);

        // activation
        if ($boxId <= 6) {
            $cards = $game->drawDomainCards($playerId, 2, true);

            // discard any unplayable cards
            foreach ($cards as $idx => $card) {
                // are there 4 checked boxes matching its color?
                $checkedColorCount = count(array_filter(
                    $checkedBoxes,
                    fn ($box) => $box->boxId >= 7 && self::COLORS[$box->boxId] == $card->card->lammas
                ));
                if ($checkedColorCount >= 4) {
                    $game->discardCard($playerId, $card->id);
                    unset($cards[$idx]);
                }
            }

            // if available flags, switch to Lammas state
            if (count($cards) > 0) {
                $game->gamestate->nextPrivateState($playerId, "lammas");
            }
            $reward = Reward::none(NAME, $boxId);
            return $this->basicCheckBox($game, $playerId, $boxId, true, [Resource::SERFS], $reward);

        // filling in a flag
        } else if (array_key_exists($boxId, self::COLORS)) {
            // is it filled already, and do we have the card?
            if ($this->idFilled($playerId, LAMMAS_NAME, $boxId, $checkedBoxes)) {
                throw new UserException("Box already filled");
            }

            // find and discard a hand card matching the color we're trying to check
            $handCards = $game->playerHandCards($playerId);
            $toPlay = array_find($handCards, fn ($card) => $card->card->lammas === self::COLORS[$boxId]);
            if ($toPlay === null) {
                throw new UserException("Invalid color choice");
            }
            $game->discardCard($playerId, $toPlay->id);
            if (count($handCards) === 1) {
                // if that was the last card, go back to regular checkbox state
                $game->gamestate->nextPrivateState($playerId, "checkboxes");
            }

            // did we just get a reward?
            $rewardBoxIds = $this->rewardBoxIds($boxId);
            foreach ($rewardBoxIds as $idx => $rewardBoxId) {
                $checked = true;
                foreach ([$rewardBoxId - 1, $rewardBoxId + 1] as $rewardFlagId) {
                    if ($boxId !== $rewardFlagId && !$this->idFilled($playerId, LAMMAS_NAME, $rewardFlagId, $checkedBoxes)) {
                        $checked = false;
                        break;
                    }
                }
                if (!$checked) {
                    unset($rewardBoxIds[$idx]);
                }
            }

            $game->dump("rb", $rewardBoxIds);
            $reward = count($rewardBoxIds) > 0 ?
                Reward::boxes(LAMMAS_NAME, $boxId, [LAMMAS_NAME => $rewardBoxIds]) :
                Reward::none(LAMMAS_NAME, $boxId);
            $out = $this->basicCheckBox($game, $playerId, $boxId, false, [], $reward);

            if ($game->gamestate->getCurrentStateId($playerId) === StateConstants::LAMMAS) {
                // we haven't switched states, so notify new available
                $checked = $game->allCheckedBoxes($playerId, "LAMMAS");
                $game->notifyNewAvailable($playerId, ["LAMMAS" =>
                    $this->validFlagPlacements($game, $playerId, $checked, $game->playerHandCards($playerId))
                ]);
            }
            return $out;

        // reward for filling in a box between flags
        } else if (array_key_exists($boxId, self::REWARDS) && !$pay) {
            return Reward::boxes(LAMMAS_NAME, $boxId, [self::REWARDS[$boxId]]);
            //return $this->basicCheckBox($game, $playerId, $boxId, false, [], $reward);
        }
    }
};