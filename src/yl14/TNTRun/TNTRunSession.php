<?php

namespace yl14\TNTRun;

use pocketmine\Player;

class TNTRunSession {

    /** *@var int*/
    private $sessionid = 0;

    /** *@var pocketmine\Player[]*/
    private $players = [];

    /** *@var Array*/
    private $settings = [];

    public function __construct(int $sessionid, array $settings) {
        $this->sessionid = $sessionid;
        $this->settings = $settings;
    }

    public function getSessionId() : int {
        return $this->sessionid;
    }

    
}