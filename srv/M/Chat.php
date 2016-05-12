<?php

/**
 * 聊天模块   on 20110526
 */
class M_Chat {
	/** 禁止聊天 */
	const BAN_TALKING = 1;
	/** 允许聊天 */
	const ALLOW_TALKING = 0;

	const CHAN_WORD  = 1;
	const CHAN_UNION = 2;
	const CHAN_WAR   = 3;
	const CHAN_TEAM  = 4;
	const CHAN_CITY  = 5;

	static public function addMsg($val) {
		$h   = date('YmdH');
		$rc  = new B_Cache_RC(T_Key::CHAT_WORLD, $h);
		$ret = $rc->rpush($val);
		return $ret;
	}

	static public function getMsg($cityId) {
		$now        = time();
		$list       = array();
		$returnData = array();

		$rc       = new B_Cache_RC(T_Key::CITY_CHAT, $cityId);
		$lastVist = $rc->get();

		$t = time();
		$n = 0;
		if (!empty($lastVist)) {
			list($t, $n) = explode('|', $lastVist);
		}

		$lastTimeKey = date('YmdH', $t);
		$nowTimeKey  = date('YmdH');

		$rc1     = new B_Cache_RC(T_Key::CHAT_WORLD, $nowTimeKey);
		$newN    = $rc1->lLen();
		$newVist = $now . '|' . $newN;
		if (!empty($lastVist) && ($now - $t) < T_App::ONE_MINUTE) {
			if ($lastTimeKey == $nowTimeKey) {
				$list = $rc1->lrange($n, $newN);
			} else if ($lastTimeKey != $nowTimeKey) {
				$rc2 = new B_Cache_RC(T_Key::CHAT_WORLD, $lastTimeKey);

				$lastList = $rc2->lrange($n, -1);
				$nowList  = $rc1->lrange($n, $newN);

				$list = array_merge($lastList, $nowList);
			}
		}
		$rc->set($newVist, T_App::ONE_DAY);

		return $list;
	}

	/**
	 * 世界频道添加消息
	 * @author HeJunyun on 20110526
	 * @param $id         无意义
	 * @param string $nickName 用户昵称
	 * @param string $message 消息内容
	 * @return int $errNo  错误编号
	 */
	static public function addWorldMessage($nickName, $message, $chatChannel = T_Chat::CHAT_WORLD) {
		$now   = time();
		$errNo = T_ErrNo::CHAT_ADD_FALL;
		$h     = date('YmdH');

		$channel = isset(T_Chat::$chatType[$chatChannel]) ? $chatChannel : T_Chat::CHAT_WORLD;
		$arr     = array($nickName, $message, time(), $channel);

		$msg = json_encode(array(self::CHAN_WORD, 0, $arr));
		$ret = self::addMsg($msg);

		$ret && $errNo = '';
		return $errNo;
	}

	/**
	 * 联盟频道添加消息
	 * @author HeJunyun on 20110526
	 * @param int $unionId 联盟ID
	 * @param string $nickName 发送人昵称
	 * @param string $message 消息内容
	 * @return int $errNo  错误编号
	 */
	static public function addUnionMessage($unionId = 0, $nickName, $message, $chatChannel = '') {
		$errNo = T_ErrNo::CHAT_ADD_FALL;
		if ($unionId < 1) {
			return T_ErrNo::NOT_IN_UNION;
		}
		$d   = date('Ymd');
		$arr = array($nickName, $message, time());

		$msg = json_encode(array(self::CHAN_UNION, $unionId, $arr));
		$ret = self::addMsg($msg);
		$ret && $errNo = '';
		return $errNo;
	}

	/**
	 * 战场频道添加消息
	 * @author HeJunyun on 20110526
	 * @param int $warId 战场ID
	 * @param string $nickName 发送人昵称
	 * @param string $message 消息内容
	 * @return int $errNo  错误编号
	 */
	static public function addWarMessage($warId, $nickName, $message, $chatChannel = '') {
		$errNo = T_ErrNo::CHAT_ADD_FALL;
		if ($warId < 1) {
			return T_ErrNo::NOT_IN_WAR;
		}

		$arr = array($nickName, $message, time());

		$msg = json_encode(array(self::CHAN_WAR, $warId, $arr));
		$ret = self::addMsg($msg);

		$ret && $errNo = '';
		return $errNo;
	}

	/**
	 * 队伍频道添加消息
	 * @author HeJunyun
	 * @param int $teamId 队伍ID
	 * @param string $nickName 发送人昵称
	 * @param string $message 消息内容
	 * @return int $errNo  错误编号
	 */
	static public function addTeamMessage($teamId, $nickName, $message, $chatChannel = '') {
		$errNo = T_ErrNo::CHAT_ADD_FALL;
		if ($teamId < 1) {
			return T_ErrNo::NOT_IN_TEAM;
		}

		$arr = array($nickName, $message, time());
		$msg = json_encode(array(self::CHAN_TEAM, $teamId, $arr));
		$ret = self::addMsg($msg);
		$ret && $errNo = '';
		return $errNo;
	}

