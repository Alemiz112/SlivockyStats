<?php
namespace alemiz\SlivockyStats\provider;

use pocketmine\scheduler\Task;

class MySQLPing extends Task{
    private $plugin;
    private $mysql;


    public function __construct(MySql $plugin, \mysqli $mysql){
        $this->plugin = $plugin;
        $this->mysql = $mysql;
    }
    public function onRun(int $currentTick){
        if(!$this->mysql->ping()){
            $this->plugin->reconnect();
        }
    }
}