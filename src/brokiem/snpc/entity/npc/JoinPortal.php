<?php

/**
 * Copyright (c) 2021 brokiem
 * SimpleNPC is licensed under the GNU Lesser General Public License v3.0
 */

declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use conquest\object\utils\GameFactory;
use entity_factory\CustomEntityIds;
use fallendead\level\map;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use conquest\form\JoinForm;

class JoinPortal extends BaseNPC {
    public float $height = 0.0;
    public float $width = 0.0;

	protected int $tick_counter = 0;

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setScale(1);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public function attack(EntityDamageEvent $source) : void{
        parent::attack($source);
        if($source instanceof EntityDamageByEntityEvent){
            Server::getInstance()->dispatchCommand($source->getDamager(), "/conq play");
            //Server::getInstance()->dispatchCommand($source->getDamager(), "/game");
        }
    }

	public function entityBaseTick(int $tickDiff = 1) : bool{
		if(!$this->isAlive()||$this->isClosed()){
			return parent::entityBaseTick($tickDiff);
		}
		parent::entityBaseTick($tickDiff);
		++$this->tick_counter;
		if($this->tick_counter >= 10){
			foreach($this->getWorld()->getPlayers() as $player){
				if($player->getPosition()->distance($this->location) <= 9){// 3 ** 2
					$this->onJoin($player);
				}
			}
			$this->tick_counter = 0;
		}
		return true;
	}

	private function onJoin(Player $player) : void{
		$player->sendForm(new JoinForm());

	}

    public static function getNetworkTypeId(): string {
        return CustomEntityIds::JOINPORTAL()->getId();
    }
}
