<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Auction;

use DeadSafari\Auction\Main;
use DeadSafari\Auction\Session\Session;
use Error;
use Exception;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\block\utils\ColoredTrait;
use pocketmine\block\Wool;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\TextFormat;
use poggit\libasynql\result\SqlSelectResult;

class AuctionManager {

    /** @var Auction[] */
    private array $auctions = [];

    public function __construct() {
        $this->fetchAndParse();
    }

    /** @return Auction[] */
    public function getAuctions(): array {
        return $this->auctions;
    }

    private function fetchAndParse(): void {
        Main::getInstance()->getDatabaseManager()->getAuctions(
            /** @param SqlSelectResult[] */
            function (array $results): void {
                $rows = $results[0]->getRows();
                foreach ($rows as $row) {
                    $itemId = $row["item"];

                    Main::getInstance()->getDatabaseManager()->getItemById(
                        $itemId,
                        function (array $results) use($row) {
                            $dataStr = $results[0]->getRows()[0]["data"];
                            $data = json_decode($dataStr, true);
                            $auction = new Auction($row["_id"], $row["item"], $row["author"], $row["expires"], $results[0]->getRows()[0]["price"], $data);
                            $this->addAuction($auction);
                        }
                    );
                }
                return;
            }
        );
    }

    public function addAuction(Auction $auction): void {
        // Main::getInstance()->getServer()->getLogger()->info(TextFormat::DARK_PURPLE . "LOADED AUCTION OF " . $auction->getRawData()["aliases"][0]);
        $this->auctions[] = $auction;
    }

    public function removeAuction(Auction $auction): void {
        unset($this->auctions[array_search($auction, $this->auctions)]);

        Main::getInstance()->getDatabaseManager()->removeAuction($auction->getId());
        Main::getInstance()->getDatabaseManager()->removeItem($auction->getItemId());
    }

    public function parseItem(Item $item): array {
        $itemData = [
            "aliases" => StringToItemParser::getInstance()->lookupAliases($item),
            "custom_name" => $item->getCustomName(),
            "count" => $item->getCount(),
            "durability" => -1,
            "enchantments" => []
        ];

        if ($item instanceof Durable) {
            $itemData["durability"] = $item->getDamage();
        }

        foreach ($item->getEnchantments() as $enchantment) {
            $itemData["enchantments"][] = [
                "id" => EnchantmentIdMap::getInstance()->toId($enchantment->getType()),
                "level" => $enchantment->getLevel(),
            ];
        }

        return $itemData;
    }

    public function assembleItem(array $data): Item {
        $item = StringToItemParser::getInstance()->parse($data["aliases"][0]);

        $item->setCustomName($data["custom_name"]);
        $item->setCount($data["count"]);
        if ($item instanceof Durable) {
            $item->setDamage($data["durability"]);
        }

        foreach ($data["enchantments"] as $enchantmentData) {
            print_r($enchantmentData);
            $enchantment = EnchantmentIdMap::getInstance()->fromId($enchantmentData["id"]);
            $item->addEnchantment(new EnchantmentInstance($enchantment, $enchantmentData["level"]));
        }

        return $item;
    }

    public function buy(Auction $auction, Session $session): void {

        // ! Change this to fit your money plugin

        if ($session->getMoney() < ($auction->getPrice())) {
            $session->getPlayer()->sendMessage(TextFormat::RED . "You do not have enough money to buy this!");
            return;
        }

        $session->setMoney($session->getMoney() - $auction->getPrice());

        $claimMenu = InvMenu::create(InvMenu::TYPE_CHEST);
        $claimMenu->getInventory()->setItem(13, $auction->getItem());
        $claimMenu->setInventoryCloseListener(
            function (Player $player, Inventory $inventory) use($auction): void {
                if (empty($inventory->getContents())) {
                    Main::getInstance()->getAuctionManager()->removeAuction($auction);
                    Main::getInstance()->getDatabaseManager()->getPlayerMoney(
                        $auction->getAuthor(),
                        function (array $result) use($auction): void {
                            $prevMoney = $result[0]->getRows()[0]["money"];
                            $newMoney = $auction->getPrice() + $prevMoney;

                            Main::getInstance()->getDatabaseManager()->setPlayerMoney($auction->getAuthor(), $newMoney);
                        }
                    );
                    $player->sendMessage(TextFormat::GREEN . "You purchased " . TextFormat::RESET . TextFormat::GOLD . $auction->getItem()->getName() . TextFormat::RESET . TextFormat::GREEN . " for " . strval($auction->getPrice()) . "!");
                    return;
                }
            }
        );
        $claimMenu->send($session->getPlayer());
    }

    public function incrementAuctionManually(Auction $auction): void {
        $this->auctions[] = $auction;
    }

    public function sell(Item $item, int $price, Session $session): void {
        $session->getPlayer()->getInventory()->removeItem($item);
        $itemData = $this->parseItem($item);
        $time = time();
        // add seconds worth of 2 days
        $time += 172800;
        Main::getInstance()->getDatabaseManager()->createAuction($session->getPlayer()->getXuid(), $time, $itemData, $price);
    }
}