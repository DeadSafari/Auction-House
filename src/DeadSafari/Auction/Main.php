<?php

declare(strict_types=1);

namespace DeadSafari\Auction;

use DeadSafari\Auction\Database\DatabaseManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase {

    private DatabaseManager $databaseManager;
    private self $instance;

    public function onLoad(): void {
        self::$instance = $this;
        $this->getLogger()->info(C::YELLOW . "Auction House is now loaded.");
    }

    public function onEnable(): void {
        $this->databaseManager = new DatabaseManager();
        $this->getLogger()->info(C::GREEN . "Auction House is now enabled.");
    }

    public function onDisable(): void {
        $this->getLogger()->info(C::RED . "Auction House is now disabled.");
    }


    public function getDatabaseManager(): DatabaseManager {
        return $this->databaseManager;
    }

    public static function getInstance(): Main {
        return self::$instance;
    }
}