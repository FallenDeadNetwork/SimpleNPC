<?php
declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use entity_factory\CustomEntityIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class TgtKitNpc extends BaseNPC{
    private const TYPE_ZERO = 0.0;

    public float $height = 2;
    public float $width = 0.5;

    /** @var int[]|float[] */
    protected array $sum = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    protected int $counter = 0;
    protected ?float $beforesun = null;
    protected int $hasInfinity = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null){
        parent::__construct($location, $nbt);
        $this->setMaxHealth(200);
        $this->setHealth(200);
    }

    protected function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->counter >= 20){
            $this->counter = -1;
        }
        $this->sum[++$this->counter] = self::TYPE_ZERO;
        $sum = array_sum($this->sum);
        if($this->beforesun !== $sum){
            $this->setNameTag(sprintf("%.3f", $sum));
            $this->sendData(null);
            $this->beforesun = $sum;
        }
        return parent::entityBaseTick($tickDiff);
    }

    public function attack(EntityDamageEvent $source) : void{
        parent::attack($source);
        $this->sum[$this->counter] += $source->getBaseDamage();
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void{
        parent::syncNetworkData($properties);
        $properties->setByte(EntityMetadataProperties::ALWAYS_SHOW_NAMETAG, 1);
        $properties->setGenericFlag(EntityMetadataFlags::CAN_SHOW_NAMETAG, true);
    }

    public function kill() : void{
        $this->hasInfinity = 20;
        $this->setNameTag("infinity");
        $this->sendData(null);
        parent::kill();
    }

    protected function onDeathUpdate(int $tickDiff) : bool{
        return ((--$this->hasInfinity) <= 0);
    }

    protected function getInitialSizeInfo() : EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width, 1.5);
    }

    public static function getNetworkTypeId() : string{
        return CustomEntityIds::TGT()->getId();
    }
}