<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy;

use JsonSerializable;

class StateConstants {
    const int GAME_START = 2;

    const int INITIAL_SETUP = 3;
    const int NEW_ROUND = 4;
    const int CHOOSE_PATH_CARDS = 5;
    const int CHECK_BOXES_LOOP = 6;
    const int CHECK_BOXES = 7;
    const int ST_VALENTINES_FESTIVAL = 8;
    const int KNIGHTS_TRAINING = 9;
    const int BREWHOUSE = 10;
    const int MICHAELMAS = 11;
    const int LAMMAS = 12;
    const int CHECK_BOXES_DONE = 13;

    // setup for attack phase (food cost, reveal hidden, etc.)
    const int BEGIN_DEFENSE = 14;

    // the outer state for parallel private attacks
    const int RECEIVE_ATTACKS = 15;
    // player may use muster tokens if available
    const int MUSTER = 16;
    // player may use tactics
    const int USE_TACTICS = 17;
    // the player may allocate damage and beer if available
    const int ALLOCATE_DEFENDERS = 18;
    // the player is done receiving attacks
    const int ATTACKS_DONE = 19;

    const int ROUND_CLEANUP = 20;

    const int GAME_END = 99;
}

/** Counters for each player. Values are camelCase so JS can use them comfortably. */
enum Resource: string {
    case SERFS = "serfs";
    case CRAFTSMEN = "craftsmen";
    case PATRONS = "patrons";
    case SOLDIERS = "soldiers";
    case KNIGHTS = "knights";
    case SILVER = "silver";
    case FOOD = "food";
    case MATERIALS = "materials";
    case MUSTER_TOKENS = "musterTokens";
    case BEER = "beer";

    // not really "resources", but this hooks them into Rewards easily!
    case TENT = "tent";
    case GATE = "gate";
    case MOAT = "moat";

    case WALL_LEFT = "wallLeft";
    case WALL_RIGHT = "wallRight";
    case WALL_BOTTOM = "wallBottom";
    case WALL_TOP = "wallTop";

    case TOWER_LEFT_TOP = "towerLeftTop";
    case TOWER_LEFT_BOTTOM = "towerLeftBottom";
    case TOWER_RIGHT_TOP = "towerRightTop";
    case TOWER_RIGHT_BOTTOM = "towerRightBottom";
}

enum KnightsTraining: string {
    case SCROLL = "scroll";
    case ARMS = "arms";
    case BOW = "bow";
    case HORSESHOE = "horseshoe";
}
enum BrewhouseSymbol {
    case GRAIN;
    case WATER;
    case HOP;
    case FOAM;
}
enum Lammas {
    case RED;
    case GREEN;
    case YELLOW;
    case PURPLE;
}
enum Tournament {
    case LEFT;
    case RIGHT;
    case CENTER;
}

class DomainCard {
    public function __construct(
        public KnightsTraining $symbol,
        public int $valentine,
        public int $michaelmas,
        public Tournament $lance,
        public Tournament $shield,
        public BrewhouseSymbol $brewhouse,
        public Lammas $lammas,
    ) {}
}

