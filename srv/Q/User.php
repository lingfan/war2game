<?php

class Q_User extends B_DB_Dao {
	protected $_name = 'user';
	protected $_connType = 'game';
	protected $_primary = 'id';


	/**
	 * 获取威望排行榜
	 * @author HeJunyun
	 */
	public function getUserRankingsByRenown() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, nickname AS NickName, renown AS Renown, mil_rank AS MilRank, mil_medal AS MilMedal, union_id as UnionId, signature AS Signature, face_id AS FaceId, gender AS Gender
		FROM `city`
		ORDER BY renown DESC, mil_medal DESC, created_at {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 获取军功排行榜
	 * @author HeJunyun
	 */
	public function getUserRankingsByMilMedal() {
		$limit = '';
		if (M_Ranking::CITY_TOTAL_LIMIT != 0) {
			$limit = ' LIMIT ' . M_Ranking::CITY_TOTAL_LIMIT;
		}

		$sql = "
		SELECT id AS ID, nickname AS NickName, renown AS Renown, mil_rank AS MilRank, mil_medal AS MilMedal, union_id as UnionId, signature AS Signature, face_id AS FaceId, gender AS Gender
		FROM `city`
		ORDER BY mil_medal DESC, renown DESC, created_at {$limit}";

		$rows = $this->fetchAll($sql);
		return $rows;
	}

	/**
	 * 根据城市ID获取用户昵称
	 * @author HeJunyun
	 * @param int $cityId 城市ID
	 */
	public function getUserNickNameByCityId($cityId) {
		$row = $this->get($cityId);
		return $row['nickname'];
	}


	public function totalUser($start, $end, $consumer_id = 0) {
		$where = '';
		if ($consumer_id > 0) {
			$where = " AND consumer_id = '{$consumer_id}'";
		}
		$sql = "SELECT create_at FROM `user` WHERE create_at >= '{$start}' AND create_at <= '{$end}'  {$where} ORDER BY create_at ASC;";

		$rows = $this->fetchAll($sql);
		$arr = array();
		foreach ($rows as $val) {
			$day = date('Ymd', $val['create_at']);
			$arr[$day] = isset($arr[$day]) ? $arr[$day] + 1 : 1;
		}
		$newArr = array();
		foreach ($arr as $d => $n) {
			$newArr[] = array('date' => $d, 'num' => $n);
		}
		return $newArr;
	}

	public function totalUserNum($consumer_id = 0) {
		$where = array();
		if ($consumer_id > 0) {
			$where['consumer_id'] = $consumer_id;
		}
		$num = $this->count($where);
		return $num;
	}

	/**
	 * 获取某时间段内的注册量
	 * @author HeJunyun
	 * @param int $start
	 * @param int $end
	 */
	public function countUser($start, $end, $consumer_id = 0) {
		$start = $start - 1;
		$sql = "SELECT COUNT(id) AS num FROM `user` WHERE create_at > '{$start}' AND create_at < '{$end}'";
		if ($consumer_id > 0) {
			$sql .= " AND consumer_id = '{$consumer_id}'";
		}

		$row = $this->fetch($sql);
		return $row['num'];
	}

	public function loginTimes($min = 0, $max = 0) {
		$sql = "SELECT COUNT(id) AS num FROM `user` WHERE login_times >= '{$min}'";
		if ($max > 0) {
			$sql .= " AND login_times < '{$max}'";
		}
		$row = $this->fetch($sql);
		return $row['num'];
	}
}

?>