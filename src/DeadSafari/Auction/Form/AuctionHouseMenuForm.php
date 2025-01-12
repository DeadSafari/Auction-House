<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Form;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;

class AuctionHouseMenuForm {

    public static function send(Player $player): void {
        $form = new SimpleForm(
            function (Player $player, ?int $data) {
                switch ($data) {
                    case 0:
                        return;

                    case null:
                        return;
                }
            }
        );

        $form->setTitle(C::RED . "Auction House");

        $form->addButton(C::DARK_RED . "Browse auctions\n" . C::GOLD . "View auctions created by other players", -1, "");
        $form->addButton(C::DARK_RED . "Sell\n" . C::GOLD . "Create an auctions yourself. The item selected in your hand will be sold", -1, "");
        $form->addButton(C::DARK_RED . "View my auctions\n" . C::GOLD . "View and manage the auctions created by you.", -1, "");

        $player->sendForm($form);
    }
}