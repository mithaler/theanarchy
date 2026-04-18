-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- theanarchy implementation : © Michael Louis Thaler <michael.louis.thaler@gmail.com>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `card_type` VARCHAR(16) NOT NULL,
--   `card_type_arg` INT NOT NULL,
--   `card_location` VARCHAR(16) NOT NULL,
--   `card_location_arg` INT NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;


-- Additional player fields for overall state
ALTER TABLE player ADD (
    -- Tent: left to right
    tent TINYINT NOT NULL DEFAULT 0,

    -- Castle levels: 0 to whatever
    gate TINYINT NOT NULL DEFAULT 0,
    left_wall TINYINT NOT NULL DEFAULT 0,
    right_wall TINYINT NOT NULL DEFAULT 0,
    bottom_wall TINYINT NOT NULL DEFAULT 0,
    top_wall TINYINT NOT NULL DEFAULT 0,
    tower_left_top TINYINT NOT NULL DEFAULT 0,
    tower_left_bottom TINYINT NOT NULL DEFAULT 0,
    tower_right_top TINYINT NOT NULL DEFAULT 0,
    tower_right_bottom TINYINT NOT NULL DEFAULT 0,
    moat TINYINT NOT NULL DEFAULT 0
);


-- Boxes checked by players
CREATE TABLE IF NOT EXISTS checked_box (
    -- The player who checked the box
    player_id INT NOT NULL,

    -- The section the box is in. Always matches the row name as it appears on the sheet exactly,
    -- like "WALLS", "QUARRY & FOREST" or "ST. VALENTINE'S FESTIVAL".
    section VARCHAR(64) NOT NULL,

    -- The ID of the box.
    -- If the row is simple left-to-right, matches the position (the first checked box is numbered 1).
    -- Other schemes are documented as they appear in PHP code.
    box_id TINYINT NOT NULL,

    -- For cases where a number is written in the box, NULL if not needed.
    -- I think this is only MICHAELMAS.
    written_value TINYINT,

    PRIMARY KEY (player_id, section, box_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
