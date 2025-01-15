<?php

declare(strict_types=1);

namespace DeadSafari\Auction;

use CortexPE\Commando\PacketHooker;
use DeadSafari\Auction\Auction\AuctionManager;
use DeadSafari\Auction\BlackAuction\BlackAuctionManager;
use DeadSafari\Auction\Commands\AuctionHouseCommand;
use DeadSafari\Auction\Database\DatabaseManager;
use DeadSafari\Auction\Session\SessionListener;
use DeadSafari\Auction\Session\SessionManager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase {

    private DatabaseManager $databaseManager;
    private SessionManager $sessionManager;
    private AuctionManager $auctionManager;
    private BlackAuctionManager $blackAuctionManager;
    private static self $instance;

    public function onLoad(): void {
        self::$instance = $this;
        $this->getLogger()->info(C::YELLOW . "Auction House is now loaded.");
    }

    public function onEnable(): void {

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        $this->databaseManager = new DatabaseManager();
        $this->sessionManager = new SessionManager();
        $this->auctionManager = new AuctionManager();
        $this->blackAuctionManager = new BlackAuctionManager();
        $this->registerEvents();
        $this->registerCommands();
        $this->getLogger()->info(C::GREEN . "Auction House is now enabled.");
    }

    public function onDisable(): void {
        $this->getLogger()->info(C::RED . "Auction House is now disabled.");
    }

    public function registerEvents(): void {
        $this->getServer()->getPluginManager()->registerEvents(new SessionListener(), $this);
    }

    public function registerCommands(): void {
        $this->getServer()->getCommandMap()->register("ah", new AuctionHouseCommand($this, "ah", "Auction House commands", ["auction", "auction-house"]));
    }


    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }

    public function getDatabaseManager(): DatabaseManager {
        return $this->databaseManager;
    }

    public function getAuctionManager(): AuctionManager {
        return $this->auctionManager;
    }

    public function getBlackAuctionManager(): BlackAuctionManager {
        return $this->blackAuctionManager;
    }

    public static function getInstance(): self {
        return self::$instance;
    }
}