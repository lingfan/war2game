<?php

/**
 * 所有同步时间
 * @author chenhui on 20110714
 */
class C_Sync extends C_I {
	/**
	 * 同步建筑CD时间
	 * @author chenhui on 20110714
	 * @param int $idx 索引(从1开始)
	 * @return int CD剩余秒数
	 */
	public function ABuildCD($idx) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$arr_cd_time = json_decode($cityInfo['cd_build'], true);
		$idx = intval($idx) - 1; //索引从1开始
		if (isset($arr_cd_time[$idx])) {
			//$arr_cd_time[$idx] = is_numeric($arr_cd_time[$idx]) ? $arr_cd_time[$idx].'_'.'1' : $arr_cd_time[$idx];//容错处理
			$arrT = explode('_', $arr_cd_time[$idx]);
			$cd_time_left = M_Formula::calcCDTime($arrT[0]);
			$fT = ($cd_time_left < 1) ? T_App::ADDUP_CAN : $arrT[1];
			$data = array($cd_time_left, $fT);

			$errNo = '';

			M_City::syncCDBuild2Front($cityInfo['id'], $cityInfo['cd_build'], $cityInfo['cd_build_num']); //同步建筑CD时间至前端接口整体
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步科技升级CD时间
	 * @author chenhui on 20110714
	 * @return int CD剩余秒数
	 */
	public function ATechCD() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = M_Formula::calcTechCDTime($cityInfo['cd_tech'], $cityInfo['cd_tech_num']);

		$errNo = '';
		return B_Common::result($errNo, $data);
	}


	/**
	 * 同步武器研究CD时间
	 * @author chenhui on 20110714
	 * @return int CD剩余秒数
	 */
	public function AWeaponCD() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$arrT = explode('_', $cityInfo['cd_weapon']);
		$cd_time_left = M_Formula::calcCDTime($arrT[0]);
		$fT = ($cd_time_left < 1) ? T_App::ADDUP_CAN : $arrT[1];
		$data = array($cd_time_left, $fT);

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步解救CD时间
	 * @author duhuihui on 20121031
	 * @return int CD剩余秒数
	 */
	public function ARescueCD() {
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE);
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步 副本CD时间
	 * @author huwei on 20110714
	 * @return int CD剩余秒数
	 */
	public function AFbCD() {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$data = $objPlayer->CD()->toFront(O_CD::TYPE_FB);
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步招募结束时间
	 * @author chenhui on 20110811
	 * @return int CD剩余秒数
	 */
	public function ASeekEndTime() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$seekInfo = M_Hero::getSeekInfo($cityInfo['id']);
		$cd_time = $seekInfo['end_time'];
		$cd_time_left = M_Formula::calcCDTime($cd_time);
		$data = array($cd_time_left);

		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步招募成功后保留时间
	 * @author chenhui on 20110811
	 * @return int CD剩余秒数
	 */
	public function ASeekSuccKeepTime() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$seekInfo = M_Hero::getSeekInfo($cityInfo['id']);
		$cd_time = $seekInfo['succ_keep_time'];
		$cd_time_left = M_Formula::calcCDTime($cd_time);
		$data = array($cd_time_left);

		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 英雄学院更新剩余时间
	 * @author chenhui on 20110714
	 * @return int 剩余秒数
	 */
	public function AHeroRefresh() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$list = M_Hero::getHeroCollegeList($cityInfo['id']);
		$cd_time_left = M_Formula::calcCDTime($list['time']);
		$data = array($cd_time_left);

		$errNo = '';
		return B_Common::result($errNo, $data);
	}

	/**
	 * 寻将剩余CD时间
	 * @author chenhui on 20110714
	 * @return int 剩余秒数
	 */
	public function AHeroFind() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$seekInfo = M_Hero::getSeekInfo($cityInfo['id']);
		if (isset($seekInfo['end_time'])) {
			$cd_time = $seekInfo['end_time'];
			$cd_time_left = M_Formula::calcCDTime($cd_time);

			$data = array($cd_time_left);

			$errNo = '';
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * @see CSync::ABuildCD
	 */
	public function ASyncBuildCD($idx) {
		$obj = new C_Sync();
		return $obj->ABuildCD($idx);
	}

	/**
	 * @see CSync::ATechCD
	 */
	public function ASyncTechCD() {
		$obj = new C_Sync();
		return $obj->ATechCD();
	}

	/**
	 * @see CSync::AWeaponCD
	 */
	public function ASyncWeaponCD() {
		$obj = new C_Sync();
		return $obj->AWeaponCD();
	}

	/**
	 * @see CSync::ATaxCD
	 */
	public function ASyncTaxCD() {
		$obj = new C_Sync();
		return $obj->ATaxCD();
	}

	/**
	 * @see CSync::ARescueCD
	 */
	public function ASyncRescueCD() {
		$obj = new C_Sync();
		return $obj->ARescueCD();
	}

	/**
	 * @see CSync::AFBCD
	 */
	public function ASyncFBCD() {
		$obj = new C_Sync();
		return $obj->AFbCD();
	}

	/**
	 * @see CSync::ASeekSuccKeepTime
	 */
	public function ASyncSuccKeepTime() {
		$obj = new C_Sync();
		return $obj->ASeekSuccKeepTime();
	}

	/**
	 * @see CSync::ASeekEndTime
	 */
	public function ASyncHFEndtime() {
		$obj = new C_Sync();
		return $obj->ASeekEndTime();
	}
}

?>