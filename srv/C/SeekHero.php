<?php

class C_SeekHero extends C_I {
	/**
	 * 寻将系统信息
	 * @author huwei
	 * @return array[ErrNo,Data]
	 */
	public function AInfo() {
		$val = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$opType = 1;
		$findInfo = $objPlayer->SeekHero()->get();
		$errNo = '';

		$cityId = $cityInfo['id'];
		if (!empty($findInfo['tpl_id'])) {
			$val = M_Hero::baseInfo($findInfo['tpl_id']);
		}

		$nextCostMilpay = $objPlayer->SeekHero()->calcCost($opType, $findInfo['find_num']);

		$armyId = !empty($val['army_id']) ? $val['army_id'] : 1;

		$data = array(
			'CityId' => $cityId,
			'NickName' => isset($val['nickname']) ? $val['nickname'] : '',
			'Gender' => isset($val['gender']) ? $val['gender'] : '',
			'Quality' => isset($val['quality']) ? $val['quality'] : '',
			'Level' => isset($val['level']) ? $val['level'] : '',
			'FaceId' => isset($val['face_id']) ? $val['face_id'] : '',
			'IsLegend' => 1,
			'Exp' => isset($val['exp']) ? $val['exp'] : '',
			'AttrLead' => isset($val['attr_lead']) ? $val['attr_lead'] : '',
			'AttrCommand' => isset($val['attr_command']) ? $val['attr_command'] : '',
			'AttrMilitary' => isset($val['attr_military']) ? $val['attr_military'] : '',
			'AttrEnergy' => isset($val['attr_energy']) ? $val['attr_energy'] : '',
			'AttrMood' => isset($val['attr_mood']) ? $val['attr_mood'] : '',
			'StatPoint' => isset($val['stat_point']) ? floor($val['stat_point']) : '',
			'GrowRate' => isset($val['grow_rate']) ? $val['grow_rate'] : '',
			'SkillSlotNum' => isset($val['skill_slot_num']) ? $val['skill_slot_num'] : '',
			'SkillSlot' => isset($val['skill_slot']) ? $val['skill_slot'] : '',
			'SkillSlot1' => isset($val['skill_slot_1']) ? $val['skill_slot_1'] : '',
			'SkillSlot2' => isset($val['skill_slot_2']) ? $val['skill_slot_2'] : '',
			'Desc' => isset($val['desc']) ? $val['desc'] : '',
			//'Detail'		=>	isset($val['detail'])?$val['detail']:'',
			'SuccRate' => $findInfo['succ_rate'],
			'HireTime' => $findInfo['hire_time'],
			'LastFindTime' => $findInfo['last_time'],
			'SuccKeepTime' => M_Formula::calcCDTime($findInfo['keep_time']),
			'TryHireTime' => M_Formula::calcCDTime($findInfo['end_time']),
			'Flag' => $findInfo['flag'],
			'TimePropsId' => $findInfo['time_props_id'],
			'RatePropsId' => $findInfo['rate_props_id'],
			'FindNum' => $findInfo['find_num'],
			//'CDTime'		=>	M_Formula::calcCDTime($findInfo['cd_time']),
			'NextCostMilpay' => $nextCostMilpay,
			'EndTime' => M_Formula::calcCDTime($findInfo['end_time']),
			'ArmyId' => $armyId,
			'WeaponId' => T_Hero::$army2weapon[$armyId],
		);
		return B_Common::result($errNo, $data);
	}

