<?php

/**
 * 用户模块
 * @author 胡威 <william.hu@live.com>
 *
 */
class M_User {
	/** 前端分页大小 */
	static $pageSize = 9;


	/** 正常 */
	const STATUS_NORMAL = 0;
	/** 禁止 */
	const STATUS_FORBID = 1;


	static public function getIdByUsername($name, $consumerId, $serverId) {
		$username = md5($name . '|' . $consumerId . '|' . $serverId);
		$info     = B_DB::instance('User')->getBy(array('username' => $username));

		if (!empty($info['id'])) {
			$id = $info['id'];
		} else {
			$up = array(
				'username'    => $username,
				'nickname'    => $name,
				'consumer_id' => $consumerId,
				'server_id'   => $serverId,
				'invite_id'   => 0,
				'created_at'  => time(),
			);
			$id = B_DB::instance('User')->insert($up);
		}
		return $id;
	}

	/**
	 * 玩家列表
	 * @author Hejunyun
	 * @param int $page
	 * @param int $rows
	 * @param string $sidx
	 * @param string $sord
	 */
	static public function cityList($formVals) {

		$curPage = max(1, $formVals['page']);
		$offset  = max(10, $formVals['rows']);
		$start   = ($curPage - 1) * $offset;
		$list    = B_DB::instance('City')->getList($start, $offset, $formVals['filter']);
		return $list;
	}

	static public function totalUser($formVals) {
		$num = B_DB::instance('City')->count($formVals['filter']);
		return $num;
	}

	/**
	 * 根据用户名获取用户ID
	 * @author Hejunyun
	 * @param string $username
	 * @return int id
	 */
	static public function getInfoByUsername($username) {
		$row = B_DB::instance('User')->getBy(array('username_ext' => $username));
		return $row;
	}


	/**
	 * 获取用户信息
	 * @author huwei at 2010/03/30
	 * @param int $user_id
	 * @return array
	 */
	static public function getInfo($userId) {
		$userInfo = B_DB::instance('User')->get($userId);
		return $userInfo;
	}


	/**
	 * 创建新用户
	 * @param int $consumerId 运营商ID
	 * @param string $username 用户账号
	 * @return int 新用户ID/false
	 */
	static public function create($consumerId, $username, $ip = '', $isAdult = 0, $username_ext = '', $server_id = 0, $inviteId = 0) {
		$ret = false;
		if (!empty($consumerId) && !empty($username)) {
			$nowtime  = time();
			$filedArr = array(
				'consumer_id'     => $consumerId,
				'username'        => $username,
				'username_ext'    => $username_ext,
				'server_id'       => $server_id,
				'last_visit_time' => $nowtime,
				'last_visit_ip'   => $ip,
				'login_times'     => 1,
				'create_at'       => $nowtime,
				'is_adult'        => $isAdult,
				'invite_id'       => $inviteId,
			);
			$ret      = B_DB::instance('User')->insert($filedArr);
		}
		return $ret;
	}


	/**
	 * 更新玩家信息
	 * @author chenhui on 20110608
	 * @param array $info 数组(包含userid,更新数据)
	 * @author bool
	 */
	static public function updateInfo($info) {
		$ret = false;
		if (is_array($info) && isset($info['id']) && count($info) > 1) {
			$userId = $info['id'];
			unset($info['id']);
			$ret = B_DB::instance('User')->update($info, $userId);
		}
		return $ret;
	}

	/**
	 * 删除用户数据
	 * @author huwei
	 * @return int $userId
	 */
	static public function del($userId) {
		return B_DB::instance('User')->delete($userId);
	}


	/**
	 * 删除用户基本信息key
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function delUserInfoByRedis($userId) {
		$rc  = new B_Cache_RC(T_Key::USER_INFO, $userId);
		$ret = $rc->delete();
		return $ret;
	}


	/**
	 * 获取某时间段内的注册量
	 * @author HeJunyun
	 * @param int $start
	 * @param int $end
	 */
	static public function countUser($start, $end, $consumer_id = 0) {
		$num = B_DB::instance('User')->countUser($start, $end, $consumer_id);
		return $num;
	}

}

?>