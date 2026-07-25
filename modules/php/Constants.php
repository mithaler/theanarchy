<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy;

class StateConstants {
    const int GAME_START = 2;

    const int INITIAL_SETUP = 3;
    const int ROUND_SETUP = 4;
    const int CHOOSE_PATH_CARDS = 5;
    const int CHECK_BOXES_LOOP = 6;
    const int CHECK_BOXES = 7;
    const int ST_VALENTINES_FESTIVAL = 8;
    const int KNIGHTS_TRAINING = 9;
    const int BREWHOUSE = 10;
    const int MICHAELMAS = 11;
    const int LAMMAS = 12;
    const int CHECK_BOXES_DONE = 13;

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

enum Tactic {
    case COVERS;
    case ROCKS;
    case HOT_OIL;
    case LOGS;
    case BOLTS;
}

enum Defense {
    case WALLS;
    case TOWERS;
    case MOAT;
    case GATE;
}

enum AttackSide {
    case NONE;
    case NORTH;
    case WEST;
    case EAST;
    case SOUTH;
}

class AttackCard {
    /**
     * @param ?Tactic $tactic The allowed tactic, if any.
     * @param array<string, AttackSide[]> $attacks The attacks incoming ("A" => AttackSide::NORTH).
     * @param Defense[] Allowed defenses. (Walls and towers are implicit from oncoming attacks.)
     * @param bool $fierce Whether this is a fierce attack.
     */
    public function __construct(
        public ?Tactic $tactic,
        public array $attacks,
        public array $defenses,
        public bool $fierce = false,
    ) {}
}

const ATTACK_CARDS = [
    1 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::SOUTH]], [Defense::GATE]),
    2 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::SOUTH]], [Defense::GATE]),
    3 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::SOUTH]], [Defense::GATE]),
    4 => new AttackCard(Tactic::HOT_OIL, ["D" => [AttackSide::SOUTH]], [Defense::GATE]),
    5 => new AttackCard(Tactic::HOT_OIL, ["E" => [AttackSide::SOUTH]], [Defense::GATE], true),
    6 => new AttackCard(Tactic::HOT_OIL, ["E" => [AttackSide::SOUTH]], [Defense::GATE], true),
    7 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], []),
    8 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], []),
    9 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], []),
    10 => new AttackCard(Tactic::COVERS, ["C" => [AttackSide::NONE]], []),
    11 => new AttackCard(Tactic::COVERS, ["A" => [AttackSide::NONE]], [], true),
    12 => new AttackCard(Tactic::COVERS, ["A" => [AttackSide::NONE]], [], true),
    13 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::SOUTH]], [Defense::MOAT]),
    14 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::WEST]], [Defense::MOAT]),
    15 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::EAST]], [Defense::MOAT]),
    16 => new AttackCard(Tactic::LOGS, ["A" => [AttackSide::NORTH]], [Defense::MOAT]),
    17 => new AttackCard(Tactic::LOGS, ["E" => [AttackSide::WEST]], [Defense::MOAT], true),
    18 => new AttackCard(Tactic::LOGS, ["E" => [AttackSide::EAST]], [Defense::MOAT], true),
    19 => new AttackCard(Tactic::BOLTS, ["C" => [AttackSide::NORTH], "B" => [AttackSide::SOUTH]], [Defense::TOWERS]),
    20 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::NORTH], "C" => [AttackSide::SOUTH]], [Defense::TOWERS]),
    21 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::WEST], "C" => [AttackSide::EAST]], [Defense::TOWERS]),
    22 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::EAST], "C" => [AttackSide::WEST]], [Defense::TOWERS]),
    23 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::EAST], "A" => [AttackSide::WEST]], [Defense::TOWERS], true),
    24 => new AttackCard(Tactic::BOLTS, ["B" => [AttackSide::NORTH], "A" => [AttackSide::SOUTH]], [Defense::TOWERS], true),
    25 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::SOUTH]], [Defense::WALLS]),
    26 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::WEST]], [Defense::WALLS]),
    27 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::EAST]], [Defense::WALLS]),
    28 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::NORTH]], [Defense::WALLS]),
    29 => new AttackCard(Tactic::ROCKS, ["E" => [AttackSide::SOUTH]], [Defense::WALLS], true),
    30 => new AttackCard(Tactic::ROCKS, ["E" => [AttackSide::NORTH]], [Defense::WALLS], true),
    31 => new AttackCard(null, ["C" => [AttackSide::WEST, AttackSide::EAST], "B" => [AttackSide::SOUTH]], [Defense::WALLS]),
    32 => new AttackCard(null, ["C" => [AttackSide::NORTH, AttackSide::SOUTH], "B" => [AttackSide::WEST]], [Defense::WALLS]),
    33 => new AttackCard(null, ["C" => [AttackSide::NORTH, AttackSide::SOUTH], "B" => [AttackSide::EAST]], [Defense::WALLS]),
    34 => new AttackCard(null, ["C" => [AttackSide::EAST, AttackSide::WEST], "B" => [AttackSide::NORTH]], [Defense::WALLS]),
    35 => new AttackCard(null, ["B" => [AttackSide::NORTH, AttackSide::SOUTH], "D" => [AttackSide::WEST]], [Defense::WALLS], true),
    36 => new AttackCard(null, ["B" => [AttackSide::NORTH, AttackSide::SOUTH], "D" => [AttackSide::EAST]], [Defense::WALLS], true),
];

const FINAL_ESCALADE_CARDS = [
    37 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::WEST, AttackSide::SOUTH]], [Defense::WALLS]),
    38 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::EAST, AttackSide::SOUTH]], [Defense::WALLS]),
    39 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::NORTH, AttackSide::SOUTH]], [Defense::WALLS]),
    40 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::NORTH, AttackSide::EAST]], [Defense::WALLS]),
    41 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::NORTH, AttackSide::WEST]], [Defense::WALLS]),
    42 => new AttackCard(Tactic::ROCKS, ["A" => [AttackSide::EAST, AttackSide::WEST]], [Defense::WALLS]),
];

class PlayerAttackCard {
    public function __construct(
        public string $id,
        public int $position,
        public bool $faceUp,
        public DomainCard $card,
    ) {}

    public static function fromCardArray(array $from): PlayerAttackCard {
        $id = $from["id"];
        $card = ((int) $id <= 36) ? ATTACK_CARDS[$id] : FINAL_ESCALADE_CARDS[$id];
        $locArgs = \explode("_", $from["location_arg"]);
        $position = (int) $locArgs[0];
        $faceUp = \count($locArgs) > 1;
        return new PlayerAttackCard($id, $position, $faceUp, $card);
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
