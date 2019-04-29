<?php

namespace alemiz\SlivockyStats;


use alemiz\SlivockyStats\provider\MySQL;
use alemiz\SlivockyStats\Ranks\Ranks;
use alemiz\SlivockyStats\Ranks\RankTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandExecutor;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;

use pocketmine\scheduler\Task as PluginTask;

use alemiz\SlivockyStats\Texts;
use alemiz\SlivockyStats\Minigames;

use _64FF00\PurePerms\PurePerms;


class SlivockyStats extends PluginBase implements Listener{
    /**
     * @var Config
     */
    public $cfg;

    public $text;
    /**
     * @var MySql
     */
    public $provider;
    public $rank;
    
    public function onEnable(){
        @mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig();
		$this->saveResource("ranks.yml");

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if ($this->cfg->get("HungerGames")["enable"] == "true"){
            $this->getScheduler()->scheduleRepeatingTask(new Minigames\HungerGames($this), 4 * 20);
        }
        if ($this->cfg->get("BuildUHC")["enable"] == "true"){
            $this->getScheduler()->scheduleRepeatingTask(new Minigames\BuildUHC($this), 4 * 20);
        }
        if ($this->cfg->get("SkyWars")["enable"] == "true"){
            $this->getScheduler()->scheduleRepeatingTask(new Minigames\SkyWars($this), 4 * 20);
        }

        $this->showTexts($this->cfg->get("MainInterval"));

        if ($this->cfg->get("MySql") == "true"){
            $this->provider = new provider\MySql($this);
            $this->provider->connect();
            $this->getScheduler()->scheduleRepeatingTask(new RankTask($this), 1.5 * 20);
        }
    }

    /**
     * @return Ranks|string
     */
    public function getRank(){
        if ($this->cfg->get("MySql") == "true"){
            return new Ranks($this);
        }else return "Please Enable MYSQL";
    }

    /**
     * @param int $interval
     */
    public function showTexts($interval){
        /* Define Texts locations - not USED */
        $about = new Texts\About($this);
        $basic = new Texts\Basic($this);

        /* Launch Texts */

        if ($data = $this->cfg->get("AboutText")["enable"] === "true") {
            $this->getScheduler()->scheduleRepeatingTask(new Texts\About($this), $interval * 20);
        }
        if ($data = $this->cfg->get("BasicTexts")["enable"] === "true") {
            $this->getScheduler()->scheduleRepeatingTask(new Texts\Basic($this), $interval * 20);
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name= $player->getName();

        if ($this->cfg->get("MySql") == "true"){
            if(!$this->provider->accountExists($name)){
                $this->getLogger()->debug("Rank for '".$name."' is not found. Creating account...");
                $this->provider->createAccount($name);
            }

            switch ($this->getPlayerGroup($player)){
                case "Owner":
                    $player->setNameTag("§7[§cOwner§7] §r{$player->getName()}");
                    break;
                case "Builder":
                    $player->setNameTag("§7[§eBuilder§7] §r{$player->getName()}");
                    break;
                case "Admin":
                    $player->setNameTag("§7[§3Admin§7] §r{$player->getName()}");
                    break;
                case "Helper":
                    $player->setNameTag("§7[§9Helper§7] §r{$player->getName()}");
                    break;
                case "Youtuber":
                    $player->setNameTag("§7[§4Y§fT§7] §r{$player->getName()}");
                    break;
                case "VIP":
                    $player->setNameTag("§7[§e§lVIP§7] §r{$player->getName()}");
                    break;
                case "EpicVIP":
                    $player->setNameTag("§7[§dEpicVIP§7] §r{$player->getName()}");
                    break;
                default:
                    $nameTag = $this->getRank()->getRank($player,1);
                    $player->setNameTag("{$nameTag} §r{$player->getName()}");
                    break;
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if ($this->cfg->get("MySql") == "true"){
            $player->setNameTag($player->getName());
        }
    }

    public function onChat(PlayerChatEvent $event){
        if ($this->cfg->get("MySql") == "true") {
            $message = $event->getMessage();
            $player = $event->getPlayer();

            switch ($this->getPlayerGroup($player)){
                case "Owner":
                    $event->setFormat("§7[§cOwner§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Builder":
                    $event->setFormat("§7[§eBuilder§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Admin":
                    $event->setFormat("§7[§3Admin§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Helper":
                    $event->setFormat("§7[§9Helper§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Youtuber":
                    $event->setFormat("§7[§4Y§fT§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "VIP":
                    $event->setFormat("§7[§e§lVIP§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "EpicVIP":
                    $event->setFormat("§7[§dEpicVIP§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                default:
                    $nameTag = $this->getRank()->getRank($player,1);
                    $event->setFormat("{$nameTag} §r§b{$player->getName()} §e>§r {$message}");
                    break;
            }
        }
    }

    public function getPlayerGroup(Player $player): string{
        $purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        if($purePerms instanceof PurePerms){
            $group = $purePerms->getUserDataMgr()->getData($player)['group'];
            if($group !== null){
                return $group;
            }else{
                return "No Rank";
            }
        }else{
            return "Soon...";
        }
    }

}