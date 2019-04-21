<?php

namespace alemiz\SlivockyStats\Ranks;

use pocketmine\utils\TextFormat;

class Ranks {
    private $plugin;

    public function __construct($plugin){
        $this->plugin = $plugin;
    }

    public function rankUP($player, $oldXP, $newXP){
        $name = $player->getName();
        $rank = $this->checkRank($player, $oldXP);

        $rank1 = $this->plugin->cfg->get("Ranks")["Rank1"];
        $rank2 = $this->plugin->cfg->get("Ranks")["Rank2"];
        $rank3 = $this->plugin->cfg->get("Ranks")["Rank3"];
        $rank4 = $this->plugin->cfg->get("Ranks")["Rank4"];
        $rank5 = $this->plugin->cfg->get("Ranks")["Rank5"];
        $rank6 = $this->plugin->cfg->get("Ranks")["Rank6"];


        if ($oldXP == 0){
            $player->addTitle("§aRank UP!", "#{$rank1} §d".$newXP."XP");
        }
        if ($rank == "rank1" && $newXP > 50){
            $player->addTitle("§aRank UP!", "#{$rank2} §d".$newXP."XP");
        }
        if ($rank == "rank2" && $newXP > 100){
            $player->addTitle("§aRank UP!", "#{$rank3} §d".$newXP."XP");
        }
        if ($rank == "rank3" && $newXP > 200){
            $player->addTitle("§aRank UP!", "#{$rank4} §d".$newXP."XP");
        }
        if ($rank == "rank4" && $newXP > 350){
            $player->addTitle("§aRank UP!", "#{$rank5} §d".$newXP."XP");
        }
        if ($rank == "rank5" && $newXP > 600){
            $player->addTitle("§aRank UP!", "#{$rank6} §d".$newXP."XP");
        }
    }

    public function checkRank($player, $xp){
        //$xp = $this->plugin->provider->getXP($player);

        if ($xp > 0 && $xp <= 50) return "rank1";
        if ($xp > 50 && $xp <= 100) return "rank2";
        if ($xp > 100 && $xp <= 200) return "rank3";
        if ($xp > 200 && $xp <= 350) return "rank4";
        if ($xp > 350 && $xp <= 600) return "rank5";
        if ($xp > 600) return "rank6";

    }
}