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

enum Resource: string {
    case SERFS = "serfs";
    case CRAFTSMEN = "craftsmen";
    case PATRONS = "patrons";
    case SOLDIERS = "soldiers";
    case KNIGHTS = "knights";
    case SILVER = "silver";
    case FOOD = "food";
    case MATERIALS = "materials";
    case MUSTER_TOKENS = "muster_tokens";
}
