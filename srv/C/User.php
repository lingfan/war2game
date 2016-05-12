<?php

/**
 * 用户控制器
 */
class C_User extends C_I {
	/**
	 * 用户信息
	 * @author huwei
	 * @return array(ErrNo,Data)
	 */
	public function AInfo() {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$cityInfo = $this->objPlayer->getCityBase();

		$userId =$this->objPlayer->getId();

		$errNo = '';
		$data = array(
			'UserId' => $userId,
			'CityId' => isset($cityInfo['id'])?$cityInfo['id']:0,
			'UserName' => isset($cityInfo['username']) ? $cityInfo['username'] : '',
			'UserNameUnique' => isset($cityInfo['username']) ? $cityInfo['username'] : '',
			'NickName' => isset($cityInfo['nickname']) ? $cityInfo['nickname'] : '',
			'FaceId' => isset($cityInfo['face_id']) ? $cityInfo['face_id'] : 1,
			'Gender' => isset($cityInfo['gender']) ? $cityInfo['gender'] : 1,
			'IsAdult' => isset($cityInfo['is_adult']) ? $cityInfo['is_adult'] : 0,
			'OnlineTime' => isset($cityInfo['online_time']) ? $cityInfo['online_time'] : 0,
			'JoinTime' => isset($cityInfo['create_at']) ? $cityInfo['create_at'] : 0,
			'NewGuideStep' => isset($cityInfo['new_guide_step']) ? $cityInfo['new_guide_step'] : 0,
			'LastVisitTime' => isset($cityInfo['last_visit_time']) ? $cityInfo['last_visit_time'] : 0,
		);

		return B_Common::result($errNo, $data);
	}

	/**
	 * 更新新手步骤
	 * @author huwei on 20111114
	 * @param int $times
	 */
	public function AUpGuide($times = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$cityId = $this->objPlayer->getId();
		$times = min($times, 1000);
		if (!empty($cityId) && $times > $this->objPlayer->City()->new_guide_step) {

			$this->objPlayer->City()->new_guide_step = $times;
			$ret = $this->objPlayer->save();
			if ($ret) {
				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}


	/**
	 * @see CRank::AGetRankings
	 */
	public function AGetRankings($rankingsType = 1, $page = 1) {
		$obj = new C_Rank();
		return $obj->AGetRankings($rankingsType, $page);
	}

	/**
	 * @see CRank::AGetRenownRankByNickName
	 */
	public function AGetRenownRankByNickName($nickName = '') {
		$obj = new C_Rank();
		return $obj->AGetRenownRankByNickName($nickName);
	}


	/**
	 * @see CRank::AGetWarexpRankByNickName
	 */
	public function AGetMilmedalRankByNickName($nickName = '') {
		$obj = new C_Rank();
		return $obj->AGetWarexpRankByNickName($nickName);
	}

	/**
	 * @see CRank::AGetUnionRankByUnionName
	 */
	public function AGetUnionRankByUnionName($name = '') {
		$obj = new C_Rank();
		return $obj->AGetUnionRankByUnionName($name);
	}

	/**
	 * @see CRank::AGetUnionRankByUnionName
	 */
	public function AGetRecordRankByNickName($name = '') {
		$obj = new C_Rank();
		return $obj->AGetRecordRankByNickName($name);
	}
	//
}

?>