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

class LogoNPC extends BaseNPC {

    public float $height = 1.6;
    public float $width = 3.0;

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string {
        return CustomEntityIds::DECORATION_LOGO()->getId();
    }
}
