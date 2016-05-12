<?php

/**
 * 消息逻辑层
 * @author chenhui on 20110505
 */
class M_Message {

	/**    未读消息    */
	const MSG_UNREAD = 0;
	/**    已读消息    */
	const MSG_READ = 1;
	/**    未删消息    */
	const MSG_UNDEL = 0;
	/**    已删消息    */
	const MSG_DEL = 1;
	/**    玩家消息    */
	const USER_SEND = 1;
	/**    系统消息    */
	const SYS_SEND = 2;
	/**    消息最多显示条数    */
	const MSG_MAX_NUM = 100;
	/*	消息标题最多允许字节数	*/
	//const MSG_TITLE_NUM	 = 35;
	/*	消息内容最多允许字节数	*/
	//const MSG_CONTENT_NUM	 = 400;

	/**
	 * 获取邮件标题、内容字符数限制
	 * @author chenhui on 20120921
	 * @return array
	 */
	static public function getMailNumLimit() {
		$ret = array(35, 200); //标题字符数限制、内容字符数限制
		if (ETC_NO == 'vn') {
			$ret = array(120, 600); //标题字符数限制、内容字符数限制
		}
		return $ret;
	}

	/**
	 * 获取某玩家所有未删信息
	 * @author chenhui on 20110627
	 * @author huwei modified on 20111015
	 * @param int owner 接收者cityId
	 * @return array 相应信息(二维数组)
	 */
	static public function getAllMessage($owner) {
		$list   = array();
		$cityId = intval($owner);
		if ($cityId > 0) {
			$idArr = self::_getCityMsgList($cityId);
			if (!empty($idArr)) {
				$num = 0;
				foreach ($idArr as $msgId) {
					$info = self::_getCityMsgInfo($msgId);
					if (!empty($info['id']) && $num < M_Message::MSG_MAX_NUM) {
						$list[$info['id']] = $info;
					}
					$num++;
				}
			}
		}
		return $list;
	}

	/**
	 * 获取某玩家所有发送信息
	 * @author chenhui on 20110728
	 * @param int $sender 发送者cityId
	 * @return array 相应信息(二维数组)
	 */
	static public function getAllSenderMessage($sender) {
		$ret = B_DB::instance('Message')->getAllBySender($sender);
		return !empty($ret) ? $ret : array();
	}

	/**
	 * 根据消息ID获取消息内容
	 * @author chenhui on 20110505
	 * @author huwei on 20111015
	 * @param int msgid 消息ID
	 * @return array 相应信息(一维数组)
	 */
	static public function getMsgById($msgid) {
		$ret   = false;
		$msgid = intval($msgid);
		if ($msgid > 0) {
			$info = self::_getCityMsgInfo($msgid);

			if (!empty($info['id'])) {
				$ret = $info;
			}
		}
		return $ret;
	}

	/**
	 * 根据信息ID更新信息
	 * @author chenhui on 20110818
	 * @param int $msgId 信息ID
	 * @param array $updInfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function updateInfo($msgId, $updInfo) {
		$ret   = false;
		$msgId = intval($msgId);
		if ($msgId > 0 && is_array($updInfo)) {
			$ret = self::_setCityMsgInfo($msgId, $updInfo, true);
		}
		return $ret;
	}


	static public function delMsgInfo($cityId, $ids) {
		$errID  = array();
		$cityId = intval($cityId);

		if ($cityId > 0 && is_array($ids)) {
			foreach ($ids as $msgId) {
				$row = self::_getCityMsgInfo($msgId);

				$bUp = false;
				if (!empty($row['id']) && $row['owner'] == $cityId) {
					$fieldArr = array('status' => M_Message::MSG_DEL);
					$bUp      = self::_delCityMsgList($cityId, $msgId);
					if ($bUp) {
						self::_delCityMsgInfo($msgId);

						if ($row['flag'] != M_Message::MSG_READ) {
							M_Message::delNureadMsg($cityId, $msgId);
						}
					}

				}
				if (!$bUp) {
					array_push($errID, $msgId);
				}
			}
		}
		return $errID;
	}

	/**
	 * 插入信息
	 * @author chenhui  on 20110506
	 * @param array info 要插入的信息
	 * @return bool
	 */
	static public function insert($info) {
		$ret = false;
		if (!empty($info['owner'])) {
			$msgId = B_DB::instance('Message')->insert($info);
			if ($msgId) {
				$listUp   = self::_setCityMsgList($info['owner'], $msgId);
				$syncData = self::_getCityMsgInfo($msgId);
				$infoUp   = self::_setCityMsgInfo($msgId, $syncData);

				M_Sync::addQueue($syncData['owner'], M_Sync::KEY_MESSAGE, array($msgId => $syncData)); //同步数据!
				$ret = $msgId;
			}
		}
		return $ret;
	}

