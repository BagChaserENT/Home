<?php

declare(strict_types=1);

namespace bagchaser\home\command\console;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\lang\Translatable;

use pocketmine\plugin\PluginOwned;

use bagchaser\home\Core;

use bagchaser\home\api\Home;

class ViewHomesCommand extends Command implements PluginOwned {

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
        $this->setPermission("home.cmd.console");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if($sender instanceof Player){
            $sender->sendMessage("This command can only be used in the console!");
            return;
        }

        if(!isset($args[0])){
            $sender->sendMessage($this->getUsage());
            return;
        }

        if(empty(Home::getInstance()->getHomesHack($args[0]))){
            $sender->sendMessage("This player doesn't have any homes or does not exist...");
            return;
        }

        $homes = Home::getInstance()->getHomesHack($args[0]);
        $homeCount = count($homes);
        $sender->sendMessage("Homes of " . $args[0] . " (". $homeCount . "):");
        foreach($homes as $home){
            $sender->sendMessage("- " . $home["name"]);
        }
    }

    public function getOwningPlugin() : Core{
        return $this->plugin;
    }
}