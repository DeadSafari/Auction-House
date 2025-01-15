<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Commands;

use CortexPE\Commando\BaseCommand;
use DeadSafari\Auction\Main;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class BlackAuctionCommand extends BaseCommand {

    public function prepare(): void {
        $this->setPermission("bah.use");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $auction = Main::getInstance()->getBlackAuctionManager()->getBlackAuction();
        if ($auction === null) {
            $sender->sendMessage(TextFormat::RED . 'There are currently no running Black Auctions. The next auction will be in {}{} seconds');
            return;
        }

        $sender->sendMessage(TextFormat::RED . "You have been entered into a Black Auction. You may place your bids");
        $auction->add($sender);
    }
}