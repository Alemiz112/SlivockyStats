<?php
namespace alemiz\SlivockyStats\Ranks;

use alemiz\SlivockyStats\SlivockyStats;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class RankListener implements Listener{

    /** @var SlivockyStats  */
    protected $plugin;

    public function __construct(SlivockyStats $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name= $player->getName();

        if ($this->plugin->cfg->get("MySql") == "true"){
            if(!$this->plugin->provider->accountExists($name)){
                $this->plugin->getLogger()->debug("Rank for '".$name."' is not found. Creating account...");
                $this->plugin->provider->createAccount($name);
            }

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

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if ($this->plugin->cfg->get("MySql") == "true"){
            $player->setNameTag($player->getName());
        }
    }

    public function onChat(PlayerChatEvent $event){
        if ($this->plugin->cfg->get("MySql") == "true") {
            $message = $event->getMessage();
            $player = $event->getPlayer();

            switch ($this->plugin->getPlayerGroup($player)){
                case "Owner":
                    $event->setFormat("§7[§cOwner§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Builder":
                    $event->setFormat("§7[§eBuilder§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Admin":
                    $event->setFormat("§7[§3Admin§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Helper":
                    $event->setFormat("§7[§9Helper§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "Youtuber":
                    $event->setFormat("§7[§4Y§fT§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "VIP":
                    $event->setFormat("§7[§e§lVIP§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                case "EpicVIP":
                    $event->setFormat("§7[§dEpicVIP§7] §r§b{$player->getName()} §e>§r {$message}");
                    break;
                default:
                    $nameTag = $this->plugin->getRank()->getRank($player,1);
                    $event->setFormat("{$nameTag} §r§b{$player->getName()} §e>§r {$message}");
                    break;
            }
        }
    }
}
