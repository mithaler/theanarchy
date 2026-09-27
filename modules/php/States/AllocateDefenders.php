<?php

namespace BGA\Games\theanarchy\States;

use Bga\GameFramework\Actions\Types\IntParam;
use Bga\GameFramework\Actions\Types\JsonParam;
use Bga\GameFramework\Actions\Types\StringParam;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\UserException;
use BGA\Games\theanarchy\AttackSide;
use BGA\Games\theanarchy\CastleDefender;
use BGA\Games\theanarchy\Game;
use BGA\Games\theanarchy\PlayerAttackCard;
use BGA\Games\theanarchy\StateConstants;
use BGA\Games\theanarchy\Resource;

class AllocateDefenders extends GameState {
    public function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: StateConstants::ALLOCATE_DEFENDERS,
            type: StateType::PRIVATE,
            description: clienttranslate('${actplayer} must choose which defenders will take damage'),
            descriptionMyTurn: clienttranslate('${you} must choose which defenders will take damage'),
            transitions: [
                'attacksDone' => StateConstants::ATTACKS_DONE,
            ],
        );
    }

    public function getArgs(int $currentPlayerId) {
        // the frontend already knows how many defenders/muster tokens the player has

        // what's the next attack?
        $attack = $this->game->nextPlayerAttack($currentPlayerId);

        $attackSides = $this->game->attacksBySide($currentPlayerId, $attack);
        $defenders = $this->game->getCastleDefenders($currentPlayerId);
        $needsChoice = $this->needsChoice($attackSides, $defenders);
        return [
            'attack' => $this->game->attackRepr($attack, $currentPlayerId),
            'needsChoice' => $needsChoice,
        ];
    }

    /**
     * Executes the attack:
     * * Checks how much damage gets through, and incurs damage.
     * * Exhausts/kills defending workers/tactics.
     * @param int $playerId
     * @return void
     */
    private function executeAttack(int $playerId) {
    }

    /**
     * Returns the total strength of an array of defenders.
     * @param CastleDefender[] $defenders The defenders.
     * @return int Their total strength.
     */
    private function totalStrength(array $defenders): int {
        return array_sum(array_map(fn ($def) => $def->strength(), $defenders));
    }

    /**
     * Returns the set of sides the player has a choice of defenders on.
     * @param array<string, int> $attackSides
     * @param array<string, CastleDefender[]> $defenders
     * @return AttackSide[]
     */
    private function needsChoice(array $attackSides, array $defenders): array {
        $out = [];
        foreach ($attackSides as $side => $attackStrength) {
            $defenseStrength = $this->totalStrength($defenders[$side]);
            if ($defenseStrength > $attackStrength) {
                $out[] = $side;
            }
        }
        return $out;
    }

    #[PossibleAction]
    public function actChooseDefenders(
        int $currentPlayerId,
        #[JsonParam(name: "choices")] array $choices,
    ) {
        // TODO: actually do the thing!
    }

    public function zombie() {
        // TODO: just take them
    }
}