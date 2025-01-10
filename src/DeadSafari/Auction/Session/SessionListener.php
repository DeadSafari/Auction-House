<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Session;

use DeadSafari\Auction\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class SessionListener implements Listener {

    public function PlayerLoginEvent(PlayerLoginEvent $event): void {
        Main::getInstance()->getSessionManager()->createSession($event->getPlayer());
    }

    public function PlayerQuitEvent(PlayerQuitEvent $event): void {
        Main::getInstance()->getSessionManager()->deleteSession($event->getPlayer());
    }
}