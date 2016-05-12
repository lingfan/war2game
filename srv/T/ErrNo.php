<?php

/**
 * 错误代号表
 * @example 错误编码由4位 组成 前面2位表示 错误所属模块 后面2位表示 当前模块的错误代码
 */
class T_ErrNo {
	/** 参数错误  */
	const ERR_PARAM = 1001;
	/** 入口参数错误  */
	const ERR_PROXY_PARAM = 1002;
	/** 错误操作*/
	const ERR_ACTION = 1003;
	/** 服务器关闭 */
	const SERVER_OFF = 1004;
	/** 服务器维护  */
	const SERVER_MAINTENANCE = 1005;
	/** 操作过快  */
	const ERR_OP_MANY = 1006;
	/** 数据库执行错误 */
	const ERR_DB_EXECUTE = 1012;
	/** 错误配置 */
	const ERR_CONF = 1013;
	/** 更新操作失败 */
	const ERR_UPDATE = 1014;
	/** 模块维护中 */
	const MODULE_MAINTENANCE = 1015;

	/** 错误消费(进款失败)*/
	const ERR_INCOME = 1089;
	/** 错误消费(付款失败)*/
	const ERR_PAY = 1090;
	/** 您没有足够数量的金钱 */
	const NO_ENOUGH_GOLD = 1091;
	/** 您没有足够数量的粮食 */
	const NO_ENOUGH_FOOD = 1092;
	/** 您没有足够数量的石油 */
	const NO_ENOUGH_OIL = 1093;
	/** 您没有足够数量的人口 */
	const NO_ENOUGH_PEOPLE = 1094;
	/** 您没有足够数量的军饷 */
	const NO_ENOUGH_MILIPAY = 1095;
	/** 您没有足够数量的点券 */
	const NO_ENOUGH_COUPON = 1096;
	/** 您没有足够数量的活力 */
	const NO_ENOUGH_ENERGY = 1097;
	/** 您没有足够数量的军令 */
	const NO_ENOUGH_MILORDER = 1098;
	/** 您没有足够士兵 */
	const NO_ENOUGH_ARMY = 1099;
	/** 您没有足够数量的威望 */
	const NO_ENOUGH_RENOWN = 1100;
	/** 您没有足够数量的突围积分 */
	const NO_ENOUGH_POINT = 1101;
	/** 您没有足够数量的活跃度积分 */
	const NO_ENOUGH_ACTIVENESS = 1102;

	/** 用户未登录  */
	const NO_LOGIN = 2001;
	/** 用户名已存在  */
	const USERNAME_EXIST = 2002;
	/** 用户名非法  */
	const USERNAME_ILLEGAL = 2003;
	/** 用户未激活  */
	const USER_STATUS_NO_ACTIVE = 2004;
	/** 用户禁止  */
	const USER_STATUS_FORBID = 2005;
	/** 用户不存在  */
	const USER_NO_EXIST = 2006;
	/** 非用户本人数据  */
	const USER_NOT_SELF_DATA = 2007;
	/** 用户不能发邮件给自己  */
	const USER_MSG_SELF = 2008;
	/** 攻击方处于新手保护期  */
	const USER_ATK_IS_PROTECTED = 2009;
	/** 防守方处于新手保护期  */
	const USER_DEF_IS_PROTECTED = 2010;
	/** 该玩家实力太强您无法攻击  */
	const USER_ATK_LEVEL_OVER = 2011;
	/** 该玩家实力过于弱小您无法攻击  */
	const USER_ATK_LEVEL_DOWN = 2012;
	/** 玩家威望不够，军衔无法再次升级  */
	const USER_MILRANK_CANT_UP = 2013;
	/** 玩家军衔不够，无法再次领奖  */
	const USER_MILRANK_AWARD_CANT = 2014;
	/** 玩家军衔奖励已完成，无法再次领奖  */
	const USER_MILRANK_AWARD_OVER = 2015;
	/** 玩家军衔本日奖励已领取，无法再次领奖  */
	const USER_MILRANK_DAILY_OVER = 2016;

	/** 昵称已存在  */
	const NICKNAME_EXIST = 3001;
	/** 昵称非法  */
	const NICKNAME_ILLEGAL = 3002;
	/** 昵称长度错误  */
	const NICKNAME_LENGTH_ERR = 3003;

	/** 功勋 不足  */
	const NO_ENOUGH_MILMEDAL = 3004;
	/** 用户未进排名  */
	const USER_NOTIN_RANKING = 3005;


