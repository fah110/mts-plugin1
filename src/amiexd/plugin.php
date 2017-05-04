<?php
namespace amiexd;

use pocketmine\IPlayer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\PrimedTNT;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecuter;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\network\protocol\SetEntityLinkPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\entity\DroppedItem;
use pocketmine\entity\Human;
use pocketmine\entity\Creature;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\ChestInventory;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest;
use pocketmine\level\sound\GhastSound;
use pocketmine\scheduler\CallbackTask;
//use amiexd\task\TimeCommand;
//use amiexd\task\SimpleMessagesTask;

use amiexd\cmd\ClearLaggCommand;
use amiexd\cmd\BuyEnchant;

//use amiexd\cmd\RestartMeCommand;
//use amiexd\task\AutoBroadcastTask;
//use amiexd\task\CheckMemoryTask;
//use amiexd\task\RestartServerTask;

use amiexd\task\ParticlesPlus;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\event\player\PlayerUseFishingRodEvent;
use pocketmine\scheduler\PluginTask;

use amiexd\UpdaterEvent;

class plugin extends PluginBase implements Listener{
    protected $exemptedEntities = [];
	 public $drops = array();
	 private $function_a1, $timer, $target, $EconomyS, $Kill, $killrate;
    private $webEndings = array(".net",".com",".leet.cc",".ddns.net","op",".tk"); 

    public $ammo = [];
    private $task;
    public $data;
    public $economy = false;
    public $EconomyAPI;
	//private $config;

	
	private static $object = null;
	
	public static function getInstance(){
		return self::$object;
	}
	
	public function onLoad(){
		if(!self::$object instanceof meowPlugin){
			self::$object = $this;
		}
		$this->data = $this->getDataFolder();
	}
	 
      //.    meow Plugin 
      //.    ผู้สร้าง: MeowZaaaa
      //.    ---เป็นปลั่กรวมๆ---
      //.    ไม่ได้ใช้โปรแกรมเขียนPhpทั้งนั้น
	 public function onEnable(){
       $this->config = $this->getConfig();
         //@mkdir($this->getDataFolder());
		//@mkdir($this->getDataFolder() . "data/");
        if(!file_exists($this->getDataFolder() . "config.yml")) {
            @mkdir($this->getDataFolder());
            @mkdir($this->getDataFolder() . "data/");
             file_put_contents($this->getDataFolder() . "config.yml",$this->getResource("config.yml"));
        }
        if($this->config->get("Economy-Plugin") == "Economy") {
            if(is_dir($this->getServer()->getPluginPath()."EconomyAPI")) {
		$this->getLogger()->info(TextFormat::GREEN."KillerEarnMoney v1.0.0 Enabled with Economy!");
		$this->economy = true;
            }else{
		$this->getLogger()->info(TextFormat::RED."KillerEarnMoney could not be loaded, I can't find the Economy plugin");
		$this->economy = false;
            }
        }
       elseif($this->config->get("Economy-Plugin") == "PocketMoney") {
            if(is_dir($this->getServer()->getPluginPath()."PocketMoney")) {
		$this->getLogger()->info(TextFormat::GREEN."KillerEarnMoney Enabled with PocketMoney!");
		$this->economy = true;
            }else{
		$this->getLogger()->info(TextFormat::RED."KillerEarnMoney could not be loaded, I can't find the PocketMoney plugin");
		$this->economy = false;
            }
        }
       //ตัวรับปลั้กอิน
       $this->EconomyAPI = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		 //$this->saveFiles();
		 $this->reloadConfig();
		 $this->dropitemworld = $this->getConfig()->get("dropitemworld"); 
		 $this->saveDefaultConfig();
              $c = $this->getConfig()->getAll();
              $t = $c["Interval"] * 1200;
              $num = 0;
              foreach ($c["Items"] as $i) {
              $r = explode(":",$i);
      $this->itemdata[$num] = array("id" => $r[0],"meta" => $r[1],"amount" => $r[2]);
      $num++;
    }
       /***register&command***/
      $this->registerAll();
		 $this->getServer()->getPluginManager()->registerEvents($this, $this);
              /****Task****/
              //$this->getServer()->getScheduler()->scheduleRepeatingTask(new ParticlesPlus($this), 10);
              $this->getServer()->getScheduler()->scheduleRepeatingTask(new Gift($this),$t);

               $this->task = new UpdaterEvent($this);
       /*****จบการเปิดปลั้กอิน******/
	}
	
		
	 private function registerAll(){
		/***commandmap***/
		 $this->getServer()->getCommandMap()->register("mc-meow", new ClearLaggCommand($this));
             $this->getServer()->getCommandMap()->register("buyenchant", new BuyEnchant($this));
		/***tasks***/
	}
  /* public function gui1(){
		 foreach($this->getServer()->getOnlinePlayers() as $p){
                $tps = $this->getServer()->getTicksPerSecond();
			 $pName = $p->getPlayer()->getName();
			 $pMoney = $this->EconomyS->mymoney($pName);
			 $pOnline = count(Server::getInstance()->getOnlinePlayers());
			 $pFull = Server::getInstance()->getMaxPlayers();
			 $score = $this->killrate->getScore($pName);                                                                                                  
                $p->sendTip("                                                §bMts§a-§cSurvival§f:§aTPS§f[$tps] \n                                                        §eyou: $pName\n                                                         §eplayers: $pOnline\n                                                         §ekills: $score\n                                                         §emoney: $pMoney\n                                                 §a------------------------"  ) ;
		}
	}*/
	 public function onDisable(){
	}
	
