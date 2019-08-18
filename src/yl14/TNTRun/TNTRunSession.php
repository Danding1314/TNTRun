<?php

declare(strict_types=1);

namespace yl14\TNTRun;

use pocketmine\Player;

class TNTRunSession {

    private $sessionid = 0;
    private $plugin;
    
    private $status = 0;

    private $players = [];
    private $spectators = [];
    private $position = [];
    private $settings = [];

    public function __construct(TNTRun $plugin, int $sessionid, array $position, array $settings) {
        $this->plugin = $plugin;
        $this->sessionid = $sessionid;
        $this->position = $position;
        $this->settings = $settings;
    }

    public function getSessionId() : int {
        return $this->sessionid;
    }

    public function getWaitPosition() : array {
        return $this->position['wait'];
    }

    public function getPlayPosition() : array {
        return $this->position['play'];
    }

    public function getMinPlayer() : int {
        return $this->settings['minplayer'];
    }

    public function getMaxPlayer() : int {
        return $this->settings['maxplayer'];
    }

    public function getWaitTime() : int {
        return $this->settings['waittime'];
    }

    public function getGameTime() : int {
        return $this->settings['gametime'];
    }

    public function getPlayer(Player $player) : ?Player {
        return $this->players[$player->getName()] ?? null;
    }

    public function getSpectator(Player $player) : ?Player {
        return $this->spectators[$player->getName()] ?? null;
    }

    public function getPlayers() : array {
        return $this->players;
    }

    public function getSpectators() : array {
        return $this->spectators;
    }

    public function getStatus() : int {
        return $this->status;
    }

    public function addPlayer(Player $player) : bool {
        if(!isset($this->players[$player->getName()])) {
            $this->players[$player->getName()] = $player;
            $this->plugin->getServer()->getPluginManager()->callEvent(new \yl14\TNTRun\event\TNTRunSessionModifyEvent($this->plugin, $this));
            return true;
        }
        return false;
    }

    public function removePlayer(Player $player) : bool {
        if(isset($this->players[$player->getName()])) {
            unset($this->players[$player->getName()]);
            $this->plugin->getServer()->getPluginManager()->callEvent(new \yl14\TNTRun\event\TNTRunSessionModifyEvent($this->plugin, $this));
            return true;
        }
        return false;
    }

    public function addSpectator(Player $player) : bool {
        if(!isset($this->spectators[$player->getName()])) {
            $this->spectators[$player->getName()] = $player;
            $this->plugin->getServer()->getPluginManager()->callEvent(new \yl14\TNTRun\event\TNTRunSessionModifyEvent($this->plugin, $this));
            return true;
        }
        return false;
    }

    public function removeSpectator(Player $player) : bool {
        if(isset($this->spectators[$player->getName()])) {
            unset($this->spectators[$player->getName()]);
            $this->plugin->getServer()->getPluginManager()->callEvent(new \yl14\TNTRun\event\TNTRunSessionModifyEvent($this->plugin, $this));
            return true;
        }
        return false;
    }

    public function setStatus(int $status) {
        $this->status = $status;
        $this->plugin->getServer()->getPluginManager()->callEvent(new \yl14\TNTRun\event\TNTRunSessionModifyEvent($this->plugin, $this));
    }
}