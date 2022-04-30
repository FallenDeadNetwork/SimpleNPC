<?php

/**
 * Copyright (c) 2021 brokiem
 * SimpleNPC is licensed under the GNU Lesser General Public License v3.0
 */

declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use entity_factory\CustomEntityIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;

class ConqPortalNPC extends BaseNPC {
    public float $height = 0.0;
    public float $width = 0.0;

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
            //Server::getInstance()->dispatchCommand($source->getDamager(), "/conq play");
            //Server::getInstance()->dispatchCommand($source->getDamager(), "/game");
        }
    }

    public static function getNetworkTypeId(): string {
        return CustomEntityIds::CONQ_PORTAL()->getId();
    }
}
