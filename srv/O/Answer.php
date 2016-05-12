<?php

class O_Answer implements O_I {
	private $_change = false;
	private $_sync = array();
	public $base = array();
	public $nowTime = 0;
	public $nowDate = 0;

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$data = array();
		if (!empty($extraInfo['answer_list'])) {
			$data = json_decode($extraInfo['answer_list'], true);
		}

		$this->nowTime = time();
		$this->nowDate = date('Ymd');

		$this->_point = isset($data[0]) ? $data[0] : 0; //积分
		$this->_date = isset($data[1]) ? $data[1] : $this->nowDate; //日期
		$this->_times = isset($data[2]) ? $data[2] : 0; //次数
		$this->_curNum = isset($data[3]) ? $data[3] : 0; //当前问题数量
		$this->_curId = isset($data[4]) ? $data[4] : 0; //当前问题编号
		$this->_endTime = isset($data[5]) ? $data[5] : 0; //结束时间

		if ($this->_date != $this->nowDate) {
			$this->_date = $this->nowDate;
			$this->_curNum = 1;
			$this->_endTime = 0;
			$this->_curId = $this->_genId();
			$this->_times = 0;
			$this->_change = true;
		}

	}


	public function info() {

		if ($this->_curNum > 0 && !empty($this->_endTime) && !empty($this->_curId) && $this->nowTime > $this->_endTime) {
			$this->_curNum += 1;
			$this->_curId = $this->_genId();
			$this->_endTime = $this->nowTime + M_Config::getVal('question_time');
			$this->_change = true;
		}

		$ret = array(
			'point' =>$this->_point,
			'date' =>$this->_date,
			'times' =>$this->_times,
			'curNum' =>$this->_curNum,
			'curId' =>$this->_curId,
			'endTime' =>$this->_endTime
		);

		return $ret;
	}

	public function decrPoint($num) {
		$this->_point -= $num;
		return $this->_point;
	}

	public function next($str) {
		$ret = 0;
		$questionInfo = M_Base::answer($this->_curId);
		if ($str == $questionInfo['result']) {
			$ret = 1;
			$this->_point += M_Config::getVal('question_point');
		}

		//下一个问题
		$num = 0;
		$endTime = 0;
		$nextId = 0;
		if ($this->_curNum < M_Config::getVal('question_num')) {
			$nextId = $this->_genId();
			$num = $this->_curNum + 1;
			$endTime = $this->nowTime + M_Config::getVal('question_time');
		}

		$this->_curNum = $num;
		$this->_endTime = $endTime;
		$this->_curId = $nextId;

		$this->_change = true;
		return $ret;
	}

	public function resetId() {
		$this->_curId = $this->_genId();
		$this->_times += 1;
		$this->_curNum = 1;
		$this->_endTime = 0;
		$this->_change = true;

		return $this->_curId;
	}

	public function start() {
		$this->_endTime = $this->nowTime + M_Config::getVal('question_time');
		return $this->_endTime;
	}

	/**
	 * @return array [积分,日期,次数,当前问题数量,当前问题编号,结束时间]
	 */
	public function get() {
		return array($this->_point, $this->_date, $this->_times, $this->_curNum, $this->_curId, $this->_endTime);
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	public function total() {
		$apcKey = T_Key::BASE_QUESTION . '_NUM';
		$info = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseQuestion')->count();
			APC::set($apcKey, $info);
		}
		return $info;
	}

	private function _genId() {
		return rand(1, $this->total());
	}
}