	/** 城市已经存在  */
	const CITY_EXIST = 4001;
	/** 城市初始错误  */
	const CITY_INIT_ERR = 4002;
	/** 城市不存在  */
	const CITY_NO_EXIST = 4003;
	/** 城市资源更新失败 */
	const CITY_RES_UPDATE_FAIL = 4004;
	/** 城市名称非法 */
	const CITY_NAME_ILLEGAL = 4005;
	/** 城市名称存在 */
	const CITY_NAME_EXIST = 4006;
	/** 城市名称长度错误 */
	const CITY_NAME_LENGTH_ERR = 4007;
	/** 城市坐标位置已满 */
	const CITY_MAP_FULL_POS = 4008;
	/** 城市资源不足*/
	const CITY_RES_LACK = 4009;
	/** 城市资源已满仓,不能进行此操作 */
	const CITY_RES_FULL = 4010;
	/** 市场交易额已达上限,不能进行此操作 */
	const CITY_TRADE_FULL = 4011;
	/** 玩家活力值超过上限,不能进行此操作 */
	const CITY_ENERGY_FULL = 4012;
	/** 城市在线奖励条件不足*/
	const CITY_OL_AWARD_NO = 4013;
	/** 玩家军令值超过上限,不能进行此操作 */
	const CITY_MILORDER_FULL = 4014;
	/** 城市处于迁城CD时间内 */
	const CITY_CD_MOVE_CITY = 4015;
	/** 城市正处于行军攻击方或防守方,不能迁城 */
	const CITY_MARCH_ING = 4016;
	/** 此道具只能将城市在本洲随机空地迁移 */
	const CITY_CANT_MOVE_ZONE = 4017;
	/** 此城市被占领不能迁城 */
	const CITY_OCCUPIED_MOVE_ZONE = 4018;
	/** 此城市有城市属地不能迁城 */
	const CITY_OCCUPION_MOVE_ZONE = 4019;
	/** 城市改名道具数量错误 */
	const CITY_MODIFY_NAME_PROPS_ERR = 4020;

	/** 此建筑不可多建，您已经有此建筑了 */
	const BUILD_CANT_MULTI = 5001;
	/** 前提建筑条件不满足 */
	const BUILD_NO_PRE_BUILD_COND = 5002;
	/** 前提科技条件不满足 */
	const BUILD_NO_PRE_TECH_COND = 5003;
	/** 新建建筑失败，数据执行错误 */
	const BUILD_CREATE_FAIL = 5004;
	/** 升级建筑失败，数据执行错误 */
	const BUILD_UPGRADE_FAIL = 5005;

	/** 移动建筑失败，数据执行错误 */
	const BUILD_MOVE_FAIL = 5007;
	/** 建筑移动失败，原建筑位置数据错误 */
	const BUILD_OLD_POS_ERR = 5008;
	/** 此建筑等级已经是最高等级了 */
	const BUILD_MAX_LEVEL_NOW = 5009;
	/** 此建筑等级已经是0级或者不存在了 */
	const BUILD_MIN_LEVEL_NOW = 5010;
	/** 此位置已经有建筑了 */
	const BUILD_POS_USED = 5011;
	/** 建筑队列已满，CD时间未结束 */
	const BUILD_MAX_ROW_NOW = 5012;
	/** 此城市允许建筑CD队列数已达最大值 */
	const BUILD_FINAL_ROW_NUM = 5013;
	/** 其它建筑等级不能超过城镇中心等级 */
	const BUILD_CANT_OVER_CENTER = 5014;
	/** 空闲人口不足，不能拆除住宅 */
	const HOUSE_CANT_DEGRADE = 5015;
	/** 此建筑超过可多建最大数量 */
	const BUILD_OVER_MAX_NUM = 5016;

	/** 此科技等级已经是最高等级了 */
	const TECH_MAX_LEVEL_NOW = 6001;
	/** 升级科技失败，数据执行错误 */
	const TECH_UPGRADE_FAIL = 6002;
	/** 升级科技失败，CD时间未结束 */
	const TECH_CD_TIME = 6003;

	/** 武器研究失败，CD时间未结束 */
	const WEAPON_CD_TIME = 7001;
	/** 武器放弃失败，常规武器不能放弃 */
	const WEAPON_CANT_GIVEUP = 7002;
	/** 开启武器槽失败，没有未开启的武器槽 */
	const WEAPON_NO_MORE_SLOT = 7003;
	/** 图纸合成失败，没有空余的开启的武器槽 */
	const WEAPON_NO_BLANK_SLOT = 7004;
	/** 图纸合成失败，图纸对应的武器ID非法 */
	const WEAPON_ID_ILLEGAL = 7005;
	/** 没有该武器 */
	const WEAPON_NOT_HAVE = 7006;
	/** 武器和兵种不匹配 */
	const WEAPON_NOT_MATCH = 7007;
	/** 武器存在 */
	const WEAPON_EXIST = 7008;
	/** 武器无法使用 */
	const WEAPON_NOT_USE = 7009;
	/** 该武器槽已经开启过 */
	const WEAPON_SLOT_IS_OPEN = 7010;


	/** 模板库中无传奇军官数据 */
	const HERO_TPL_NO_DATA = 8000;
	/** 寻将表中无传奇军官数据 */
	const HERO_SEEK_NO_DATA = 8001;

