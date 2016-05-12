<?php

class C_Rank extends C_I {
	/**
	 * 查看排行榜
	 * @author HeJunyun
	 */
	public function AGetRankings($rankingsType = 1, $page = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$page = intval($page);
		$data = array();
		if (isset(M_Ranking::$funcArr[$rankingsType]) && $page > 0) {
			//检查用户是否存在
			$objPlayer = $this->objPlayer;
			$cityInfo = $objPlayer->getCityBase();
			if ($cityInfo['mil_medal'] >= M_Ranking::$getRankMinMilMedal) {
				$func = M_Ranking::$funcArr[$rankingsType];
				$data = M_Ranking::$func($page);

				$errNo = '';
			} else {
				//军功小于1000，不能查看排行榜
				$errNo = T_ErrNo::NO_ENOUGH_MILMEDAL;
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 根据用户（城市）昵称获取该用户威望排名
	 * @author HeJunyun
	 * @param string $nickName 用户（城市）昵称
	 */
	public function AGetRenownRankByNickName($nickName = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($cityInfo['mil_medal'] >= M_Ranking::$getRankMinMilMedal) {
			$id = M_City::getCityIdByNickName($nickName);
			if ($id > 0) {
				$rank = M_Ranking::getRenownRankingsByCityId($id);
				if ($rank) {
					$errNo = '';
					$data = $rank;
				} else {
					$errNo = T_ErrNo::USER_NOTIN_RANKING; //不在排名内
				}
			} else {
				$errNo = T_ErrNo::USER_NO_EXIST; //该用户不存在
			}
		} else {
			//军功小于1000，不能查看排行榜
			$errNo = T_ErrNo::NO_ENOUGH_MILMEDAL;
		}


		return B_Common::result($errNo, $data);
	}


	/**
	 * 根据用户（城市）昵称获取该用户军功排名
	 * @author HeJunyun
	 * @param string $nickName 用户（城市）昵称
	 */
	public function AGetWarexpRankByNickName($nickName = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($cityInfo['mil_medal'] >= M_Ranking::$getRankMinMilMedal) {
			$id = M_City::getCityIdByNickName($nickName);
			if ($id > 0) {
				$rank = M_Ranking::getMilmedalRankingsByCityId($id);
				if ($rank) {

					$errNo = '';
					$data = $rank;
				} else {
					$errNo = T_ErrNo::USER_NOTIN_RANKING; //不在排名内
				}
			} else {
				$errNo = T_ErrNo::USER_NO_EXIST; //该用户不存在
			}
		} else {
			//军功小于1000，不能查看排行榜
			$errNo = T_ErrNo::NO_ENOUGH_MILMEDAL;
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 根据联盟名称获取该联盟排名
	 * @author HeJunyun
	 * @param string $name 联盟名称
	 */
	public function AGetUnionRankByUnionName($name = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($cityInfo['mil_medal'] >= M_Ranking::$getRankMinMilMedal) {
			$unionInfo = M_Union::getUnionByName($name);

			if (!empty($unionInfo['id'])) {
				$rank = isset($unionInfo['rank']) ? $unionInfo['rank'] : 0;
				if ($rank) {

					$errNo = '';
					$data = $rank;
				} else {
					$errNo = T_ErrNo::USER_NOTIN_RANKING; //不在排名内
				}
			} else {
				$errNo = T_ErrNo::UNION_NOT_EXIST; //不存在
			}
		} else {
			//军功小于1000，不能查看排行榜
			$errNo = T_ErrNo::NO_ENOUGH_MILMEDAL;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 根据用户（城市）昵称获取该用户战绩值排名
	 * @author HeJunyun
	 * @param string $nickName 用户（城市）昵称
	 */
	public function AGetRecordRankByNickName($nickName = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$id = M_City::getCityIdByNickName($nickName);
		if ($id > 0) {

			$rank = M_Ranking::getRecordRankingsByCityId($id);
			if (!empty($rank) && $rank < 200) {

				$errNo = '';
				$data = $rank;
			} else {
				$errNo = T_ErrNo::USER_NOTIN_RANKING; //不在排名内
			}
		} else {
			$errNo = T_ErrNo::USER_NO_EXIST; //该用户不存在
		}

		return B_Common::result($errNo, $data);
	}

	public function APoint($page = 1, $len = 9) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$max = 50;
		$ret = M_Ranking::syncPoint($max, $len);

		$flag = T_App::SUCC;
		$errNo = '';
		$data['list'] = isset($ret['page'][$page]) ? $ret['page'][$page] : array();
		$data['total'] = $max;
		$data['page'] = $page;
		$data['len'] = $len;


		return B_Common::result($errNo, $data);
	}

	public function APointByNickname($nick = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$rank = 0;
		$max = 50;
		$ret = M_Ranking::syncPoint($max, 9);
		if (!empty($nick)) {
			$id = M_City::getCityIdByNickName($nick);
			if (!empty($id)) {
				if (isset($ret['no'][$id])) {
					$flag = T_App::SUCC;
					$errNo = '';
					$data = $ret['no'][$id];
				} else {
					$errNo = T_ErrNo::USER_NOTIN_RANKING; //不在排名内
				}
			} else {
				$errNo = T_ErrNo::USER_NO_EXIST; //该用户不存在
			}
		}

		return B_Common::result($errNo, $data);
	}

}

?>