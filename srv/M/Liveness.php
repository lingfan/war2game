<?php

/**
 *玩家活跃度
 */
class M_Liveness {
	/** 登陆游戏 */
	const GET_POINT_ONLINE = 1;
	/** 军团占领要塞 */
	const GET_POINT_UNION_OCCUPIED = 2;
	/** 完成日常任务 */
	const GET_POINT_DAILY_TASK = 3;
	/** 消灭法西斯 */
	const GET_POINT_TEMPNPC = 4;
	/** 攻击玩家 */
	const GET_POINT_ATK_CITY = 5;
	/** 探索NPC属地 */
	const GET_POINT_EXPLORE = 6;


	/** 得到积分类型 */
	static $category = array(
		M_Liveness::GET_POINT_ONLINE         => '登陆游戏',
		M_Liveness::GET_POINT_UNION_OCCUPIED => '军团占领要塞',
		M_Liveness::GET_POINT_DAILY_TASK     => '完成日常任务',
		M_Liveness::GET_POINT_TEMPNPC        => '消灭法西斯',
		M_Liveness::GET_POINT_ATK_CITY       => '攻击玩家',
		M_Liveness::GET_POINT_EXPLORE        => '探索NPC属地',
	);
}

?>