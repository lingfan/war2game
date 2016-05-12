<?php

/**
 * 效果定义
 * 功能 + 增（减）+ 类型
 */
class T_Effect {
	/** 建筑效果 */
	static $Build = array(
		'BUILD_UP_DECR_TIME' => '减少建筑升级CD时间',
		'TECH_UP_DECR_TIME' => '减少科技升级CD时间',
		'GOLD_INCR_YIELD' => '金钱产量值',
		'FOOD_INCR_YIELD' => '粮食产量值',
		'OIL_INCR_YIELD' => '石油产量值',
		//'STORAGE_INCR_TL'		=> '增加存储上限',
		//'PEOPLE_INCR_TL'		=> '增加人口上限',
		'RADAR_INCR_RANGE' => '增加雷达范围',
	);

	/** 科技效果 */
	static $Tech = array(
		'GOLD_INCR_YIELD' => '增加金钱产量',
		'FOOD_INCR_YIELD' => '增加粮食产量',
		'OIL_INCR_YIELD' => '增加石油产量',
		'WEAPON' => '武器科技[废弃]',
		'ARMY_RELIFE' => '复活战后已死兵力',

		'FOOT_INCR_LIFE' => '提升步兵生命',
		'FOOT_INCR_ATT' => '提升步兵攻击',
		'FOOT_INCR_DEF' => '提升步兵防御',
		'FOOT_INCR_SP' => '提升步兵速度',

		'GUN_INCR_LIFE' => '提升炮兵生命',
		'GUN_INCR_ATT' => '提升炮兵攻击',
		'GUN_INCR_DEF' => '提升炮兵防御',
		'GUN_INCR_SP' => '提升炮兵速度',

		'ARMOR_INCR_LIFE' => '提升装甲生命',
		'ARMOR_INCR_ATT' => '提升装甲攻击',
		'ARMOR_INCR_DEF' => '提升装甲防御',
		'ARMOR_INCR_SP' => '提升装甲速度',

		'AIR_INCR_LIFE' => '提升航空生命',
		'AIR_INCR_ATT' => '提升航空攻击',
		'AIR_INCR_DEF' => '提升航空防御',
		'AIR_INCR_SP' => '提升航空速度',
	);

	/** 军官技能效果
	 * AN = Army Num
	 * MVE = Move
	 * RGE = Range
	 * LIF = life
	 * SHT = shoot
	 * array(效果定义,全体[0]或兵种[1,2,3,4],数值)
	 */
	static $SkillAimType = array(
		'0' => '所有',
		'SKY' => '对空',
		'LAND' => '对地',
	);

	/**
	 * 基础技能类型
	 * 几率固定为100%
	 */
	static $SkillBaseType = array(
		'INCR_LEA' => '[基础]增加统帅(统帅%)',
		'INCR_COM' => '[基础]增加指挥(指挥%)',
		'INCR_MIL' => '[基础]增加军事(军事%)',
		'INCR_VIM' => '[基础]增加精力(精力%)',
		'INCR_AN' => '[基础]增加带兵数(兵数%)',
		'DECR_AN' => '[基础]减少带兵数(兵数%)',
	);

	static $SkillBattleType = array(
		'INCR_RGE' => '[战斗基础]增加视野(视野)',
		'INCR_MVE' => '[战斗基础]增加移动力(移动力)',
		'INCR_SHT' => '[战斗基础]增加射程(射程)',
		'DECR_RGE' => '[战斗基础]降低视野(视野)',
		'DECR_MVE' => '[战斗基础]降低移动力(移动力)',
		'DECR_SHT' => '[战斗基础]降低射程(射程)',

		'INCR_ATK' => '[战斗]增加攻击(攻击%)',
		'INCR_DEF' => '[战斗]增加防御(防御%)',
		'INCR_LIF' => '[战斗]增加生命(生命%)',

		'LEA_INCR_ATK' => '[战斗]统帅增加攻击(统帅,攻击%)',
		'COM_INCR_ATK' => '[战斗]指挥增加攻击(指挥,攻击%)',
		'MIL_INCR_CRIT' => '[战斗]军事增加暴击几率(军事,暴击几率%)',


		'DECR_ARMY_INCR_ATK' => '[战斗]兵数越少攻击越高(兵力%,攻击%;兵力%,攻击%)',
		'GT_ARMY_INCR_ATK' => '[战斗]敌方兵力大于自己,攻击越高(兵力%,攻击%;兵力%,攻击%)',
		'RANGE_INCR_ATK' => '[战斗]距离提高攻击(距离,攻击%;距离,攻击%)',

		'UNAIM' => '[战斗]无法找到目标(范围)',
		'UNMOVE' => '[战斗]无法移动(无)',
		'ATK_HURT' => '[战斗]攻击持续伤害(伤害%)',
		'ATK_HARM' => '[战斗]攻击附加伤害(伤害%)',

		'UNVIEW_AN' => '[战斗]无法查看带兵数(无)',
		'UNVIEW_SELF' => '[战斗]无法看到自己(无)',

		'RESTOR_AN' => '[战斗]恢复士兵数(伤害值%)',
		'INCR_MISS' => '[战斗]增加闪避几率(闪避%)',
		'INCR_CRIT' => '[战斗]增加暴击几率(暴击%)',
		'ADD_HURT' => '[战斗]增加伤害值(伤害值%)',
		'DEL_HURT' => '[战斗]减少伤害值(伤害值%)',
		'UNATK' => '[战斗]无法攻击(无)',

		'LEA_INCR_DEF' => '[战斗]统帅增加防御(统帅,防御%)',
		'MIL_INCR_LIF' => '[战斗]军事增加生命百分比(军事,生命加成%)',


	);

