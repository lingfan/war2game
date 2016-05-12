<?php

class T_Hero {


	/** 英雄品质 */
	const HERO_BULE_LEGEND = 5;
	const HERO_PURPLE_LEGEND = 6;
	const HERO_RED = 7;
	const HERO_GOLD = 8;

	/** 蓝5, 紫6, 红7, 金8*/
	static $heroQual = array(
		self::HERO_BULE_LEGEND => '蓝',
		self::HERO_PURPLE_LEGEND => '紫',
		self::HERO_RED => '红',
		self::HERO_GOLD => '金',
	);

	/** 普通技能 */
	const SKILL_TYPE_NORMAL = 1;
	/** 特殊技能 */
	const SKILL_TYPE_SPECIAL = 2;
	static $skillType = array(
		self::SKILL_TYPE_NORMAL => '普通技能',
		self::SKILL_TYPE_SPECIAL => '特殊技能',
	);

	/** 随机 */
	const RANDOM = 0;
	/** 步兵 兵种ID */
	const ID_FOOT = 1;
	/** 装甲  兵种ID */
	const ID_ARMOR = 2;
	/** 航空  兵种ID */
	const ID_AIR = 3;
	/** 炮兵  兵种ID */
	const ID_GUN = 4;

	/** 兵种类型 */
	static $type = array(
		self::RANDOM => '随机兑换',
		self::ID_FOOT => '步兵系',
		self::ID_ARMOR => '装甲兵系',
		self::ID_AIR => '航空兵系',
		self::ID_GUN => '炮兵系',
	);

	static $army2weapon = array(
		self::ID_FOOT => 11001,
		self::ID_ARMOR => 21001,//轻型坦克
		self::ID_AIR => 31001,//歼击机
		self::ID_GUN => 41001,//迫击炮
	);


	/** 未招募 */
	const FIND_FLAG_INIT = 0;
	/** 招募中 */
	const FIND_FLAG_PROC = 1;
	/** 招募失败 */
	const FIND_FLAG_FAIL = 2;
	/** 招募成功 */
	const FIND_FLAG_SUCC = 3;

	/** 招募状态：未招募 */
	const IS_HIRED_FALSE = 0;
	/** 招募状态：已招募 */
	const IS_HIRED_TRUE = 0;


	/** 空闲 */
	const FLAG_FREE = 0;
	/** 行军 */
	const FLAG_MOVE = 1;
	/** 战斗 */
	const FLAG_WAR = 2;
	/** 死亡 */
	const FLAG_DIE = 3;
	/** 驻守 */
	const FLAG_HOLD = 4;
	/** 组队 */
	const FLAG_TEAM = 5;
	static $heroFlag = array(
		self::FLAG_FREE => '空闲',
		self::FLAG_MOVE => '行军',
		self::FLAG_WAR => '战斗',
		self::FLAG_DIE => '死亡',
		self::FLAG_HOLD => '驻守',
		self::FLAG_TEAM => '组队',
	);

	/** 穿戴装备 */
	const EQUIP_WEAR = 'wear';
	/** 卸下装备 */
	const EQUIP_REMOVE = 'remove';

	/** 不出战 */
	const FIGHT_NOT = 0;
	/** 被轰炸出战 */
	const FIGHT_BOMB = 1;
	/** 被攻击出战 */
	const FIGHT_ATK = 2;
	/** 都出战 */
	const FIGHT_ALL = 3;

	/** 自动补兵 */
	const AUTO_FILL_ARMY = 1;


	/** 是否自动成长(分配属性点) 1是0否 */
	const IS_AOTO_GROW = 1;


	/*--------军官培养相关----------*/
	/** 军功培养不能让军功低于此值 */
	const TRAINING_MIN_MEDAL = 2000;

	/** 军官培养无属性增长 */
	const TRAINING_UP_ZERO = 0;
	/** 军官培养单属性增长 */
	const TRAINING_UP_ONE = 1;
	/** 军官培养双属性增长 */
	const TRAINING_UP_TWO = 2;
	/** 军官培养三属性增长 */
	const TRAINING_UP_THREE = 3;
	/** 军官培养三属性下降 */
	const TRAINING_DOWN_THREE = -3;

	/** 普通培养 军功 */
	const TRAINING_TYPE_ONE = 1;
	/** 中级培养 5军饷 */
	const TRAINING_TYPE_TWO = 2;
	/** 高级培养 20军饷 */
	const TRAINING_TYPE_THREE = 3;

	/** 培养消耗系数 */
	static $TrainCostRate = array(
		self::TRAINING_TYPE_ONE => 100, //军功
		self::TRAINING_TYPE_TWO => 2, //军饷
		self::TRAINING_TYPE_THREE => 5, //军饷
	);

	/** 培养最大消耗系数 */
	static $TrainMaxRate = array(
		self::TRAINING_TYPE_ONE => 100, //军功
		self::TRAINING_TYPE_TWO => 10, //军饷
		self::TRAINING_TYPE_THREE => 10, //军饷
	);

	/** 军官培养属性点增长概率 */
	static $trainingRate = array(
		//普通培养
		self::TRAINING_TYPE_ONE => array(
			self::TRAINING_UP_ONE => 50,
			self::TRAINING_UP_TWO => 20,
			self::TRAINING_UP_THREE => 15,
			self::TRAINING_DOWN_THREE => 15
		),
		//中级培养
		self::TRAINING_TYPE_TWO => array(
			self::TRAINING_UP_ONE => 30,
			self::TRAINING_UP_TWO => 35,
			self::TRAINING_UP_THREE => 25,
			self::TRAINING_DOWN_THREE => 10
		),
		//高级培养
		self::TRAINING_TYPE_THREE => array(
			self::TRAINING_UP_ONE => 15,
			self::TRAINING_UP_TWO => 50,
			self::TRAINING_UP_THREE => 30,
			self::TRAINING_DOWN_THREE => 5
		),
	);

	/** 军官培养属性点增长点数 */
	static $trainingGrow = array(
		self::TRAINING_TYPE_ONE => array(1, 2),
		//中级培养
		self::TRAINING_TYPE_TWO => array(1, 4),
		//高级培养
		self::TRAINING_TYPE_THREE => array(2, 6),
	);

	/** 军官培养属性点减少计算参数 */
	static $trainingDown = array(
		self::TRAINING_TYPE_ONE => array(50, 30),
		//中级培养
		self::TRAINING_TYPE_TWO => array(30, 10),
		//高级培养
		self::TRAINING_TYPE_THREE => array(10, 5),
	);

	/** 轮回加成值 */
	static $recycleAdd = 100;

	static $logTitle = array(
		0 => '寻将招募',
		1 => 'VIP抽取',
		2 => '奖励军官',
		3 => '学院招募',
		4 => '学习技能',
		5 => '拍卖行上架',
		6 => '解雇军官',
		7 => '培养军官',
		8 => '拍卖行出售',
		9 => '拍卖行购买',
		10 => '拍卖行领取',
		11 => '遗忘技能',
	);

	static $findHeroType = array(
		1 => 20009,
		2 => 20010,
		3 => 20011,
	);
}

?>