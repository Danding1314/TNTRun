<?php

namespace yl14\TNTRun;

/**
 * 游乐14制作
 * 一切为了滑稽岛
 * @8/12/2019
 */

use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
    TextFormat as TF, Config
};
use pocketmine\command\{
    CommandSender, Command
};

use yl13\GameCoreAPI\GameCoreAPI;

class TNTRun extends PluginBase {

    /** * @var string*/
    private $gcid;

    /** *@var TNTRun*/
    private static $instance;

    /** *@var Array*/
    private $Sessions = [];

    public function onEnable() : void{
        $this->gcid = GameCoreAPI::getInstance()->api->getGameCoreAPI()->registerGame("TNT跑酷", "游乐14");
        $this->getLogger()->notice(TF::GREEN . "TNT跑酷插件已启动！作者:游乐14");
        $this->getLogger()->notice(TF::YELLOW . "当前版本: v1.0.0_TEST");
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onDisable() : void{
        $this->getLogger()->warning("TNT跑酷已关闭！");
    }

    static public function getInstance() :TNTRun {
        return self::$instance;
    }

    public function findSession(Array $filter = []) : ?int {
        if(isset($filter['map'])) {

        }
        //TODO
    }

    private function getSession(int $sessionid) : TNTRunSession {
        //TODO
    }

    private function createSession()
}