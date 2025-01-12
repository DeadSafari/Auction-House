<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Commands;

use CortexPE\Commando\BaseCommand;
use DeadSafari\Auction\Commands\Subcommands\AuctionHouseSellCommand;
use DeadSafari\Auction\Form\AuctionHouseMenuForm;
use DeadSafari\Auction\Main;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AuctionHouseCommand extends BaseCommand {

    public function prepare(): void {
        $this->setPermission("ah.use");
        $this->registerSubCommand(new AuctionHouseSellCommand(Main::getInstance(), "sell", "Sell an item", []));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        AuctionHouseMenuForm::send($sender);
    }
}