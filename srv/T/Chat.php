<?php

/**
 * 聊天 广播
 */
class T_Chat {
	/** 世界频道 */
	const CHAT_WORLD = 1;
	/** 联盟频道 */
	const CHAT_UNION = 2;
	/** 战场频道 */
	const CHAT_WAR = 3;
	/** 私聊频道 */
	const CHAT_OWNER = 4;
	/** 系统频道 */
	const CHAT_SYS = 5;
	/** 队伍频道 */
	const CHAT_TEAM = 6;
	/** 玩家广播  */
	const CHAT_CITY_RADIO = 7;
	/** 系统广播  */
	const CHAT_SYS_RADIO = 8;

	static $chatType = array(
		self::CHAT_WORLD => '世界',
		self::CHAT_UNION => '联盟',
		self::CHAT_WAR => '战场',
		self::CHAT_OWNER => '私人',
		self::CHAT_SYS => '系统',
		self::CHAT_TEAM => '队伍',
		self::CHAT_CITY_RADIO => '玩家广播',
		self::CHAT_SYS_RADIO => '系统广播',
	);

	/** 能取到记录的最小时间段（秒） */
	const CHAT_MIN_TIME = 30;

	/**  广播停留时间  **/
	const SYS_RADIO_STAY_TIME = 5;
	/**  广播优先级 **/
	const SYS_RADIO_PRIO = 0;

	/** 广播消耗军饷 */
	const RADIO_COST = 20;
}