const DOMAIN_CARDS = [
    1  => new DomainCard(KnightsTraining::SCROLL, 4, 3, Tournament::RIGHT, Tournament::CENTER, BrewhouseSymbol::GRAIN, Lammas::YELLOW),
    2  => new DomainCard(KnightsTraining::ARMS, 1, 2, Tournament::LEFT, Tournament::RIGHT, BrewhouseSymbol::WATER, Lammas::RED),
    3  => new DomainCard(KnightsTraining::ARMS, 3, 6, Tournament::CENTER, Tournament::LEFT, BrewhouseSymbol::GRAIN, Lammas::GREEN),
    4  => new DomainCard(KnightsTraining::SCROLL, 6, 7, Tournament::RIGHT, Tournament::LEFT, BrewhouseSymbol::WATER, Lammas::PURPLE),
    5  => new DomainCard(KnightsTraining::SCROLL, 2, 7, Tournament::CENTER, Tournament::RIGHT, BrewhouseSymbol::WATER, Lammas::PURPLE),
    6  => new DomainCard(KnightsTraining::BOW, 6, 5, Tournament::RIGHT, Tournament::CENTER, BrewhouseSymbol::HOP, Lammas::YELLOW),
    7  => new DomainCard(KnightsTraining::HORSESHOE, 3, 4, Tournament::LEFT, Tournament::CENTER, BrewhouseSymbol::HOP, Lammas::PURPLE),
    8  => new DomainCard(KnightsTraining::BOW, 4, 1, Tournament::RIGHT, Tournament::LEFT, BrewhouseSymbol::FOAM, Lammas::GREEN),
    9  => new DomainCard(KnightsTraining::HORSESHOE, 3, 8, Tournament::CENTER, Tournament::LEFT, BrewhouseSymbol::FOAM, Lammas::RED),
    10 => new DomainCard(KnightsTraining::SCROLL, 2, 3, Tournament::CENTER, Tournament::RIGHT, BrewhouseSymbol::GRAIN, Lammas::YELLOW),
    11 => new DomainCard(KnightsTraining::BOW, 2, 5, Tournament::RIGHT, Tournament::CENTER, BrewhouseSymbol::HOP, Lammas::YELLOW),
    12 => new DomainCard(KnightsTraining::BOW, 2, 1, Tournament::CENTER, Tournament::RIGHT, BrewhouseSymbol::FOAM, Lammas::GREEN),
    13 => new DomainCard(KnightsTraining::SCROLL, 4, 7, Tournament::RIGHT, Tournament::LEFT, BrewhouseSymbol::WATER, Lammas::PURPLE),
    14 => new DomainCard(KnightsTraining::HORSESHOE, 5, 4, Tournament::LEFT, Tournament::CENTER, BrewhouseSymbol::HOP, Lammas::PURPLE),
    15 => new DomainCard(KnightsTraining::ARMS, 5, 6, Tournament::LEFT, Tournament::CENTER, BrewhouseSymbol::GRAIN, Lammas::GREEN),
    16 => new DomainCard(KnightsTraining::HORSESHOE, 1, 4, Tournament::LEFT, Tournament::RIGHT, BrewhouseSymbol::HOP, Lammas::PURPLE),
    17 => new DomainCard(KnightsTraining::ARMS, 5, 2, Tournament::LEFT, Tournament::RIGHT, BrewhouseSymbol::WATER, Lammas::RED),
    18 => new DomainCard(KnightsTraining::HORSESHOE, 1, 8, Tournament::LEFT, Tournament::RIGHT, BrewhouseSymbol::FOAM, Lammas::RED),
    19 => new DomainCard(KnightsTraining::SCROLL, 6, 3, Tournament::RIGHT, Tournament::CENTER, BrewhouseSymbol::GRAIN, Lammas::YELLOW),
    20 => new DomainCard(KnightsTraining::BOW, 6, 1, Tournament::CENTER, Tournament::RIGHT, BrewhouseSymbol::FOAM, Lammas::GREEN),
    21 => new DomainCard(KnightsTraining::HORSESHOE, 5, 8, Tournament::CENTER, Tournament::LEFT, BrewhouseSymbol::FOAM, Lammas::RED),
    22 => new DomainCard(KnightsTraining::ARMS, 1, 6, Tournament::LEFT, Tournament::CENTER, BrewhouseSymbol::GRAIN, Lammas::RED),
    23 => new DomainCard(KnightsTraining::ARMS, 3, 2, Tournament::CENTER, Tournament::LEFT, BrewhouseSymbol::WATER, Lammas::RED),
    24 => new DomainCard(KnightsTraining::BOW, 4, 5, Tournament::RIGHT, Tournament::LEFT, BrewhouseSymbol::GRAIN, Lammas::YELLOW),
];

/** A domain card held by a player, containing its ID (so it can be moved easily). */
class PlayerDomainCard {
    public function __construct(
        public string $id,
        public DomainCard $card,
    ) {}

    public static function fromCardArray(array $from): PlayerDomainCard {
        $id = $from["id"];
        $card = DOMAIN_CARDS[$from["type"]];
        return new PlayerDomainCard($id, $card);
    }
}

enum Tactic: string {
    case COVERS = "COVERS";
    case ROCKS = "ROCKS";
    case HOT_OIL = "HOT OIL";
    case LOGS = "LOGS";
    case BOLTS = "BOLTS";
}

enum Defense {
    case WALLS;
    case TOWERS;
    case MOAT;
    case GATE;
}