	/** 寻将失败 */
	const HERO_FIND_FAIL = 8002;
	/** 寻将数据更新失败 */
	const HERO_UPDATE_FIND_FAIL = 8004;
	/** 寻将进行中 */
	const HERO_FIND_PROC = 8005;
	/** 招募失败 */
	const HERO_HIRE_ERR = 8006;
	/** 军官名字长度错误*/
	const HERO_NAME_LENGTH_ERR = 8007;
	/** 军官更新名字失败 */
	const HERO_UPDATE_NAME_FAIL = 8008;
	/** 军官复活失败 */
	const HERO_RELIFE_FAIL = 8009;
	/** 清除寻将CD时间失败 */
	const HERO_CLEAR_CDTIME_FAIL = 8010;
	/** 军官数量已满 */
	const HERO_NUM_FULL_FAIL = 8011;
	/** 寻将数据更新状态失败 */
	const HERO_UPDATE_FLAG_FAIL = 8012;
	/** 军官名字非法 */
	const HERO_NAME_ILLEGAL = 8013;
	/** 军官穿戴装备失败 */
	const HERO_EQUIP_FAIL = 8014;
	/** 军官解除装备失败 */
	const HERO_UNEQUIP_FAIL = 8015;
	/** 该装备已穿戴  */
	const HERO_EQUIP_AGIN = 8016;
	/** 解除装备失败，军官身上未找到该装备 */
	const HERO_EQUIP_NOTIN = 8017;
	/** 军官该技能槽已使用  */
	const HERO_SLOT_FALL = 8018;
	/** 军官该技能槽未使用  */
	const HERO_SLOT_NOT_USE = 8019;
	/** 军官已招募 */
	const HERO_EXIST = 8020;
	/** 军官已存在出战列表中[配兵,武器数值不对] */
	const HERO_EXIST_FIGHT = 8021;
	/** 军官不存在出战列表中 */
	const HERO_NO_EXIST_FIGHT = 8022;
	/** 军官不存在 */
	const HERO_NO_EXIST = 8024;
	/** 军官错误属性点 */
	const HERO_ERR_POINT = 8025;
	/** 军官更新属性点失败 */
	const HERO_UPDATE_POINT_FAIL = 8026;
	/** 寻将表中冷却时间未过期 */
	const HERO_SEEK_CDTIME = 8027;
	/** 寻将操作失败 */
	const HERO_SEEK_FAIL = 8029;
	/** 寻将表中状态标记错误 */
	const HERO_SEEK_FLAG_ERR = 8028;
	/** 军官非空闲状态 */
	const HERO_NOT_FREE = 8032;
	/** 招募条件不足 */
	const HIRE_NOT_ENOUGH = 8030;
	/** 培养条件不足,培养后军功不能低于2000 */
	const TRAINING_NOT_ENOUGH = 8031;
	/** 军官数量不足 */
	const HERO_NOT_NUM = 8032;
	/** 军官状态不满足 */
	const HERO_STATUS_ERR = 8033;

	/** 军官经验道具存在 */
	const HERO_EXP_ITEM_EXIST = 8034;
	/** 经验道具效果值非法 */
	const HERO_EXP_ITEM_EFFECT_ILLEGAL = 8035;
	/** 经验道具数量不足 */
	const HERO_EXP_ITEM_NOT_ENOUGH = 8036;
	/** 经验道具的经验为空 */
	const HERO_EXP_ITEM_EMPTY_EXP = 8037;
	/** 经验道具在军官身上 */
	const HERO_EXP_ITEM_IN_HERO = 8038;
	/** 军官经验道具不存在 */
	const HERO_EXP_ITEM_NO_EXIST = 8039;
	/** 经验道具不在当前军官身上*/
	const HERO_EXP_ITEM_NO_HERO = 8040;
	/** 军官等级已满 */
	const HERO_EXP_FULL_LEVEL = 8041;

	/** 城市道具数量不足 */
	const PROPS_NOT_ENOUGH = 9000;
	/** 道具使用失败，此道具不可以直接使用 */
	const PROPS_CANT_DIRECT_USE = 9001;
	/** 使用技能书失败 */
	const SKILL_BOOK_USE_FALL = 9002;
	/** 免战CD时间未结束，不能连续使用免战道具 */
	const AVOID_WAR_CD_HOLD = 9003;
	/** 出征失败，进攻方处于免战状态 */
	const AVOID_WAR_SELF = 9004;
	/** 出征失败，防守方处于免战状态 */
	const AVOID_WAR_ENEMY = 9005;
	/** 道具使用错误[效果标签和功能不匹配] */
	const PROPS_WRONG_USE = 9006;
	/** 道具出售错误[非可出售道具]  */
	const PROPS_WRONG_SALE = 9007;
	/** 此城市被占领不能迁城 */
	const CITY_OCCUPIED_AVOID_WAR = 9008;
	/** 此城市有城市属地不能迁城 */
	const CITY_OCCUPION_AVOID_WAR = 9009;
	/** 城市被占领无法使用免战道具 */
	const AVOID_WAR_CITY_HOLD = 9010;
	/** 有占领城市无法使用免战道具 */
	const AVOID_WAR_HOLD_CITY = 9011;
	/** 此城市没有在使用免战道具(不能消除) */
	const CITY_NO_AVOID_WAR = 9012;
	/** 出征失败，进攻方处于免占领状态 */
	const AVOID_HOLD_SELF = 9013;
	/** 出征失败，防守方处于免占领状态 */
	const AVOID_HOLD_ENEMY = 9014;
	/** 军官经验道具不可以重复使用 */
	const HERO_EXP_CANT_CONTINUE = 9015;

	/** 兵种ID非法 */
	const ARMY_ID_ILLEGAL = 1101;
	/** 要解散兵种数量大于已有数量 */
	const ARMY_LESS_DISMISS = 1102;
	/** 兵种熟练度未达到升级所需 */
	const ARMY_LESS_PROFIC = 1103;
	/** 此兵种等级已经是最高等级了 */
	const ARMY_MAX_LEVEL_NOW = 1104;
	/** 部队出征失败 */
	const ARMY_MARCH_FAIL = 1105;
	/** 部队组成失败 */
	const ARMY_TROOP_FAIL = 1106;
	/** 兵种等级不够 */
	const ARMY_LEVEL_NO_ENOUGH = 1107;
	/** 兵种数量超出能带的数量 */
	const ARMY_NUM_EXCEED = 1108;

