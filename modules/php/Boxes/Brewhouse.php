<?php

namespace BGA\Games\theanarchy\Boxes;

use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\BrewhouseSymbol;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Boxes\BoxType;
use BGA\Games\theanarchy\Boxes\Reward;

class Brewhouse extends BoxType {
    public string $name = "BREWHOUSE";

    private const array BOXES = [
        1 => BrewhouseSymbol::WATER,
        2 => BrewhouseSymbol::HOP,
        3 => BrewhouseSymbol::GRAIN,
        4 => BrewhouseSymbol::FOAM,
        5 => BrewhouseSymbol::HOP,
        6 => BrewhouseSymbol::WATER,
        7 => BrewhouseSymbol::FOAM,
        8 => BrewhouseSymbol::GRAIN,
        9 => BrewhouseSymbol::FOAM,
        10 => BrewhouseSymbol::GRAIN,
        11 => BrewhouseSymbol::WATER,
        12 => BrewhouseSymbol::HOP,
        13 => BrewhouseSymbol::GRAIN,
        14 => BrewhouseSymbol::FOAM,
        15 => BrewhouseSymbol::HOP,
        16 => BrewhouseSymbol::WATER,
    ];

    private const array SETS = [
        [1, 2, 3, 4],
        [5, 6, 7, 8],
        [9, 10, 11, 12],
        [13, 14, 15, 16],
        [1, 5, 9, 13],
        [2, 6, 10, 14],
        [3, 7, 11, 15],
        [4, 8, 12, 16],
    ];

    /**
     * Returns the number of beer sets completed by the box ID (which may be 0, 1 or 2).
     * @param int $boxId The newly-checked box ID.
     * @param array $currBoxes The currently checked set of boxes.
     * @return int 0, 1, or 2
     */
    private function setsCompleted(int $playerId, int $boxId, array $currBoxes): int {
        $out = 0;
        foreach (self::SETS as $set) {
            if (!\in_array($boxId, $set)) {
                continue;
            }
            // we now know the new box is in the set; do 3 of the set appear in $currBoxes?
            $setCount = array_reduce(
                $set,
                fn ($count, $setItem) =>
                    $this->idFilled($playerId, "BREWHOUSE", $setItem, $currBoxes) ?
                        $count + 1 :
                        $count,
                0,
            );
            if ($setCount == 3) {
                $out += 1;
            }
        }
        return $out;
    }

    private function validBoxesFromCards(Game $game, int $playerId, array $currBoxes, array $handCards): array {
        $boxSet = [];
        foreach ($handCards as $card) {
            $symbol = $card->card->brewhouse;
            foreach (self::BOXES as $id => $box) {
                if ($box == $symbol) {
                    $boxSet[$id] = true;
                }
            }
        }
        return array_values(
            array_filter(
                array_keys($boxSet),
                fn ($boxId) => !$this->idFilled($playerId, "BREWHOUSE", $boxId, $currBoxes)
            )
        );
    }

    public function validBoxes(Game $game, int $playerId, array $currBoxes): array {
        // Brewhouse boxes are only clickable in Brewhouse state
        if ($game->gamestate->getCurrentStateId($playerId) != StateConstants::BREWHOUSE) {
            return [];
        }

        $cards = $game->playerHandCards($playerId);
        return $this->validBoxesFromCards($game, $playerId, $currBoxes, $cards);
    }

    public function check(Game $game, int $playerId, ?int $boxId = null, bool $pay = true, ?string $choice = null): ?Reward {
        /* OK, this one's weird.

        When called with a null boxId, that means the player clicked on an Entertainment box with
        Brewhouse on it, which means we don't actually check any boxes: we just draw cards, add
        them to the player's "hand", and enter the Brewhouse state so the player can fulfill them.
        (This may happen when we're already in that state, if coming from the double-Brewhouse box;
        in that case just draw more cards.)

        If called with a set boxId, ensure there's a card with that symbol, fill in that box, and
        discard a card with that symbol on it. If no more cards, return to CheckBoxes state.

        pay should never be true. */

        // we're coming from Entertainment; draw cards and enter Brewhouse state
        if ($boxId == null) {
            $game->drawDomainCards($playerId, 2, true);
            if ($game->gamestate->getCurrentStateId($playerId) != StateConstants::BREWHOUSE) {
                 $game->gamestate->nextPrivateState($playerId, "brewhouse");
            }
            return null;  // no reward! we're only shifting state
        }

        // we're coming from Brewhouse: check the box and possibly discard cards
        $cards = $game->playerHandCards($playerId);
        $currBoxes = $game->allCheckedBoxes($playerId);

        // do validation; normally the CheckBoxes state does it, but Brewhouse doesn't, to save DB queries
        if (!\in_array($boxId, $this->validBoxesFromCards($game, $playerId, $currBoxes, $cards))) {
            throw new UserException("Box not available");
        }

        // OK now we actually need a reward
        $beers = $this->setsCompleted($playerId, $boxId, $currBoxes);
        if ($beers > 0) {
            $reward = Reward::resources("BREWHOUSE", $boxId, [Resource::BEER->value => $beers]);
        } else {
            $reward = Reward::none("BREWHOUSE", $boxId);
        }

        // discard a card of the type and check if we need to stay in this state
        $symbol = self::BOXES[$boxId];
        foreach ($cards as $key => $card) {
            if ($card->card->brewhouse == $symbol) {
                $game->notify->all("DISCARD", "", ["cardId" => $card->id]);
                $game->discardCard($playerId, $card->id);
                unset($cards[$key]);
                break;
            }
        }

        $newAvail = null;
        if (\count($cards) > 0) {
            $newAllCheckedBoxes = $game->allCheckedBoxes($playerId);
            $newAvail = SECTIONS["BREWHOUSE"]->validBoxes($game, $playerId, $newAllCheckedBoxes);
        }

        if ($newAvail == null || \count($newAvail) == 0) {
            // no new available boxes; we're done
            $game->gamestate->nextPrivateState($playerId, "checkboxes");

            // there might still be cards in hand, just no available boxes; in that case discard them
            if (\count($cards) > 0) {
                $game->domainCards->moveAllCardsInLocation("{$playerId}_hand", "{$playerId}_discard");
            }
        } else {
            // if we're still in this state, update newAvailable
            $game->notifyNewAvailable($playerId, ["BREWHOUSE" => $newAvail]);
        }

        return $this->checkAndPay($game, $playerId, $boxId, false, [], $reward);
    }

}