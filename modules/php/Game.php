<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * theanarchy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

namespace Bga\Games\theanarchy;


require_once(__DIR__ . "/Constants.php");
require_once(__DIR__ . "/Boxes/BoxType.php");
require_once(__DIR__ . "/Boxes/Sections.php");

use Bga\GameFramework\Components\Counters\PlayerCounter;
use Bga\GameFramework\Components\Counters\TableCounter;

use const Bga\Games\theanarchy\Boxes\SECTIONS;
use Bga\Games\theanarchy\States\InitialSetup;
use Bga\Games\theanarchy\Resource;
use Bga\Games\theanarchy\Boxes\Box;
use Bga\Games\theanarchy\Boxes\Reward;

class Game extends \Bga\GameFramework\Table {
    public static array $CARD_TYPES;

    public TableCounter $round;

    /**
     * All player resource counters.
     * @var array<string, PlayerCounter>
     */
    public array $playerResources;

    /**
     * Your global variables labels:
     *
     * Here, you can assign labels to global variables you are using for this game. You can use any number of global
     * variables with IDs between 10 and 99. If you want to store any type instead of int, use $this->globals instead.
     *
     * NOTE: afterward, you can get/set the global variables with `getGameStateValue`, `setGameStateInitialValue` or
     * `setGameStateValue` functions.
     */
    public function __construct() {
        parent::__construct();

        $this->round = $this->bga->counterFactory->createTableCounter("round", 1);

        $this->playerResources = [];
        foreach (Resource::cases() as $resource) {
            $this->playerResources[$resource->name] = $this->bga->counterFactory->createPlayerCounter($resource->value);
        }

        /* example of notification decorator.
        // automatically complete notification args when needed
        $this->bga->notify->addDecorator(function(string $message, array $args) {
            if (isset($args['player_id']) && !isset($args['player_name']) && str_contains($message, '${player_name}')) {
                $args['player_name'] = $this->getPlayerNameById($args['player_id']);
            }

            if (isset($args['card_id']) && !isset($args['card_name']) && str_contains($message, '${card_name}')) {
                $args['card_name'] = self::$CARD_TYPES[$args['card_id']]['card_name'];
                $args['i18n'][] = ['card_name'];
            }

            return $args;
        });*/
    }

    /**
     * Computes and returns the current game progression.
     * The number returned must be an integer between 0 and 100.
     * @return int
     */
    public function getGameProgression() {
        return ($this->round->get()) - 1 * 20;
    }

    public function resources(Resource|string $resource): PlayerCounter {
        if (\is_string($resource)) {
            $resource = Resource::from($resource);
        }
        return $this->playerResources[$resource->name];
    }

