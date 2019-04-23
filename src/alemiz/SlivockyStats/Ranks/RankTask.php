<?php
namespace alemiz\SlivockyStats\Ranks;

use alemiz\SlivockyStats\SlivockyStats;
use pocketmine\scheduler\Task as PluginTask;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

use pocketmine\utils\Config;

class RankTask extends PluginTask{

    private $plugin;

    public $ranks = [];

    public function __construct(SlivockyStats $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $Tick){
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
            $rank = $this->plugin->getRank()->getRank($player);
            $nameTag = $this->plugin->getRank()->getRank($player,1);

            if (isset($this->ranks[$player->getName()])){
                if ($rank != $this->ranks[$player->getName()]){
                    $player->addTitle("§aRankUP!", "§f{$this->ranks[$player->getName()]} -> {$rank}");
                    $player->getLevel()->broadcastLevelSoundEvent($player->asVector3(), LevelSoundEventPacket::SOUND_TWINKLE);

                    switch ($this->plugin->getPlayerGroup($player)){
                        case "VIP":
                            $player->setNameTag("§e§lVIP §r{$player->getName()}");
                            break;
                        case "EpicVIP":
                            $player->setNameTag("§dEpicVIP §r{$player->getName()}");
                            break;
                        default:
                            $nameTag = $this->plugin->getRank()->getRank($player,1);
                            $player->setNameTag("{$nameTag} §r{$player->getName()}");
                            break;
                    }
                }
            }
            $this->ranks[$player->getName()] = $rank;
        }
    }
}