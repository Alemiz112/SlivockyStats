<?php

namespace alemiz\SlivockyStats;


use alemiz\SlivockyStats\provider\MySQL;
use alemiz\SlivockyStats\Ranks\RankListener;
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

    /** @var null | array */
    public $textParticles;
    
    public function onEnable(){
        @mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig();
		$this->saveResource("ranks.yml");

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new RankListener($this), $this);

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
    //Begin of LISTENER

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();

        //Floating Text particles
        $this->addParticles($player);
    }
    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event){
        $targetLevel = $event->getTarget();

        if (is_null($this->textParticles)) return;

        foreach ($this->textParticles as $level => $particles){
            /** @var FloatingTextParticle $particle */
            foreach ($particles as $particle){
                if ($targetLevel->getFolderName() == $level){
                    $particle->setInvisible(false);
                    $targetLevel->addParticle($particle, [$event->getEntity()]);
                    echo "IN";
                }else{
                    $particle->setInvisible(true);
                    $lev = $event->getOrigin();
                    $lev->addParticle($particle, [$event->getEntity()]);
                    echo "Out";
                }
            }
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
            $about->aboutCreate();
        }
        if ($data = $this->cfg->get("BasicTexts")["enable"] === "true") {
            $basic->basicCreate();
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

    /**
     * @param Player $player
     */
    public function addParticles(Player $player){
        if (is_null($this->textParticles)) return;

        foreach ($this->textParticles as $level){
            foreach ($level as $particle){
                if (!$particle instanceof FloatingTextParticle) continue;

                foreach ($particle->encode() as $packet){
                    $particle->setInvisible(false);
                    $player->dataPacket($packet);
                }
            }
        }
    }

}