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

use Bga\Games\theanarchy\States\PlayerTurn;
use Bga\GameFramework\Components\Counters\PlayerCounter;
use Bga\GameFramework\Components\Counters\TableCounter;

class Game extends \Bga\GameFramework\Table {
    public static array $CARD_TYPES;

    public TableCounter $round;

    public PlayerCounter $serfs;
    public PlayerCounter $craftsmen;
    public PlayerCounter $patrons;
    public PlayerCounter $soldiers;
    public PlayerCounter $knights;
    public PlayerCounter $silver;
    public PlayerCounter $food;
    public PlayerCounter $materials;

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

        $this->serfs = $this->bga->counterFactory->createPlayerCounter('serfs');
        $this->craftsmen = $this->bga->counterFactory->createPlayerCounter('craftsmen');
        $this->patrons = $this->bga->counterFactory->createPlayerCounter('patrons');
        $this->soldiers = $this->bga->counterFactory->createPlayerCounter('soldiers');
        $this->knights = $this->bga->counterFactory->createPlayerCounter('knights');
        $this->silver = $this->bga->counterFactory->createPlayerCounter('silver');
        $this->food = $this->bga->counterFactory->createPlayerCounter('food');
        $this->materials = $this->bga->counterFactory->createPlayerCounter('materials');

        self::$CARD_TYPES = [
            1 => [
                "card_name" => clienttranslate('Troll'), // ...
            ],
            2 => [
                "card_name" => clienttranslate('Goblin'), // ...
            ],
            // ...
        ];

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
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     */
    public function getGameProgression() {
        // TODO: compute and return the game progression

        return 0;
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

    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    protected function getAllDatas(int $currentPlayerId): array {
        $result = [];

        $this->round->fillResult($result);

        $result["players"] = $this->getCollectionFromDb(
            "SELECT
                player_id AS id,
                player_score AS score,
                tent, gate, moat,
                left_wall AS leftWall,
                right_wall as rightWall,
                bottom_wall as bottomWall,
                top_wall AS topWall,
                tower_left_top AS towerLeftTop,
                tower_left_bottom AS towerLeftBottom,
                tower_right_top AS towerRightTop,
                tower_right_bottom AS towerRightBottom
            FROM `player`"
        );

        // Transform all those int fields into actual ints because this framework doesn't for some reason
        foreach ($result["players"] as &$player) {
            foreach ([
                "tent", "gate", "moat",
                "leftWall", "rightWall", "bottomWall", "topWall",
                "towerLeftTop", "towerRightTop", "towerLeftBottom", "towerRightBottom",
            ] as $field) {
                $player[$field] = (int) ($player[$field]);
            }
        }

        $this->serfs->fillResult($result);
        $this->craftsmen->fillResult($result);
        $this->patrons->fillResult($result);
        $this->soldiers->fillResult($result);
        $this->knights->fillResult($result);
        $this->silver->fillResult($result);
        $this->food->fillResult($result);
        $this->materials->fillResult($result);

        return $result;
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = []): string {
        $this->round->initDb(1);

        $this->serfs->initDb(array_keys($players), initialValue: 1);
        $this->craftsmen->initDb(array_keys($players));
        $this->patrons->initDb(array_keys($players));
        $this->soldiers->initDb(array_keys($players), initialValue: 2);
        $this->knights->initDb(array_keys($players));
        $this->silver->initDb(array_keys($players), initialValue: 1);
        $this->food->initDb(array_keys($players), initialValue: 1);
        $this->materials->initDb(array_keys($players), initialValue: 1);

        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("(%s, '%s', '%s')", [
                $player_id,
                array_shift($default_colors),
                addslashes($player["player_name"]),
            ]);
        }

        static::DbQuery(
            sprintf(
                "INSERT INTO `player` (`player_id`, `player_color`, `player_name`) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Init global values with their initial values.

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.

        // Dummy content.
        // $this->tableStats->init('table_teststat1', 0);
        // $this->playerStats->init('player_teststat1', 0);

        // TODO: Setup the initial game situation here.

        // Activate first player once everything has been initialized and ready.
        $this->activeNextPlayer();

        return PlayerTurn::class;
    }

    /**
     * Example of debug function.
     * Here, jump to a state you want to test (by default, jump to next player state)
     * You can trigger it on Studio using the Debug button on the right of the top bar.
     */
    public function debug_goToState(int $state = 3) {
        $this->gamestate->jumpToState($state);
    }

    /**
     * Another example of debug function, to easily test the zombie code.
     */
    public function debug_playOneMove() {
        $this->bga->debug->playUntil(fn(int $count) => $count == 1);
    }

    /*
    Another example of debug function, to easily create situations you want to test.
    Here, put a card you want to test in your hand (assuming you use the Deck component).

    public function debug_setCardInHand(int $cardType, int $playerId) {
        $card = array_values($this->cards->getCardsOfType($cardType))[0];
        $this->cards->moveCard($card['id'], 'hand', $playerId);
    }
    */
}
