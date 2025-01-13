<?php

declare(strict_types=1);

namespace DeadSafari\Auction\Auction;

use pocketmine\scheduler\Task;

class AuctionExpiredTask extends Task {

    private Auction $auction;

    public function __construct(Auction $auction) {
        $this->auction = $auction;
    }

    public function onRun(): void {


        return;
    }
}