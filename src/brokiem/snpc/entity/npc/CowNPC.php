<?php

declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use pocketmine\entity\Entity;

class CowNPC extends BaseNPC {

    public const NETWORK_ID = Entity::COW;

    public $height = 1.4;
    public $width = 1;
}
