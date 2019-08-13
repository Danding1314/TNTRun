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

    /** *@var TNTRunSession[]*/
    private $Sessions = [];

    /** *@var Array*/
    private $onset = [];

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

    private function createSession() {}

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $name = $cmd->getName();
        if($name == "tntr") {
            if(!isset($args[0])) {
                if($sender instanceof \pocketmine\Player) {
                    $sender->chat("/tntr help");
                    return true;
                }
            }
            switch($args[0]) {

                default:
                    if($sender instanceof \pocketmine\Player) {
                        $sender->chat("/tntr help");
                        return true;
                    }
                    return false;
                break;

                case 'help':
                    $message = [
                        '=====TNT跑酷帮助=====',
                        '/tntr help 帮助界面',
                        '/tntr c/create <name> 创建新的游戏房间配置文件',
                        '/tntr r/remove <name> 移除游戏房间配置文件',
                        '/tntr reload <name> 重载游戏房间配置文件'
                    ];
                    $sender->sendMessage(implode("\n", $message));
                    return true;
                break;

                case 'c':
                case 'create':
                    if(!isset($args[1])) {
                        $sender->sendMessage(TF::RED . "输入错误，请检查你的指令");
                        return false;
                    }
                    if(!$sender instanceof \pocketmine\Player) {
                        $sender->sendMessage(TF::RED . "你不是玩家！");
                        return false;
                    }
                    if(isset($this->onset[$sender->getName()])) {
                        $sender->sendMessage(TF::GREEN . "已取消房间" . $this->onset[$sender->getName()]['name'] . "的设置");
                        unset($this->onset[$sender->getName()]);
                        return true;
                    }
                    $this->onset[$sender->getName()] = array(
                        'name' => $args[1],
                        'position' => array(
                            'play' => array(
                                'x' => 0,
                                'y' => 0,
                                'z' => 0,
                                'level' => 'world'
                            ),
                            'wait' => array(
                                'x' => 0,
                                'y' => 0,
                                'z' => 0,
                                'level' => 'world'
                            )
                        ),
                        'settings' => array(
                            'maxplayer' => 10,
                            'minplayer' => 2,
                            'gametime' => 300
                        )
                    );
                    $sender->sendMessage("你开始创建房间" . $args[1] . ",输入/tntr p可以设置游玩地点，/tntr w可以设置玩家等待地点，一切设置完后，输入/tntr f即可完成");
                    return true;
                break;

                case 'p':
                    if(!isset($this->onset[$sender->getName()])) {
                        return false;
                    }
                    $this->onset[$sender->getName()]['position']['x'] = $sender->x;
                    $this->onset[$sender->getName()]['position']['y'] = $sender->y;
                    $this->onset[$sender->getName()]['position']['z'] = $sender->z;
                    $this->onset[$sender->getName()]['position']['level'] = ;
            }
        }
    }
}