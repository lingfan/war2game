<?php

class O_FindHero implements O_I {
	private $_data = array();
	private $_change = false;
	private $_sync = array();
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo = $objPlayer->getCityExtra();
		$data = array();
		if (!empty($extraInfo['find_hero'])) {
			$data = json_decode($extraInfo['find_hero'], true);
		}
		if (empty($data)) {
			$data = array(
				'tpl_id' => $this->_hireHeroInfo(T_Hero::HERO_BULE_LEGEND), //军官模板ID
				'flag' => 0, //状态[0未招募|1已招募]
			);
			$this->_change = true;
		}

		$this->_data = $data;
	}


	/**
	 * 获取城市数据
	 * @return array
	 */
	public function get() {
		return $this->_data;
	}


	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	public function isChange() {
		return $this->_change;
	}

	public function start($type=1) {
		$rateArr = $this->_baseRate($type);
		$heroQual = B_Utils::dice($rateArr);
		$this->_data['tpl_id'] = $this->_hireHeroInfo($heroQual);
		$this->_data['flag'] = 0;
		$this->_change = true;
		return $this->_data['tpl_id'];
	}


	private function _baseRate($type = 1) {
		$baseRate = M_Config::getVal('hero_seek_rate');

		$ret = array(
			T_Hero::HERO_BULE_LEGEND => $baseRate[$type][0],
			T_Hero::HERO_PURPLE_LEGEND => $baseRate[$type][1],
			T_Hero::HERO_RED => $baseRate[$type][2],
			T_Hero::HERO_GOLD => $baseRate[$type][3],
		);
		return $ret;
	}

	/**
	 * @param int $qual 品质
	 * @return array [军官ID, 招募概率, 招募时间]
	 */
	private function _hireHeroInfo($qual) {
		$tmp = array();
		//array(军官ID, 出现概率, 招募概率, 招募时间)
		$baseRateTime = M_Config::getVal('hero_rate_time');
		foreach ($baseRateTime[$qual] as $val) {
			$tmp[$val[0]] = $val[1];
		}
		$heroId = B_Utils::dice($tmp);
		return $heroId;

	}

	public function hire() {
		$heroId = 0;
		if (!empty($this->_data['tpl_id']) && $this->_data['flag'] == 0) {
			$heroId = M_Hero::moveTplHeroToCityHero($this->_objPlayer, $this->_data['tpl_id'], B_Logger::H_ACT_FIND);
			$this->_data['flag'] = 1;
			$this->_change = true;
		}
		return $heroId;
	}
}