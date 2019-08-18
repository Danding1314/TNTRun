<?php

declare(strict_types=1);

namespace yl14\TNTRun;

use pocketmine\event\Listener;

class EventListener implements Listener {

    private $plugin;

    public function __construct(TNTRun $plugin) {
        $this->plugin = $plugin;
    }

    
}