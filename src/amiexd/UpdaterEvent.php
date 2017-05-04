<?php

namespace amiexd;

use pocketmine\scheduler\PluginTask;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\Level;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest;
use pocketmine\inventory\ChestInventory;

class UpdaterEvent extends PluginTask{
    public $canTakeItem = true;
	public $player;
	public $chest;
	public $t_delay = 3*20;
	public $ids = array(260,384,334,352,364,264,310,311,312,313,266,265,264,388,57,41,466,276,278);
	
	public function setItem($index,int $id,$count,$dmg = 0){
	    $item = Item::get($id);
	    $item->setCount($count);
	    if($id==260){
	        $item->setCount((int)rand(1,5));
	    }
	    if($id==384){
	        $item->setCount((int)rand(1,3));
	    }
	    if($id==334){
	        $item->setCount((int)rand(1,4));
	    }
	    if($id==352){
	        $item->setCount((int)rand(1,5));
	    }
	    if($id==388){
	        $item->setCount(1);
	    }
	    if($id==364){
	        $item->setCount((int)rand(1,4));
	    }
	    if((int)rand(1,25)==2){
	        $enchant = Enchantment::getEnchantment(12);
	        $enchant->setLevel(10);
	        $item->addEnchantment($enchant);
	    }
	    if($id==276||$id==310||$id==311||$id==312||$id==313||$id==278||$id==57||$id==41){
	        $item->setCount(1);
	        if($id==278&&(int)rand(1,3)==1){
	            $ef = Enchantment::getEnchantment(15);
	            $ef->setLevel((int)rand(1,2));
	            $item->addEnchantment($ef);
	            if((int)rand(1,2)==3){
	               $dura = Enchantment::getEnchantment(17);
	               $dura->setLevel(1);
	               $item->addEnchantment($dura);
	            }
	            if((int)rand(1,2)==3){
	               $for = Enchantment::getEnchantment(18);
	               $for->setLevel((int)rand(2,3));
	               $item->addEnchantment($for);
	            }
	        }
	        if($id==276&&(int)rand(1,3)==2){
	            $sharp = Enchantment::getEnchantment(9);
	            $sharp->setLevel((int)rand(2,3));
	            $item->addEnchantment($sharp);
	            if((int)rand(1,2)==1){
	                $knock = Enchantment::getEnchantment(12);
	                $knock->setLevel((int)rand(1,1));
	                $item->addEnchantment($knock);
	            }
	            if((int)rand(1,3)==2){
	               $dura = Enchantment::getEnchantment(17);
	               $dura->setLevel((int)rand(2,3));
	               $item->addEnchantment($dura);
	            }
	        }
	        foreach(array(310,311,312,313) as $armors){
	            if($id==$armors){
	                $protect = Enchantment::getEnchantment(0);
	                $protect->setLevel((int)rand(3,3));
	                $item->addEnchantment($protect);
	            }
	        }
	    }
	    $item->setDamage($dmg);
	    $this->chest->getInventory()->setItem($index,$item);
	}
	
	public function onRun($timer){
	    if($this->chest!=NULL){
		    $this->t_delay--;
	     	if($this->t_delay>=0){
	     		if($this->chest instanceof Chest){
	     		    $this->setItem(0,102,1);
                    $this->setItem(1,102,1);
                    $this->setItem(2,102,1);
                    $this->setItem(3,102,1);
                    $this->setItem(4,102,1);
                    $this->setItem(5,102,1);
                    $this->setItem(6,102,1);
                    $this->setItem(7,102,1);
                    $this->setItem(8,102,1);
	     		    $this->setItem(15,102,1);
	     		    $this->setItem(16,102,1);
	     		    $this->setItem(17,102,1);
	     		    $this->setItem(18,102,1);
	     		    $this->setItem(19,102,1);
	     		    $this->setItem(20,102,1);
	     		    $this->setItem(21,102,1);
	     		    $this->setItem(22,102,1);
	     		    $this->setItem(23,102,1);
	     		    $this->setItem(24,102,1);
	     		    $this->setItem(25,102,1);
	     		    $this->setItem(26,102,1);
	     		    $this->setItem(27,102,1);
		    		$this->setItem(9,$this->ids[(int)rand(0,18)],1);
		    		$this->setItem(10,$this->ids[(int)rand(0,18)],1);
		    		$this->setItem(11,$this->ids[(int)rand(0,18)],1);
		    		$this->setItem(12,$this->ids[(int)rand(0,18)],1);
		    	    $this->setItem(13,$this->ids[(int)rand(0,18)],1);
		    		$this->setItem(14,$this->ids[(int)rand(0,18)],1);
		    	}
	    	}
	    	if($this->t_delay==-1){
	    	    if($this->chest instanceof Chest){
	     		    $this->setItem(0,0,1);
                    $this->setItem(1,0,1);
                    $this->setItem(2,0,1);
                    $this->setItem(3,0,1);
                    $this->setItem(4,0,1);
                    $this->setItem(5,0,1);
                    $this->setItem(6,0,1);
                    $this->setItem(7,0,1);
                    $this->setItem(8,0,1);
	     		    $this->setItem(15,0,1);
	     		    $this->setItem(16,0,1);
	     		    $this->setItem(17,0,1);
	     		    $this->setItem(18,0,1);
	     		    $this->setItem(19,0,1);
	     		    $this->setItem(20,0,1);
	     		    $this->setItem(21,0,1);
	     		    $this->setItem(22,0,1);
	     		    $this->setItem(23,0,1);
	     		    $this->setItem(24,0,1);
	     		    $this->setItem(25,0,1);
	     		    $this->setItem(26,0,1);
	     		    $this->setItem(27,0,1);
	    	        $this->setItem(9,0,0);
		        	$this->setItem(10,0,0);
		        	$this->setItem(11,0,0);
		        	$this->setItem(12,$this->ids[(int)rand(0,18)],1);
		        	$this->setItem(13,0,0);
		         	$this->setItem(14,0,0);
		        	$this->canTakeItem = true;
	           }
	       }
	   }
	}
	
}
