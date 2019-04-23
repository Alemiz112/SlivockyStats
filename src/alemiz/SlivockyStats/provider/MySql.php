<?php
namespace alemiz\SlivockyStats\provider;

use pocketmine\Player;

use onebone\economyapi\EconomyAPI;

class MySql {
    private $plugin;

    public $conn;

    public function __construct($plugin){
        $this->plugin = $plugin;
    }

    public function connect(){
        $host = $this->plugin->cfg->get("host");
        $dbname = $this->plugin->cfg->get("db");
        $username = $this->plugin->cfg->get("user");
        $password = $this->plugin->cfg->get("password");

        //if ($this->plugin->cfg->get("MySql") === "true"){
            $this->conn = new \mysqli($host, $username, $password, $dbname);

            if ($this->conn->connect_error) {
                $this->getLogger()->critical("Cant connect to MYSQL");
            }

            if(!$this->conn->query("CREATE TABLE IF NOT EXISTS user_xp(
			username VARCHAR(20) PRIMARY KEY,
			xp FLOAT
			);")){
                $this->plugin->getLogger()->critical("Error creating table: " . $this->conn->error);
                return;
            }

        $this->plugin->getScheduler()->scheduleRepeatingTask(new MySQLPing($this, $this->conn), 600);
    }

    /**
     * Database Connection restart after fail
     */
    public function reconnect(){
            $this->connect();
    }

    /**
     * @param $player
     * @return bool
     */
    public function accountExists($player){

        $player = strtolower($player);
        $result = $this->conn->query("SELECT * FROM user_xp WHERE username='".$this->conn->real_escape_string($player)."'");
        return $result->num_rows > 0 ? true:false;
    }

    /**
     * @param $player
     * @return bool
     */
    public function createAccount($player){

        $player = strtolower($player);
        if(!$this->accountExists($player)){
            $this->conn->query("INSERT INTO user_xp (username, xp) VALUES ('".$this->conn->real_escape_string($player)."', 0);");
            return true;
        }
        return false;
    }

    /**
     * @param $player
     * @return bool
     */
    public function getXP($player){
        $player = $player->getName();

        $player = strtolower($player);
        $res = $this->conn->query("SELECT xp FROM user_xp WHERE username='".$this->conn->real_escape_string($player)."'");
        $ret = $res->fetch_array()[0] ?? false;
        $res->free();
        return $ret;
    }

    /**
     * @param $player
     * @param $xp
     * @return mixed
     */
    public function addXP($player, $xp){

        $oldXP = $this->getXP($player);
        $p = $player;

        $player = $player->getName();

        $player = strtolower($player);
        $amount = (float) $xp;
        $this->conn->query("UPDATE user_xp SET xp = xp + $amount WHERE username='".$this->conn->real_escape_string($player)."'");

        $newXP = $oldXP + $xp;
    }

    /**
     * @param $player
     * @param int $xp
     */
    public function killXP($player,int $xp){
        $player = $player->getName();

        $player = strtolower($player);
        $amount = (float) $xp;
        $this->conn->query("UPDATE user_xp SET xp = xp - $amount WHERE username='".$this->conn->real_escape_string($player)."'");
    }

    /**
     * @param Player $player
     * @param int $xp
     */
    public function changeXP(Player $player,int $xp){
        $currency = $xp * $this->plugin->cfg->get("Currency"); //Set Currency of XP to $$

        $money = $this->plugin->getServer()->getPluginManager()->getPlugin("EconomyAPI");

        if ($this->getXP($player) >= $xp){
            if ($money !== null){
                $this->killXP($player, $xp);
                EconomyAPI::getInstance()->addMoney($player, $currency);
                $player->sendMessage("§bTransfer was sucesfull!");
            }
        }else{
            $player->sendMessage("§cYou dont Have enough XP!");
        }

    }

}