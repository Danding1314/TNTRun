<?php

declare(strict_types=1);

namespace yl14\TNTRun;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
    TextFormat as TF, Config
};
use pocketmine\command\{
    Command, CommandSender
};
use pocketmine\level\Position;
use pocketmine\Player;

use yl13\GameCoreAPI\GameCoreAPI;

class TNTRun extends PluginBase {

    private $gcid = 0;

    private $Sessions = [];
    private $onset = [];

    private static $instance;

    public function onEnable() {
        $this->getLogger()->notice(TF::YELLOW . 'TNTRun初始化中...');
        $this->gcid = GameCoreAPI::getInstance()->api->gamecore->registerGame("TNTRun", "游乐14");
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder() . 'rooms')) {
            @mkdir($this->getDataFolder() . 'rooms');
        }
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->notice(TF::GREEN . 'TNTRun启动!');
    }

    public function onLoad() {
        self::$instance = $this;
    }

    public function onDisable() {
        $this->getLogger()->warning("TNTRun已关闭");
    }

    static public function getInstance() : TNTRun {
        return self::$instance;
    }

    public function SearchSession(Player $player, array $filter = []) : bool{
        //filter not implement yet
        foreach($this->Sessions as $Session) {
            if($Session instanceof TNTRunSession) {
                $join = $this->joinSession($player, $Session->getSessionId());
                if($join) {
                    return true;
                }
                continue;
            }
            continue;
        }
        //啥都没有欸，那接下来需要创建一个新的房间
    }

    private function joinSession(Player $player, int $sessionid) : bool{
        $Session = $this->getSession($sessionid);
        if($Session instanceof TNTRunSession) {
            if($Session->getStatus() == 0 or $Session->getStatus() == 1) {
                if(!(count($Session->getPlayers()) + 1) > $Session->getMaxPlayer()) {
                    //ok没问题了
                    $Session->addPlayer($player);
                    $waitposition = $Session->getWaitPosition();
                    $player->teleport(new Position($waitposition['x'], $waitposition['y'], $waitposition['z'], $this->getServer()->getLevelByName($waitposition['level'])));
                    $player->setImmobile();
                    GameCoreAPI::getInstance()->api->getChatChannelAPI()->addPlayer($this->gcid, (string)$sessionid, array($player));
                    GameCoreAPI::getInstance()->api->getChatChannelAPI()->broadcastMessage($this->gcid, (string)$sessionid, $player->getName() . TF::GREEN . "加入了房间！");
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    public function getSession(int $sessionid) : ?TNTRunSession{
        return $this->Sessions[$sessionid] ?? null;
    }

    public function saveSession(int $sessionid, TNTRunSession $Session) {
        if(isset($this->Sessions[$sessionid])) {
            $this->Sessions[$sessionid] = $Session;
        }
    }

    private function createSession(int $sessionid, array $position, array $settings) : bool{
        if(!isset($this->Sessions[$sessionid])) {
            $this->Sessions[$sessionid] = new TNTRunSession($this, $sessionid, $position, $settings);
            GameCoreAPI::getInstance()->api->getChatChannelAPI()->create($this->gcid, (string)$sessionid);
            $this->debug("Created Session with id" . $sessionid);
            return true;
        }
        return false;
    }

    private function debug($message) {
        $this->getLogger()->notice(TF::GRAY . "[DEBUG]" . TF::RESET . $message);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $name = $cmd->getName();
        if($name == "tntr") {
            if(!$sender instanceof \pocketmine\Player) {
                $sender->sendMessage(TF::RED . "只允许玩家使用！");
                return false;
            }
            if(!isset($args[0])) {
                $sender->chat("/tntr help");
                return true;
            }
            switch($args[0]) {

                default:
                    $sender->chat("/tntr help");
                    return true;
                break;

                case 'help':
                    $msg = [
                        '=====TNTRun帮助=====',
                        '/tntr help TNTRun的帮助界面',
                        '/tntr c/create <房间名> 创建新的TNTRun的房间配置文件',
                        '/tntr r/remove <房间名> 移除TNTRun的房间配置文件',
                        '/tntr r/reload <房间名> 重新加载TNTRun的房间配置文件'
                    ];
                    $sender->sendMessage(implode("\n", $msg));
                    return true;
                break;

                case 'c':
                case 'create':
                    if(!isset($args[1])) {
                        $sender->sendMessage(TF::RED . "指令输入错误！");
                        $sender->chat("/tntr help");
                        return false;
                    }
                    if(isset($this->onset[$sender->getName()])) {
                        $sender->sendMessage(TF::GREEN . "已停止创建" . TF::WHITE . $this->onset[$sender->getName()]['name']);
                        return true;
                    }
                    if(is_file($this->getDataFolder() . 'rooms/' . $args[1] . '.yml')) {
                        $sender->sendMessage(TF::RED . "输入的名字和已存在的配置文件重名，请换一个名字！");
                        return false;
                    }
                    $this->onset[$sender->getName()] = array(
                        'name' => $args[1],
                        'position' => array(
                            'wait' => array(
                                'x' => $sender->x,
                                'y' => $sender->y,
                                'z' => $sender->z,
                                'level' => $sender->getLevel()->getFolderName()
                            ),
                            'play' => array(
                                'x' => $sender->x,
                                'y' => $sender->y,
                                'z' => $sender->z,
                                'level' => $sender->getLevel()->getFolderName()
                            )
                        ),
                        'settings' => array(
                            'waittime' => 10,
                            'gametime' => 300,
                            'minplayer' => 2,
                            'maxplayer' => 20
                        )
                    );
                    $sender->sendMessage("开始设置" . $args[1] . ",设置等待地点输入/tntr w, 设置游玩地点输入/tntr p, 一切设置完成后,输入/tntr f完成配置");
                    return true;
                break;

                case 'r':
                case 'remove':
                    if(is_file($this->getDataFolder() . 'rooms/' . $args[1] . '.yml')) {
                        $delete = unlink($this->getDataFolder() . 'rooms/' . $args[1] . '.yml');
                        if(!$delete) {
                            $sender->sendMessage(TF::RED . "无法删除文件，pm是否有足够的权限？");
                            return true;
                        }
                        $sender->sendMessage(TF::GREEN . "删除房间配置文件" . TF::WHITE . $args[1] . TF::GREEN . "成功");
                        return true;
                    }
                    $sender->sendMessage(TF::RED . "无法找到房间配置文件" . TF::WHITE . $args[1] . TF::RED . ",你真的有创建它吗？");
                    return true;
                break;

                case 'r':
                case 'reload':
                    if(is_file($this->getDataFolder() . 'rooms/' . $args[1] . '.yml')) {
                        $config = new Config($this->getDataFolder() . 'rooms/' . $args[1] . '.yml');
                        $config->reload();
                        $sender->sendMessage(TF::GREEN . "房间配置文件" . TF::WHITE . $args[1] . TF::GREEN . "重载成功！");
                        return true;
                    }
                    $sender->sendMessage(TF::RED . "无法找到房间配置文件" . TF::WHITE . $args[1] . TF::RED . ",你真的有创建它吗？");
                    return true;
                break;

                case 'w':
                   if(isset($this->onset[$sender->getName()])) {
                        $this->onset[$sender->getName()]['position']['wait']['x'] = $sender->x;
                        $this->onset[$sender->getName()]['position']['wait']['y'] = $sender->y;
                        $this->onset[$sender->getName()]['position']['wait']['z'] = $sender->z;
                        $this->onset[$sender->getName()]['position']['wait']['level'] = $sender->getLevel()->getFolderName();
                        $sender->sendMessage("设置等待地点:\nx:" . $sender->x . "\ny:" . $sender->y . "\nz:" . $sender->z . "\nlevel:" . $sender->getLevel()->getFolderName());
                        return true;
                   }
                   return false;
                break;

                case 'p':
                    if(isset($this->onset[$sender->getName()])) {
                        $this->onset[$sender->getName()]['position']['play']['x'] = $sender->x;
                        $this->onset[$sender->getName()]['position']['play']['y'] = $sender->y;
                        $this->onset[$sender->getName()]['position']['play']['z'] = $sender->z;
                        $this->onset[$sender->getName()]['position']['play']['level'] = $sender->getLevel()->getFolderName();
                        $sender->sendMessage("设置游玩地点:\nx:" . $sender->x . "\ny:" . $sender->y . "\nz:" . $sender->z . "\nlevel:" . $sender->getLevel()->getFolderName());
                        return true;
                    }
                    return false;
                break;

                case 'f':
                    if(isset($this->onset[$sender->getName()])) {
                        $name = $this->onset[$sender->getName()]['name'];
                        $config = new Config($this->getDataFolder() . 'rooms/' . $name . '.yml');
                        $config->setAll($this->onset[$sender->getName()]);
                        $config->save();
                        unset($this->onset[$sender->getName()]);
                        $sender->sendMessage(TF::GREEN . "已完成" . TF::WHITE. $name . TF::GREEN . "的设置，依然有更多设置可以进行哦，比如等待时间啥的，可以进入配置文件里面调整后再/tntr reload你的文件");
                        return true;
                    }
            }
        }
    }
}