enum AttackSide: string {
    case NONE = "none";
    case TOP = "top";
    case LEFT = "left";
    case RIGHT = "right";
    case BOTTOM = "bottom";
}

class AttackCard {
    /**
     * @param ?Tactic $tactic The allowed tactic, if any.
     * @param array<string, AttackSide[]> $attacks The attacks incoming ("A" => AttackSide::TOP).
     * @param Defense Allowed defense. (Walls and towers are implicit from oncoming attacks.)
     * @param int $back The card ID of the back of this card, to hide which attack it is when face down.
     * @param bool $strong Whether this is a fierce attack.
     */
    public function __construct(
        public ?Tactic $tactic,
        public array $attacks,
        public ?Defense $defense,
        public int $back,
        public bool $strong = false,
        public bool $defenderWorkers = true,
    ) {}
}

const ATTACK_CARDS = [
    1 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::BOTTOM]], Defense::GATE, 1),
    2 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::BOTTOM]], Defense::GATE, 1),
    3 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::BOTTOM]], Defense::GATE, 1),
    4 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::BOTTOM]], Defense::GATE, 1),
    5 => new AttackCard(Tactic::HOT_OIL, ["E" => [AttackSide::BOTTOM]], Defense::GATE, 1, true),
    6 => new AttackCard(Tactic::HOT_OIL, ["E" => [AttackSide::BOTTOM]], Defense::GATE, 1, true),
    7 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], null, 7, defenderWorkers: false),
    8 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], null, 7, defenderWorkers: false),
    9 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], null, 7, defenderWorkers: false),
    10 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], null, 7, defenderWorkers: false),
    11 => new AttackCard(Tactic::COVERS, ["A" => [AttackSide::NONE]], null, 7, true, defenderWorkers: false),
    12 => new AttackCard(Tactic::COVERS, ["A" => [AttackSide::NONE]], null, 7, true, defenderWorkers: false),
    13 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::BOTTOM]], Defense::MOAT, 13),
    14 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::LEFT]], Defense::MOAT, 13),
    15 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::RIGHT]], Defense::MOAT, 13),
    16 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::TOP]], Defense::MOAT, 13),
    17 => new AttackCard(Tactic::LOGS, ["E" => [AttackSide::LEFT]], Defense::MOAT, 13, true),
    18 => new AttackCard(Tactic::LOGS, ["E" => [AttackSide::RIGHT]], Defense::MOAT, 13, true),
    19 => new AttackCard(Tactic::BOLTS, ["C" => [AttackSide::TOP], "B" => [AttackSide::BOTTOM]], Defense::TOWERS, 19),
    20 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::TOP], "C" => [AttackSide::BOTTOM]], Defense::TOWERS, 19),
    21 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::LEFT], "C" => [AttackSide::RIGHT]], Defense::TOWERS, 19),
    22 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::RIGHT], "C" => [AttackSide::LEFT]], Defense::TOWERS, 19),
    23 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::RIGHT], "A" => [AttackSide::LEFT]], Defense::TOWERS, 19, true),
    24 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::TOP], "A" => [AttackSide::BOTTOM]], Defense::TOWERS, 19, true),
    25 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::BOTTOM]], Defense::WALLS, 25),
    26 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::LEFT]], Defense::WALLS, 25),
    27 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::RIGHT]], Defense::WALLS, 25),
    28 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::TOP]], Defense::WALLS, 25),
    29 => new AttackCard(Tactic::ROCKS, ["E" => [AttackSide::BOTTOM]], Defense::WALLS, 25, true),
    30 => new AttackCard(Tactic::ROCKS, ["E" => [AttackSide::TOP]], Defense::WALLS, 25, true),
    31 => new AttackCard(null, ["C" => [AttackSide::LEFT, AttackSide::RIGHT], "B" => [AttackSide::BOTTOM]], Defense::WALLS, 31),
    32 => new AttackCard(null, ["C" => [AttackSide::TOP, AttackSide::BOTTOM], "B" => [AttackSide::LEFT]], Defense::WALLS, 31),
    33 => new AttackCard(null, ["C" => [AttackSide::TOP, AttackSide::BOTTOM], "B" => [AttackSide::RIGHT]], Defense::WALLS, 31),
    34 => new AttackCard(null, ["C" => [AttackSide::RIGHT, AttackSide::LEFT], "B" => [AttackSide::TOP]], Defense::WALLS, 31),
    35 => new AttackCard(null, ["B" => [AttackSide::TOP, AttackSide::BOTTOM], "D" => [AttackSide::LEFT]], Defense::WALLS, 31, true),
    36 => new AttackCard(null, ["B" => [AttackSide::TOP, AttackSide::BOTTOM], "D" => [AttackSide::RIGHT]], Defense::WALLS, 31, true),
];

