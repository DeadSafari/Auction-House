<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Database;

use Closure;
use DeadSafari\Auction\Auction\Auction;
use DeadSafari\Auction\Main;
use pocketmine\player\Player;
use poggit\libasynql\base\DataConnectorImpl;
use poggit\libasynql\libasynql;
use poggit\libasynql\result\SqlInsertResult;
use poggit\libasynql\result\SqlSelectResult;
use poggit\libasynql\SqlThread;

class DatabaseManager {

    private DataConnectorImpl $db;

    public function __construct() {
        $this->db = libasynql::create(Main::getInstance(), Main::getInstance()->getConfig()->get("database"), ["mysql" => "mysql.sql"]);
        $this->initTables();
    }

    private function initTables(): void {
        $this->db->executeImplRaw(
            [0 => "CREATE TABLE IF NOT EXISTS auction (_id INT AUTO_INCREMENT PRIMARY KEY, author VARCHAR(255) NOT NULL, expires INT(11), item INT NOT NULL)",
            1 => "CREATE TABLE IF NOT EXISTS item (_id INT AUTO_INCREMENT PRIMARY KEY, price INT NOT NULL, data JSON NOT NULL)",
            2 => "CREATE TABLE IF NOT EXISTS player (xuid VARCHAR(255) PRIMARY KEY NOT NULL, money INT DEFAULT 10000000)"],
            [0 => [], 1 => [], 2 => []],
            [0 => SqlThread::MODE_GENERIC, 1 => SqlThread::MODE_GENERIC, 2 => SqlThread::MODE_GENERIC],
            function () {},
            null
        );
    }

    public function createAuction(string $author_xuid, int $expiry, array $itemData, int $price): void {
        $this->db->executeImplRaw(
            [0 => "INSERT INTO item (price, data) VALUES (?, ?)"],
            [0 => [$price, json_encode($itemData)]],
            [0 => SqlThread::MODE_INSERT],
            /** @param $results SqlInsertResult[] */
            function (array $results) use($author_xuid, $expiry, $itemData, $price): void {
                $id = $results[0]->getInsertId();

                $this->db->executeImplRaw(
                    [0 => "INSERT INTO auction (author, expires, item) VALUES (?, ?, ?)"],
                    [0 => [$author_xuid, $expiry, $id]],
                    [0 => SqlThread::MODE_INSERT],
                    function (array $results_) use($id, $author_xuid, $expiry, $itemData, $price){
                        $results_[0]->getInsertId();
                        $auction = new Auction($id, $results_[0]->getInsertId(), $author_xuid, $expiry, $price, $itemData);
                        Main::getInstance()->getAuctionManager()->addAuction($auction);
                    },
                    null
                );
            },
            null
        );
    }

    public function createPlayer(Player $player): void {
        $this->db->executeImplRaw(
            [0 => "INSERT IGNORE INTO player (xuid) VALUES (?)"],
            [0 => [$player->getXuid()]],
            [0 => SqlThread::MODE_INSERT],
            function () {},
            null
        );
    }

    public function getPlayerMoney(string $xuid, Closure $callback): void {
        $this->db->executeImplRaw(
            [0 => "SELECT * FROM player WHERE xuid = ?"],
            [0 => [$xuid]],
            [0 => SqlThread::MODE_SELECT],
            $callback,
            null
        );
    }

    public function setPlayerMoney(string $xuid, int $money): void {
        $this->db->executeImplRaw(
            [0 => "UPDATE player SET money = ? WHERE xuid = ?"],
            [0 => [$money, $xuid]],
            [0 => SqlThread::MODE_CHANGE],
            function () {},
            null
        );
    }

    public function getItemById(int $itemId, Closure $callback): void {
        $this->db->executeImplRaw(
            [0 => "SELECT * FROM item WHERE _id = ?"],
            [0 => [$itemId]],
            [0 => SqlThread::MODE_SELECT],
            $callback,
            null
        );
    }

    public function removeAuction(int $auctionId): void {
        $this->db->executeImplRaw(
            [0 => "DELETE FROM auction WHERE _id = ?"],
            [0 => [$auctionId]],
            [0 => SqlThread::MODE_CHANGE],
            function () {},
            null
        );
    }

    public function removeItem(int $itemId): void {
        $this->db->executeImplRaw(
            [0 => "DELETE FROM item WHERE _id = ?"],
            [0 => [$itemId]],
            [0 => SqlThread::MODE_CHANGE],
            function () {},
            null
        );
    }

    public function getAuctions(Closure $closure): void {
        $this->db->executeImplRaw(
            [0 => "SELECT * FROM auction;"],
            [0 => []],
            [0 => SqlThread::MODE_SELECT],
            $closure,
            null
        );
    }
}