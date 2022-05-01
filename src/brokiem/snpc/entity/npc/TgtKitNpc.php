<?php
declare(strict_types=1);

namespace brokiem\snpc\entity\npc;

use brokiem\snpc\entity\BaseNPC;
use entity_factory\CustomEntityIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\world\sound\AnvilFallSound;

class TgtKitNpc extends BaseNPC{
    private const TYPE_ZERO = 0.0;

    public float $moveX = 0.0;
    public float $moveZ = 0.0;

    public float $height = 2;
    public float $width = 0.5;

    /** @var int[]|float[] */
    protected array $sum = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    protected int $counter = 0;
    protected ?float $beforesun = null;
    protected int $hasInfinity = 0;

    protected bool $can_add_sound = false;

    protected int $con_cooltime = 0;
    protected int $show_nametag_count = 0;


    public function __construct(Location $location, ?CompoundTag $nbt = null){
        parent::__construct($location, $nbt);
        $this->setMaxHealth(200);
        $this->setHealth(200);
    }

    protected function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->counter >= 40){
            $this->counter = -1;
        }
        $this->sum[++$this->counter] = self::TYPE_ZERO;
        $sum = array_sum($this->sum);
        if($this->beforesun !== $sum){
            $this->setNameTag(sprintf("%.3f", $sum));
            $this->sendData(null);
            $this->beforesun = $sum;
        }
        //$max = max($this->sum);
        if($this->can_add_sound){
            if($sum >= 20){
                $this->broadcastSound(new AnvilFallSound());
                $this->can_add_sound = false;
            }
        }else{
            if($sum < 20){
                $this->can_add_sound = true;
            }
        }

        --$this->show_nametag_count;
        --$this->con_cooltime;

        $this->motion->x = $this->moveX;
        $this->motion->z = $this->moveZ;

        if($this->isCollidedHorizontally&&$this->con_cooltime <= 0){
            $this->moveX = -$this->moveX;
            $this->moveZ = -$this->moveZ;
            $this->con_cooltime = 60;
        }

        return parent::entityBaseTick($tickDiff);
    }

    public function attack(EntityDamageEvent $source) : void{
        parent::attack($source);
        $this->show_nametag_count = 40;
        $this->sum[$this->counter] += $source->getFinalDamage();
        $sum = array_sum($this->sum);

        if(!$source instanceof EntityDamageByEntityEvent) return;
        $damager = $source->getDamager();

        if(!$damager instanceof Player) return;
        $this->sendActionBarMessage($damager, $this->getPosition()->distance($damager->getPosition()), $source->getFinalDamage());

        if($this->can_add_sound){
            if($sum >= 20){
                $this->getWorld()->addSound($source->getDamager()->getPosition(), new AnvilFallSound);
                $this->can_add_sound = false;
            }
        }
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void{
        parent::syncNetworkData($properties);
        if($this->show_nametag_count >= 1){
            $properties->setByte(EntityMetadataProperties::ALWAYS_SHOW_NAMETAG, 1);
            $properties->setGenericFlag(EntityMetadataFlags::CAN_SHOW_NAMETAG, true);
        }else{
            $properties->setByte(EntityMetadataProperties::ALWAYS_SHOW_NAMETAG, 0);
            $properties->setGenericFlag(EntityMetadataFlags::CAN_SHOW_NAMETAG, false);
        }
    }

    public function kill() : void{
        $this->show_nametag_count = 40;
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

    public function sendActionBarMessage(Player $player, float $distance, float $damage):void{
        $player->sendActionBarMessage(
            'ダメージ: '.round($damage).
            '距離: '.round($distance)
        );
    }
}