const FINAL_ESCALADE_CARDS = [
    37 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::LEFT, AttackSide::BOTTOM]], Defense::WALLS, 37),
    38 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::RIGHT, AttackSide::BOTTOM]], Defense::WALLS, 37),
    39 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::TOP, AttackSide::BOTTOM]], Defense::WALLS, 37),
    40 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::TOP, AttackSide::RIGHT]], Defense::WALLS, 37),
    41 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::TOP, AttackSide::LEFT]], Defense::WALLS, 37),
    42 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::RIGHT, AttackSide::LEFT]], Defense::WALLS, 37),
];

class PlayerAttackCard implements JsonSerializable {
    public function __construct(
        public string $id,
        public AttackCard $card,
    ) {}

    public static function fromCardArray(array $from): PlayerAttackCard {
        $type = $from['card_type'];
        $card = ((int) $type <= 36) ? ATTACK_CARDS[$type] : FINAL_ESCALADE_CARDS[$type];
        return new PlayerAttackCard($type, $card);
    }

    public function jsonSerialize(): mixed {
        return ['id' => $this->id];
    }
}

class PathCard {
    public function __construct(
        public int $serfs,
        public int $craftsmen,
        public int $materials,
        public int $patrons,
        public int $silver,
    ) {}

    public static function translatedName(string $name): string {
        // PHP is stupid and doesn't let you clienttranslate at module scope :|
        return match ($name) {
            "abbot" => clienttranslate("Abbot"),
            "advocate" => clienttranslate("Advocate"),
            "architect" => clienttranslate("Architect"),
            "baron" => clienttranslate("Baron"),
            "bellwether" => clienttranslate("Bellwether"),
            "silversmith" => clienttranslate("Silversmith"),
            "captain" => clienttranslate("Captain"),
            "champion" => clienttranslate("Champion"),
            "commander" => clienttranslate("Commander"),
            "conqueror" => clienttranslate("Conqueror"),
            "defender" => clienttranslate("Defender"),
            "emissary" => clienttranslate("Emissary"),
            "engineer" => clienttranslate("Engineer"),
            "excavator" => clienttranslate("Excavator"),
            "foreman" => clienttranslate("Foreman"),
            "mentor" => clienttranslate("Mentor"),
            "merrymaker" => clienttranslate("Merrymaker"),
            "monk" => clienttranslate("Monk"),
            "partisan" => clienttranslate("Partisan"),
            "priest" => clienttranslate("Priest"),
            "recruiter" => clienttranslate("Recruiter"),
            "scout" => clienttranslate("Scout"),
            "tactician" => clienttranslate("Tactician"),
            "warrior" => clienttranslate("Warrior"),
            "weaponsmith" => clienttranslate("Weaponsmith"),
        };
    }
}

const PATH_CARDS = [
    "abbot" => new PathCard(3, 3, 2, 3, 2),
    "advocate" => new PathCard(2, 4, 3, 3, 0),
    "architect" => new PathCard(3, 2, 3, 4, 1),
    "baron" => new PathCard(3, 3, 3, 2, 2),
    "bellwether" => new PathCard(3, 3, 4, 3, 0),
    "silversmith" => new PathCard(4, 2, 3, 2, 2),
    "captain" => new PathCard(3, 4, 3, 2, 1),
    "champion" => new PathCard(4, 3, 2, 4, 0),
    "commander" => new PathCard(3, 3, 3, 2, 2),
    "conqueror" => new PathCard(3, 3, 4, 3, 0),
    "defender" => new PathCard(3, 2, 3, 4, 1),
    "emissary" => new PathCard(2, 4, 3, 4, 0),
    "engineer" => new PathCard(4, 3, 2, 4, 0),
    "excavator" => new PathCard(4, 3, 3, 3, 0),
    "foreman" => new PathCard(2, 3, 4, 3, 1),
    "mentor" => new PathCard(2, 4, 2, 3, 2),
    "merrymaker" => new PathCard(2, 4, 2, 3, 2),
    "monk" => new PathCard(2, 3, 4, 3, 1),
    "partisan" => new PathCard(3, 2, 4, 3, 1),
    "priest" => new PathCard(4, 2, 3, 2, 2),
    "recruiter" => new PathCard(3, 3, 2, 3, 2),
    "scout" => new PathCard(4, 3, 3, 3, 0),
    "tactician" => new PathCard(3, 4, 3, 2, 1),
    "warrior" => new PathCard(4, 3, 3, 3, 0),
    "weaponsmith" => new PathCard(3, 2, 4, 3, 1),
];

