<?php

/**
 * 好友接口
 */
class Buddy extends C_I {
	/**
	 * 添加好友
	 * @author
	 * @param
	 * @return
	 */
	public function AAdd($arg1, $arg2) {
		$s['a'] = $arg1;
		$s['b'] = $arg2;
		//or T_App::FAIL
		$data = $s;
		return B_Common::result($flag, $data);
	}

	/**
	 * 删除好友
	 * @author
	 * @param
	 * @return
	 */
	public function ARemove() {

	}

	/**
	 * 黑名单
	 * @author
	 * @param
	 * @return
	 */
	public function ABlock() {

	}
}