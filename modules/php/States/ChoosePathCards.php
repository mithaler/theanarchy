<?php

declare(strict_types=1);

namespace Bga\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\StateType;
use Bga\GameFramework\UserException;

use Bga\Games\theanarchy\Game;
use BGA\Games\theanarchy\PathCard;
use BGA\Games\theanarchy\Resource;
use BGA\Games\theanarchy\StateConstants;
use const BGA\Games\theanarchy\PATH_CARDS;

class ChoosePathCards extends GameState {
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: StateConstants::CHOOSE_PATH_CARDS,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must select a Path card'),
            descriptionMyTurn: clienttranslate('${you} must select a Path card'),
            transitions: [
                'next' => StateConstants::CHOOSE_PATH_CARDS,
                'checkboxes' => StateConstants::CHECK_BOXES_LOOP,
            ]
        );
    }

    public function getArgs(): array {
        $pathCards = $this->game->pathCards->getCardsInLocation('table');
        return ['choices' => array_values(array_map(fn ($card) => $card["type"], $pathCards))];
    }

    #[PossibleAction()]
    public function actChoosePathCard(
        int $currentPlayerId,
        #[StringParam(name: "cardId", alphanum: true)] string $choice,
    ) {
        if (!\array_key_exists($choice, PATH_CARDS)) {
            throw new UserException("Invalid path card");
        }

        // find the card
        $currChoices = $this->game->pathCards->getCardsInLocation("table");
        $chosen = array_find($currChoices, fn ($c) => $c["type"] === $choice);
        if ($chosen === null) {
            throw new UserException("Invalid path card");
        }

        // give the player their stuff
        $chosenCard = PATH_CARDS[$chosen['type']];
        $this->game->resources(Resource::SERFS)->inc($currentPlayerId, $chosenCard->serfs);
        $this->game->resources(Resource::CRAFTSMEN)->inc($currentPlayerId, $chosenCard->craftsmen);
        $this->game->resources(Resource::MATERIALS)->inc($currentPlayerId, $chosenCard->materials);
        $this->game->resources(Resource::PATRONS)->inc($currentPlayerId, $chosenCard->patrons);
        if ($chosenCard->silver > 0) {
            $this->game->resources(Resource::SILVER)->inc($currentPlayerId, $chosenCard->silver);
        }

        // move the card to the player's path card set
        $this->game->pathCards->moveCard($chosen["id"], 'hand', $currentPlayerId);
        $newArgs = $this->getArgs();
        $this->game->notify->all(
            'cardChosen',
            clienttranslate('${player_name} chooses the ${card} Path card'),
            [
                'player_id' => $currentPlayerId,
                'player_name' => $this->game->getPlayerNameById($currentPlayerId),
                'card' => PathCard::translatedName($choice),
                ...$newArgs,
            ],
        );

        // to next player, or checkboxes if done
        if (\count($newArgs['choices']) === 0) {
            $this->game->gamestate->nextState('checkboxes');
        } else {
            $this->game->activeNextPlayer();
            $this->game->gamestate->nextState('next');
        }
    }

    public function zombie() {
        return null; // TODO
    }
}