	/**
	 * 发送系统邮件
	 * @param int $cityId 接收者城市ID
	 * @param string $title 消息标题
	 * @param string $content 消息内容
	 * @return bool
	 */
	static public function sendSysMessage($cityId, $title, $content, $passGateway = true) {
		$ret = false;
		if (!empty($cityId) && !empty($title) && !empty($content)) {
			$msgInfo = array(
				'title'     => $title,
				'content'   => $content,
				'sender'    => 0,
				'owner'     => $cityId,
				'flag'      => self::MSG_UNREAD,
				'status'    => self::MSG_UNDEL,
				'type'      => self::SYS_SEND,
				'create_at' => time()
			);
			$ret     = self::insert($msgInfo);
			M_Message::addNureadMsg($cityId, $ret);
			M_Message::syncUnRead2Front($cityId, $passGateway); //同步未读消息数据和是否已满

		}
		return $ret;
	}

	/**
	 * 同步最新的某城市未读消息数量和是否已满至前端接口
	 * @author chenhui on 20110818
	 * @param int $cityId 城市ID
	 */
	static public function syncUnRead2Front($cityId, $passGateway = true) {
		if (!empty($cityId)) {
			$msRow = array(
				'UnreadMsgNum' => M_Message::getNureadMsgNum($cityId),
				'IsMsgFull'    => M_Message::isMessageFull($cityId),
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow, $passGateway); //同步未读消息条数
		}
	}

	/**
	 * 获取当前玩家邮件数量
	 * @author chenhui on 20120215
	 * @param int $cityId 城市ID
	 * @return int 数量
	 */
	static public function getAllMessageNum($cityId) {
		$num = 0;
		if (!empty($cityId)) {
			$rc = new B_Cache_RC(T_Key::CITY_MESSAGE_LIST, $cityId);
			if ($rc->exists()) {
				$num = $rc->scard();
			}
		}
		return $num;
	}

	/**
	 * 判断某玩家消息是否已满
	 * @author chenhui on 20110927
	 * @param int $cityId 接收者cityId
	 * @return bool
	 */

