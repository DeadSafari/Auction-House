<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Auction;

use DeadSafari\Auction\Main;
use DeadSafari\Auction\Session\Session;
use Error;
use Exception;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\block\utils\ColoredTrait;
use pocketmine\block\Wool;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\TextFormat as C;
use poggit\libasynql\result\SqlSelectResult;

class AuctionManager {

    /** @var Auction[] */
    private array $items;

    public function __construct() {
        $this->fetchAndParse();
    }

    private function fetchAndParse(): void {
        Main::getInstance()->getDatabaseManager()->getAuctions(
            /** @param SqlSelectResult[] */
            function (array $rows): void {
                //TODO:
                return;
            }
        );
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

    public function sell(Item $item, int $price, Session $session): void {
        $session->getPlayer()->getInventory()->removeItem($item);
        $itemData = $this->parseItem($item);
        Main::getInstance()->getDatabaseManager()->createAuction($session->getPlayer()->getXuid(), -1, $itemData, $price);
    }
}