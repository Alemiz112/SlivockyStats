<?php

namespace alemiz\SlivockyStats;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
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

class SlivockyStats extends PluginBase implements Listener{
    
    public $cfg;

    public $text;
    
    public function onEnable(){
		$this->getLogger()->info("SlivockyStats has been enabled!");
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if ($this->cfg->get("HungerGames")["enable"] == "true"){
            $this->getScheduler()->scheduleRepeatingTask(new Minigames\HungerGames($this), 4 * 20);
        }
        if ($this->cfg->get("BuildUHC")["enable"] == "true"){
            $this->getScheduler()->scheduleRepeatingTask(new Minigames\BuildUHC($this), 4 * 20);
        }

        $this->showTexts($this->cfg->get("MainInterval"));
    }

    public function onDisable() {
        $this->getLogger()->info("SlivockyStats has been disabled!");
    }

    /**
     * @param int $interval
     */
    public function showTexts($interval){
        /* Define Texts locations */
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
}