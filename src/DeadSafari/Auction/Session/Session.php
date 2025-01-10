<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Session;

use pocketmine\player\Player;

class Session {

    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function getPlayer(): Player {
        return $this->player;
    }
}