    public function upgradeTableDb($from_version) {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "ALTER TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "CREATE TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    protected function getAllDatas(int $currentPlayerId): array {
        $result = [];

        $this->round->fillResult($result);

        $result["players"] = $this->getCollectionFromDb(
            "SELECT player_id AS id, player_score AS score FROM `player`"
        );

        foreach ($this->playerResources as $counter) {
            $counter->fillResult($result);
        }

        $checkedBoxes = $this->allCheckedBoxes();
        foreach ($this->boxesByPlayer($checkedBoxes) as $playerId => $boxes) {
            $result["players"][$playerId]["checkedBoxes"] = $boxes;
        }

        return $result;
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = []): string {
        $this->round->initDb(1);

        $initialValues = [
            Resource::SERFS->value => 1,
            Resource::SOLDIERS->value => 2,
            Resource::SILVER->value => 1,
            Resource::FOOD->value => 1,
            Resource::MATERIALS->value => 1,
        ];

        foreach (Resource::cases() as $resource) {
            $this->resources($resource)->initDb(
                array_keys($players),
                initialValue: $initialValues[$resource->value] ?? 0
            );
        }

        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $playerId => $player) {
            $query_values[] = vsprintf("(%s, '%s', '%s')", [
                $playerId,
                array_shift($default_colors),
                addslashes($player["player_name"]),
            ]);
        }

        static::DbQuery(
            \sprintf(
                "INSERT INTO `player` (`player_id`, `player_color`, `player_name`) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->insertInitialProduction(array_keys($players));
        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.
        // $this->tableStats->init('table_teststat1', 0);
        // $this->playerStats->init('player_teststat1', 0);

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();

        return InitialSetup::class;
    }

    private function insertInitialProduction($playerIds) {
        $query = "INSERT INTO checked_box (player_id, section, box_id) VALUES ";
        $values = [];
        foreach ($playerIds as $playerId) {
            $values[] = "($playerId, 'SERFS', 1)";
            $values[] = "($playerId, 'MATERIALS', 1)";
            $values[] = "($playerId, 'SILVER', 1)";
            $values[] = "($playerId, 'FOOD', 1)";
            $values[] = "($playerId, 'SOLDIERS', 1)";
            $values[] = "($playerId, 'SOLDIERS', 2)";
        }
        $query .= implode(", ", $values);
        static::DbQuery($query);
    }

    /** Debug: jump to a state. */
    public function debug_goToState(int $state = 6) {
        $this->gamestate->jumpToState($state);
    }


    /** Debug: test zombie code. */
    public function debug_playOneMove() {
        $this->bga->debug->playUntil(fn(int $count) => $count == 1);
    }

    /** Debug: give me a pile of stuff to check boxes with. */
    public function debug_giveMeResources() {
        $resources = array_reduce(["serfs", "craftsmen", "patrons", "soldiers", "knights", "silver", "food", "materials"], function ($items, $resource) {
            $items[$resource] = 10;
            return $items;
        }, []);

        Reward::resources("DEBUG", 0, $resources)->grant($this, (int) $this->getCurrentPlayerId());
        $currPlayerId = (int) $this->getCurrentPlayerId();
        $newAllCheckedBoxes = $this->allCheckedBoxes($currPlayerId);
        $this->notify->player(
            $currPlayerId, "newAvailable", "",
            $this->getAvailableBoxes($currPlayerId, $newAllCheckedBoxes)
        );
    }

    public function allPlayerIds(): array {
        return $this->getObjectListFromDB("SELECT player_id FROM player", true);
    }

    /**
     * Returns all checked boxes.
     * If playerId is not null, filters down to boxes checked by that player.
     * @param int|null $playerId An optional player ID to filter on.
     * @return Box[] A list of boxes.
     */
    public function allCheckedBoxes(int | null $playerId = null): array {
         $query = "SELECT * FROM checked_box";
         if ($playerId) {
             $query .= " WHERE player_id = $playerId";
         }
         $query .= " ORDER BY player_id, section, box_id ASC";
         $boxes = $this->getObjectListFromDB($query);
         return array_map(Box::fromDb(...), $boxes);
     }

    /**
     * Returns all boxes currently checkable by the player.
     * @param int $playerId The player to check.
     * @param array $allCheckedBoxes All boxes checked by the player.
     * @return array<string, int[]>
     */
    public function getAvailableBoxes(int $playerId, array $allCheckedBoxes): array {
        return array_reduce(
            SECTIONS,
            function($acc, $section) use ($allCheckedBoxes, $playerId) {
                $acc[$section->name] = $section->validBoxes($this, $playerId, $allCheckedBoxes);
                return $acc;
            },
            [],
        );
    }

     public function boxesByPlayer(array $boxes): array {
        $out = [];
        foreach ($boxes as $box) {
            $out[$box->playerId][$box->section][] = $box->boxId;
        }
        return $out;
     }

     public static function checkBox(int $playerId, string $section, int $boxId, string|null $writtenValue = null) {
        if ($writtenValue) {
            Game::DbQuery("INSERT INTO checked_box (player_id, section, box_id, written_value) VALUES ($playerId, '$section', $boxId, '$writtenValue')");
        } else {
            Game::DbQuery("INSERT INTO checked_box (player_id, section, box_id) VALUES ($playerId, '$section', $boxId)");
        }
     }

}
