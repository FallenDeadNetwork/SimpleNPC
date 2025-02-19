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
use pocketmine\nbt\tag\CompoundTag;

class AttackHeliNPC extends BaseNPC {

    public float $height = 0.0;
    public float $width = 0.0;

    protected function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setScale(0.78);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string {
        return CustomEntityIds::ATTACK_HELI()->getId();
    }
}
