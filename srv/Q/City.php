<?php

class Q_City extends B_DB_Dao {
	protected $_name = 'city';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 获取城市信息通过坐标
	 * @param int $x
	 * @param int $y
	 * @param int $area
	 * @return array
	 */
	public function getInfoByPos($posNo) {
		$posNo = intval($posNo);
		$row = $this->getBy(array('pos_no' => $posNo));
		return $row;
	}


	/**
	 * 插入城市信息
	 * @author 胡威  at 2011/03/28
	 * @param array $info [nickname, pos_no gold, gold_grow, gold_store, food, food_grow, food_store, oil, oil_grow, oil_store, max_store, max_population]
	 * @return int/bool
	 */
	public function add($info) {
		$info['created_at'] = time();
		$id = $this->insert($info);
		return $id;
	}

	/**
	 * 检查城市名称是否存在
	 * @param string $name
	 * @return bool
	 */
	public function getCityIdCityName($nickname) {
		$row = $this->getBy(array('nickname' => $nickname));
		return isset($row['id']) ? $row['id'] : false;
	}

	public function getPage($page, $offset, $parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$whereArr[] = "{$key}='{$val}'";
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($page - 1) * $offset;
		$sql = "SELECT u.id, u.username, c.nickname, c.vip_level, c.renown, c.mil_medal, c.mil_pay, c.coupon, u.status FROM `user` AS u LEFT JOIN city AS c ON u.city_id = c.id {$where} LIMIT {$start}, {$offset}";
		$rows = $this->fetchAll($sql);
		return $rows;
	}

	public function total($parms = '') {
		$sql = "SELECT COUNT(u.id) as num FROM `user` AS u LEFT JOIN city AS c ON u.city_id = c.id";
		$row = $this->fetch($sql);
		return $row['num'];
	}

	/**
	 * 获取通关人数
	 * @author Hejunyun
	 * @param int $fbNo 关卡编号
	 */
	public function getPastFbPerson($fbNo) {
		$num = $this->count(array('last_fb_no' => array('>=', $fbNo)));
		return $num;
	}

	/**
	 * 得到创建城市日期超过$day天的新手
	 * @author duhuihui
	 * @return array 城市信息(一维数组)
	 */
	public function getExpiredNew() {
		$t = time() - T_App::NEWBE_PROTECT_TIME;
		$newbie = M_City::NEWBIE_GUARD_YES;
		$row = $this->getsBy(array('newbie' => $newbie, 'created' => array('<', $t)));
		return $row;
	}

	/**
	 * 获取某时间段内的注册量
	 * @author duhuihui
	 * @param int $start
	 * @param int $end
	 * @param int $consumer_id
	 */
	public function countCityId($start, $end, $consumer_id = 0) {
		$a1 = $start - 1;
		$a2 = $end + 1;

		$sql = "SELECT id AS city_id FROM `city`  WHERE created_at > '{$a1}' AND created_at < '{$a2}'";
		if ($consumer_id > 0) {
			$sql .= " AND consumer_id = '{$consumer_id}'";
		}
		$row = $this->fetchAll($sql);
		return $row;
	}

	/**
	 * 获取军衔等级所对应人数
	 * @author chenhui on 20130109
	 * @return array 2D
	 */
	public function getMilRankNum() {
		$sql = 'SELECT `mil_rank`,COUNT(1) AS `num` FROM `city` GROUP BY `mil_rank`';
		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 获取某时间段内的注册量
	 * @author chenhui on 20130308
	 * @param int $start
	 * @param int $end
	 * @param int $level
	 */
	public function getCityRank($start, $end, $level) {
		$sql = "SELECT `id`,`nickname`,`level`,FROM_UNIXTIME(`created_at`,'%Y-%m-%d %H:%i:%s') create_at
				FROM `city` WHERE created_at >= '{$start}' AND created_at <= '{$end}' AND `level` >= '{$level}' ORDER BY `level` DESC,created_at DESC";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

}

?>