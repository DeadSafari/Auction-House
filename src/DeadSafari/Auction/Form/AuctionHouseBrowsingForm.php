<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Form;

use DeadSafari\Auction\Auction\Auction;
use DeadSafari\Auction\Main;
use jojoe77777\FormAPI\SimpleForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\TextFormat;

class AuctionHouseBrowsingForm {

    public static function send(Player $player): void {

        $auctions = Main::getInstance()->getAuctionManager()->getAuctions();

        $form = new SimpleForm(
            function (Player $player, ?Auction $auction): void {
                if ($auction === null) {
                    return;
                }

                $menu = InvMenu::create(InvMenu::TYPE_CHEST);

                $menu->getInventory()->setItem(13, $auction->getItem());

                $yes = VanillaBlocks::CONCRETE()->setColor(DyeColor::GREEN());
                $yes = $yes->asItem();
                $yes->setCustomName(TextFormat::GREEN . "Confirm Purchase");
                $tag = new CompoundTag;
                $tag->setByte("yes", 1);
                $yes->setCustomBlockData($tag);

                $back = VanillaBlocks::BED()->setColor(DyeColor::RED());
                $back = $back->asItem();
                $back->setCustomName(TextFormat::RED . "Return to auction");

                $no = VanillaBlocks::CONCRETE()->setColor(DyeColor::RED());
                $no = $no->asItem();
                $tag = new CompoundTag;
                $tag->setByte("yes", 0);
                $no->setCustomBlockData($tag);
                $no->setCustomName(TextFormat::RED . "Decline Purchase");
                $menu->getInventory()->setItem(21, $yes);
                $menu->getInventory()->setItem(22, $back);
                $menu->getInventory()->setItem(23, $no);

                $menu->setListener(function (InvMenuTransaction $transaction) use($player, $auction) {
                    if ($transaction->getItemClicked()->getCustomBlockData()->getByte("yes") === 1) {
                        $player->removeCurrentWindow();
                        $session = Main::getInstance()->getSessionManager()->getSession($player);
                        if ($session->getMoney() < ($auction->getPrice())) {
                            $player->sendMessage(TextFormat::RED . "You do not have enough money to buy this!");
                            return $transaction->discard();
                        }

                        $session->setMoney($session->getMoney() - $auction->getPrice());

                        $claimMenu = InvMenu::create(InvMenu::TYPE_CHEST);
                        $claimMenu->getInventory()->setItem(13, $auction->getItem());
                        $claimMenu->send($player);
                        Main::getInstance()->getAuctionManager()->removeAuction($auction);
                        return $transaction->discard();

                    }
                    elseif ($transaction->getItemClicked()->getCustomBlockData()->getByte("yes") === 0) {
                        $player->removeCurrentWindow();
                    }
                    if ($transaction->getItemClicked()->getTypeId() === VanillaBlocks::BED()->asItem()->getTypeId()) {
                        $player->removeCurrentWindow();
                    }
                    return $transaction->discard();
                });

                $menu->send($player, null);
            }
        );

        foreach ($auctions as $auction) {
            $form->addButton($auction->getItem()->getName() . TextFormat::RESET . "\n" . TextFormat::DARK_PURPLE . "Price: " . TextFormat::GOLD . strval($auction->getPrice()), -1, "", $auction);
        }

        $form->setTitle(C::RED . "Auction House");

        $player->sendForm($form);
    }
}