	/** 此任务未完成 */
	const TASK_NOT_COMP = 1200;

	/** 您尚未加入军团 */
	const NOT_IN_UNION = 1301;
	/** 您尚未加入战场 */
	const NOT_IN_WAR = 1401;
	/** 发送消息失败 */
	const CHAT_ADD_FALL = 1501;
	/** 缺少接收人 */
	const CHAR_NOT_ACCEPTER = 1502;
	/** 发言太过频繁 */
	const CHAT_FREQUENT = 1503;
	/** 您已被禁言  */
	const BAN_TALKING = 1504;
	/** 邮件标题或内容长度超过限制  */
	const M_SG_OVER_LIMIT = 1505;
	/** 邮件标题含有非法字符  */
	const M_SG_TITLE_BLOCK = 1506;
	/** 邮件内容含有非法字符  */
	const M_SG_CONTENT_BLOCK = 1507;
	/** 不在该战场内 不能使用该战场频道 */
	const NOT_IN_WARMAP = 1508;
	/** 不能重复发言 */
	const CHAT_REPEAT = 1509;
	/** 不能给自己发送消息 */
	const THINK_ALOUD = 1510;
	/** 不在队伍中 */
	const NOT_IN_TEAM = 1511;

	/** 装备不存在 */
	const EQUIP_NO_EXIST = 1601;
	/** 装备状态更新失败 */
	const EQUIP_STATE_FALL = 1602;
	/** 装备已强化到顶级 */
	const EQUIP_ISMAX_LEVEL = 1603;
	/** 强化石等级不够 */
	const STRONG_STONE_FALL = 1604;
	/** 装备加点失败  */
	const EQUIP_ADD_ATTR_FALL = 1605;
	/** 装备强化扣除资源失败  */
	const STRONG_EQUIP_MINUS_GOLD_FALL = 1606;
	/** 扣除强化道具失败  */
	const STRONG_MINUS_PRO_FALL = 1607;
	/** 幸运池点数不够  */
	const LUCK_NOT_ENOUGH = 1608;
	/** 强化操作失败  */
	const STRONG_OP_FALL = 1609;
	/** 装备模板不存在 */
	const EQUIP_TPL_NO_EXIST = 1610;
	/** 该装备不能合成 */
	const EQUIP_CANNOT_FUSIONING = 1611;
	/** 装备包数量已满 */
	const EQUI_NUM_FULL = 1612;
	/** 该装备不能升级 */
	const EQUIP_CANNOT_UPLEVEL = 1613;
	/** 该装备已穿戴*/
	const EQUIP_WEAR = 1614;
	/** 该装备拍卖中*/
	const EQUIP_ON_SALE = 1615;
	/** 道具包数量已满 */
	const PROPS_NUM_FULL = 1616;
	/** 图纸背包数量已满 */
	const DRAW_NUM_FULL = 1617;
	/** 背包数量已满 */
	const FIRE_NUM_FULL = 1618;
	/** 套装装备属性不同*/
	const SUIT_EQUIP_NO = 1619;
	/** 套装不可以合成*/
	const SUIT_SYNTHESIS_NO = 1620;
	/** 套装不可以升级*/
	const SUIT_UPGRADE_NO = 1621;
	/** 套装不可以升级*/
	const SUIT_STRENGTHEN_NO = 1623;
	/** 装备背包已满*/
	const EQUIP_NUM_FULL = 1624;
	/** 材料包数量已满 */
	const MATERIAL_NUM_FULL = 1625;
	/** 背包已整理好，不需要重新整理 */
	const DRAW_NOT_ARRANGE = 1626;
	/** 此背包为空*/
	const DRAW_IS_EMPTY = 1627;
	/** 装备删除失败*/
	const EQUIP_DEL_FAIL = 1628;

	/** 技能学习失败 */
	const STUDY_SKILL_FALL = 1700;
	/** 遗忘学习失败 */
	const FORGET_SKILL_FALL = 1701;
	/** 技能已满 */
	const SKILL_FULL = 1702;

	/** 部队不存在 */
	const MARCH_NO_EXIST = 1800;
	/** 尚未建立雷达，无法察觉敌情 */
	const NO_EXIST_RADAR = 1801;
	/** 部队返回中 */
	const MARCH_ON_BACK = 1802;
	/** 部队战斗中 */
	const MARCH_ON_FIGHT = 1803;
	/** 无法撤回  */
	const MARCH_CAN_NOT_BACK = 1804;
	/** 存在副本战斗  */
	const MARCH_FB_WAR_EXIST = 1805;
	/** 没有开启此副本关卡  */
	const MARCH_NO_FB_POINT = 1806;
	/** 副本CD时间已满  */
	const FB_CD_TIME_LOCK = 1807;
	/** 部队驻扎中  */
	const MARCH_ON_HOLD = 1809;
	/** 副本数据错误(章节,战役ID非法)  */
	const FB_DATA_ERR = 1810;
	/** 此章节,战役未打完全部关卡  */
	const FB_NOT_COMPLETE = 1811;
	/** 据点行军超过最大数量 */
	const MARCH_CAMP_MAX_NUM = 1812;


