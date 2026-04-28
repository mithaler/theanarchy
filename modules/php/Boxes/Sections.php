<?php

namespace BGA\Games\theanarchy\Boxes;

require_once(__DIR__ . "/BasicRow.php");

/**
 * @var array<string, BoxType>
 */
const SECTIONS = [
    "QUARRY & FOREST" => new QuarryForest(),
    "FARMS" => new Farms(),
    "TRAINING GROUNDS" => new TrainingGrounds(),
];
