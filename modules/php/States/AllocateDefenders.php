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
     * @param array<string, array<int, int>> $choices
     * @return void
     */
    private function executeAttack(int $playerId, array $choices = []) {
        $attack = $this->game->nextPlayerAttack($playerId);
        $attackSides = $this->game->attacksBySide($playerId, $attack);
        $defenders = $this->game->getCastleDefenders($playerId);
        $beerCounter = $this->game->resources(Resource::BEER);

        // for each attack side:
        //   is there more strenghth getting through than defenders?
        //   if so, inflict damage
        //   and hurt workers
        $deadDefenders = [];
        $exhaustedDefenders = [];
        $succeeded = true;
        foreach ($attackSides as $side => $attackStrength) {
            $sideDefenders = $defenders[$side] ?? [];
            $defenderStrength = $this->totalStrength($sideDefenders);
            $gotThrough = $attackStrength - $defenderStrength;
            if ($gotThrough > 0) {
                $succeeded = false;
                // all of the defenders die
                $deadDefenders = [...$deadDefenders, ...$sideDefenders];
            } else if ($gotThrough === 0) {
                // attack exactly matched defender strength, defenders die but defense was successful
                $deadDefenders = [...$deadDefenders, ...$sideDefenders];
            } else {
                // the player must have selected which ones get hit
                $hitChoices = $choices[$side];
                if (!$hitChoices) {
                    throw new UserException("Need a defender choice for side $side");
                }
                // find the actual defender objects for those choices
                foreach ($hitChoices as $defId => $hitStr) {
                    $defender = array_find($sideDefenders, fn ($def) => $def->id === $defId);
                    if ($defender === null) {
                        throw new UserException("Invalid defender id on $side: $defId");
                    }

                    // hitStr is 0 (beered), 1, or 2
                    if ($defender->type === "knights") {
                        if ($hitStr === 0) {
                            throw new UserException("Knights cannot drink beer!");
                        } else if ($defender->exhausted && $hitStr === 2) {
                            throw new UserException("That knight is already wounded and cannot take 2 damage");
                        } else if ($hitStr === 1) {
                            $exhaustedDefenders[] = $defender;
                        } else if ($hitStr === 2) {
                            $deadDefenders[] = $defender;
                        }
                    } else {
                        if ($hitStr === 2) {
                            throw new UserException("That kind of defender cannot take 2 damage");
                        } else if ($hitStr === 0) {
                            if ($beerCounter->get($playerId) < 1) {
                                throw new UserException("You don't have enough beer!");
                            }
                            $beerCounter->inc($playerId, -1);
                            $exhaustedDefenders[] = $defender;
                        } else if ($hitStr === 1) {
                            $deadDefenders[] = $defender;
                        }
                    }
                }
            }
        }

        // TODO: exhaust/kill workers, mark success!
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
        // got to be really careful about validation here
        foreach ($choices as $side => $ids) {
            // keys are sides
            if (!\in_array($side, ["top", "left", "right", "bottom"])) {
                throw new UserException("Invalid side");
            }
            // values are defender ID => strength of hit (0 (beer), 1, or 2)
            foreach ($ids as $defId => $hit) {
                if (!\is_int($defId) || !\is_int($hit) || $hit > 2 || $hit < 0) {
                    throw new UserException("Invalid defender ID");
                }
            }
        }
    }

    public function zombie() {
        // TODO: just take them
    }
}