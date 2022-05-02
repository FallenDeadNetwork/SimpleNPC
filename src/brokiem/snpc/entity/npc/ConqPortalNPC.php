<?php

/**
 * Copyright (c) 2021 brokiem
 * SimpleNPC is licensed under the GNU Lesser General Public License v3.0
 */

declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use entity_factory\CustomEntityIds;
use fdguns\event\EntityDamageByGunEvent;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use conquest\form\JoinForm;
use pocketmine\Server;

class ConqPortalNPC extends BaseNPC{
	public float $height = 2.5;
	public float $width = 0.5;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setScale(6.7);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo($this->height, $this->width);
	}

	public function attack(EntityDamageEvent $source) : void{
		parent::attack($source);
		if($source instanceof EntityDamageByEntityEvent&&$source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			$this->onJoin($source->getDamager());
		}
	}

	public static function getNetworkTypeId() : string{
		return CustomEntityIds::CONQ_PORTAL()->getId();
	}

	private function onJoin(Player $player) : void{
		$player->sendForm(new JoinForm());

	}
}
