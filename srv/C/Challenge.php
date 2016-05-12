<?php

/**
 * 挑战
 */
class C_Challenge extends C_I {
	/**
	 * 排名信息
	 *
	 */
	public function AInfo() {
		$errNo = '';
		$data = array(
			'fight' => 11000,
			'max_rank' => 1,
			'cur_rank' => 1,
			'max_win_num' => 100,
			'cur_win_num' => 100,
			'next_award_time' => time() + 180,
			'rank_award_list' => array(
				array('rank' => 100, 'award_data' => array()),
				array('rank' => 200, 'award_data' => array()),
				array('rank' => 300, 'award_data' => array()),
				array('rank' => 500, 'award_data' => array()),
				array('rank' => 1000, 'award_data' => array()),
			),
			'city_list' => array(
				array('nickname' => 'xxx1', 'rank' => 1001, 'id' => 1),
				array('nickname' => 'xxx2', 'rank' => 1002, 'id' => 2),
				array('nickname' => 'xxx3', 'rank' => 1003, 'id' => 3),
				array('nickname' => 'xxx4', 'rank' => 1004, 'id' => 4),
				array('nickname' => 'xxx5', 'rank' => 1005, 'id' => 5),
			),
			'log' => array(
				array('title' => 'xxxx', 'id' => 1),
				array('title' => 'xxxx', 'id' => 1),
				array('title' => 'xxxx', 'id' => 1),
				array('title' => 'xxxx', 'id' => 1),
			),
		);

		return B_Common::result($errNo, $data);
	}

	/**
	 * 挑战
	 *
	 */
	public function ADo($rankNo = 0) {

	}

	/**
	 * 领取奖励
	 *
	 */
	public function AAward() {
	}
}