	/**
	 * 私聊频道添加消息
	 * @author HeJunyun on 20110526
	 * @param string $accepter 接收人昵称
	 * @param string $nickName 发送人昵称
	 * @param string $message 消息内容
	 * @return int $errNo  错误编号
	 */
	static public function addOwnerMessage($accepter = '', $nickName, $message, $chatChannel = '') {
		$errNo = T_ErrNo::CHAT_ADD_FALL;
		if ($accepter == '') {
			return T_ErrNo::CHAR_NOT_ACCEPTER;
		}
		$arr = array($nickName, $message, time());

		$msg = json_encode(array(self::CHAN_CITY, $accepter, $arr));
		$ret = self::addMsg($msg);
		$ret && $errNo = '';
		return $errNo;
	}

	/**
	 * 系统频道添加消息
	 * @author HeJunyun on 20110526
	 * @param string $message 消息内容
	 * @param int $start 开始时间
	 * @param int $end 结束时间
	 * @param int $interval 间隔时间
	 * @param int $isRepeat 是否循环公告
	 * @return int $errNo  错误编号
	 */
	static public function addSysMessage($data) {
		if (isset($data['title']) && trim($data['title'])) {
			$res = B_DB::instance('ServerNotice')->insert($data);
			if ($res) {
				$rc = new B_Cache_RC(T_Key::CHAT_SYS);
				$rc->delete();
			}
		}
		return $res;
	}

	static public function setSysMessage($id, $data) {
		$res = false;
		$id  = intval($id);
		if ($id) {
			$res = B_DB::instance('ServerNotice')->update($data, $id);
			if ($res) {
				$rc = new B_Cache_RC(T_Key::CHAT_SYS);
				$rc->delete();
			}
		}
		return $res;
	}

	/**
	 * 获取所有系统消息
	 * @author HeJunyun on 20110527
	 * @return Array 系统消息列表
	 */
	static public function getSysMessage() {
		$now  = time();
		$rc   = new B_Cache_RC(T_Key::CHAT_SYS);
		$data = $rc->jsonget();
		//@todo 系统消息靠后天添加更新
		if ($data === false) {
			$data = B_DB::instance('ServerNotice')->getAll();
			if (!empty($data)) {
				$ret = $rc->jsonset($data);
			} else {
				$ret = $rc->jsonset(array());
			}
		}

		return $data;
	}

	/**
	 * 删除系统消息
	 * @author HeJunyun on 20110613
	 * @param string $msg 消息内容
	 * @return bool
	 */
	static public function delSysMsg($id) {
		$res = B_DB::instance('ServerNotice')->delete($id);
		if (!$res) {
			return false;
		}
		$rc = new B_Cache_RC(T_Key::CHAT_SYS);
		return $rc->delete();

	}

	static public function runSendSysNotice() {
		$list    = (array)M_Chat::getSysMessage();
		$rc1     = new B_Cache_RC(T_Key::TMP_EXPIRE, 'notice');
		$tmpList = $rc1->jsonget();

		$now = time();
		$i   = 0;
		foreach ($list as $key => $val) {
			$tmpVal     = isset($tmpList[$key]) ? $tmpList[$key] : array();
			$tmpNum     = isset($tmpVal['tmp_num']) ? $tmpVal['tmp_num'] : 0;
			$expireTime = isset($tmpVal['expire_time']) ? $tmpVal['expire_time'] : 0;

			$intervalTime = isset($val['interval_time']) ? $val['interval_time'] : 1;
			$needTimes    = !empty($val['play_times']) ? ($tmpNum < $val['play_times']) : true;

			if ($needTimes &&
				$val['start_time'] < $now &&
				$val['end_time'] > $now &&
				$expireTime < $now
			) {
				$i++;
				//系统公告格式(标题[语言包格式], 优先级, 停留时间)
				$title   = json_encode(array($val['title']));
				$message = implode("\t", array($title, $val['sord'], $val['suspension']));
				if ($val['type'] == 0) {
					$channel = T_Chat::CHAT_SYS;
					M_Chat::addWorldMessage($val['id'], $message, $channel);
				} else if ($val['type'] == 1) {
					$channel = T_Chat::CHAT_SYS_RADIO;
					M_Chat::addWorldMessage($val['id'], $message, $channel);
				}

				$tmpList[$key]['tmp_num']     = $tmpNum + 1;
				$tmpList[$key]['expire_time'] = $now + $intervalTime;
			}
		}

		if ($i > 0) {
			$rc = new B_Cache_RC(T_Key::CHAT_SYS);
			$rc->jsonset($list);
			$rc1->jsonset($tmpList);
		}
	}

}

?>