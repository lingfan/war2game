<?php

class T_Battle {
	/** 暴击攻击加成 */
	const CRIT_ADDNUM = 1.35;

	/** 战斗初始玩家等待时间 s*/
	const OP_BATTLE_INIT_WAIT_PVP_TIME = 10;
	/** 战斗初始AI等待时间 s*/
	const OP_BATTLE_INIT_WAIT_PVE_TIME = 5;
	/** 玩家战斗每回合时间 s*/
	const OP_BATTLE_BOUT_PVP_TIME = 45;
	/** NPC战斗每回合时间 s*/
	const OP_BATTLE_BOUT_PVE_TIME = 45;
	/** 战斗回合数*/
	const OP_BATTLE_BOUT_NUM = 40;

	/** 攻击方操作 */
	const CUR_OP_ATK = 1;
	/** 防守方操作 */
	const CUR_OP_DEF = 2;

	/** 手动操作 */
	const OP_M = 0;
	/** 自动操作 */
	const OP_A = 1;

	/** 攻击 */
	const ATK_HIT = 1;
	/** 暴击 */
	const ATK_CRIT = 2;
	/** 闪避 */
	const ATK_MISS = 16;
	/** 无攻击 */
	const ATK_NO = 0;


	/** 战斗等待 */
	const STATUS_WAIT = 1;
	/** 战斗进行 */
	const STATUS_PROC = 2;
	/** 战斗结果计算 */
	const STATUS_RESULT = 3;
	/** 战斗进行中等待 */
	const STATUS_PROC_WAIT = 4;
	/** 战斗结束 */
	const STATUS_END = 9;


	/** 移动操作 */
	const OP_ACT_MOVE = 1;
	/** 攻击操作 */
	const OP_ACT_ATK = 2;
	/** 使用计策 */
	const OP_ACT_PLOY = 3;
	/** 回合结束 */
	const OP_ACT_END = 4;
	/** 撤退操作 */
	const OP_ACT_ESC = 5;
	/** 切换AI操作 */
	const OP_ACT_AI = 6;

	/** 英雄初始化标记 */
	const OP_HERO_INIT_FLAG = 0;
	/** 英雄已移动的标记 */
	const OP_HERO_MOVE_FLAG = 1;
	/** 英雄已攻击的标记 */
	const OP_HERO_ATK_FLAG = 2;
	/** 英雄已反击的标记 */
	const OP_HERO_HIT_FLAG = 4;
	/** 英雄已AI操作标记 */
	const OP_HERO_AI_FLAG = 8;


	/** 等待操作队列类型 */
	const HANDLE_TYPE_WAIT_P_V_NPC = 1;
	/** 等待操作队列类型 */
	const HANDLE_TYPE_WAIT_P_V_FB = 2;
	/** 等待操作队列类型 */
	const HANDLE_TYPE_WAIT_P_V_P = 3;
	/** 自动操作队列类型 */
	const HANDLE_TYPE_AUTO = 4;
	/** AI操作队列类型 */
	const HANDLE_TYPE_AI = 5;


	/** 默认情绪 */
	const MOOD_NO = 1;
	/** 愤怒：提高攻击力百分比10% */
	const MOOD_INCR_ATK = 2;
	/** 镇定：提高防御力百分比10% */
	const MOOD_INCR_DEF = 3;
	/** 兴奋：提高移动力1格 */
	const MOOD_INCR_MOVE = 4;
	/** 狂热：提高暴击出现概率5% */
	const MOOD_INCR_CRIT = 5;
	/** 胆怯：减少攻击力百分比10% */
	const MOOD_DECR_ATK = 6;
	/** 冲动：减少防御力百分比10% */
	const MOOD_DECR_DEF = 7;
	/** 恐惧：减少移动力1格 */
	const MOOD_DECR_MOVE = 8;
	/** 自负：减少暴击出现概率5% */
	const MOOD_DECR_CRIT = 9;

	/** 情绪类型 */
	static $moodType = array(
		self::MOOD_NO => array('name' => '默认', 'val' => '0'),
		self::MOOD_INCR_ATK => array('name' => '愤怒', 'val' => '10'),
		self::MOOD_INCR_DEF => array('name' => '镇定', 'val' => '10'),
		self::MOOD_INCR_MOVE => array('name' => '兴奋', 'val' => '1'),
		self::MOOD_INCR_CRIT => array('name' => '狂热', 'val' => '5'),
		self::MOOD_DECR_ATK => array('name' => '胆怯', 'val' => '-10'),
		self::MOOD_DECR_DEF => array('name' => '冲动', 'val' => '-10'),
		self::MOOD_DECR_MOVE => array('name' => '恐惧', 'val' => '-1'),
		self::MOOD_DECR_CRIT => array('name' => '自负', 'val' => '-5'),
	);

	const QUICK_TIME = 120;
}

?>