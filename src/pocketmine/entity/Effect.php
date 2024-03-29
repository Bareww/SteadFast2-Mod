<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\entity;

use pocketmine\entity\effects\InvisibilityEffect;
use pocketmine\entity\effects\HarmingEffect;
use pocketmine\entity\effects\HealingEffect;
use pocketmine\entity\effects\HungerEffect;
use pocketmine\entity\effects\PoisonEffect;
use pocketmine\entity\effects\RegenerationEffect;
use pocketmine\entity\effects\SaturationEffect;
use pocketmine\entity\effects\SlownessEffect;
use pocketmine\entity\effects\SpeedEffect;
use pocketmine\entity\effects\WitherEffect;
use pocketmine\network\protocol\MobEffectPacket;
use pocketmine\Player;


class Effect{
	const SPEED = 1;
	const SLOWNESS = 2;
	const HASTE = 3;
	const SWIFTNESS = 3;
	const FATIGUE = 4;
	const MINING_FATIGUE = 4;
	const STRENGTH = 5;
	const HEALING = 6;
//	const HARMING = 7;
	const JUMP = 8;
	const NAUSEA = 9;
	const CONFUSION = 9;
	const REGENERATION = 10;
	const DAMAGE_RESISTANCE = 11;
	const FIRE_RESISTANCE = 12;
	const WATER_BREATHING = 13;
	const INVISIBILITY = 14;
	const BLINDNESS = 15;
	const NIGHT_VISION = 16;
	const HUNGER = 17;
	const WEAKNESS = 18;
	const POISON = 19;
	const WITHER = 20;
	const HEALTH_BOOST = 21;
	const ABSORPTION = 22;
	const SATURATION = 23;

	/** @var Effect[] */
	protected static $effects;

