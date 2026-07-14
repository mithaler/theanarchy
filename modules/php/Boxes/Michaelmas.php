<?php

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\Boxes\Box;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\StateConstants;

/**
 * The format is:
 * 1-6: activation boxes
 * 7-24: sets of 2 (7-8, 9-10 etc), left to right, top to bottom
 * 25-33: results, left to right, top to bottom
 */
class Michaelmas extends BoxType {
    public string $name = "MICHAELMAS";

    private const array THRESHOLDS = [1, 2, 4, 5, 7, 9];

    private const array SET_REWARDS = [
        [null],
        [null],
        [null, null, null, "INFLUENCE"],
        [null, null, null],
        [null, null],
        [null, "JOY"],
        [null, "JOY"],
        [null, null],
        [null, null, null],
    ];

    // null is an unspecified number
    private const array SET_TARGETS = [null, null, 2, 4, 6, 8, 10, 12, 14];

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        // does not handle the Michaelmas state!
        $curr = $this->highestCheckedBox($playerId, $currBoxes, ignoreAbove: 6);
        if (
            $curr < 6 &&
            $this->idFilled($playerId, "ENTERTAINMENT", self::THRESHOLDS[$curr], $currBoxes) &&
            $this->canPay($game, $playerId, [Resource::SERFS])
        ) {
            return [$curr + 1];
        }
        return [];
    }

    /**
     * Returns the index of the set within SET_REWARDS/TARGETS this box ID points to.
     * @param int $boxId The box ID.
     * @return int The set index.
     */
    private function setIndex(int $boxId): int {
        return ($boxId - 7) / 2;
    }

    /**
     * Returns an array keyed by the available numbers, where the values are arrays of box IDs that
     * the number could be written into.
     * @param array $availableNumbers
     * @param int $playerId
     * @param Box[] $currBoxes
     * @return Array<number, number[]>
     */
    public function validBoxesByNum(array $availableNumbers, int $playerId, array $currBoxes): array {
        // boxId -> writtenValue
        $checked = [];
        foreach ($currBoxes as $box) {
            if ($box->section == $this->name && $box->playerId == $playerId) {
                $checked[$box->boxId] = $box->writtenValue;
            }
        }

        $out = [];
        foreach ($availableNumbers as $num) {
            for ($boxId = 7; $boxId <= 24; $boxId++) {
                if (array_key_exists($boxId, $checked)) {
                    continue;
                }

                // 7-10 are always checkable
                if ($boxId <= 10) {
                    $out[$num][] = $boxId;
                    continue;
                }

                // get the target value for this box's set
                $target = self::SET_TARGETS[$this->setIndex($boxId)];
                // get the other box ID and value (if checked) in this set
                $otherBoxId = $boxId % 2 == 1 ? $boxId + 1 : $boxId - 1;
                $otherValue = array_key_exists($otherBoxId, $checked) ? $checked[$otherBoxId] : null;

                if ($otherValue !== null && $num + $otherValue == $target) {
                    // only allow putting the number in a box if it would complete the set
                    $out[$num][] = $boxId;
                } else if ($otherValue === null && $num <= $target - 1) {
                    // allow putting a number in the set if it wouldn't make the set uncompletable
                    $out[$num][] = $boxId;
                }
            }
        }
        return $out;
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): ?Reward {
        $currBoxes = $game->allCheckedBoxes($playerId);

        // activate it: draw cards, check for valid placements, enter state if so
        if ($boxId <= 6) {
            if (!$this->canPay($game, $playerId, [Resource::SERFS])) {
                throw new UserException("You don't have the resources");
            }

            // check if there are any valid number placements; if so, switch states
            $cards = $game->drawDomainCards($playerId, 2, true);
            $nums = array_map(fn ($card) => $card->card->michaelmas, $cards);
            $validBoxes = $this->validBoxesByNum($nums, $playerId, $currBoxes);

            // immediately discard any cards that are unplayable!
            foreach ($cards as $idx => $card) {
                if (!\array_key_exists($card->card->michaelmas, $validBoxes)) {
                    // card can't be played! discard it
                    $game->discardCard($playerId, $card->id);
                    unset($cards[$idx]);
                }
            }

            // are any cards left?
            if (\count($cards) > 0) {
                $game->gamestate->nextPrivateState($playerId, "michaelmas");
            } else {
                // no valid placements, just discard the cards and don't switch states
                $game->domainCards->moveAllCardsInLocation("{$playerId}_hand", "{$playerId}_discard");
            }

            $reward = Reward::none("MICHAELMAS", $boxId);
            return $this->basicCheckBox($game, $playerId, $boxId, $pay, [Resource::SERFS], $reward);
        } else if ($boxId > 6 && $choice !== null) {
            $choices = \explode(",", $choice);
            $cards = $game->playerHandCards($playerId);
            $nums = \array_map(fn ($card) => $card->card->michaelmas, $cards);
            $valid = $this->validBoxesByNum($nums, $playerId, $currBoxes);
            $writtenValue = (int) $choices[0];
            if (!\in_array($boxId, $valid[$writtenValue])) {
                throw new UserException("Invalid Michaelmas choice");
            }

            // discard a card with the number being written
            $toDiscard = null;
            foreach ($cards as $idx => $card) {
                if ($card->card->michaelmas === $writtenValue) {
                    $toDiscard = $card;
                    unset($cards[$idx]);
                    break;
                }
            }
            if ($toDiscard === null) {
                // sanity check -- if this happened our validation is bugged!?
                throw new UserException("Can't discard!?");
            }
            $game->discardCard($toDiscard->id, $playerId);

            // TODO: handle case where discarding that card made the _other_ card unplayable
            // isn't this box great :'(

            // was that the last card? if so, return to normal checkboxing
            if (\count($cards) === 0) {
                $game->gamestate->nextPrivateState($playerId, "checkboxes");
            }

            // was a set just completed?
            $otherBoxId = $boxId % 2 == 1 ? $boxId + 1 : $boxId - 1;
            if ($this->idFilled($playerId, $this->name, $otherBoxId, $currBoxes)) {
                // we completed a set!
                $setIdx = $this->setIndex($boxId);
                $rewardSet = self::SET_REWARDS[$setIdx];
                $resourceChoices = \array_slice($choices, 1);
                $nonChoiceRewards = \array_filter($rewardSet, fn ($r) => $r !== null);
                $expectedResourceChoices = \count($rewardSet) - \count($nonChoiceRewards);
                if (\count($resourceChoices) != $expectedResourceChoices) {
                    throw new UserException("Expected $expectedResourceChoices choices, got " . \count($resourceChoices));
                }

                // transform into the format Reward wants
                $resourceRewards = [];
                foreach ($resourceChoices as $resource) {
                    $res = Resource::from($resource);
                    if (\in_array($res->value, $resourceRewards)) {
                        $resourceRewards[$res->value]++;
                    } else {
                        $resourceRewards[$res->value] = 1;
                    }
                }
                $reward = new Reward($this->name, $boxId, $resourceRewards, $nonChoiceRewards);
            } else {
                // we didn't complete a set
                $reward = Reward::none($this->name, $boxId);

                // notify new available, only if we haven't switched states
                if ($game->gamestate->getCurrentStateId($playerId) == StateConstants::MICHAELMAS) {
                    $newAllBoxes = [...$currBoxes, new Box($this->name, $boxId, $playerId, $writtenValue)];
                    $game->notify->player($playerId, "michaelmasUpdate", "",
                        ["availableBoxesByNum" => $this->validBoxesByNum(
                            \array_map(fn ($card) => $card->card->michaelmas, $cards),
                            $playerId,
                            $newAllBoxes,
                        )]
                    );
                }
            }

            return $this->basicCheckBox($game, $playerId, $boxId, false, [], $reward, $writtenValue);
        }
        throw new UserException("Cannot check Michaelmas");
    }
}