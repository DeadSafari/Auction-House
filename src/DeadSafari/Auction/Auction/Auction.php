<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Auction;

use DeadSafari\Auction\Main;
use pocketmine\item\Item;

class Auction {

    private int $id;
    private string $author;
    private int $expiry;
    private int $price;
    private array $rawData;
    private Item $item;

    public function __construct(int $id, string $author, int $expiry, int $price, array $rawData) {
        $this->id = $id;
        $this->author = $author;
        $this->expiry = $expiry;
        $this->price = $price;
        $this->rawData = $rawData;
        $this->item = Main::getInstance()->getAuctionManager()->assembleItem($rawData);
    }

    public function getId(): int {
        return $this->id;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getExpiry(): int {
        return $this->expiry;
    }

    public function getPrice(): int {
        return $this->price;
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