	/** 战斗地图不存在  */
	const WAR_MAP_NOT_EXIST = 1901;
	/** 战斗地图类型错误  */
	const WAR_MAP_TYPE_ERR = 1902;

	/** 战报删除有误 */
	const REPORT_DEL_ERR = 2000;
	/** 消息删除有误 */
	const MESSAGE_DEL_ERR = 2110;

	#军团
	/** 创建军团失败  */
	const ERR_CREATE_UNION = 3100;
	/** 军团名称长度不合法  */
	const ERR_LONG_NAME = 3101;
	/** 非法军团名称  */
	const ERR_NAME = 3102;
	/** 军团公告长度不合法  */
	const ERR_LONG_NOTICE = 3103;
	/** 军团公告含有非法字符  */
	const ERR_NOTICE = 3104;
	/** 玩家已加入军团  */
	const HAS_JOINED_UNION = 3105;
	/** 职位不够 无权操作 */
	const UNION_NO_POWER = 3106;
	/** 军团人数已满 */
	const FULL_OF_UNION = 3107;
	/** 已发出申请，请等待审核 */
	const ALREADY_SEND = 3108;
	/** 该军团不存在 */
	const UNION_NOT_EXIST = 3109;
	/** 该军团已经在军团外交关系表中 */
	const UNION_EXIST_REL = 3110;
	/** 军团资金不够 */
	const UNION_GOLD_NO_ENOUGH = 3111;
	/** 军团已到顶级 */
	const UNION_IS_TOP_LEVEL = 3112;
	/** 请先辞去军团职务 */
	const UNION_MOVE_POWER = 3113;
	/** 该盟不军团盟外交关系表中 */
	const UNION_NOT_EXIST_REL = 3114;
	/** 今日军团奖励已领取 */
	const UNION_AWARD_REPEAT = 3115;
	/** 军团名称已存在 */
	const UNION_NAME_EXIST = 3116;
	/** 军团科技已到顶级 */
	const UNION_TECH_ISTOP = 3117;
	/** 军团等级不够 */
	const UNION_LEVEL_NO_ENOUGH = 3118;
	/** 军团相同 */
	const UNION_THE_SAME = 3119;
	/** 军团不同 */
	const UNION_NOT_SAME = 3120;
	/** 改名道具数量不足 */
	const UNION_PROPS_ERR = 3121;
	/** 权限不足 */
	const UNION_POS_ERR = 3122;
	/** 军团冷却中*/
	const UNION_NOT_CD = 3123;

	/** 战斗队列数据添加错误 */
	const BATTLE_QUEUE_ADD_ERR = 4101;
	/** 战斗队列数据删除错误 */
	const BATTLE_QUEUE_DEL_ERR = 4102;

	/** 战斗数据错误 */
	const BATTLE_DATA_ERR = 4103;
	/** 战斗操作错误 */
	const BATTLE_OP_ERR = 4104;
	/** 不在战斗攻击范围 */
	const BATTLE_NOT_ATK_RANG = 4105;
	/** 非战斗进行状态 */
	const BATTLE_NOT_PROC = 4106;
	/** 非手动操作状态 */
	const BATTLE_NOT_M_OP = 4107;
	/** 非当前操作方 */
	const BATTLE_NOT_CUR_OP = 4108;
	/** 回合数已最大 */
	const BATTLE_MAX_BOUT_NUM = 4109;
	/** AI操作无变化 */
	const BATTLE_NOT_CHANGE_AI = 4110;
	/** 无可移动坐标 */
	const BATTLE_NOT_MOVE_POS = 4111;
	/** 不可移动 */
	const BATTLE_NOT_MOVE = 4112;
	/** 不在移动范围内 */
	const BATTLE_NOT_MOVE_RANGE = 4113;
	/** 已移动操作 */
	const BATTLE_NOT_MOVE_LIST = 4114;
	/** 已攻击操作 */
	const BATTLE_NOT_ATK_LIST = 4115;

	/** 收藏目标名称非法  */
	const COLLECT_NAME_ERR = 4201;
	/** 收藏目标名称长度错误  */
	const COLL_NAME_LEN_ERR = 4202;
	/** 收藏目标坐标已存在  */
	const COLL_POS_EXIST = 4203;
	/** 收藏目标坐标数量已满  */
	const COLL_SUM_MAX = 4204;
	/** 目标坐标未收藏  */
	const COLL_POS_NOT_EXIST = 4205;

