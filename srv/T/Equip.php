<?php

class T_Equip {
	/** 装备品质  白色*/
	const EQUIP_WHITE = 1;
	/** 装备品质  绿色*/
	const EQUIP_GREEEN = 2;
	/** 装备品质  蓝色*/
	const EQUIP_BLUE = 3;
	/** 装备品质  紫色*/
	const EQUIP_PURPLE = 4;
	/** 装备品质  红色*/
	const EQUIP_RED = 5;
	/** 装备品质  金色*/
	const EQUIP_GOLD = 6;
	/** 白1, 绿2, 蓝3, 紫4,  红5, 金6*/
	static $equipQual = array(
		self::EQUIP_WHITE => '白',
		self::EQUIP_GREEEN => '绿',
		self::EQUIP_BLUE => '蓝',
		self::EQUIP_PURPLE => '紫',
		self::EQUIP_RED => '红',
		self::EQUIP_GOLD => '金',
	);

	/** 军帽 */
	const EQUIP_HAT = 1;
	/** 军服 */
	const EQUIP_UNIFORM = 2;
	/** 武器 */
	const EQUIP_WEAPON = 3;
	/** 军徽 */
	const EQUIP_MEDAL = 4;
	/** 军靴 */
	const EQUIP_SHOES = 5;
	/** 座驾 */
	const EQUIP_SIT = 6;
	/** 宝物 */
	const EQUIP_EXP = 7;

	/** 装备位置 */
	static $equipPos = array(
		self::EQUIP_HAT => '军帽',
		self::EQUIP_UNIFORM => '军服',
		self::EQUIP_WEAPON => '手枪',
		self::EQUIP_MEDAL => '军徽',
		self::EQUIP_SHOES => '军靴',
		self::EQUIP_SIT => '座驾',
		self::EQUIP_EXP => '宝物',
	);

	/** 装备位置对应字段 **/
	static $posFieldArr = array(
		self::EQUIP_HAT => 'equip_cap',
		self::EQUIP_UNIFORM => 'equip_uniform',
		self::EQUIP_WEAPON => 'equip_arm',
		self::EQUIP_MEDAL => 'equip_medal',
		self::EQUIP_SHOES => 'equip_shoes',
		self::EQUIP_SIT => 'equip_sit',
		self::EQUIP_EXP => 'equip_exp'
	);

	//装备对应的基础属性点
	static $posBaseAttr = array(
		self::EQUIP_HAT => array('base_military'),
		self::EQUIP_UNIFORM => array('base_lead'),
		self::EQUIP_WEAPON => array('base_command'),
		self::EQUIP_MEDAL => array('base_lead', 'base_military'),
		self::EQUIP_SHOES => array('base_lead', 'base_command'),
		self::EQUIP_SIT => array('base_command', 'base_military')
	);


	//装备各品质出售价格 系数
	static $sellParam = array(
		self::EQUIP_WHITE => 200,
		self::EQUIP_GREEEN => 500,
		self::EQUIP_BLUE => 1000,
		self::EQUIP_PURPLE => 2000,
		self::EQUIP_RED => 3000,
		self::EQUIP_GOLD => 5000,
	);

	/** 装备已使用 */
	const EQUIP_IS_USE = 1;
	/** 装备未使用 */
	const EQUIP_NOT_USE = 0;

	/*********************全新装备系统定义**************************/
	/** 装备等级 */
	static $equipLevel = array(
		1, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100
	);

	//装备对应的基础属性点        同原来

	/** 装备合成后的强化等级计算系数 */
	const LEVEL_PARAM = 0.7;

	/** 套装 指挥 */
	const TZ_ZH = 1;
	/** 套装 军事 */
	const TZ_JS = 2;
	/** 套装 统帅 */
	const TZ_TS = 3;

	const TZ_ALLATTR = 4; //全属性增加
	const TZ_FOOT_ATK = 5; //步兵伤害加成
	const TZ_GUN_ATK = 6; //炮兵伤害加成
	const TZ_ARMOR_ATK = 7; //装甲兵伤害加成
	const TZ_AIR_ATK = 8; //航空兵伤害加成

	const TZ_FOOT_DEF = 9; //步兵减免伤害
	const TZ_GUN_DEF = 10; //炮兵减免伤害
	const TZ_ARMOR_DEF = 11; //装甲兵减免伤害
	const TZ_AIR_DEF = 12; //航空兵减免伤害

	const TZ_ALL_ATK = 13; //所有兵种伤害加成
	const TZ_ALL_DEF = 14; //所有兵种减免伤害

	const TZ_ALL_CRIT = 15; //所有兵种暴击加成


	// 	const TZ_CRIT = 5;//所有兵种暴击加成
	// 	const TZ_AL_ATK = 6;//伤害加成
	// 	const TZ_AL_DEF = 7;//减少伤害
	// 	const TZ_AL_LIFE = 8;//生命加成
	// 	const TZ_AL_ARMY = 9;//增加带兵数
	// 	static $equipSuit = array(
	// 			self::TZ_ZH			            => '指挥加成',
	// 			self::TZ_JS			            => '军事加成',
	// 			self::TZ_TS			            => '统帅加成',
	// 			self::TZ_ALLATTR			    => '全属性值加成',
	// 			self::TZ_CRIT		            => '暴击加成',
	// 			self::TZ_AL_ATK		            => '伤害加成',
	// 			self::TZ_AL_DEF		            => '减少伤害',
	// 			self::TZ_AL_LIFE		        => '生命加成',
	// 			self::TZ_AL_ARMY		        => '增加带兵数',

	// 	);
	/** 各品质套装每提升10级增加属性点 */
	static $suitAddNum = array(
		self::EQUIP_PURPLE => 5,
		self::EQUIP_RED => 8,
		self::EQUIP_GOLD => 12
	);

	/** 各位置装备对应的军官字段 */
	static $equipPosWithHeroColumn = array(
		self::EQUIP_HAT => 'equip_cap',
		self::EQUIP_UNIFORM => 'equip_uniform',
		self::EQUIP_WEAPON => 'equip_arm',
		self::EQUIP_MEDAL => 'equip_medal',
		self::EQUIP_SHOES => 'equip_shoes',
		self::EQUIP_SIT => 'equip_sit',
		self::EQUIP_EXP => 'equip_exp',
	);


	/** 升级装备成功率分类 */
	static $upEquipSuccRate = array(
		0 => 30,
		1 => 60,
		2 => 100,
	);
	/** 升级装备品质不降级几率分类 */
	static $upEquipQuality = array(
		0 => 0,
		1 => 100,
		2 => 100,
	);
	/** 升级装备强化等级不降级几率分类 */
	static $upEquipLevel = array(
		0 => 0,
		1 => 50,
		2 => 100,
	);

	static $eLogTitle = array(
		0 => '出售',
		1 => '合成保留',
		2 => '升级',
		3 => '强化',
		4 => '获取',
		5 => '拍卖行上架',
		6 => '拍卖行出售',
		7 => '拍卖行购买',
		8 => '拍卖行领取',
		9 => '合成消失',
	);
}

?>