	static public function isMessageFull($cityId) {
		$ret    = 0;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$hasNum = self::getAllMessageNum($cityId);
			if ($hasNum >= M_Message::MSG_MAX_NUM) {
				$ret = 1;
			}
		}
		return $ret;
	}

	/**
	 * 设置最近发送消息的缓存
	 * @author chenhui on 20110921
	 * @param int $cityId 城市ID
	 * @param string $md5Str 加密后的内容
	 * @return bool
	 */
	static public function setLastMsgCache($cityId, $md5Str) {
		$ret = false;
		if (!empty($cityId) && !empty($md5Str)) {
			$rc  = new B_Cache_RC(T_Key::CITY_MSG, $cityId);
			$ret = $rc->set($md5Str, T_App::ONE_HOUR);
		}
		return $ret;
	}

	/**
	 * 获取最近发送消息的缓存
	 * @author chenhui on 20110921
	 * @param int $cityId 城市ID
	 * @return string 加密后的内容
	 */
	static public function getLastMsgCache($cityId) {
		$ret = '';
		if (!empty($cityId)) {
			$rc = new B_Cache_RC(T_Key::CITY_MSG, $cityId);
			if ($rc->exists()) {
				$ret = $rc->get();
			}
		}
		return $ret;
	}

	/**
	 * 获取城市消息列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @return array
	 */
	static private function _getCityMsgList($cityId) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_MESSAGE_LIST, $cityId);
			$ret = $rc->smembers();
			if (empty($ret)) {
				$row = B_DB::instance('Message')->getAllByOwner($cityId);
				$i   = 0;
				foreach ($row as $info) {
					$i++;
					$rc->sadd($info['id']);
					$ret[] = $info['id'];
				}
			}

			if (!empty($ret)) {
				sort($ret);
			}
		}
		return $ret;
	}

	/**
	 * 添加城市消息列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @param int $msgId
	 * @return array
	 */
	static private function _setCityMsgList($cityId, $msgId) {
		$ret    = false;
		$cityId = intval($cityId);
		$msgId  = intval($msgId);
		if ($cityId > 0 && $msgId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_MESSAGE_LIST, $cityId);
			$ret = $rc->sadd($msgId);
		}
		return $ret;
	}

	/**
	 * 删除城市消息列表
	 * @author huwei on 20111014
	 * @param int $cityId
	 * @param int $msgId
	 * @return array
	 */
	static private function _delCityMsgList($cityId, $msgId) {
		$ret      = false;
		$cityId   = intval($cityId);
		$reportId = intval($msgId);
		if ($cityId > 0 && $msgId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_MESSAGE_LIST, $cityId);
			if ($rc->sismember($msgId)) {
				$ret = $rc->srem($msgId);
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * 获取城市装备信息
	 * @author huwei on 20111014
	 * @param int $msgId
	 * @return array
	 */
	static private function _getCityMsgInfo($msgId) {
		$ret   = false;
		$msgId = intval($msgId);
		if ($msgId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_MESSAGE_INFO, $msgId);
			$ret = $rc->hgetall();
			if (empty($ret['id'])) {
				$fieldArr = B_DB::instance('Message')->get($msgId);
				if (!empty($fieldArr)) {
					self::_setCityMsgInfo($msgId, $fieldArr);
					$ret = $fieldArr;
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新城市消息信息
	 * @author huwei on 20111015
	 * @param int $msgId
	 * @param array $fieldArr
	 * @param bool $isUp
	 * @return array
	 */
	static private function _setCityMsgInfo($msgId, $fieldArr, $isUp = false) {
		$ret   = false;
		$msgId = intval($msgId);

		if (!empty($msgId) && is_array($fieldArr) && !empty($fieldArr)) {
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (in_array($key, T_DBField::$messageFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_MESSAGE_INFO, $msgId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret && $isUp) {
					$fields = $fieldArr;
					B_DB::instance('Message')->update($fields, $msgId);
				}
			}
		}
		return $ret ? $info : false;
	}

	/**
	 * 删除城市城市信息
	 * @author huwei on 20111015
	 * @param int $msgId
	 * @return array
	 */
	static private function _delCityMsgInfo($msgId) {
		$ret   = false;
		$msgId = intval($msgId);
		if ($msgId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_MESSAGE_INFO, $msgId);
			$ret = $rc->delete();

			if ($ret) {
				$bDel = B_DB::instance('Message')->delete($msgId);
			}

		}
		return $ret;
	}

	/**
	 * 添加未读消息
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @param int $msgId
	 * @return bool
	 */
	static public function addNureadMsg($cityId, $msgId) {
		$ret = false;
		if (!empty($cityId) && !empty($msgId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_MSG_UNREAD, $cityId);
			$ret = $rc->sadd($msgId);
		}

		return $ret;
	}

	/**
	 * 删除未读消息
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @param int $msgId
	 * @return bool
	 */
	static public function delNureadMsg($cityId, $msgId) {
		$ret = false;
		if (!empty($cityId) && !empty($msgId)) {
			$rc = new B_Cache_RC(T_Key::CITY_MSG_UNREAD, $cityId);
			if ($rc->sismember($msgId)) {
				$ret = $rc->srem($msgId);
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * 获取未读消息数量
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @return int
	 */
	static public function getNureadMsgNum($cityId) {
		$ret = 0;
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_MSG_UNREAD, $cityId);
			$ret = $rc->scard();
		}
		return $ret;
	}

	/**
	 * 修正未读消息数量
	 * 次方法主要用于修复数据 在在初始化信息时调用
	 * 平常调用用 getNureadMsgNum方法
	 * @author huwei on 20120215
	 * @param int $cityId
	 * @return void
	 */
	static public function getNureadMsg($cityId) {
		$ret = array();
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_MSG_UNREAD, $cityId);
			$ids = $rc->smembers();
			foreach ($ids as $id) {
				$row = M_Message::getMsgById($id);
				if (empty($row['id']) || $row['flag'] == M_Message::MSG_READ) {
					$rc->srem($id);
				} else {
					$ret[] = $id;
				}
			}
		}
		return $ret;
	}
}

?>