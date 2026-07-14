<?php

namespace BGA\Games\theanarchy\Boxes;

use Bolts;
use Chapel;
use Covers;
use Gate;
use HotOil;
use Keep;
use KnightsTraining;
use Lammas;
use Logs;
use Michaelmas;
use Mint;
use Moat;
use Rocks;
use Stables;
use StValentinesFestival;
use Tactics;
use Tournaments;
use Tower;
use Wall;

require_once(__DIR__ . "/BasicRow.php");
require_once(__DIR__ . "/FortificationRow.php");
require_once(__DIR__ . "/UnclickableRow.php");
require_once(__DIR__ . "/WealthWheel.php");
require_once(__DIR__ . "/SimpleBuildings.php");
require_once(__DIR__ . "/StValentinesFestival.php");
require_once(__DIR__ . "/Tactics.php");
require_once(__DIR__ . "/Chapel.php");
require_once(__DIR__ . "/Tournaments.php");
require_once(__DIR__ . "/Brewhouse.php");
require_once(__DIR__ . "/Michaelmas.php");
require_once(__DIR__ . "/Lammas.php");

/**
 * @param array<string, BoxType>
 */
const SECTIONS = [
    // Fortification rows
    "GATE" => new Gate(),
    "TOWER" => new Tower(),
    "WALL" => new Wall(),
    "MOAT" => new Moat(),

    // Basic resource rows
    "QUARRY & FOREST" => new QuarryForest(),
    "FARMS" => new Farms(),
    "TRAINING GROUNDS" => new TrainingGrounds(),

    "COVERS" => new Covers(),
    "ROCKS" => new Rocks(),
    "HOT OIL" => new HotOil(),
    "LOGS" => new Logs(),
    "BOLTS" => new Bolts(),

    // Production rows
    "SERFS" => new Serfs(),
    "CRAFTSMEN" => new Craftsmen(),
    "MATERIALS" => new Materials(),
    "PATRONS" => new Patrons(),
    "SILVER" => new Silver(),
    "FOOD" => new Food(),
    "SOLDIERS" => new Soldiers(),
    "KNIGHTS" => new Knights(),

    // Point rows
    "BRAVERY" => new Bravery(),
    "LOYALTY" => new Loyalty(),
    "INFLUENCE" => new Influence(),
    "MIGHT" => new Might(),

    // Wealth wheel
    "GUILDSMEN" => new Guildsmen(),
    "ALLIES" => new Allies(),
    "MERCENARIES" => new Mercenaries(),
    "SIEGECRAFT" => new Siegecraft(),
    "SIEGECRAFT_construction" => new SiegecraftConstruction(),

    // Leadership rows
    "GOVERNANCE" => new Governance(),
    "WARCRAFT" => new Warcraft(),
    "WORSHIP" => new Worship(),
    "ENTERTAINMENT" => new Entertainment(),

    "KEEP" => new Keep(),
    "MINT" => new Mint(),
    "STABLES" => new Stables(),
    "RAMPARTS" => new Ramparts(),
    "TACTICS" => new Tactics(),

    "CHAPEL" => new Chapel(),
    "KNIGHTS TRAINING" => new KnightsTraining(),
    "ST VALENTINES FESTIVAL" => new StValentinesFestival(),
    "TOURNAMENTS" => new Tournaments(),
    "BREWHOUSE" => new Brewhouse(),
    "MICHAELMAS" => new Michaelmas(),
    "LAMMAS" => new Lammas(),
];