	/** VIP等级不够不能进行此操作  */
	const VIP_NOT_LEVEL = 4301;
	/** VIP商城购买玩家限量物品超过限制数量  */
	const SHOP_USER_OVER_LIMIT = 4302;
	/** VIP商城购买系统限量物品超过限制数量  */
	const SHOP_SYS_OVER_LIMIT = 4303;
	/** VIP此等级对应的奖励装备非法  */
	const VIP_AWARD_EQUI_ERR = 4304;
	/** VIP此等级对应的奖励装备已赠送  */
	const VIP_AWARD_EQUI_HAVE = 4305;
	/** VIP此等级对应的奖励传奇军官非法  */
	const VIP_AWARD_HERO_ERR = 4306;
	/** VIP此等级对应的奖励传奇军官已赠送  */
	const VIP_AWARD_HERO_HAVE = 4307;
	/** VIP此等级今天购买军令次数已满  */
	const VIP_BUY_MILORDER_NO_TIMES = 4308;
	/** VIP此等级今天购买军令次数已满  */
	const VIP_BUY_ENERGY_NO_TIMES = 4309;
	/** VIP此等级今天购买VIP资源包次数已满  */
	const VIP_SHOP_RES_NO_TIMES = 4310;
	/** VIP此等级本次有效期内购买VIP功能次数已满  */
	const VIP_FUNCTION_NO_TIMES = 4311;
	/** VIP宝箱今天已领取  */
	const VIP_PACK_RECEIVED = 4312;

	/** 此拍卖交易数据错误  */
	const AUC_DATA_ERR = 4401;
	/** 此拍卖交易不能被一口价购买  */
	const AUC_NOT_PRICE_ONLY = 4402;
	/** 竞拍价格不大于上一次竞拍价  */
	const AUC_NEW_PRICE_LESS = 4403;
	/** 此拍卖交易已过期  */
	const AUC_EXPIRED = 4404;
	/** 此交易状态不是拍卖中  */
	const AUC_NOT_ING = 4405;
	/** 此交易状态不是拍卖完成托管中  */
	const AUC_NOT_SUCC = 4406;
	/** 此交易物品托管已过期  */
	const AUC_KEEP_EXPIRED = 4407;
	/** 此交易状态不是拍卖完成托管中或未上架  */
	const AUC_NOT_FAIL = 4408;
	/** 此拍卖物品数据错误[类型与ID不匹配或物品状态非法] */
	const AUC_GOODS_DATA_ERR = 4409;
	/** 此玩家拍卖挂单数已满  */
	const AUC_LIST_FULL = 4410;
	/** 竞拍价格必须小于一口价  */
	const AUC_NEW_PRICE_MORE = 4411;
	/** 军官品质不满足拍卖要求  */
	const AUC_HERO_QUALITY_LOW = 4412;
	/** 装备品质不满足拍卖要求  */
	const AUC_EQUI_QUALITY_LOW = 4413;
	/** 不能购买或竞拍自己拍卖的物品  */
	const AUC_CANT_BUY_OWNER = 4414;
	/** 拍卖记录插入错误  */
	const AUC_INSERT_ERR = 4415;
	/** 拍卖的物品数据错误  */
	const AUC_GOODS_UPDATE_ERR = 4416;
	/** 拍卖的物品不是自己的  */
	const AUC_NOT_SELF = 4417;
	/** 拍卖的物品是自己的  */
	const AUC_IS_SELF = 4418;
	/** 拍卖的保管类型错误  */
	const AUC_TYPE_ERR = 4419;
	/** 装备已被绑定不可以拍卖  */
	const AUC_NOT_BINGING = 4420;
	/** 有他人正在购买此物品  */
	const AUC_BEEN_BUG_SB = 4421;
	/** 有他人正在竞价此物品  */
	const AUC_BEEN_BID_SB = 4422;


	/** 此野外坐标点不是空地  */
	const WILD_POS_NOT_SPACE = 4501;
	/** 空地 */
	const WILD_POS_IS_SPACE = 4502;
	/** 迁城失败[野外地图数据删除失败] */
	const WILD_MAP_DEL_FAIL = 4503;

	/** 据点未开放 */
	const CAMPAIGN_NO_OPEN = 5501;
	/** 据点已加入 */
	const CAMPAIGN_HAD_JOIN = 5502;
	/** 据点加入无权限 */
	const CAMPAIGN_NO_PERM = 5503;


	/** 据点未加入 */
	const CAMPAIGN_NO_JOIN = 5504;
	/** 据点巡逻次数限制*/
	const CAMPAIGN_NO_TIMES = 5505;
	/** 据点不存在 */
	const CAMPAIGN_NO_EXISTS = 5506;
	/** 据点不能占据 */
	const CAMPAIGN_NO_HOLD = 5507;
	/** 据点不能攻击*/
	const CAMPAIGN_NO_ATK = 5508;
	/** 当前据点基地无军队*/
	const CAMPAIGN_NO_MARCH = 5509;
	/** 据点团长领取奖励无权限 */
	const CAMPAIGN_AWARD_NO_PERM = 5510;
	/** 据点团长奖励已领取 */
	const CAMPAIGN_AWARD_HAD = 5511;
	/** 据点部队战斗中 */
	const CAMPAIGN_MARCH_BATTLE = 5512;
	/** 奖励已领取 */
	const AWARD_HAD = 5600;
	/** 奖励不存在 */
	const AWARD_NOT_EXISTS = 5601;
	/** 奖励领取失败 */
	const AWARD_GET_FAIL = 5602;
	/** 奖励存在 */
	const AWARD_EXISTS = 5603;
	/** 抽取奖励操作失败 */
	const AWARD_DRAW_FAIL = 5604;

	/** 无效的卡号 */
	const CODE_NO_EXIST = 15001;
	/** 该卡已使用 */
	const CODE_IS_USE = 15002;
	/** 该类卡已领取 */
	const CODE_TYPE_IS_USE = 15003;
	/** 提交太频繁 */
	const SUBMIT_TOO_FAST = 15004;


