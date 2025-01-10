<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Session;

use pocketmine\player\Player;

class SessionManager {

    /** @var Session[] */
    private array $sessions;

    public function __construct() {
        $this->sessions = [];
    }

    public function createSession(Player $player): Session {
        $session = new Session($player);
        $this->sessions[] = $session;
        return $session;
    }

    public function deleteSession(Player $player): void {
        $session = $this->getSession($player);
        if ($session === null) {
            return;
        }

        unset($this->sessions[array_search($session, $this->sessions)]);
    }

    public function getSession(Player $player): ?Session {
        foreach($this->sessions as $session) {
            if ($session->getPlayer() === $player) {
                return $session;
            }
        }
        return null;
    }
}