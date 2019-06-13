<?php
/**
* Created by PhpStorm.
 * User: Alik
* Date: 27/03/2019
* Time: 17:53
*/
namespace alemiz\SlivockyStats\Texts;

use alemiz\SlivockyStats\SlivockyStats;
use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\Command;

use pocketmine\scheduler\Task as PluginTask;


class About{
    public $cfg;

    public $plugin;

    public function __construct(SlivockyStats $plugin){
        $this->plugin = $plugin;
    }

    public function aboutCreate(){
        $data = $this->plugin->cfg->get("AboutText");

        $x = $data["x"];
        $y = $data["y"];
        $z = $data["z"];
        $world = $data["level"];

        $this->plugin->textParticles[$world][] = new FloatingTextParticle(new Vector3($x, $y,  $z), $data["text"]);
    }
}