	/**
	 * 寻将系统中寻将操作
	 * @author huwei
	 * @return array[ErrNo,Data]
	 */
	public function AFind() {
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = $cityInfo['id'];
		$opType = 1;
		$objSeekHero = $objPlayer->SeekHero();
		$findInfo = $objSeekHero->get();

		$pirce = $objSeekHero->calcCost($opType, $findInfo['find_num']);
		Logger::debug(array(__METHOD__, $findInfo['find_num'], $pirce));
		$objPlayer->City()->mil_pay -= $pirce;

		$err = '';
		if (!$objSeekHero->canFind()) {
			$err = T_ErrNo::HERO_SEEK_FLAG_ERR;
		} else if ($objPlayer->City()->mil_pay < 0) {
			$err = T_ErrNo::NO_ENOUGH_MILIPAY;
		}

		$errNo = $err;
		if (empty($err)) {
			$heroTplId = $objSeekHero->find();

			$heroInfo = M_Hero::baseInfo($heroTplId);
			$errNo = T_ErrNo::HERO_TPL_NO_DATA;
			if (!empty($heroInfo)) {
				$findInfo = $objSeekHero->get();
				$objPlayer->Quest()->check('hero_find', array('num' => 1));
				$objPlayer->save();

				$objPlayer->Log()->expense(T_App::MILPAY, $pirce, B_Log_Trade::E_FindHero, $heroInfo['id']);

				$errNo = T_ErrNo::HERO_FIND_FAIL;

				if (is_array($heroInfo) && !empty($heroInfo)) {
					$nextCostMilpay = $objSeekHero->calcCost($opType, $findInfo['find_num']);

					$armyId = !empty($heroInfo['army_id']) ? $heroInfo['army_id'] : 1;
					$data = array(
						'CityId' => $cityId,
						'NickName' => $heroInfo['nickname'],
						'FaceId' => $heroInfo['face_id'],
						'Gender' => $heroInfo['gender'],
						'Quality' => $heroInfo['quality'],
						'Level' => $heroInfo['level'],
						'IsLegend' => 1,
						'AttrLead' => $heroInfo['attr_lead'],
						'AttrCommand' => $heroInfo['attr_command'],
						'AttrMilitary' => $heroInfo['attr_military'],
						'AttrEnergy' => $heroInfo['attr_energy'],
						'AttrMood' => $heroInfo['attr_mood'],
						'SkillSlotNum' => $heroInfo['skill_slot_num'],
						'SkillSlot' => $heroInfo['skill_slot'],
						'SkillSlot1' => $heroInfo['skill_slot_1'],
						'SkillSlot2' => $heroInfo['skill_slot_2'],
						'Desc' => !empty($heroInfo['desc']) ? $heroInfo['desc'] : '',
						'Detail' => !empty($heroInfo['detail']) ? $heroInfo['detail'] : '',
						'GrowRate' => $heroInfo['grow_rate'],
						'SuccRate' => $findInfo['succ_rate'],
						'HireTime' => $findInfo['hire_time'],
						'FindNum' => $findInfo['find_num'] + 1,
						//'CDTime'		=>	M_Formula::calcCDTime($findInfo['cd_time']),
						'NextCostMilpay' => $nextCostMilpay,
						'LastFindTime' => $findInfo['last_time'],
						'SuccKeepTime' => $findInfo['keep_time'],
						'Flag' => T_Hero::FIND_FLAG_INIT,
						'TimePropsId' => $findInfo['time_props_id'],
						'RatePropsId' => $findInfo['rate_props_id'],
						'EndTime' => M_Formula::calcCDTime($findInfo['end_time']),
						'ArmyId' => $armyId,
						'WeaponId' => T_Hero::$army2weapon[$armyId],
					);
					$errNo = '';
				}
			}
		}
		return B_Common::result($errNo, $data);
	}


