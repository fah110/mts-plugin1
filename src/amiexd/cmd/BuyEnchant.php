<?php

namespace amiexd\cmd;

use pocketmine\event\TranslationContainer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\utils\Config; 
use pocketmine\item\enchantment\Enchantment;

use amiexd\plugin;
//use onebone\economyapi\EconomyAPI;

class BuyEnchant extends Command{

  public $plugin;

  public function __construct(plugin $plugin) {
    parent::__construct("buyenchant", "สุ่มเอ็นซาน", "/buyenchant <help>", ["be"]);
    $this->setPermission("mts.command.buyenchant");
    $this->plugin = $plugin;
  }

  public function getPlugin() {
    return $this->plugin;
  }

 public function execute(CommandSender $sender, $label, array $params){
    $money = $this->plugin->EconomyAPI->myMoney($sender);
$item = $sender->getInventory()->getItemInHand();$enchantment = Enchantment::getEnchantment(mt_rand(0, 24))->setLevel((int)rand(1,3));
$amount = $this->plugin->config->get("amount");;
		if($money >= $amount)
		{
			$this->plugin->EconomyAPI->reduceMoney($sender, $amount);
			$sender->sendMessage("§aทำการสุ่มเอ็นชาตไอเท็มสำเร็จ !!");
			$item->addEnchantment($enchantment);
            $sender->getInventory()->setItemInHand($item);
		}else{
			$sender->sendMessage("§cจำนวนเงินของคุณไม่พอ !");
          }
      }
   
}
