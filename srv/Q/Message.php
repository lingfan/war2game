<?php

/**
 * 消息数据库层
 * @author chenhui on 20110505
 */
class Q_Message extends B_DB_Dao {
	protected $_name = 'message';
	protected $_connType = 'game';
	protected $_primary = 'id';

	public function getAllByOwner($ownerId) {
		$rows = $this->getsBy(array('owner' => $ownerId, 'status' => M_Message::MSG_UNDEL));
		return $rows;
	}

	/**
	 * 获取某玩家所有发送的信息
	 * @author chenhui on 20110728
	 * @param int sender 发送者cityId
	 * @return array 相应信息(二维数组)
	 */
	public function getAllBySender($sender) {
		$row = $this->getsBy(array('sender' => $sender));
		return $row;
	}
}

?>