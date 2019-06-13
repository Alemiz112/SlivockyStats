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


class Basic{
    public $plugin;

    public function __construct(SlivockyStats $plugin){
        $this->plugin = $plugin;
    }

    public function basicCreate(){
        $data = $this->plugin->cfg->get("BasicTexts");

        $x = $data["x1"];
        $y = $data["y1"];
        $z = $data["z1"];

        $x2 = $data["x2"];
        $y2 = $data["y2"];
        $z2 = $data["z2"];
        $world = $data["level"];

        $this->plugin->textParticles[$world][] = new FloatingTextParticle(new Vector3($x, $y,  $z), $data["text1"], $data["title1"]);
        $this->plugin->textParticles[$world][] = new FloatingTextParticle(new Vector3($x2, $y2,  $z2), $data["text2"], $data["title2"]);
    }
}