<?php
namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class DoublePlant extends Flowable{

	protected $id = self::DOUBLE_PLANT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeReplaced(){
		return true;
	}

	public function getName(){
		static $names = [
			0 => "Sunflower",
			1 => "Lilac",
			2 => "Double Tallgrass",
			3 => "Large Fern",
			4 => "Rose Bush",
			5 => "Peony"
		];
		return $names[$this->meta & 0x07];
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->isTransparent() === true && !$this->getSide(0) instanceof DoubleTagPlant){ //Replace with common break method
				$this->getLevel()->setBlock($this, new Air(), false, false, true);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(0);
		$up = $this->getSide(1);
		if($down->getId() === self::GRASS or $down->getId() === self::DIRT){
			$this->getLevel()->setBlock($block, $this, true);
			$this->getLevel()->setBlock($up, Block::get($this->id, $this->meta ^ 0x08), true);
			return true;
		}
		return false;
	}

	public function onBreak(Item $item){
		$up = $this->getSide(1);
		$down = $this->getSide(0);
		if(($this->meta & 0x08) === 0x08){ // This is the Top part of flower
			if($up->getId() === $this->id and $up->meta !== 0x08){ // Checks if the block ID and meta are right
				$this->getLevel()->setBlock($up, new Air(), true, true);
			}
			elseif($down->getId() === $this->id and $down->meta !== 0x08){
				$this->getLevel()->setBlock($down, new Air(), true, true);
			}
		}
		else{ // Bottom Part of flower
			if($up->getId() === $this->id and ($up->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($up, new Air(), true, true);
			}
			elseif($down->getId() === $this->id and ($down->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($down, new Air(), true, true);
			}
		}
	}

	public function getDrops(Item $item){
		if(($this->meta & 0x08) !== 0x08){
			return [[Item::DOUBLE_PLANT,$this->meta,1]];
		}
		else
			return [];
	}

}
