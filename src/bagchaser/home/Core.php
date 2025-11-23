<?php

declare(strict_types=1);

namespace bagchaser\home;

use pocketmine\plugin\PluginBase;

use bagchaser\home\api\Home;

use bagchaser\home\command\HomeCommand;
use bagchaser\home\command\HomeListCommand;
use bagchaser\home\command\DeleteHomeCommand;
use bagchaser\home\command\SetHomeCommand;
use bagchaser\home\command\console\ViewHomesCommand;

class Core extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getCommandMap()->registerAll("Home", [
            new HomeCommand($this, "home", "Teleport to your home", "Usage: /home <name>"),
            new HomeListCommand($this, "homelist", "List of you homes", null, ["homes"]),
            new DeleteHomeCommand($this, "delhome", "Delete your home", "Usage: /delhome <name>", ["deletehome", "removehome"]),
            new SetHomeCommand($this, "sethome", "Set your home", "Usage: /sethome <name>")
        ]);

        $this->getserver()->getCommandMap()->register("Home_Console", new ViewHomesCommand($this, "viewhomes", "View homes of a player", "Usage: /viewhomes <player>", ["vhomes"]));
    }

    protected function onDisable() : void{
        Home::getInstance()->close();
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}