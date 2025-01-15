<?php

declare(strict_types=1);

namespace DeadSafari\Auction\BlackAuction;

use DeadSafari\Auction\Main;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class BlackAuctionManager {

    private ?BlackAuction $blackAuction = null;

    public function getLootTable(): array {
        return [
            [VanillaItems::DIAMOND_HELMET(), 1],
            [VanillaItems::LEATHER_CAP(), 5],
            [VanillaItems::DIAMOND_CHESTPLATE(), 1],
            [VanillaItems::LEATHER_TUNIC(), 6],
        ];
    }

    public function getBlackAuction(): ?BlackAuction {
        return $this->blackAuction;
    }

    public function getItem(): Item {
        $lootTable = $this->getLootTable();

        $arr = [];
        foreach ($lootTable as $loot) {
            for ($x = 0; $x <= $loot[1]; $x++) {
                $arr[] = $loot[0];
            }
        }

        /** @var Item */
        $item = array_rand($arr);

        return $item;
    }

    public function startAuction(): void {
        Main::getInstance()->getServer()->broadcastMessage(TextFormat::RED . "Black Auction is now starting! Run /bah to participate.");


    }
}