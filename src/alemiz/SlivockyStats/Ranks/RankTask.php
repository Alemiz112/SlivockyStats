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
                        case "Owner":
                            $player->setNameTag("§7[§cOwner§7] §r{$player->getName()}");
                            break;
                        case "Builder":
                            $player->setNameTag("§7[§eBuilder§7] §r{$player->getName()}");
                            break;
                        case "Admin":
                            $player->setNameTag("§7[§3Admin§7] §r{$player->getName()}");
                            break;
                        case "Helper":
                            $player->setNameTag("§7[§9Helper§7] §r{$player->getName()}");
                            break;
                        case "Youtuber":
                            $player->setNameTag("§7[§4Y§fT§7] §r{$player->getName()}");
                            break;
                        case "VIP":
                            $player->setNameTag("§7[§e§lVIP§7] §r{$player->getName()}");
                            break;
                        case "EpicVIP":
                            $player->setNameTag("§7[§dEpicVIP§7] §r{$player->getName()}");
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