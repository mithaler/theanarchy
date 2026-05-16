<?php
declare(strict_types=1);

namespace BGA\Games\theanarchy;

class StateConstants {
    const GAME_START = 2;

    const INITIAL_SETUP = 3;
    const ROUND_SETUP = 4;
    const CHOOSE_PATH_CARDS = 5;
    const CHECK_BOXES = 6;

    const GAME_END = 99;
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
