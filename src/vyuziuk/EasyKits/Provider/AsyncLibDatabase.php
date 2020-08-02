<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\Provider;

use pocketmine\Player;
use poggit\libasynql\libasynql;
use vyuziuk\EasyKits\Main;
use vyuziuk\EasyKits\Objects\Kit;

/**
 * Class AsyncLibDatabase
 * @package vyuziuk\EasyKits\Provider
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class AsyncLibDatabase{
    /** @var \poggit\libasynql\DataConnector */
    private $database;
    /** @var self */
    private static $instance;

    const EASY_KITS_INIT = "easykits.init";
    const EASY_KITS_ADD_PLAYER = "easykits.addplayer";
    const EASY_KITS_UPDATE_PLAYER = "easykits.updateplayer";
    const EASY_KITS_SELECT_PLAYER = "easykits.selectplayer";

    public function __construct(Main $main){
        $this->database = libasynql::create($main, $main->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql"
        ]);
        $this->database->executeChange(self::EASY_KITS_INIT);
        self::$instance = $this;
    }

    public function newPlayer(Player $player, Kit $kit): void{
        $this->getDatabase()->executeInsert(self::EASY_KITS_ADD_PLAYER, [
            "player" => $player->getLowerCaseName(),
            "time" => (time() + (60 * 60 * $kit->getTime()))
        ]);
    }

    public function selectPlayer(Player $player, callable $fun = null): void{
        $this->getDatabase()->executeSelect(self::EASY_KITS_SELECT_PLAYER, [
            "player" => $player->getLowerCaseName()
        ], function(array $rows) use ($fun){
            $fun($rows);
        });
    }

    public function updatePlayer(Player $player, Kit $kit): void{
        $this->getDatabase()->executeChange(self::EASY_KITS_UPDATE_PLAYER, [
            "player" => $player->getLowerCaseName(),
            "time" => time() * (60 * 60 * $kit->getTime())
        ]);
    }

    /**
     * @return AsyncLibDatabase
     */
    public static function getInstance(): AsyncLibDatabase{
        return self::$instance;
    }

    /**
     * @return \poggit\libasynql\DataConnector
     */
    public function getDatabase(): \poggit\libasynql\DataConnector{
        return $this->database;
    }
}