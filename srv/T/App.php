<?php

/**
 * 应用常量
 */
class T_App {
	/** 正常 */
	const NORMAL = 0;
	/** 成功 */
	const SUCC = 1;
	/** 失败 */
	const FAIL = 0;

	/** 服务器关闭 */
	const SERVER_ON = 0;
	/** 服务器开放 */
	const SERVER_OFF = 1;
	/** 服务器维护 */
	const SERVER_MAINTENANCE = 2;

	/** 是否合服区 */
	const IS_HEFU = 0;

	/** 最大字符长度7个汉字 或 14个英文或数字*/
	const MAX_NAME_LENGTH = 14;
	/** 最小2个汉字,4个英文或数字*/
	const MIN_NAME_LENGTH = 4;

	const RES_SYNC_TIME = 5; //分钟

	const RES_FOOD_NAME = 'food';
	const RES_OIL_NAME = 'oil';
	const RES_GOLD_NAME = 'gold';

	const RES_FOOD = 1; //粮食
	const RES_OIL = 2; //石油
	const RES_GOLD = 3; //金钱

	static $ResType = array(
		self::RES_FOOD => self::RES_FOOD_NAME,
		self::RES_OIL => self::RES_OIL_NAME,
		self::RES_GOLD => self::RES_GOLD_NAME,
	);

	/** 男 */
	const GENDER_MALE = 1;
	/** 女 */
	const GENDER_FEMALE = 2;

	static $genderType = array(
		self::GENDER_MALE => '男',
		self::GENDER_FEMALE => '女',
	);

	/** 黄种人 */
	const RACE_YELLOW = 1;
	/** 白种人 */
	const RACE_WHITE = 2;

	/** 军饷 */
	const MILPAY = 1;
	/** 点券 */
	const COUPON = 2;
	/** 消费类型 */
	static $payType = array(
		self::MILPAY => '军饷',
		self::COUPON => '点券',
	);

	/** 威望 */
	const RENOWN = 1;
	/** 功勋 */
	const WAREXP = 2;

	/** 活力购买单价(军饷) */
	const ENERGY_BUY_RATE = 1;
	/** 活力可购买最终上限 */
	const ENERGY_TOP_LIMIT = 1000;
	/** 军令购买单价(军饷) */
	const MILORDER_BUY_RATE = 2;
	/** 军令可购买最终上限 */
	const MILORDER_TOP_LIMIT = 900;

	/** 元首改名花费军饷数 */
	const CG_NICKNAME_COST = 50;

	/** CD时间队列可累加状态 */
	const ADDUP_CAN = 1;
	/** CD时间队列不可累加状态 */
	const ADDUP_CANT = 0;

	//const USER_STATUS_NO_ACTIVE = 0;
	const USER_STATUS_NORMAL = 0;
	const USER_STATUS_FORBID = 1;
	const USER_STATUS_LEAVE = 2;
	/** 用户状态 */
	static $userStatus = array(
		self::USER_STATUS_NORMAL => '正常',
		self::USER_STATUS_FORBID => '禁止',
		self::USER_STATUS_LEAVE => '休假',
	);
	/** 新手保护时间（秒） */
	const USER_PROTECTED_TIME = 172800;

	/** 一周 */
	const ONE_WEEK = 604800;
	/** 一天 */
	const ONE_DAY = 86400;
	/** 一小时 */
	const ONE_HOUR = 3600;
	/** 一分钟 */
	const ONE_MINUTE = 60;

	/** 新手保护期 (秒) 3天 */
	const NEWBE_PROTECT_TIME = 259200;

	const CONFIG_APC_TTL = 0; //一直存在

	/** 一直存在缓存中 */
	const EVER_CACHE_TTL = 0; //

	/** 晴天 */
	const WEATHER_CLEAR = 1;
	/** 雨天 */
	const WEATHER_RAIN = 2;
	/** 雾天 */
	const WEATHER_FOG = 3;
	/** 大风 */
	const WEATHER_WIND = 4;
	/** 天气类型*/
	static $weather = array(
		self::WEATHER_CLEAR => '晴天',
		self::WEATHER_RAIN => '雨天',
		self::WEATHER_FOG => '雾天',
		self::WEATHER_WIND => '大风',
	);


	/** 平原 */
	const TERRAIN_PLAIN = 1;
	/** 山地 */
	const TERRAIN_MOUNT = 2;
	/** 沙漠 */
	const TERRAIN_DESERT = 3;
	/** 森林 */
	const TERRAIN_FOREST = 4;
	/** 水域 */
	const TERRAIN_WATER = 5;
	/** 地形类型*/
	static $terrain = array(
		self::TERRAIN_PLAIN => '平原',
		self::TERRAIN_MOUNT => '山地',
		self::TERRAIN_DESERT => '沙漠',
		self::TERRAIN_FOREST => '森林',
		self::TERRAIN_WATER => '水域',
	);

	/** 地貌标志 */
	const SCENIC_WATER = 1;
	const SCENIC_FOREST = 2;
	const SCENIC_MOUNT = 3;
	/** 地貌标志类型 */
	static $scenicType = array(
		self::SCENIC_WATER => '水域',
		self::SCENIC_FOREST => '森林',
		self::SCENIC_MOUNT => '山地',
	);

	/** 亚洲 */
	const MAP_ASIA = 1;
	/** 欧洲 */
	const MAP_EUROPE = 2;
	/** 非洲*/
	const MAP_AFRICA = 3;

	/** 副本 */
	const MAP_FB = 8;
	/** 据点 */
	const MAP_CAMPAIGN = 9;
	/** 多人副本 */
	const MAP_MULTI_FB = 10;

	static $map = array(
		self::MAP_ASIA => '亚洲',
		self::MAP_EUROPE => '欧洲',
		self::MAP_AFRICA => '非洲',
	);

	/** 军衔等级对应数组 */
	static $mil_rank = array(
		0 => '士官',
		1 => '少尉',
		2 => '中尉',
		3 => '上尉',
		4 => '少校',
		5 => '中校',
		6 => '上校',
		7 => '少将',
		8 => '中将',
		9 => '上将',
		10 => '元帅',
	);

	/** 提取行军数据中 最近时间段内的数据记录 的时间节点 (秒) */
	const EXPIRE_WAR_MARCH = 120;
	/** 提取寻将数据中 最近时间段内的数据记录 的时间节点(秒) */
	const EXPIRE_HERO_FIND = 120;
	/** 提取附属地数据中 最近时间段内数据记录 的时间节点 (秒) */
	const EXPIRE_MAP_HOLD = 120;
	/** 行军到达前N秒之后不能撤回  */
	const CAN_NOT_BACK = 30;

	/** 方法执行时间 */
	const METHOD_EXEC_TIME = 1;
	/** 接口最大调用次数 */
	const MAX_GATWAY_CALL_TIMES = 5;
	/** 装备图标数量 */
	const EQUIP_FACE_NUM = 60;

	/** 出征副本消耗军令*/
	const MARCH_FB_COST_MILORDER = 1;
	/** 出征玩家消耗军令*/
	const MARCH_CITY_COST_MILORDER = 2;

	/** 新手礼包 道具ID */
	const NEWBE_CARD_PROPS_ID = 52;
	/** 新兵礼包 道具ID */
	const NEWBE_PROPS_ID = 30020;
	/** VIP资源包 道具ID */
	const SHOP_RES_ID = 128;

	/** 系统中未明确限制数值的统一上限值 */
	const SYS_VAL_LIMIT_TOP = 99999999;
	/** 军饷汇率 1:10 */
	const MILPAY_EXCHANGE = 10;
}

?>