<?php
/**
 * Created by PhpStorm.
 * User: Alik
 * Date: 25/03/2019
 * Time: 15:10
 */

namespace alemiz\SlivockyStats;

use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\utils\Config;

use pocketmine\scheduler\Task as PluginTask;

class FloatingText extends PluginTask{

    /** Basic Text */
    public $text;

    private $plugin;

    public $data;

    public function __construct($plugin){

        $this->plugin = $plugin;

        $spawn = new Vector3(140, 100, 140);
        $this->text = new FloatingTextParticle($spawn, "", "§c§lHunger§4Games:");
    }

    public function onRun($tick){
        $this->data = new Config($this->plugin->getDataFolder() . "kills.yml", Config::YAML);
        $kills = $this->data->getAll();
        arsort($kills);

        $this->text->setText("§aName:        §eKills:");
        $pos = 1;

        foreach ($kills as $name => $kills){
            if ($pos === 11) break;

            $text = $this->text->getText();

            $this->text->setText($text."\n§6".$pos." §a" .$name. "    §e". $kills."  ");
            $pos++;
        }
        $level = $this->plugin->getServer()->getLevelByName("world");
        if ($level) $level->addParticle($this->text);
    }
}