	const GATE_ERR_ARGS = 101;
	const GATE_ERR_TIMESTAMP = 102;
	const GATE_ERR_SIGN = 103;
	const GATE_ERR_CID = 104;
	const GATE_ERR_SERVER_ID = 105;

	const GATE_ERR_USER_CID = 190;
	const GATE_ERR_FORBID_LOGIN = 191;
	const GATE_ERR_INIT_USER = 192;
	const GATE_ERR_NO_USER = 193;
	const GATE_ERR_NO_CITY = 194;
	const GATE_ERR_PAY = 195;
	const GATE_ERR_ORDERID_EXIST = 196;
	const GATE_ERR_MAINTENANCE = 201;
	const GATE_ERR_UNKOWN = 999;

	/** 新手指引不存在 */
	const QUEST_NOT_EXIST = 16001;
	/** 新手指引没完成 */
	const QUEST_NOT_COMPLETE = 16002;

	/** 错误的日常奖励 */
	const ERR_LOGIN_DAILY_AWARD = 170001;
	/** 日常奖励已领取 */
	const ERR_LOGIN_DAILY_HAD = 170002;
	/** 当前时间不在活动期间 不能领取日常奖励*/
	const ERR_LOGIN_DAILY_OUT = 170003;
	/** 没有幸运卡片 */
	const ERR_LUCK_CARD = 170004;
	/** 还未完成此项任务 */
	const ERR_ACTIVE_AWARD = 170005;
	/** 当前时间不在活动期间 不能领取学院奖励*/
	const ERR_ACTIVE_AWARD_OUT = 170006;
	/** 用错了道具*/
	const ERR_LUCK_CARD_PROPS = 170007;
	/** 学院任务奖励已领取 */
	const ERR_ACTIVE_AWARD_HAD = 170008;
	/** 行军时间不够 */
	const ERR_MARCH_TIME = 170009;
	/** 不是军团长或副团长不能招募 */
	const ERR_HIRE_USER = 170010;
	/** 玩家已加入军团 */
	const ERR_UNION_HAD = 170011;
	/** 玩家拒绝加入军团 */
	const ERR_UNION_REFUSE = 170012;
	/** 招募玩家超过一定的次数 */
	const ERR_UNION_TIMES = 170013;
	/** 该英雄拍卖中*/
	const HERO_ON_SALE = 170014;
	/** 军官品质不满足兑换要求  */
	const EXCHANGE_HERO_QUALITY_LOW = 170015;
	/** 当前时间不在活动期间 不能领取登陆奖励*/
	const ERR_DAYLY_AWARD_OUT = 170016;
	/** 已售完 */
	const MALL_ITEM_NO = 170017;
	/** 已被占领不可以再行军 */
	const MARCH_NO = 170018;
	/** 此城市今天已被占领的CD时间超过了4个小时 */
	const MARCH_NO_OUTTIME = 170019;
	/** 此城市已被占领，占领方与第3方为同一军团此时不可以占领 */
	const MARCH_NO_UNION = 170020;
	/** 不能占领自己的城市或此城市已是自己的属地 */
	const NO_HOLD_SELF = 170021;
	/** 不是自己占领的城市不能驻守 */
	const HOLD_NO = 170022;
	/** 该城市未被占领或者已被解救不能驻守*/
	const HOLD_NO_OCCUPIED = 170023;
	/** 有解救CD时间 */
	const OUT_RESCUE_TIME = 170024;
	/** 不是同盟不能解救 */
	const NO_RESCUE_UNION = 170025;
	/** 不能攻击被占领的城市 */
	const NO_ATK_HOLD_CITY = 170026;
	/** 已有去此目的地的行军ID不可以再驻守 */
	const HOLD_NO_MARCHID = 170027;
	/** 占领者已是同盟不能解救 */
	const NO_RESCUE_OCCUPIED_UNION = 170028;

	/** 此突击已是开始状态 */
	const STATUS_START = 180001;
	/** 此突击已是未开启状态 */
	const STATUS_END = 180002;
	/** 此突击活动此时正关闭 */
	const BREAKOUT_CLOSE = 180003;
	/** 突击今日参与次数已用完 */
	const BOUT_TIMES_OVER_DAY = 180004;
	/** 突击本次挑战次数已用完 */
	const BOUT_TIMES_OVER_DARE = 180005;
	/** 此城市此突击数据不存在 */
	const BOUT_CITY_DATA_ERR = 180006;
	/** 此突击此关卡编号错误 */
	const BOUT_OVER_OUTPOST = 180007;
	/** 此城市此突击此关卡并未通过 */
	const BOUT_CITY_NOT_PASS = 180008;
	/** 此城市此突击此关卡已经领奖了 */
	const BOUT_CITY_HAD_AWARD = 180009;
	/** 此突击此关卡并无宝箱奖励 */
	const BOUT_NO_TREA = 180010;
	/** 此突击此关卡基础数据错误 */
	const BOUT_OUTPOST_ERR = 180011;
	/** 此城市没有权限开启此突击 */
	const BOUT_CANT_START = 180012;
	/** 此城市有正在进行的突击战斗 */
	const BOUT_CITY_BATTLE_NOW = 180013;
	/** 此城市突击快速战斗正处于CD时间 */
	const BOUT_QUICK_CD_NOW = 180014;

