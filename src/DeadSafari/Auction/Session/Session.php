<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Session;

use DeadSafari\Auction\Main;
use pocketmine\player\Player;
use poggit\libasynql\result\SqlSelectResult;

class Session {

    private Player $player;
    private int $money;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->money = 0;
    }

    public function getMoney(): int {
        return $this->money;
    }

    public function fetch(): void {
        Main::getInstance()->getDatabaseManager()->getPlayerMoney(
            $this->player->getXuid(),
            function (array $results): void {
                /** @var SqlSelectResult */
                $result = $results[0];
                $rows = $result->getRows();
                $this->setMoney($rows[0]["money"]);
            }
        );
    }

    public function setMoney(int $value): void {
        $this->money = $value;
    }

    public function getPlayer(): Player {
        return $this->player;
    }
}