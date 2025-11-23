<?php

declare(strict_types=1);

namespace bagchaser\home\api;

use SQLite3;

use pocketmine\Server;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use pocketmine\world\Position;

use bagchaser\home\Core;

class Home {
    use SingletonTrait;

    protected SQLite3 $sql;

    public function __construct(){
        $this->sql = new SQLite3(Core::getInstance()->getDataFolder() . "homes.db");
        $this->sql->exec("
            CREATE TABLE IF NOT EXISTS homes (
                player TEXT,
                name TEXT,
                x REAL,
                y REAL,
                z REAL,
                world TEXT,
                PRIMARY KEY (player, name)
            );
        ");
    }

    public function getHome(Player $player, string $name) : ?array{
        $stmt = $this->sql->prepare("SELECT * FROM homes WHERE player = :player AND name = :name;");
        $stmt->bindValue(":player", $player->getName());
        $stmt->bindValue(":name", $name);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return $result !== false ? $result : null;
    }

    public function getHomes(Player $player) : array{
        $homes = [];
        $stmt = $this->sql->prepare("SELECT * FROM homes WHERE player = :player;");
        $stmt->bindValue(":player", $player->getName());
        $result = $stmt->execute();
        while(($row = $result->fetchArray(SQLITE3_ASSOC)) !== false){
            $homes[] = $row;
        }
        return $homes;
    }

    public function getHomesHack($player) : ?array{
        $player = $player instanceof Player ? $player->getName() : $player;
        $stmt = $this->sql->prepare("SELECT * FROM homes WHERE player = :player;");
        $stmt->bindValue(":player", $player);
        $homes = [];
        $result = $stmt->execute();
        while(($row = $result->fetchArray(SQLITE3_ASSOC)) !== false){
            $homes[] = $row;
        }
        return $homes;
    }

    public function setHome(Player $player, string $name) : void{
        $pos = $player->getPosition();
        $world = $player->getWorld()->getFolderName();
        $stmt = $this->sql->prepare("
            INSERT OR REPLACE INTO homes (player, name, x, y, z, world)
            VALUES (:player, :name, :x, :y, :z, :world);
        ");
        $stmt->bindValue(":player", $player->getName());
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":x", $pos->getX());
        $stmt->bindValue(":y", $pos->getY());
        $stmt->bindValue(":z", $pos->getZ());
        $stmt->bindValue(":world", $world);
        $stmt->execute();
    }

    public function deleteHome(Player $player, string $name) : void{
        $stmt = $this->sql->prepare("DELETE FROM homes WHERE player = :player AND name = :name;");
        $stmt->bindValue(":player", $player->getName());
        $stmt->bindValue(":name", $name);
        $stmt->execute();
    }

    public function teleportToHome(Player $player, string $name) : void{
        $home = $this->getHome($player, $name);
        if($home === null){
            return;
        }
        $worldName = $home["world"];
        $wm = Server::getInstance()->getWorldManager();
        if(!$wm->isWorldLoaded($worldName)){
            $wm->loadWorld($worldName);
        }
        $world = $wm->getWorldByName($worldName);
        if($world === null){
            return;
        }
        $player->teleport(new Position((float)$home["x"], (float)$home["y"], (float)$home["z"], $world));
    }

    public function close() : void{
        $this->sql->close();
    }
}