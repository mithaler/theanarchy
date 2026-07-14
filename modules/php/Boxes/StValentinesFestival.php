<?php

use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;

/**
 * Returns the box ID of a given Valentine number as it appears on a domain card.
 * @param int $pos The value that appears on the card.
 * @param int $idx 0 if female, 1 if male.
 * @return int The box ID.
 */
function valentineBoxId(int $pos, int $idx): int {
    return 6 + $idx + ($pos * 2) - 1;
}

const NAME = "ST VALENTINES FESTIVAL";

class StValentinesFestival extends BoxType {
    public string $name = NAME;

    private const array THRESHOLDS = [1, 2, 4, 5, 7, 9];

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        $curr = $this->highestCheckedBox($playerId, $currBoxes, 6);
        if (
            $curr < 6 &&
            $this->idFilled($playerId, "WORSHIP", self::THRESHOLDS[$curr], $currBoxes) &&
            $this->canPay($game, $playerId, [Resource::PATRONS])
        ) {
            return [$curr + 1];
        }
        return [];
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): Reward {
        $currBoxes = $game->allCheckedBoxes($playerId);

        if ($pay && $boxId <= 6) {
            // activation row
            $reward = Reward::none(NAME, $boxId);

            $cards = $game->drawDomainCards($playerId, 2, true);
            $game->notify->all(
                "valentinesCardDraw",
                '${player_name} draws domain cards for St. Valentine\'s Festival: female ${female}, male ${male}',
                [
                    'player_id' => $playerId,
                    'player_name' => $game->getPlayerNameById($playerId),
                    'female' => $cards[0]->card->valentine,
                    'male' => $cards[1]->card->valentine,
                ]
            );

            $needsChoice = false;
            foreach ($cards as $idx => $card) {
                $valentine = $card->card->valentine;
                $targetId = valentineBoxId($valentine, $idx);
                if (!$this->idFilled($playerId, NAME, $targetId, $currBoxes)) {
                    // easy case: it isn't filled, just fill it and move on
                    $reward->boxes[NAME][] = $targetId;
                    $game->discardCard($playerId, $card->id);
                } else {
                    // get the left and right boxes
                    $options = [
                        valentineBoxId($valentine == 1 ? 6 : $valentine - 1, $idx), // left
                        valentineBoxId($valentine == 6 ? 1 : $valentine + 1, $idx), // right
                    ];
                    $notFilled = array_filter(
                        $options,
                        fn ($opt) => !$this->idFilled($playerId, NAME, $opt, $currBoxes)
                    );
                    $notFilledCount = count($notFilled);

                    if ($notFilledCount == 2) {
                        // there are two options, we need a choice from the player
                        // move the card to the player's hand with location_arg et
                        $needsChoice = true;
                        $game->domainCards->moveCard($card->id, "{$playerId}_hand", $idx);
                    } else if ($notFilledCount == 1) {
                        // there's only one option, check it and discard the card
                        $reward->boxes[NAME][] = array_values($notFilled)[0];
                        $game->discardCard($playerId, $card->id);
                    } else if ($notFilledCount == 0) {
                        // there are no options, discard the card with no reward
                        $game->discardCard($playerId, $card->id);
                    }
                }
            }

            if ($needsChoice) {
                $game->gamestate->nextPrivateState($playerId, "stvalentinesfestival");
            }
        } else if (!$pay && $boxId > 6 && $boxId < 19) {
            // pair row; did the player just complete a pair?
            $mate = $boxId % 2 == 1 ? $boxId + 1 : $boxId - 1;
            if ($this->idFilled($playerId, NAME, $mate, $currBoxes)) {
                $reward = Reward::boxes(NAME, $boxId, [NAME]);
            } else {
                $reward = Reward::none(NAME, $boxId);
            }
        } else if (!$pay && $boxId == null) {
            // reward row
            $highest = $this->highestCheckedBox($playerId, $currBoxes);
            $boxId = $highest < 19 ? 19 : $highest + 1;
            $reward = new Reward(
                NAME,
                $boxId,
                [Resource::SERFS],
                $boxId % 2 == 1 ? ["LOYALTY"] : ["LOYALTY", "SERFS"]
            );
        }

        $this->basicCheckBox($game, $playerId, $boxId, $pay, [Resource::PATRONS], $reward);
        return $reward;
    }
}
