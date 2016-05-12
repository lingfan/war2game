<?php

/**
 * 消息控制器
 */
class C_Message extends C_I {
	/**
	 * 获取当前玩家所有未删邮件信息
	 * @author chenhui on 20110505
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AList() {

		$errNo = T_ErrNo::ERR_ACTION;
		$arrMsg = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$errNo = '';
		$arrMsgInfo = M_Message::getAllMessage($cityInfo['id']);
		if (!empty($arrMsgInfo)) {
			foreach ($arrMsgInfo as $key => $msgInfo) {
				$senderCityInfo = M_City::getInfo($msgInfo['sender']);
				$objPlayerSender = new O_Player($msgInfo['sender']);

				$titleLang = $msgInfo['title'];
				$txtLang = $msgInfo['content'];
				if (M_Message::SYS_SEND == $msgInfo['type']) {
					$txtLang = json_decode($msgInfo['content'], true);
					$titleLang = json_decode($msgInfo['title'], true);
				}

				$arrMsg[] = array(
					'MsgId' => $msgInfo['id'],
					'Title' => $titleLang,
					'Content' => $txtLang,
					'SendName' => $objPlayerSender->City()->nickname,
					'OwnName' => $cityInfo['nickname'],
					'Flag' => $msgInfo['flag'],
					'Status' => $msgInfo['status'],
					'Type' => $msgInfo['type'],
					'CreateAt' => date('Y-m-d H:i:s', $msgInfo['create_at'])
				);

			}
		}
		M_Message::syncUnRead2Front($cityInfo['id']); //同步未读消息数据和是否已满


		$data = array_reverse($arrMsg);
		return B_Common::result($errNo, $data); //按时间反序排列
	}

	/**
	 * @see  CMessage::AList
	 */
	public function AGetAllMessage() {
		$obj = new C_Message();
		return $obj->AList();
	}

	/**
	 * 设置消息读取状态(置为已读)
	 * @author chenhui on 20110505
	 * @param int $msgId 消息ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASetReaded($msgId = 0) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($msgId > 0) {
			$msgInfo = M_Message::getMsgById($msgId);
			if (!empty($msgInfo) && $cityInfo['id'] == $msgInfo['owner']) {
				if ($msgInfo['flag'] != M_Message::MSG_READ) {
					$updinfo = array('flag' => M_Message::MSG_READ);
					$ret = M_Message::updateInfo($msgId, $updinfo);
					M_Message::delNureadMsg($cityInfo['id'], $msgId);
					M_Message::syncUnRead2Front($cityInfo['id']); //同步未读消息数据和是否已满
				}

				$errNo = '';
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 删除消息(设置删除状态)
	 * @author chenhui on 20110506
	 * @param string $str_msgid 消息ID 字符串 逗号拼接
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ADelete($str_msgid = '') {

		$errNo = T_ErrNo::ERR_PARAM;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = array();
		if (!empty($str_msgid)) {
			$ids = explode(',', $str_msgid);
			if (is_array($ids)) {
				if (isset($ids[0])) {
					$data = M_Message::delMsgInfo($cityInfo['id'], $ids);
				}

				$errNo = '';
				M_Message::syncUnRead2Front($cityInfo['id']); //同步未读消息数据和是否已满
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 玩家发送消息
	 * @param string $ownname 接收者玩家昵称
	 * @param string $title 消息标题
	 * @param string $content 消息内容
	 * @return array array(1/0,array(ErrNo,Data))
	 * @todo 收件箱是否满、有效期
	 */
	public function ASend($ownname, $title, $content) {
		$ret = false;
		$data = array();

		$errNo = T_ErrNo::ERR_ACTION;

		$title = trim($title);
		$content = trim($content);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($ownname) && !empty($title) && !empty($content)) {
			if ($cityInfo['ban_talking'] < time()) {
				$err = '';

				$title = B_Utils::isBlockName($title, true);
				$content = B_Utils::isBlockName($content, true);

				$arrLimit = M_Message::getMailNumLimit(); //限制
				if (mb_strlen($title) > $arrLimit[0] || mb_strlen($content) > $arrLimit[1]) {
					$err = T_ErrNo::MSG_OVER_LIMIT;
				}
				$ownCityId = false;
				if (empty($err)) {
					$ownCityId = M_City::getCityIdByNickName($ownname);

					if (empty($ownCityId)) //接收玩家城市ID
					{
						$err = T_ErrNo::USER_NO_EXIST;;
					} else if ($ownCityId == $cityInfo['id']) {
						$err = T_ErrNo::USER_MSG_SELF;
					}
				}

				if (empty($err)) {
					$md5Str = md5($title . $content);
					$cacheVal = M_Message::getLastMsgCache($cityInfo['id']);
					if ($md5Str == $cacheVal) {
						$ret = true;
					} else {
						$msgInfo = array(
							'title' => $title,
							'content' => $content,
							'sender' => $cityInfo['id'],
							'owner' => $ownCityId,
							'flag' => M_Message::MSG_UNREAD,
							'status' => M_Message::MSG_UNDEL,
							'type' => M_Message::USER_SEND,
							'create_at' => time()
						);
						$id = M_Message::insert($msgInfo);
						$data = array('MsgId' => $id);
						if ($id > 0) {
							M_Message::setLastMsgCache($cityInfo['id'], $md5Str);
							error_log("{$ownCityId}#{$id}\n", 3, '/tmp/api');
							//添加到未读消息key
							M_Message::addNureadMsg($ownCityId, $id);
							M_Message::syncUnRead2Front($ownCityId); //同步未读消息数据和是否已满
						}
						$ret = $id;
					}

					if ($ret) {

						$errNo = '';
					} else {
						$errNo = T_ErrNo::ERR_DB_EXECUTE;
					}
				} else {
					$errNo = $err;
				}
			} else {
				$errNo = T_ErrNo::BAN_TALKING; //已被禁言
			}

		}

		return B_Common::result($errNo, $data); //发送成功
	}

}

?>