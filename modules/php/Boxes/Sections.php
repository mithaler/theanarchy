<?php

namespace BGA\Games\theanarchy\Boxes;

require_once(__DIR__ . "/BasicRow.php");
require_once(__DIR__ . "/ProductionRow.php");

/**
 * @var array<string, BoxType>
 */
const SECTIONS = [
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
];
