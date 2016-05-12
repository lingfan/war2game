<?php

class C_War extends C_I {
	/**
	 * @see CMarch::AOutList
	 */
	public function AGetOutForces() {
		$obj = new C_March();
		return $obj->AOutList();
	}

	/**
	 * @see CMarch::AOutInfo
	 */
	public function AGetOutInfo($marchId) {
		$obj = new C_March();
		return $obj->AOutInfo($marchId);
	}

	/**
	 * @see CMarch::AEnemyList
	 */
	public function AGetEnemyForces() {
		$obj = new C_March();
		return $obj->AEnemyList();
	}

	/**
	 * @see CMarch::AEnemyInfo
	 */
	public function AGetEnemyInfo($marchId) {
		$obj = new C_March();
		return $obj->AEnemyInfo($marchId);
	}

	/**
	 * @see CMarch::AOut
	 */
	public function AMarch($type, $defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$obj = new C_March();
		return $obj->AOut($type, $defPosStr, $heroIdList, $isAuto, $spPercent);
	}

	/**
	 * @see CFb::AAtk
	 */
	public function AAtkFB($fbStr, $heroIdList, $isAutoFight = 1) {
		$obj = new C_Fb();
		return $obj->AAtk($fbStr, $heroIdList, $isAutoFight);
	}

	/**
	 * @see CMarch::ABack
	 */
	public function ABackOutWarch($marchId = 0) {
		$obj = new C_March();
		return $obj->ABack($marchId);
	}

	/**
	 * @see CReport::AList
	 */
	public function AGetReportList($type = 1, $page = 1) {
		$obj = new C_Report();
		return $obj->AList($type, $page);
	}

	/**
	 * @see CReport::AInfo
	 */
	public function AGetReport($id) {
		$obj = new C_Report();
		return $obj->AInfo($id);
	}

	/**
	 * @see CReport::ADel
	 */
	public function ADelReport($ids) {
		$obj = new C_Report();
		return $obj->ADel($ids);
	}

	/**
	 * @see CReport::ADelAll
	 */
	public function ADelAllReport($type) {
		$obj = new C_Report();
		return $obj->ADelAll($type);
	}


	/**
	 * @see CMarch::ABaseSpeedAdd
	 */
	public function AGetMarchAddSpeed() {
		$obj = new C_March();
		return $obj->ABaseSpeedAdd();
	}

	/**
	 * @see CFb::ACampaignInfo
	 */
	public function AGetFbInfo($chapterNo = 0, $campaignNo = 0) {
		$obj = new C_Fb();
		return $obj->ACampaignInfo($chapterNo, $campaignNo);

	}

	/**
	 * @see CFb::AChapterInfo
	 */
	public function AGetFbChapter($chapterNo) {
		$obj = new C_Fb();
		return $obj->AChapterInfo($chapterNo);
	}

	/**
	 * @see CMarch::AOccupiedCity
	 */
	public function AMarchOccupiedCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$obj = new C_March();
		return $obj->AOccupiedCity($defPosStr, $heroIdList, $isAuto, $spPercent);
	}

	/**
	 * @see CMarch::AHoldCity
	 */
	public function AMarchHoldCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$obj = new C_March();
		return $obj->AHoldCity($defPosStr, $heroIdList, $isAuto, $spPercent);
	}

	/**
	 * @see CMarch::ARescueCity
	 */
	public function AMarchRescueCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$obj = new C_March();
		return $obj->ARescueCity($defPosStr, $heroIdList, $isAuto, $spPercent);
	}

}

?>