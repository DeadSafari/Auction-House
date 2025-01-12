<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;

class AuctionHouseBrowsingForm {

    public static function send(Player $player): void {
        $form = new SimpleForm(
            function (Player $player, ?int $data): void {
                return;
            }
        );

        $form->setTitle(C::RED . "Auction House");
    }
}