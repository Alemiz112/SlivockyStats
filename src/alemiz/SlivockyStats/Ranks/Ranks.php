<?php

namespace alemiz\SlivockyStats\Ranks;

use alemiz\SlivockyStats\SlivockyStats;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Ranks {
    private $plugin;

    public function __construct(SlivockyStats $plugin){
        $this->plugin = $plugin;
    }

    public function getRank(Player $player, int $function = 0, $xp = "new"){
        $data = new Config($this->plugin->getDataFolder()."/ranks.yml", Config::YAML);
        if ($xp === "new"){
            $xp = $this->plugin->provider->getXP($player);
        }
        switch ($function){
            case 0: //Returns RANK
                foreach ($data->get("Ranks") as $cate => $ranks){
                    $rank = explode(":", $ranks);

                    if ($xp >= $rank[2] && $xp < $rank[3]){
                        $prank = $rank[0];
                    }
                }
                if (!isset($prank)) $prank = "§7Guest";
                return $prank;

                break;
            case 1: //Return Ranks Nametag
                foreach ($data->get("Ranks") as $cate => $ranks){
                    $rank = explode(":", $ranks);

                    if ($xp >= $rank[2] && $xp < $rank[3]){
                        $nametag = $rank[1];
                    }
                }
                if (!isset($nametag)) $nametag = "§fGuest";
                return $nametag;

                break;
        }
    }

    public function neededEx(Player $player){
        $data = new Config($this->plugin->getDataFolder()."/ranks.yml", Config::YAML);

        $xp = $this->plugin->provider->getXP($player);

        foreach ($data->get("Ranks") as $cate => $ranks){
            $rank = explode(":", $ranks);

            if ($xp >= $rank[2] && $xp < $rank[3]){
                if (isset($rank[3])){
                    return $rank[3] - $xp;
                }else{
                    return "No data";
                }
            }
        }
    }
}