	/**
	 * 技能效果是否可以叠加
	 */
	static $SkillOverlayType = array(
		'INCR_LEA', 'INCR_COM', 'INCR_MIL',
		'INCR_VIM', 'INCR_AN', 'INCR_HIT',
		'INCR_CRIT', 'INCR_HIT', 'INCR_ATK', 'INCR_DEF', 'INCR_LIF'
	);

	/** 技能触发 */
	static $SkillTrigger = array(
		'ATK' => '攻击',
		'DEF' => '反击'
		//'ATT_ALL'	=> '无限制攻击触发',
		//'ATT_MIN'	=> '最小伤害值攻击触发',
		//'ATT_MAX'	=> '最大伤害值攻击触发',
		//'DEF_ALL'	=> '无限制受击触发',
		//'DEF_MIN'	=> '最小伤害值受击触发',
		//'DEF_MAX'	=> '最大伤害值受击触发',
		//'MOVE_ALL'	=> '无限制移动触发',
		//'MOVE_MIN'	=> '最小移动距离触发',
		//'MOVE_MAX'	=> '最大移动距离触发',
	);

	/** 道具效果 */
	static $Props = array(
		//'BUILD_UP_DECR_TIME'	=> '减少建筑升级CD时间',
		//'TECH_UP_DECR_TIME'		=> '减少科技升级CD时间',
		//'WEAPON_RE_DECR_TIME'	=> '减少武器研制CD时间',
		'GOLD_INCR_YIELD' => '增加金钱产量',
		'FOOD_INCR_YIELD' => '增加粮食产量',
		'OIL_INCR_YIELD' => '增加石油产量',
		'BUILD_SPECIFIC' => '提供一个指定建筑物',
		//'HERO_RELIFE'			=> '复活军官',
		'ARMY_RELIFE' => '复活士兵',
		'ARMY_INCR_ATT' => '增加所有部队攻击',
		'ARMY_INCR_DEF' => '增加所有部队防御',
		//'ARMY_INCR_LIFE'		=> '增加所有部队生命',
		'ARMY_INCR_SPEED' => '增加所有部队速度',
		'HERO_WAR_EXP_INCR' => '增加军官战斗获得经验值',
		'ARMY_WAR_EXP_INCR' => '增加士兵战斗获得熟练度',
		'FIND_INCR_SUCC' => '增加单次寻访传奇军官时的成功率',
		'FIND_DECR_TIME' => '减少单次寻访传奇军官的时间',
		'HIRE_HERO_USE' => '招募军官的消耗道具',
		//'BUILD_INCR_QUEUE'		=> '增加建筑队列',
		//'HERO_ALTER_NAME'		=> '军官改名',
		'USER_ALTER_NAME' => '元首改名',
		'UNION_ALTER_NAME' => '军团改名',
		'EQUI_INCR_STRONG' => '增加装备强化成功率',
		'SKILL_RAND_LEARN' => '技能书(随机学到该技能书内的某一技能)',
		'WEAPON_CREATE' => '图纸(消耗特定图纸生成特定武器)',
		'WEAPON_PIECE' => '图纸残页(N个图纸残页自动合成特定图纸)',
		'PLOY_COST_USE' => '使用计策的消耗道具',
		'EQUI_STRONG_GD' => '强化石(强化装备使用)',
		'AVOID_WAR' => '免进攻',
		'AVOID_HOLD' => '免占领',
		'REMOVE_AVOID_WAR' => '消除免战',
		'MOVE_CITY' => '迁城',
		'VIP_FUNCTION' => 'VIP功能',
		'NEWBIE_PACKS' => '礼包功能',
		'HERO_CARD' => '军官卡',
		'LUCK_CARD' => '幸运卡',
		'RADAR_SEEK' => '雷达功能(查看坐标)',
		'MATERIAL' => '合成材料',
		'FLOWER38' => '鲜花38节',
		'HERO_RECYCLE' => '军官轮回',
		'HERO_EXP_ITEM' => '军官经验丹',
	);

	/** VIP功能(效果) */
	static $VIP = array(
		'GOLD_INCR_YIELD' => '增加金钱产量',
		'FOOD_INCR_YIELD' => '增加粮食产量',
		'OIL_INCR_YIELD' => '增加石油产量',
		'ARMY_INCR_ATT' => '增加所有部队攻击',
		'ARMY_INCR_DEF' => '增加所有部队防御',
		'HERO_INCR_ARMY' => '增加军官带兵上限',
		'ARMY_RELIFE' => '复活战后已死兵力',
	);

