<?php
/**
 * Created by PhpStorm.
 * User: Alik
 * Date: 25/03/2019
 * Time: 17:29
 */

namespace alemiz\SlivockyStats\Minigames;

use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\utils\Config;

use pocketmine\scheduler\Task as PluginTask;

class HungerGames extends PluginTask{

    private $plugin;

    /** Basic Text */
    public $kills;
    public $wons;

    public function __construct($plugin){

        $this->plugin = $plugin;
        $kills = $plugin->cfg->get("HungerGames")["kills"];
        $wons = $plugin->cfg->get("HungerGames")["wons"];

        $kx = $kills["x"];
        $ky = $kills["y"];
        $kz = $kills["z"];

        $wx = $wons["x"];
        $wy = $wons["y"];
        $wz = $wons["z"];

        $this->kills = new FloatingTextParticle(new Vector3($kx, $ky,  $kz), "", "§e < §c§lHunger§4Games: §e> ");

        $this->wons = new FloatingTextParticle(new Vector3($wx, $wy,  $wz), "", "§e < §c§lHunger§4Games: §e> ");
    }

    public function onRun($tick){
        //Kills
        $this->kills();
        //Wons
        $this->wons();
    }

    public function kills(){
        $data = $this->plugin->getServer()->getPluginManager()->getPlugin('HungerGames')->getData(0);
        arsort($data);

        $world = $this->plugin->cfg->get("HungerGames")["level"];

        $this->kills->setText("§aName:        §eKills:");
        $pos = 1;

        foreach ($data as $name => $kills){
            if ($pos === 11) break;

            $text = $this->kills->getText();

            $this->kills->setText($text."\n§6".$pos." §a" .$name. " §7- §e". $kills);
            $pos++;
        }
        $level = $this->plugin->getServer()->getLevelByName($world);
        if ($level) $level->addParticle($this->kills);
    }

    public function wons(){
        $data = $this->plugin->getServer()->getPluginManager()->getPlugin('HungerGames')->getData(1);
        arsort($data);

        $world = $this->plugin->cfg->get("HungerGames")["level"];

        $this->wons->setText("§aName:        §eWons:");
        $pos = 1;

        foreach ($data as $name => $wons){
            if ($pos === 11) break;

            $text = $this->wons->getText();

            $this->wons->setText($text."\n§6".$pos." §a" .$name. " §7- §e". $wons);
            $pos++;
        }
        $level = $this->plugin->getServer()->getLevelByName($world);
        if ($level) $level->addParticle($this->wons);
    }
}