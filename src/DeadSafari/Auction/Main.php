<?php

declare(strict_types=1);

namespace DeadSafari\Auction;

use CortexPE\Commando\PacketHooker;
use DeadSafari\Auction\Database\DatabaseManager;
use DeadSafari\Auction\Session\SessionListener;
use DeadSafari\Auction\Session\SessionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase {

    private DatabaseManager $databaseManager;
    private SessionManager $sessionManager;
    private static self $instance;

    public function onLoad(): void {
        self::$instance = $this;
        $this->getLogger()->info(C::YELLOW . "Auction House is now loaded.");
    }

    public function onEnable(): void {

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->databaseManager = new DatabaseManager();
        $this->sessionManager = new SessionManager();
        $this->registerEvents();
        $this->getLogger()->info(C::GREEN . "Auction House is now enabled.");
    }

    public function onDisable(): void {
        $this->getLogger()->info(C::RED . "Auction House is now disabled.");
    }

    public function registerEvents(): void {
        $this->getServer()->getPluginManager()->registerEvents(new SessionListener(), $this);
    }


    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }

    public function getDatabaseManager(): DatabaseManager {
        return $this->databaseManager;
    }

    public static function getInstance(): self {
        return self::$instance;
    }
}