    public function BuyEnchant(){
       $money = $this->EconomyAPI->myMoney($sender);
       $item = $sender->getInventory()->getItemInHand();     
       $enchantment = Enchantment::getEnchantment(mt_rand(0, 24))->setLevel((int)rand(1,3));
       $amount = $this->config->get("amount");;
    }
   //สุ่มไอเทมเวลา
public function give($p,$data) {
    if($p instanceof Player && ($p->hasPermission("mts") || $p->hasPermission("mts.timeitem"))) {
      $item = new Item($data["id"],$data["meta"],$data["amount"]);
      $p->getInventory()->addItem($item);
    }
  }

  public function giveAll() {
    $data = $this->generateData();
    $this->broadcast("§f[§dServer§f] §dยินดีด้วยค่ะ!! คุณได้รับไอเทมออนไลน์!!");
    foreach($this->getServer()->getOnlinePlayers() as $p) {
      $this->give($p,$data);
    }
  }

  public function broadcast($m) {
    foreach($this->getServer()->getOnlinePlayers() as $p) {
      $p->sendMessage($m);
    }
    $this->getLogger()->info(TextFormat::YELLOW . $m);
  }

  public function generateData() {
    return $this->itemdata[mt_rand(0,(count($this->itemdata) - 1))];
  }