	public static final function init(){
		self::$effects = new \SplFixedArray(256);

		self::$effects[Effect::SPEED] = new SpeedEffect(Effect::SPEED, "%potion.moveSpeed", 124, 175, 198);
		self::$effects[Effect::SLOWNESS] = new SlownessEffect(Effect::SLOWNESS, "%potion.moveSlowdown", 90, 108, 129, true);
		self::$effects[Effect::SWIFTNESS] = new Effect(Effect::SWIFTNESS, "%potion.digSpeed", 217, 192, 67);
		self::$effects[Effect::FATIGUE] = new Effect(Effect::FATIGUE, "%potion.digSlowDown", 74, 66, 23, true);
		self::$effects[Effect::STRENGTH] = new Effect(Effect::STRENGTH, "%potion.damageBoost", 147, 36, 35);
		self::$effects[Effect::HEALING] = new HealingEffect(Effect::HEALING, "%potion.heal", 248, 36, 35);
//		self::$effects[Effect::HARMING] = new HarmingEffect(Effect::HARMING, "%potion.harm", 67, 10, 9, true);
		self::$effects[Effect::NIGHT_VISION] = new Effect(Effect::NIGHT_VISION, "%potion.nightVision", 147, 36, 35);
		self::$effects[Effect::JUMP] = new Effect(Effect::JUMP, "%potion.jump", 34, 255, 76);
		self::$effects[Effect::NAUSEA] = new Effect(Effect::NAUSEA, "%potion.confusion", 85, 29, 74, true);
		self::$effects[Effect::REGENERATION] = new RegenerationEffect(Effect::REGENERATION, "%potion.regeneration", 205, 92, 171);
		self::$effects[Effect::DAMAGE_RESISTANCE] = new Effect(Effect::DAMAGE_RESISTANCE, "%potion.resistance", 153, 69, 58);
		self::$effects[Effect::FIRE_RESISTANCE] = new Effect(Effect::FIRE_RESISTANCE, "%potion.fireResistance", 228, 154, 58);
		self::$effects[Effect::WATER_BREATHING] = new Effect(Effect::WATER_BREATHING, "%potion.waterBreathing", 46, 82, 153);
		self::$effects[Effect::INVISIBILITY] = new InvisibilityEffect(Effect::INVISIBILITY, "%potion.invisibility", 127, 131, 146);
		self::$effects[Effect::BLINDNESS] = new Effect(Effect::BLINDNESS, "%potion.blindness", 31, 31, 35, true);
		//Hunger
		self::$effects[Effect::WEAKNESS] = new Effect(Effect::WEAKNESS, "%potion.weakness", 72, 77, 72 , true);
		self::$effects[Effect::POISON] = new PoisonEffect(Effect::POISON, "%potion.poison", 78, 147, 49, true);
		self::$effects[Effect::WITHER] = new WitherEffect(Effect::WITHER, "%potion.wither", 53, 42, 39, true);
		self::$effects[Effect::HEALTH_BOOST] = new Effect(Effect::HEALTH_BOOST, "%potion.healthBoost", 248, 125, 35);
		//Absorption
        self::$effects[Effect::ABSORPTION] = new Effect(Effect::ABSORPTION, "%potion.absorption", 36, 107, 251);
		//Saturation
        self::$effects[Effect::SATURATION] = new Effect(Effect::SATURATION, "%potion.saturation", 255, 0, 255);
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public static final function getEffect($id){
		if(isset(self::$effects[$id])){
			return clone self::$effects[(int) $id];
		}
		return null;
	}

	public static final function getEffectByName($name){
		if(defined(Effect::class . "::" . strtoupper($name))){
			return self::getEffect(constant(Effect::class . "::" . strtoupper($name)));
		}
		return null;
	}

	/** @var int */
	protected $id;

	protected $name;

	protected $duration;

	protected $amplifier;

	protected $color;

	protected $show = true;

	protected $ambient = false;

	protected $bad;

	protected function __construct($id, $name, $r, $g, $b, $isBad = false){
		$this->id = $id;
		$this->name = $name;
		$this->bad = (bool) $isBad;
		$this->setColor($r, $g, $b);
	}

	public function getName(){
		return $this->name;
	}

	public function getId(){
		return $this->id;
	}

	public function setDuration($ticks){
		$this->duration = $ticks;
		return $this;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function isVisible(){
		return $this->show;
	}

	public function setVisible($bool){
		$this->show = (bool) $bool;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAmplifier(){
		return $this->amplifier;
	}

	/**
	 * @param int $amplifier
	 *
	 * @return $this
	 */
	public function setAmplifier($amplifier){
		$this->amplifier = (int) $amplifier;
		return $this;
	}

	public function isAmbient(){
		return $this->ambient;
	}

	public function setAmbient($ambient = true){
		$this->ambient = (bool) $ambient;
		return $this;
	}

	public function isBad(){
		return $this->bad;
	}

	public function canTick(){
		return false;
	}

	public function applyEffect(Entity $entity) {
	}

	public function getColor(){
		return [$this->color >> 16, ($this->color >> 8) & 0xff, $this->color & 0xff];
	}

	public function setColor($r, $g, $b){
		$this->color = (($r & 0xff) << 16) + (($g & 0xff) << 8) + ($b & 0xff);
	}
	
	public function add(Entity $entity, $modify = false) {
		if ($entity instanceof Player) {
			$pk = new MobEffectPacket();
			$pk->eid = $entity->getId();
			$pk->effectId = $this->getId();
			$pk->amplifier = $this->getAmplifier();
			$pk->particles = $this->isVisible();
			$pk->duration = $this->getDuration();
			$pk->eventId = $modify ? MobEffectPacket::EVENT_MODIFY : MobEffectPacket::EVENT_ADD;
			$entity->dataPacket($pk);
		}
	}
	
	public function remove(Entity $entity) {
		if ($entity instanceof Player) {
			$pk = new MobEffectPacket();
			$pk->eid = $entity->getId();
			$pk->eventId = MobEffectPacket::EVENT_REMOVE;
			$pk->effectId = $this->getId();
			$entity->dataPacket($pk);
		}
	}
}
