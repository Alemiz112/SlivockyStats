<?php
/**
* Created by PhpStorm.
 * User: Alik
* Date: 27/03/2019
* Time: 17:53
*/
namespace alemiz\SlivockyStats\Texts;

use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\Command;

use pocketmine\scheduler\Task as PluginTask;


class About extends PluginTask{
    public $cfg;

    public $plugin;

    public $text;

    public function __construct($plugin){
        $this->plugin = $plugin;

        $data = $plugin->cfg->get("AboutText");

        $x = $data["x"];
        $y = $data["y"];
        $z = $data["z"];

        $this->text = new FloatingTextParticle(new Vector3($x, $y,  $z), "", $data["title"]);
    }

    public function onRun($tick){
        $this->aboutCreate();
    }

    public function aboutCreate(){
        $data = $this->plugin->cfg->get("AboutText");
        $world = $data["level"];

        $this->text->setText($data["text"]);

        $level = $this->plugin->getServer()->getLevelByName($world);
        if ($level) $level->addParticle($this->text);
    }
}