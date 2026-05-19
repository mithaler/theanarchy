<?php

namespace BGA\Games\theanarchy\Boxes;

use Gate;
use Keep;
use Moat;

require_once(__DIR__ . "/BasicRow.php");
require_once(__DIR__ . "/FortificationRow.php");
require_once(__DIR__ . "/UnclickableRow.php");
require_once(__DIR__ . "/WealthWheel.php");
require_once(__DIR__ . "/Keep.php");

/**
 * @var array<string, BoxType>
 */
const SECTIONS = [
    // Fortification rows
    "GATE" => new Gate(),
    "MOAT" => new Moat(),

    // Basic resource rows
    "QUARRY & FOREST" => new QuarryForest(),
    "FARMS" => new Farms(),
    "TRAINING GROUNDS" => new TrainingGrounds(),

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
];
