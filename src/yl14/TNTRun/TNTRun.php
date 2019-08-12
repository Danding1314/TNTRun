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

use yl13\GameCoreAPI\GameCoreAPI;

class TNTRun extends PluginBase {

    private $gcid;

    private static $instance;

    public function onEnable() {
        $this->gcid = GameCoreAPI::getInstance()->api->getGameCoreAPI()->registerGame("TNT跑酷", "游乐14");
        $this->getLogger()->notice(TF::GREEN . "TNT跑酷插件已启动！作者:游乐14");
        $this->getLogger()->notice(TF::YELLOW . "当前版本: v1.0.0_TEST");
    }

    public function onLoad() {
        self::$instance = $this;
    }
}