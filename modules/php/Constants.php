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
enum Brewhouse {
    case GRAIN;
    case WATER;
    case HOP;
    case FOAM;
}
enum Lemmas {
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
        public Brewhouse $brewhouse,
        public Lemmas $lemmas,
    ) {}
}

const DOMAIN_CARDS = [
    1  => new DomainCard(KnightsTraining::SCROLL, 4, 3, Tournament::RIGHT, Tournament::CENTER, Brewhouse::GRAIN, Lemmas::YELLOW),
    2  => new DomainCard(KnightsTraining::ARMS, 1, 2, Tournament::LEFT, Tournament::RIGHT, Brewhouse::WATER, Lemmas::RED),
    3  => new DomainCard(KnightsTraining::ARMS, 3, 6, Tournament::CENTER, Tournament::LEFT, Brewhouse::GRAIN, Lemmas::GREEN),
    4  => new DomainCard(KnightsTraining::SCROLL, 6, 7, Tournament::RIGHT, Tournament::LEFT, Brewhouse::WATER, Lemmas::PURPLE),
    5  => new DomainCard(KnightsTraining::SCROLL, 2, 7, Tournament::CENTER, Tournament::RIGHT, Brewhouse::WATER, Lemmas::PURPLE),
    6  => new DomainCard(KnightsTraining::BOW, 6, 5, Tournament::RIGHT, Tournament::CENTER, Brewhouse::HOP, Lemmas::YELLOW),
    7  => new DomainCard(KnightsTraining::HORSESHOE, 3, 4, Tournament::LEFT, Tournament::CENTER, Brewhouse::HOP, Lemmas::PURPLE),
    8  => new DomainCard(KnightsTraining::BOW, 4, 1, Tournament::RIGHT, Tournament::LEFT, Brewhouse::FOAM, Lemmas::GREEN),
    9  => new DomainCard(KnightsTraining::HORSESHOE, 3, 8, Tournament::CENTER, Tournament::LEFT, Brewhouse::FOAM, Lemmas::RED),
    10 => new DomainCard(KnightsTraining::SCROLL, 2, 3, Tournament::CENTER, Tournament::RIGHT, Brewhouse::GRAIN, Lemmas::YELLOW),
    11 => new DomainCard(KnightsTraining::BOW, 2, 5, Tournament::RIGHT, Tournament::CENTER, Brewhouse::HOP, Lemmas::YELLOW),
    12 => new DomainCard(KnightsTraining::BOW, 2, 1, Tournament::CENTER, Tournament::RIGHT, Brewhouse::FOAM, Lemmas::GREEN),
    13 => new DomainCard(KnightsTraining::SCROLL, 4, 7, Tournament::RIGHT, Tournament::LEFT, Brewhouse::WATER, Lemmas::PURPLE),
    14 => new DomainCard(KnightsTraining::HORSESHOE, 5, 4, Tournament::LEFT, Tournament::CENTER, Brewhouse::HOP, Lemmas::PURPLE),
    15 => new DomainCard(KnightsTraining::ARMS, 5, 6, Tournament::LEFT, Tournament::CENTER, Brewhouse::GRAIN, Lemmas::GREEN),
    16 => new DomainCard(KnightsTraining::HORSESHOE, 1, 4, Tournament::LEFT, Tournament::RIGHT, Brewhouse::HOP, Lemmas::PURPLE),
    17 => new DomainCard(KnightsTraining::ARMS, 5, 2, Tournament::LEFT, Tournament::RIGHT, Brewhouse::WATER, Lemmas::RED),
    18 => new DomainCard(KnightsTraining::HORSESHOE, 1, 8, Tournament::LEFT, Tournament::RIGHT, Brewhouse::FOAM, Lemmas::RED),
    19 => new DomainCard(KnightsTraining::SCROLL, 6, 3, Tournament::RIGHT, Tournament::CENTER, Brewhouse::GRAIN, Lemmas::YELLOW),
    20 => new DomainCard(KnightsTraining::BOW, 6, 1, Tournament::CENTER, Tournament::RIGHT, Brewhouse::FOAM, Lemmas::GREEN),
    21 => new DomainCard(KnightsTraining::HORSESHOE, 5, 8, Tournament::CENTER, Tournament::LEFT, Brewhouse::FOAM, Lemmas::RED),
    22 => new DomainCard(KnightsTraining::ARMS, 1, 6, Tournament::LEFT, Tournament::CENTER, Brewhouse::GRAIN, Lemmas::RED),
    23 => new DomainCard(KnightsTraining::ARMS, 3, 2, Tournament::CENTER, Tournament::LEFT, Brewhouse::WATER, Lemmas::RED),
    24 => new DomainCard(KnightsTraining::BOW, 4, 5, Tournament::RIGHT, Tournament::LEFT, Brewhouse::GRAIN, Lemmas::YELLOW),
];

/** A domain card held by a player, containing its ID (so it can be moved easily). */
class PlayerDomainCard {
    public function __construct(
        public string $id,
        public DomainCard $card,
    ) {}
}
