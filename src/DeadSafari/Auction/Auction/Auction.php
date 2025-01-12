<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Auction;

use DeadSafari\Auction\Main;
use pocketmine\item\Item;

class Auction {

    private string $author;
    private int $expiry;
    private array $rawData;
    private Item $item;

    public function __construct(string $author, int $expiry, array $rawData) {
        $this->author = $author;
        $this->expiry = $expiry;
        $this->rawData = $rawData;
        $this->item = Main::getInstance()->getAuctionManager()->assembleItem($rawData);
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getExpiry(): int {
        return $this->expiry;
    }

    public function getRawData(): array {
        return $this->rawData;
    }

    public function getName(): string {
        return $this->getRawData()["aliases"][0];
    }

    public function getItem(): Item {
        return $this->item;
    }
}