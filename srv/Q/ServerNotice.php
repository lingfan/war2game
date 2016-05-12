<?php

/**
 * 聊天消息
 */
class Q_ServerNotice extends B_DB_Dao {
	protected $_name = 'server_notice';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 有效的广播列表
	 */
	public function getRadioList() {

		$data = $this->getsBy(array('type' => array('>', 0)), array('sord' => 'DESC', 'create_at' => 'ASC'));
		return $data;
	}


}

?>