class ScenarioCard {
    // resource costs at the top never change, they're purely per-round
    public function __construct(
        public ?int $requiredStrength,
        public int $a,
        public int $b,
        public int $c,
        public int $d,
        public int $e,
    ) {}

    public function strength(string $letter): int {
        $prop = strtolower($letter);
        return $this->$prop;
    }
}

const SCENARIO_CARDS = [
    // classic easy
    1 => new ScenarioCard(4, 2, 1, 1, 2, 2),
    2 => new ScenarioCard(4, 2, 2, 1, 3, 3),
    3 => new ScenarioCard(5, 3, 2, 2, 3, 4),
    4 => new ScenarioCard(5, 3, 3, 2, 4, 4),
    5 => new ScenarioCard(6, 4, 3, 3, 4, 5),

    // classic medium
    6 => new ScenarioCard(4, 2, 1, 1, 2, 3),
    7 => new ScenarioCard(4, 3, 2, 2, 3, 3),
    8 => new ScenarioCard(5, 3, 3, 2, 4, 4),
    9 => new ScenarioCard(5, 4, 3, 3, 4, 4),
    10 => new ScenarioCard(6, 4, 4, 3, 5, 5),

    // classic hard
    11 => new ScenarioCard(4, 2, 2, 1, 3, 3),
    12 => new ScenarioCard(4, 3, 2, 2, 3, 4),
    13 => new ScenarioCard(5, 4, 3, 3, 4, 4),
    14 => new ScenarioCard(5, 4, 4, 3, 5, 5),
    15 => new ScenarioCard(6, 5, 4, 4, 5, 6),

    // siege easy
    16 => new ScenarioCard(null, 2, 2, 1, 3, 3),
    17 => new ScenarioCard(null, 3, 2, 2, 3, 4),
    18 => new ScenarioCard(null, 3, 2, 2, 3, 4),
    19 => new ScenarioCard(null, 4, 4, 3, 5, 5),
    20 => new ScenarioCard(null, 5, 4, 4, 5, 6),
];

/**
 * Returns the scenario cards for this game based on mode
 * and difficulty.
 * @param string $mode "siege" or "classic"
 * @param string $difficulty "easy", "medium" or "hard"
 * @return ScenarioCard[]
 */
function scenarioCards(string $mode, string $difficulty): array {
    $start = 1;
    if ($mode === "siege") {
        $start += 15;
    }

    if ($difficulty === "medium") {
        $start += 5;
    } else if ($difficulty === "hard") {
        $start += 10;
    }

    return \array_slice(SCENARIO_CARDS, $start, 5);
}

/**
 * A castle defender -- a soldier, craftsman, knight or tactics use.
 * "Exhausted" means either a previously beered soldier/craftsman, or a knight that has taken one damage.
 */
class CastleDefender {
    public function __construct(
        public int $id,
        public int $playerId,
        public AttackSide $side,
        public string $type,
        public bool $exhausted,
    ) {}

    public static function fromDbArray(array $row): CastleDefender {
        return new CastleDefender(
            (int) $row["id"], (int) $row["player_id"], AttackSide::from($row["side"]),
            $row["type"], (boolean) $row["exhausted"],
        );
    }

    /** Returns the strength of this defender. */
    public function strength(): int {
        return match ($this->type) {
            "soldiers" | "craftsmen" | "tactics" => 1,
            "knights" => $this->exhausted ? 1 : 2,
        };
    }
}