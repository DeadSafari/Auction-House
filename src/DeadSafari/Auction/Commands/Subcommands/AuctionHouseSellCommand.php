<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Commands\Subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use DeadSafari\Auction\Main;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AuctionHouseSellCommand extends BaseSubCommand {

    public function prepare(): void {
        $this->setPermission("ah.sell.use");
        $this->registerArgument(0, new IntegerArgument("Price", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        if ($sender->getInventory()->getItemInHand()->getTypeId() === VanillaItems::AIR()->getTypeId()) {
            $sender->sendMessage(TextFormat::RED . "You you have to hold the item which you are trying to sell!");
            return;
        }

        $session = Main::getInstance()->getSessionManager()->getSession($sender);
        Main::getInstance()->getAuctionManager()->sell($sender->getInventory()->getItemInHand(), $args["Price"], $session);
    }
}