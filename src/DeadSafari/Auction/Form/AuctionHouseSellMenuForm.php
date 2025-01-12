<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Form;

use DeadSafari\Auction\Main;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AuctionHouseSellMenuForm {

    public static function send(Player $player): void {
        $item = $player->getInventory()->getItemInHand();
        if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
            $player->sendMessage(TextFormat::RED . "You you have to hold the item which you are trying to sell!");
            return;
        }
        $priceForm = new CustomForm(
            function (Player $player, array $data) use ($item){
                if ($data === null) {
                    return;
                }

                $price = $data[1];

                $priceInt = intval($price);
                if ($priceInt < 1) {
                    $player->sendMessage(TextFormat::RED . "Price cannot be less than 1!");
                    return;
                }
                $session = Main::getInstance()->getSessionManager()->getSession($player);
                Main::getInstance()->getAuctionManager()->sell($item, $priceInt, $session);
                $player->sendMessage(TextFormat::GREEN . "Your item is now successfully on the auction");
            }
        );
        $priceForm->setTitle(TextFormat::RED . "Selling Price");
        $priceForm->addLabel("You are selling " . TextFormat::RESET . TextFormat::RED . $item->getName() . TextFormat::RESET . " on the Auction House. Please decide a price for it below.");
        $priceForm->addInput("Price", "10, 20, 500, 1000, etc");
        $player->sendForm($priceForm);
    }
}