<?php

declare(strict_types=1);

namespace bagchaser\home\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\lang\Translatable;

use pocketmine\plugin\PluginOwned;

use bagchaser\home\Core;

use bagchaser\home\api\Home;

class SetHomeCommand extends Command implements PluginOwned {

    protected Core $plugin;

    protected string $name;
    protected Translatable|string $description = "";
    protected Translatable|string $usageMessage;
    protected array $aliases = [];

    public function __construct(
        Core $plugin,
        string $name,
        Translatable|string $description = "",
        Translatable|string|null $usageMessage = null,
        array $aliases = []
    ){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("home.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage("You must be a player to use this command!");
            return;
        }

        if(!isset($args[0])){
            $sender->sendMessage($this->getUsage());
            return;
        }

        Home::getInstance()->setHome($sender, $args[0]);
        $sender->sendMessage("Home " . $args[0] . " has been set!");
    }

    public function getOwningPlugin() : Core{
        return $this->plugin;
    }
}