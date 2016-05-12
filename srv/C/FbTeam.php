<?php

/**
 * 多人副本
 */
class C_FbTeam extends C_I {
	/**
	 * 获取当前所有的多人副本队伍
	 * @param int $multiFbId 多人副本ID(如果等于0则显示所有的组队进行中的FB)
	 */
	public function AList($multiFbId = 0) {

	}

	/**
	 * 建立多人副本队伍
	 * @param int $multiFbId 多人副本ID
	 */
	public function ACreate($multiFbId = 0) {

	}

	/**
	 * 加入副本队伍
	 * @param int $teamId 队伍ID
	 */
	public function AJoin($teamId = 0) {

	}

	/**
	 * 多人副本队伍信息
	 * @param int $teamId 队伍ID
	 */
	public function AInfo($teamId) {

	}

	/**
	 * 踢出队伍
	 * @param int $teamId 队伍ID
	 * @param int $pos 被踢的位置
	 */
	public function AKick($teamId, $pos) {

	}

	/**
	 * 退出队伍
	 * @param int $teamId 队伍ID
	 */
	public function AQuit($teamId) {

	}

	/**
	 * 改变队伍中队友的位置
	 * @param int $teamId 队伍ID
	 * @param int $posS [2-5]    开始位置
	 * @param int $posE [2-5]    结束位置
	 */
	public function AChangePos($teamId, $posS, $posE) {

	}

	/**
	 * 确认准备
	 * @param int $teamId 队伍ID
	 * @param string $heroIds 英雄列表 (id1,id2,id3)
	 * @param string $ployVal 计策列表    (id2:1,id2:3,id3:0)
	 */
	public function APrepareConfirm($teamId, $heroIds, $ployVal) {

	}

	/**
	 * 取消准备
	 * @param int $teamId 队伍ID
	 * @param int $pos 位置
	 */
	public function APrepareCancel($teamId) {

	}


}

?>