 public function getKills($player){
		$data = new Config($this->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		//Check data
		if($data->exists("kills") && $data->exists("deaths")){
			return $data->get("kills");
		}else{
			$data->setAll(array("kills" => 0, "deaths" => 0));
			$data->save();
		}
	}
	
	public function getDeaths($player){
		$data = new Config($this->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
		//Check data
		if($data->exists("kills") && $data->exists("deaths")){
			return $data->get("deaths");
		}else{
			$data->setAll(array("kills" => 0, "deaths" => 0));
			$data->save();
		}
	}
	
	/*** Event ***/
       //AlwaysSpawn
      public function onPlayerLogin(PlayerLoginEvent $event) {
			$player = $event->getPlayer();
			$x = $this->getServer()->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->getServer()->getDefaultLevel()->getSafeSpawn()-> getY();
			$z = $this->getServer()->getDefaultLevel()->getSafeSpawn()->getZ();
			$level = $this->getServer()->getDefaultLevel();
			$player->setLevel($level);
			$player->teleport(new Vector3($x, $y, $z, $level));
		}

     //KeepIvntroy&Killchat
	public function onPlayerDeath(PlayerDeathEvent $event){
         $victim = $event->getEntity();
         $player = $event->getEntity();
		$this->drops[$player->getName()][1] = $player->getInventory()->getArmorContents();
		$this->drops[$player->getName()][0] = $player->getInventory()->getContents();
		$event->setDrops(array());
		$player->teleport($player->getLevel()->getSpawn());
          //KillMoneyอาจจะผิดพลาดนิดหน่อย
           $cause = $event->getEntity()->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent) {
            $player = $event->getEntity();
            $killer = $event->getEntity()->getLastDamageCause()->getDamager();
            if($killer instanceof Player) {
                $imessage = str_replace("@coins", $this->config->get("Money"), $this->config->get("Message"));
                $message = str_replace("@player", $player->getName(), $imessage);
                if($this->config->get("Economy-Plugin") == "Economy") {
                    $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->addMoney($killer->getName(), $this->config->get("Money"));
                    $killer->sendMessage($message);
                }
                
            }

          elseif($this->config->get("Economy-Plugin") == "PocketMoney") {
                    $this->getServer()->getPluginManager()->getPlugin("PocketMoney")->grantMoney($killer->getName(), $this->config->get("Money"));
                    $killer->sendMessage($message);
                }
        }
    
     //ด
		if($victim instanceof Player){
			$vdata = new Config($this->getDataFolder() . "data/" . strtolower($victim->getName()) . ".yml", Config::YAML);
			//Check victim data
			if($vdata->exists("kills") && $vdata->exists("deaths")){
				$vdata->set("deaths", $vdata->get("deaths") + 1);
				$vdata->save();
			}else{
				$vdata->setAll(array("kills" => 0, "deaths" => 1)); //Add first death
				$vdata->save();
			}
			$cause = $event->getEntity()->getLastDamageCause()->getCause();
			if($cause == 1){ //Killer is an entity
				//Get Killer Entity
				$killer = $event->getEntity()->getLastDamageCause()->getDamager();
				//Get if the killer is a player
				if($killer instanceof Player){
					//Get killer data
					$kdata = new Config($this->getDataFolder() . "data/" . strtolower($killer->getName()) . ".yml", Config::YAML);
					//Check killer data
					if($kdata->exists("kills") && $kdata->exists("deaths")){
						$kdata->set("kills", $kdata->get("kills") + 1);
						$kdata->save();
					}else{
						$kdata->setAll(array("kills" => 1, "deaths" => 0)); //Add first kill
						$kdata->save();
					}
				}
			}
		}
    }
	
	public function PlayerRespawn(PlayerRespawnEvent $event){
      $this->ammo[$event->getPlayer()->getName()] = 30;
        $player = $event->getPlayer();
		if (isset($this->drops[$player->getName()])) {
			$player->getInventory()->setContents($this->drops[$player->getName()][0]);
			$player->getInventory()->setArmorContents($this->drops[$player->getName()][1]);
			unset($this->drops[$player->getName()]);
		}
    }
	 public function onDrop(PlayerDropItemEvent $event){
		 $player = $event->getPlayer();
		 if(!$player->isOp()){
			 if(in_array($player->getLevel()->getName(), $this->dropitemworld)){ 
				 $player->sendMessage("§c[Error] §fคุณไม่สามารถทิ้งไอเทมบนโลกนี้ได้ค่ะ");
			   $event->setCancelled();
			}
		}
	}
	

	 public function removeEntities(){
		 $i = 0;
		 foreach($this->getServer()->getLevels() as $level){
			 foreach($level->getEntities() as $entity){
				 if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)){
					 $entity->close();
             $i++;
					}
			}
		}
		 return $i;
	}
	
	 public function removeMobs(){
		 $i = 0;
		 foreach($this->getServer()->getLevels() as $level){
			 foreach($level->getEntities() as $entity){
				 if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)){
					 $entity->close();
					 $i++;
					}
			}
		}
		 return $i;
	}
	 public function getEntityCount(){
		 $ret = [0, 0, 0];
		 foreach($this->getServer()->getLevels() as $level){
			 foreach($level->getEntities() as $entity){
				 if($entity instanceof Human){
					 $ret[0]++;
					} else if($entity instanceof Creature){
					 $ret[1]++;
					} else {
					 $ret[2]++;
					}
			}
		}
		 return $ret;
	}
	 public function exemptEntity(Entity $entity){
		 $this->exemptedEntities[$entity->getID()] = $entity;
	}
	 public function isEntityExempted(Entity $entity){
		 return isset($this->exemptedEntities[$entity->getID()]);
	}

 public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $message = $event->getMessage();
        $playername = $event->getPlayer()->getDisplayName();
        $parts = explode('.', $message);
        if(sizeof($parts) >= 4){
            if (preg_match('/[0-9]+/', $parts[1])){
                $event->setCancelled(true);
                $player->kick("§dระวังโดนแบน!");
                echo "[Advertising]: Kicked " . $playername . " For saying: ". $message . " \n";
            }
        }
        foreach ($this->webEndings as $url){
            if (strpos($message, $url) !== FALSE){
                $event->setCancelled(true);
                $player->kick("§dระวังโดนแบน!");
                echo "[Advertising]: Kicked " . $playername . " For saying: ". $message . " \n";
            }
        }
    }
   public function handlePlayerInteractWithChest(PlayerInteractEvent $event) {
		$player = $event->getPlayer ();

		if ($event->getBlock ()->getId () == Item::TRAPPED_CHEST) {
			if ($player->getInventory ()->getItemInHand ()->getId () == Item::FEATHER) {
				$player->sendMessage ( "§f[§bChest§cBox§f] §aได้ทำการเปิดกล่องสุ่ม" );				
			} else {
				$player->sendMessage ( "§f[§bChest§cBox§f]§e ต้องใช้ขนนกในการเปิด\n§f[§bChest§cBox§f]§e ขนนกหาได้จากการออนไลน์ครบ20นาที:D" );
				$event->setCancelled ( true );
			}
		}
	}		
	
	public function onPlayerInteractEvent(PlayerInteractEvent $ev){
		$action = $ev->getAction();
		$isFeather = $ev->getItem()->getId()==288;
		$isChest = $ev->getBlock()->getId()==146;
		if($action==1&&$isFeather&&$isChest){
			$chest = $ev->getPlayer()->getLevel()->getTile(new Vector3($ev->getBlock()->getX(),$ev->getBlock()->getY(),$ev->getBlock()->getZ()));
			if($chest instanceof Chest){
				$this->task->chest = $chest;
		        $chest->setName("§aกล่องสุ่มไอเทม");
				$this->task->player = $ev->getPlayer();
				$this->task->t_delay = 3*20;
				$this->task->canTakeItem = false;
				$ciih = $ev->getPlayer()->getItemInHand();
				$ciih->setCount($ciih->getCount()-1);
				$ciih->setDamage($ciih->getDamage());
				$ev->getPlayer()->getInventory()->setItemInHand($ciih);
				$this->getServer()->getScheduler()->scheduleRepeatingTask($this->task,3);
			}
		}
	}
	
	public function onInventoryTransactionEvent(InventoryTransactionEvent $ev){
	    if($this->task!=NULL&&$this->task->canTakeItem){
	        $ev->setCancelled(false);
	    }else{
	        $ev->setCancelled(true);
	    }
	}
	
	public function onInventoryPickupItemEvent(InventoryPickupItemEvent $ev){
	    if($this->task!=NULL&&$this->task->canTakeItem){
	        $ev->setCancelled(false);
	    }else{
	        $ev->setCancelled(true);
	    }
	}
	
	public function onPlayerKickEvent(PlayerKickEvent $ev){
	    if($this->task->chest!=NULL){
	        foreach(array(9,10,11,12,13,14) as $slots){
	             $this->task->chest->getInventory()->setItem($slots,Item::get(0));
	        }
	    }
	}
	
	
	public function onPlayerQuitEvent(PlayerQuitEvent $ev){
       if(isset($this->riding[$ev->getPlayer()->getName()])){
      unset($this->riding[$ev->getPlayer()->getName()]);
         }
	    if($this->task->chest!=NULL){
	        foreach(array(9,10,11,12,13,14) as $slots){
	             $this->task->chest->getInventory()->setItem($slots,Item::get(0));
	        }
	    }
   }
   public function onMove(PlayerMoveEvent $event)
	
	{
		$player = $event->getPlayer();
 		$from = $event->getFrom();
 		$to = $event->getTo();
 		if($from->getLevel()->getBlockIdAt($from->x, $from->y - 1, $from->z) === Block::REDSTONE_BLOCK)
		
			{
				$player->setMotion((new Vector3($to->x - $from->x, $to->y - $from->y, $to->z - $from->z))->multiply(5)); /* 5 is the power, You can change it if you want */
				$player->getLevel()->addSound(new GhastSound(new Vector3($player->getX(), $player->getY(), $player->getZ())));
				$player->sendTip("§l§b<***BOOSTS***>");
			}
 	}
     
    function ontap(PlayerUseFishingRodEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if($this->ammo[$name] > 0){
			$nbt = new CompoundTag("", [
				"Pos" => new ListTag("Pos", [
					new DoubleTag("", $player->x),
					new DoubleTag("", $player->y + $player->getEyeHeight()),
					new DoubleTag("", $player->z)
					]),
				"Motion" => new ListTag("Motion", [
					new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI) * 2),
					new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
					new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI) * 2)
					]),
				"Rotation" => new ListTag("Rotation", [
					new FloatTag("", $player->yaw),
					new FloatTag("", $player->pitch)
					]),
				]);
			$entity =  Entity::createEntity(80, $player->getLevel(), $nbt, $player);
			$entity->setMotion($entity->getMotion()->multiply(2));
			$entity->spawnToAll();
			--$this->ammo[$name];
		}elseif($this->ammo[$name] == 0){
			$this->getServer()->getScheduler()->scheduleDelayedTask(new Task($this, $player), 20*5);
			$player->sendTip("§l§eกำลังรีโหลด...");
			--$this->ammo[$name];
		}else{
			$player->sendTip("§l§aยังใช้งานไม่ได้!!");
		}
		$event->setCancelled();
	}
}
class Task extends PluginTask{
	public function __construct(PluginBase $owner, Player $player){
		parent::__construct($owner);
		$this->owner = $owner;
		$this->player = $player;
	}
	public function onRun($currentTick){
		$this->owner->ammo[$this->player->getName()] = 30;
		$this->player->sendTip("§l&aรีโหลด..เสร็จสิน");
	}
}