	/** 不满足多人副本条件 战役条件 */
	const MULTI_FB_NO_FBNO = 190001;
	/** 不满足多人副本条件 等级条件 */
	const MULTI_FB_NO_LEVEL = 190002;
	/** 多人副本队伍已满 */
	const MULTI_FB_NOT_POS = 190003;
	/** 多人副本队伍已存在*/
	const MULTI_FB_CITY_EXIST = 190004;
	/** 多人副本队伍次数不足*/
	const MULTI_FB_NO_TIMES = 190005;
	/** 不存在多人副本队伍中*/
	const MULTI_FB_NO_EXIST = 190006;
	/** 多人副本已开始*/
	const MULTI_FB_HAD_START = 190007;
	/** 多人副本玩家数量不足 */
	const MULTI_FB_PLAYER_NUM_ERR = 190008;
	/** 多人副本开始非队长 */
	const MULTI_FB_NOT_HEAD = 190009;
	/** 多人副本存在城市 */
	const MULTI_FB_EXIT_CITY = 190010;
	/** 多人副本不存在npc */
	const MULTI_FB_NOT_EXIT_NPC = 190011;
	/** 多人副本不存在行军 */
	const MULTI_FB_NOT_EXIT_MARCH = 190012;
	/** 多人副本不能跨防线  */
	const MULTI_FB_NOT_DEF_LINE = 190013;

	/** 兑换道具数量不足  */
	const ERR_EXCHANGE_PROPS_NUM = 200001;
	/** 兑换道具不存在  */
	const ERR_EXCHANGE_PROPS_VAL = 200002;
	/** 兑换道具过期  */
	const ERR_EXCHANGE_EXPIRE = 200003;
	/** 兑换军饷不足  */
	const ERR_EXCHANGE_NO_MILPAY = 200004;
	/** 保持道具不足  */
	const ERR_EXCHANGE_NO_KEEP_PID = 200005;

	/** 当前越野不处于 投注状态 */
	const HORSE_NOT_BETTING = 210001;
	/** 越野 投注金额非法 */
	const HORSE_BETTING_ERR = 210002;
	/** 当前越野不处于 领奖状态 */
	const HORSE_NOT_AWARD = 210003;
	/** 您当前没有可领取奖励 */
	const HORSE_NO_AWARD = 210004;
	/** 当前越野不处于 比赛状态 */
	const HORSE_NOT_RUN = 210005;
	/** 当前场次打气超过限定次数 */
	const HORSE_ENCOUR_OVER = 210006;
	/** 打气ID非法 */
	const HORSE_ENCOUR_ID_ERR = 210007;
	/** 越野 投注马编号非法 */
	const HORSE_CODE_ERR = 210008;
	/** 越野 城市场次与系统场次不符 */
	const HORSE_CYCLE_ERR = 210009;
	/** 越野第一名不是当前玩家 */
	const HORSE_FIRST_ID_ERR = 210010;

	/** 38节活动 道具数量错误  */
	const ERR_ACTIVE_3_NUM = 310001;
	/** 38节活动 道具效果错误  */
	const ERR_ACTIVE_3_EFFECT = 310002;
	/** 38节活动 奖励数据错误  */
	const ERR_ACTIVE_3_AWARD = 310003;
	/** 38节活动 扣道具失败  */
	const ERR_ACTIVE_3_FAIL = 310004;

	const HERO_RECYCLE_LV = 320001;
	const HERO_RECYCLE_PROPS_NUM = 320002;
	const HERO_RECYCLE_EQUIP_FULL = 320003;
	const HERO_RECYCLE_CFG_ERR = 320004;

	/** 武器租借军械所等级不足  */
	const WEAPON_RENT_LV_LACK = 330001;
	/** 武器租借军饷不足  */
	const WEAPON_RENT_MILPAY_LACK = 330002;

	/** 空随机名字 */
	const EMPTY_RANDNAME = 330003;
	/** 问答积分不足 */
	const NO_ENOUGH_QUESTION_POINT = 330004;
	/** 问题不存在 **/
	const QUESTION_INFO_EMPTY = 330005;
	/** 回答不存在 */
	const QUESTION_ANSWER_EMPTY = 330006;
	/** 回答未开始 */
	const QUESTION_NOT_START = 330007;
	/** 回答已开始 */
	const QUESTION_HAD_START = 330008;


	const FLOOR_NO_OPEN = 340001;
	const FLOOR_BATTLE_EXIST = 340002;
	const FLOOR_DATA_ERR = 340003;
	const FLOOR_AWARD_HAD = 340004;
	const FLOOR_AWARD_NO = 340005;
	const FLOOR_QUICK_CD_FULL = 340006;

	const MAP_NOT_MOIVE = 340007;

	/** 一次性充值过期 */
	const SECTION_ONCE_PAY_EXPIRE = 3400008;
	const SECTION_ONCE_PAY_NOT = 3400009;
	/** 累计充值过期 */
	const SECTION_ADD_PAY_EXPIRE = 3400011;
	const SECTION_ADD_PAY_NOT = 34000012;

	/** 没达到日历奖励 */
	const NO_CALENDER_AWARD = 34000013;
}