<?php

declare(strict_types=1);

namespace yl14\TNTRun\event;

use pocketmine\event\{
    plugin\PluginEvent, Cancellable
};

class TNTRunSessionModifyEvent extends PluginEvent implements Cancellable{
    
    public static $handlerList = [];

    public function __construct(\yl14\TNTRun\TNTRun $plugin, \yl14\TNTRun\TNTRunSession $Session) {
        $this->saveSession($plugin, $Session);
    }

    private function saveSession(\yl14\TNTRun\TNTRun $plugin, \yl14\TNTRun\TNTRunSession $Session) {
        $plugin->saveSession($Session->getSessionId(), $Session);
    }
}