<?php

namespace amiexd\cmd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand; 
use amiexd\plugin;

class ClearLaggCommand extends Command implements PluginIdentifiableCommand {

  public $plugin;

  public function __construct(plugin $plugin) {
    parent::__construct("mc-meow", "help server mc-meow", "/mc-meow <help>", ["lag"]);
    $this->setPermission("mts.command.clearlag");
    $this->plugin = $plugin;
  }

  public function getPlugin() {
    return $this->plugin;
  }

  public function execute(CommandSender $sender, $alias, array $args) {
    if(!$this->testPermission($sender)) {
      return false;
    }
    if(isset($args[0])) {
      switch($args[0]) {
        case "clear":
          $sender->sendMessage("Removed " . $this->getPlugin()->removeEntities() . " entities.");
          return true;
        case "check":
          $c = $this->getPlugin()->getEntityCount();
          $sender->sendMessage("There are " . $c[0] . " players, " . $c[1] . " mobs, and " . $c[2] . " entities.");
          return true;
        case "help":
        $sender->sendMessage("§eCommand for Mc-Meow\n/mc-meow info\n/mc-meow check\n/mc-meow clearlag");
          return true;
        case "info":
          $sender->sendMessage("§f[§dMc-Meow§f]§e§lInfo\nเป็นปลั้กอินเสริมจาก §lMc-Meow\nจัดทำโดย MeowZaaaaa");
          return true;
        case "killmobs":
          $sender->sendMessage("Removed " . $this->getPlugin()->removeMobs() . " mobs.");
          return true;
        case "clearlag":
          $sender->sendMessage("§f[§dServer§f]§e ลบมอนเตอร์ไปทั้งหมด §f[" . ($d = $this->getPlugin()->removeMobs()) . " ]" . ($d == 1 ? "" : "s") . " §eลบไอเท็มที่ตกไปทั้งหมด §f[" . ($d = $this->getPlugin()->removeEntities()) . "] entity" . ($d == 1 ? "y" : "ies") . ".");
          return true;
        case "buyenchant":
          // TODO
          return true;
        default:
          return false;
      }
    }
    return false;
  }

}
