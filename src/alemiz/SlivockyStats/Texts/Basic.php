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


class Basic extends PluginTask{
    public $cfg;

    public $plugin;

    public $text1;
    public $text2;

    public function __construct($plugin){
        $this->plugin = $plugin;

        $data = $plugin->cfg->get("BasicTexts");

        $x = $data["x1"];
        $y = $data["y1"];
        $z = $data["z1"];

        $x2 = $data["x2"];
        $y2 = $data["y2"];
        $z2 = $data["z2"];

        //Text 1
        $this->text1 = new FloatingTextParticle(new Vector3($x, $y,  $z), "", $data["title1"]);

        //Text 2
        $this->text2 = new FloatingTextParticle(new Vector3($x2, $y2,  $z2), "", $data["title2"]);
    }

    public function onRun($tick){
        $this->basicCreate();
    }

    public function basicCreate(){
        $data = $this->plugin->cfg->get("BasicTexts");
        $world = $data["level"];

        //Text 1
        $this->text1->setText($data["text1"]);

        $level = $this->plugin->getServer()->getLevelByName($world);
        if ($level) $level->addParticle($this->text1);

        //Text 2
        $this->text2->setText($data["text2"]);

        $level = $this->plugin->getServer()->getLevelByName($world);
        if ($level) $level->addParticle($this->text2);
    }
}