	/** VIP所有功能点(改版) */
	static $VIPEffect = array(
		'INCR_ENERGY_LIMIT' => '增加活力上限值',
		'BUY_ENERGY' => '可购买活力次数',
		'INCR_MILORDER_LIMIT' => '增加军令上限值',
		'BUY_MILORDER' => '可购买军令次数',
		'BUILD_CD_LISTID' => '可开启最大建筑队列序号', //从1开始
		'SPECIAL_SLOTID' => '可开启最大特殊武器槽序号', //从1开始
		'MOVE_BUILD' => '是否可移动城内建筑',
		'INCR_AWARD_RATE' => '增加战斗后掉宝概率',
		'DECR_MARCH_TIME' => '减少出征时间数组',
		'HERO_AWARD' => '可抽取奖励传奇军官品质数组',
		'EQUI_AWARD' => '可抽取奖励装备数组',
		'GOLD_INCR_YIELD' => '增加金钱产量',
		'FOOD_INCR_YIELD' => '增加粮食产量',
		'OIL_INCR_YIELD' => '增加石油产量',
		'ARMY_INCR_ATT' => '增加所有部队攻击',
		'ARMY_INCR_DEF' => '增加所有部队防御',
		'HERO_INCR_ARMY' => '增加军官带兵上限',
		'ARMY_RELIFE' => '复活战后已死兵力',
		'VIP_SHOP' => 'VIP商城可出售物品配置',
		'COLONY_OPEN' => '属地开启条件',
		'TECH_CD_LISTID' => '可开启最大科技队列序号', //从1开始
		'VIP_PACKAGE' => '领取VIP宝箱',
		'SHOP_RES' => '是否可购买资源',
	);

	/** 据点(效果) */
	static $Camp = array(
		'INCR_UNION_ADL' => '添加联盟成员攻击防御生命加成',
		'INCR_UNION_RES' => '添加联盟成员资源加成',
	);

	/** 战场计策效果 */
	static $warPoly = array(
		'INCR_ONE_VIEW' => '增加单个部队视野',
		'INCR_ALL_VIEW' => '增加所有部队视野',
		'INCR_ONE_MOVE' => '增加单个部队移动力',
		'INCR_ALL_MOVE' => '增加所有部队移动力',
		'RESTOR_ONE_ARMY' => '恢复单个部队数量',
		'RESTOR_ALL_ARMY' => '恢复所有部队数量',
		'RETREAT' => '撤退',
		'RETREAT_BAN' => '禁止撤退',
		'INCR_ONE_MOOD' => '增加单个英雄情绪',
		'CLEAR_FOG' => '清除迷雾',
		'DECR_ONE_VIEW' => '降低单个部队视野',
		'DECR_ALL_VIEW' => '降低所有部队视野',
		'DECR_ONE_MOVE' => '降低单个部队移动力',
		'DECR_ALL_MOVE' => '降低所有部队移动力',
		'DECR_ONE_MOOD' => '降低单个英雄情绪',
		'DIS_ARMY_INFO' => '显示部队详细信息',
	);

	/** 付费操作定义 */
	static $payAction = array(
		'ResetHeroAttrPoint' => '重置英雄属性点', //重置英雄属性点
		'ModifyHeroName' => '修改英雄名字', //修改英雄名字
		'RelifeHero' => '复活英雄', //复活英雄
	);
	/** 套装装备效果 */

	/**
	 * 基础技能类型
	 * 几率固定为100%
	 */
	static $SuitBaseType = array(
		'TZ_ZH' => '指挥加成',
		'TZ_JS' => '军事加成',
		'TZ_TS' => '统帅加成',
		'TZ_ALLATTR' => '全属性值加成',
	);

	static $SuitBattleType = array(
		'TZ_CRIT' => '暴击加成',
		'TZ_AL_ATK' => '增加攻击力',
		'TZ_AL_DEF' => '增加防御力',
		'TZ_AL_LIFE' => '生命加成',
		'TZ_AL_ADD_HURT' => '伤害加成',
		'TZ_AL_DEF_HURT' => '减少伤害',
	);

	/**
	 * 技能效果是否可以叠加
	 */
	static $SuitOverlayType = array(
		'TZ_ZH', 'TZ_JS', 'TZ_TS',
		'TZ_ALLATTR', 'TZ_CRIT', 'TZ_AL_ATK',
		'TZ_AL_DEF', 'TZ_AL_LIFE'
	);
	// 	static $SuitBaseType = array(  //进行修改
	// 			'TZ_ZH'			=> '指挥加成',
	// 			'TZ_JS'			=> '军事加成',
	// 			'TZ_TS'		    => '统帅加成',
	// 			'TZ_ALLATTR'    => '全属性值加成',
	// 			'TZ_CRIT'       => '暴击加成',
	// 			'TZ_AL_ATK'     => '伤害加成',
	// 			'TZ_AL_DEF'     => '减少伤害',
	// 			'TZ_AL_LIFE'    => '生命加成',
	// 			'TZ_AL_ARMY'    => '增加带兵数',

	// 	);

}

?>