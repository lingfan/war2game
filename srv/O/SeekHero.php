<?php

class O_SeekHero implements O_I {
	private $_data = array();
	private $_now = 0;
	private $_change = false;
	private $_sync = array();

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$data = array();
		if (!empty($extraInfo['seek_hero'])) {
			$data = json_decode($extraInfo['seek_hero'], true);
		}

		$this->_now = time();
		$this->_nowDate = date('Ymd');
		if (empty($data) ||
			(!empty($data['tpl_id']) && T_Hero::FIND_FLAG_SUCC == $data['flag'] && $data['keep_time'] < $this->_now)
		) {
			$data = array(
				'tpl_id' => 0,
				'hire_time' => 0,
				'succ_rate' => 0,
				'time_props_id' => 0,
				'rate_props_id' => 0,
				'keep_time' => 0,
				'end_time' => 0,
				'find_num' => 0,
				'flag' => 0,
				'last_time' => $this->_nowDate,
			);
			$this->_change = true;
		}

		$this->base['hero_rate_time'] = M_Config::getVal('hero_rate_time');
		$this->base['hero_seek_cost'] = M_Config::getVal('hero_seek_cost');
		$this->base['hero_seek_rate'] = M_Config::getVal('hero_seek_rate');


		if (!empty($data['end_time']) && $this->_now < $data['end_time']) { //如果没到期 则状态为进行中
			$data['flag'] = T_Hero::FIND_FLAG_PROC;
			$data['keep_time'] = 0;
			$this->_change = true;
		}

		if ($this->_nowDate != $data['last_time']) {
			$data['find_num'] = 0;
			$data['last_time'] = $this->_nowDate;
			$this->_change = true;
		}

		$this->_data = $data;
	}

	public function get() {
		return $this->_data;
	}


	public function reset() {
		$this->_data['tpl_id'] = 0;
		$this->_data['hire_time'] = 0;
		$this->_data['succ_rate'] = 0;
		$this->_data['time_props_id'] = 0;
		$this->_data['rate_props_id'] = 0;
		$this->_data['start_time'] = 0;
		$this->_data['end_time'] = 0;
		$this->_data['keep_time'] = 0;
		$this->_data['flag'] = 0;

		$this->_change = true;
	}

	public function canFind() {
		$ret = false;
		$allowFlag = array(T_Hero::FIND_FLAG_INIT, T_Hero::FIND_FLAG_FAIL);
		if (in_array($this->_data['flag'], $allowFlag)) {
			$ret = true;
		}
		return $ret;
	}

	public function find($type = 1) {
		$ret = 0;
		if (T_Hero::FIND_FLAG_INIT == $this->_data['flag']) {
			$rateArr = $this->_baseRate($type);

			$heroQual = B_Utils::dice($rateArr);
			list($heroId, $hireRate, $hireTime) = $this->_baseRateTime($heroQual);
			$this->_data['tpl_id'] = $heroId;
			$this->_data['hire_time'] = $hireTime;
			$this->_data['succ_rate'] = $hireRate;
			$this->_data['flag'] = T_Hero::FIND_FLAG_INIT;
			$this->_data['find_num'] += 1;
			$ret = $heroId;


			$this->_change = true;
		}
		return $ret;

	}

	public function start($timePropsId, $ratePropsId) {

		//获取道具减少时间(百分比)
		$dercTime = M_Props::getPropsItemVal($timePropsId, 'FIND_DECR_TIME');
		//获取道具加成成功率
		$addRate = M_Props::getPropsItemVal($ratePropsId, 'FIND_INCR_SUCC');

		//计算招募时间(百分比)
		$hireTime = M_Formula::calcFindHeroTime($this->_data['hire_time'], $dercTime);
		//计算招募成功率
		$succRate = M_Formula::calcFindHeroRate($this->_data['succ_rate'], $addRate);

		$bSucc = B_Utils::odds($succRate);
		$keepTime = $bSucc ? ($this->_now + $hireTime + M_Config::getVal('hero_succ_keep_time') * T_App::ONE_HOUR) : 0;

		$this->_data['time_props_id'] = $timePropsId;
		$this->_data['rate_props_id'] = $ratePropsId;
		$this->_data['hire_time'] = $hireTime;
		$this->_data['succ_rate'] = $succRate;
		$this->_data['start_time'] = $this->_now;
		$this->_data['end_time'] = $this->_now + $hireTime;
		$this->_data['flag'] = $bSucc ? T_Hero::FIND_FLAG_SUCC : T_Hero::FIND_FLAG_FAIL;
		$this->_data['keep_time'] = $keepTime;

		$this->_change = true;

	}

	public function calcCost($type = 1, $num) {
		$ret = M_Formula::calcStepCost($this->_baseCost($type), $num);
		return $ret;
	}

	private function _baseRate($type = 1) {
		$ret = array(
			T_Hero::HERO_BULE_LEGEND => $this->base['hero_seek_rate'][$type][0],
			T_Hero::HERO_PURPLE_LEGEND => $this->base['hero_seek_rate'][$type][1],
			T_Hero::HERO_RED => $this->base['hero_seek_rate'][$type][2],
			T_Hero::HERO_GOLD => $this->base['hero_seek_rate'][$type][3],
		);
		return $ret;
	}

	private function _baseCost($type = 1) {
		$ret = $this->base['hero_seek_cost'][$type];
		return $ret;
	}

	/**
	 *
	 *
	 * @param int $qual 品质
	 * @return array [军官ID, 招募概率, 招募时间]
	 */
	private function _baseRateTime($qual) {
		$tmpR = $tmp = array();
		//array(军官ID, 出现概率, 招募概率, 招募时间)
		$list = $this->base['hero_rate_time'][$qual];

		foreach ($list as $val) {
			$tmpR[$val[0]] = $val[1];
			$tmp[$val[0]] = $val;
		}
		$heroId = B_Utils::dice($tmpR);
		return array($tmp[$heroId][0], $tmp[$heroId][2], $tmp[$heroId][3]);

	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}
}