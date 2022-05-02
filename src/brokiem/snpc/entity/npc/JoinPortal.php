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
use fallendead\form\game\SelectMapForm;
use fallendead\level\map;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use conquest\form\JoinForm;
use fallendead\form\game\SelectMapForm;

class JoinPortal extends BaseNPC {
	public float $height = 2.5;
	public float $width = 0.5;

	protected int $tick_counter = 0;

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
	    $this->setScale(2.0);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public function attack(EntityDamageEvent $source) : void{
        parent::attack($source);
        if($source instanceof EntityDamageByEntityEvent&&$source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
            $this->onJoin($source->getDamager());
        }
    }

	private function onJoin(Player $player) : void{
		$player->sendForm(new SelectMapForm($player->getLocale()));
	}

    public static function getNetworkTypeId(): string {
        return CustomEntityIds::JOINPORTAL()->getId();
    }
}