	public function ATryHire($ratePropsId = 0, $timePropsId = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$timePropsId = intval($timePropsId);
		$ratePropsId = intval($ratePropsId);

		$objPlayer = $this->objPlayer;
		$bFind = $objPlayer->SeekHero()->find();

		if ($bFind) {
			$err = '';

			if (!empty($timePropsId) || !empty($ratePropsId)) {
				if (!empty($timePropsId) && $objPlayer->Pack()->decrNumByPropId($timePropsId, 1)) {
					$err = T_ErrNo::PROPS_NOT_ENOUGH;
				} else if (!empty($ratePropsId) && $objPlayer->Pack()->decrNumByPropId($ratePropsId, 1)) {
					$err = T_ErrNo::PROPS_NOT_ENOUGH;
				}
			}

			if (empty($err)) {
				$objPlayer->SeekHero()->start($timePropsId, $ratePropsId);

				$objPlayer->Quest()->check('hero_hire_s', array('num' => 1));

				$timePropsId > 0 && $objPlayer->Pack()->decrNumByPropId($timePropsId, 1);
				$ratePropsId > 0 && $objPlayer->Pack()->decrNumByPropId($ratePropsId, 1);

				$errNo = '';

				$ret = $objPlayer->SeekHero()->get();

				$objPlayer->save();

				$data = array(
					'EndTime' => M_Formula::calcCDTime($ret['end_time']),
					'Flag' => T_Hero::FIND_FLAG_PROC, //更新标志为进行中
					'HireTime' => $ret['hire_time'],
					'SuccRate' => $ret['succ_rate'],
				);
			} else {
				$errNo = $err;
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 寻将系统中招募状态为成功的英雄
	 * @author huwei
	 * @return array[ErrNo,Data]
	 */
	public function ASuccHire() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;

		$cityId = $objPlayer->City()->id; //当前城市ID
		$cityLv = $objPlayer->City()->level; //当前城市等级

		$findInfo = $objPlayer->SeekHero()->get();
		//检测城市英雄是否满
		$num = M_Hero::totalCityHeroNum($cityId);
		//计算能拥有几个英雄
		$heroNumLimit = M_Formula::calcHeroNumLimit($cityLv);

		//检测寻找到的英雄是否成功
		if ($num < $heroNumLimit) {
			if (!empty($findInfo) && $findInfo['flag'] == T_Hero::FIND_FLAG_SUCC) {
				$ret = M_Hero::moveTplHeroToCityHero($cityId, $findInfo['tpl_id'], Logger::H_ACT_FIND);
				if ($ret) {
					$objPlayer->SeekHero()->reset();
					$objPlayer->save();
					$errNo = '';

				}
			} else {
				$errNo = T_ErrNo::HERO_SEEK_FAIL;
			}
		} else {
			$errNo = T_ErrNo::HERO_NUM_FULL_FAIL;
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 寻将系统中 放弃成功寻找到得英雄
	 * @author huwei
	 * @return array[ErrNo,Data]
	 */
	public function ACancel() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$findInfo = $objPlayer->SeekHero()->get();

		$cityId = $cityInfo['id'];
		//寻将系统中无英雄
		if (!empty($findInfo['tpl_id']) &&
			in_array($findInfo['flag'], array(T_Hero::FIND_FLAG_SUCC, T_Hero::FIND_FLAG_PROC))
		) {
			$objPlayer->SeekHero()->reset();

			$ret = $objPlayer->save();
			if ($ret) {
				$data = array(
					'Flag' => T_Hero::FIND_FLAG_FAIL,
					'EndTime' => 0,
				);

				$errNo = '';
			}
		} else {
			$errNo = T_ErrNo::HERO_SEEK_FAIL;
			$msg = array(__METHOD__, $cityId, T_ErrNo::HERO_SEEK_FAIL);
			Logger::debug($msg);
		}


		return B_Common::result($errNo, $data);

	}

	public function ADraw($type = 1) {
		$val = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$opType = 1;
		$findInfo = $objPlayer->SeekHero()->get();
		$errNo = '';

		if (!empty($findInfo['tpl_id'])) {
			$val = M_Hero::baseInfo($findInfo['tpl_id']);
		}

		$nextCostMilpay = $objPlayer->SeekHero()->calcCost($opType, $findInfo['find_num']);

		$armyId = !empty($val['army_id']) ? $val['army_id'] : 1;

		$data = array(
			'CityId' => $cityInfo['id'],
			'NickName' => isset($val['nickname']) ? $val['nickname'] : '',
			'Gender' => isset($val['gender']) ? $val['gender'] : '',
			'Quality' => isset($val['quality']) ? $val['quality'] : '',
			'Level' => isset($val['level']) ? $val['level'] : '',
			'FaceId' => isset($val['face_id']) ? $val['face_id'] : '',
			'Exp' => isset($val['exp']) ? $val['exp'] : '',
			'AttrLead' => isset($val['attr_lead']) ? $val['attr_lead'] : '',
			'AttrCommand' => isset($val['attr_command']) ? $val['attr_command'] : '',
			'AttrMilitary' => isset($val['attr_military']) ? $val['attr_military'] : '',
			'AttrEnergy' => isset($val['attr_energy']) ? $val['attr_energy'] : '',
			'AttrMood' => isset($val['attr_mood']) ? $val['attr_mood'] : '',
			'GrowRate' => isset($val['grow_rate']) ? $val['grow_rate'] : '',
			'SkillSlotNum' => isset($val['skill_slot_num']) ? $val['skill_slot_num'] : '',
			'SkillSlot' => isset($val['skill_slot']) ? $val['skill_slot'] : '',
			'SkillSlot1' => isset($val['skill_slot_1']) ? $val['skill_slot_1'] : '',
			'SkillSlot2' => isset($val['skill_slot_2']) ? $val['skill_slot_2'] : '',
			'Desc' => isset($val['desc']) ? $val['desc'] : '',
			'Flag' => $findInfo['flag'],
			'NextCostMilpay' => $nextCostMilpay,
			'ArmyId' => $armyId,
			'WeaponId' => T_Hero::$army2weapon[$armyId],
		);
		return B_Common::result($errNo, $data);
	}


	/**
	 * 寻将开始
	 * @param int $type
	 * @return array
	 */
	public function AStart($type = 0) {
		$data = array();
		$err = '';
		if ($type) {
			if (!isset(T_Hero::$findHeroType[$type])) {
				$err = T_ErrNo::HERO_FIND_FAIL;
			} else if (!$this->objPlayer->Pack()->decrNumByPropId(T_Hero::$findHeroType[$type])) {
				$err = T_ErrNo::PROPS_NOT_ENOUGH;
			}

			if (empty($err)) {
				$this->objPlayer->FindHero()->start($type);
			}
		}

		$errNo = $err;
		if (empty($err)) {
			$findInfo = $this->objPlayer->FindHero()->get();
			$heroInfo = array();
			if (!empty($findInfo['tpl_id'])) {
				$val = M_Hero::baseInfo($findInfo['tpl_id']);
				$armyId = $val['army_id'];
				$heroInfo = array(
					'NickName' => isset($val['nickname']) ? $val['nickname'] : '',
					'Gender' => isset($val['gender']) ? $val['gender'] : '',
					'Quality' => isset($val['quality']) ? $val['quality'] : '',
					'Level' => isset($val['level']) ? $val['level'] : '',
					'FaceId' => isset($val['face_id']) ? $val['face_id'] : '',
					'AttrLead' => isset($val['attr_lead']) ? $val['attr_lead'] : '',
					'AttrCommand' => isset($val['attr_command']) ? $val['attr_command'] : '',
					'AttrMilitary' => isset($val['attr_military']) ? $val['attr_military'] : '',
					'AttrEnergy' => isset($val['attr_energy']) ? $val['attr_energy'] : '',
					'AttrMood' => isset($val['attr_mood']) ? $val['attr_mood'] : '',
					'GrowRate' => isset($val['grow_rate']) ? $val['grow_rate'] : '',
					'SkillSlotNum' => isset($val['skill_slot_num']) ? $val['skill_slot_num'] : '',
					'SkillSlot' => isset($val['skill_slot']) ? $val['skill_slot'] : '',
					'SkillSlot1' => isset($val['skill_slot_1']) ? $val['skill_slot_1'] : '',
					'SkillSlot2' => isset($val['skill_slot_2']) ? $val['skill_slot_2'] : '',
					'Desc' => isset($val['desc']) ? $val['desc'] : '',
					'ArmyId' => $armyId,
					'WeaponId' => T_Hero::$army2weapon[$armyId],

				);
			}

			$errNo = '';
			$data = array(
				'HeroInfo' => $heroInfo,
				'Flag' => $findInfo['flag'],
				'Props' => array(
					1 => $this->objPlayer->Pack()->getNumByPropsId(T_Hero::$findHeroType[1]),
					2 => $this->objPlayer->Pack()->getNumByPropsId(T_Hero::$findHeroType[2]),
					3 => $this->objPlayer->Pack()->getNumByPropsId(T_Hero::$findHeroType[3])
				),
			);

			$this->objPlayer->save();
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 招募
	 * @return array
	 */
	public function AHire() {
		$data = array();

		$cityId = $this->objPlayer->City()->id; //当前城市ID
		$cityLv = $this->objPlayer->City()->level; //当前城市等级

		//检测城市英雄是否满
		$num = M_Hero::totalCityHeroNum($cityId);
		//计算能拥有几个英雄
		$heroNumLimit = M_Formula::calcHeroNumLimit($cityLv);

		//检测寻找到的英雄是否成功
		$errNo = T_ErrNo::HERO_NUM_FULL_FAIL;
		if ($num < $heroNumLimit) {
			$ret = $this->objPlayer->FindHero()->hire();
			$this->objPlayer->save();
			$errNo = T_ErrNo::HERO_HIRE_ERR;
			if ($ret) {
				$errNo = '';
				$data['HeroId'] = $ret;
				$data['Flag'] = 1;
			}

		}


		return B_Common::result($errNo, $data);
	}
}

?>