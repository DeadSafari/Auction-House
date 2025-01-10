<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Database;

use DeadSafari\Auction\Main;
use poggit\libasynql\base\DataConnectorImpl;
use poggit\libasynql\libasynql;
use poggit\libasynql\SqlThread;

class DatabaseManager {

    private DataConnectorImpl $db;

    public function __construct() {
        $this->db = libasynql::create(Main::getInstance(), Main::getInstance()->getConfig()->get("database"), ["mysql" => "mysql.sql"]);
        $this->initTables();
    }

    private function initTables(): void {
        $this->db->executeImplRaw(
            [0 => "CREATE TABLE IF NOT EXISTS auction (_id INT AUTO_INCREMENT NOT NULL, author VARCHAR(255) NOT NULL, expires INT(11), item INT NOT NULL)",
            1 => "CREATE TABLE IF NOT EXISTS item (_id INT AUTO_INCREMENT NOT NULL, data JSON NOT NULL)"],
            [0 => [], 1 => []],
            [0 => SqlThread::MODE_GENERIC, 1 => SqlThread::MODE_GENERIC],
            function () {},
            null
        );
    }
}