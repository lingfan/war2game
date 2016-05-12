<?php

class C_Skill extends C_I {
	/**
	 * 学习技能
	 * @author HeJunyun
	 * @param int $heroId 英雄ID
	 * @param int $sLevel 学习等级[1初级学习 2中级学习 3高级学习]
	 * @param int $payType 付费类型(1军饷,2礼券)
	 */
	public function ALearn($heroId, $sLevel, $payType = 1) {

		$errNo = T_ErrNo::ERR_ACTION;
		$heroId = intval($heroId);
		$sLevel = intval($sLevel);
		$payType = ($payType == T_App::COUPON) ? T_App::COUPON : T_App::MILPAY;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$data = array();
		if ($heroId > 0 && in_array($sLevel, array(M_Skill::LEARN_PRIMARY, M_Skill::LEARN_MIDDLE, M_Skill::LEARN_SENIOR))
		) {
			$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);

			if (isset($heroInfo['id'])) {
				$heroArmyNumAdd = M_Hero::heroArmyNumAdd($cityInfo['id'], $cityInfo['union_id']);

				$canLearn = false;
				if ($heroInfo['skill_slot_num'] == 1 && !$heroInfo['skill_slot_1']) {
					$canLearn = true;
				}
				if ($heroInfo['skill_slot_num'] == 2 && (!$heroInfo['skill_slot_1'] || !$heroInfo['skill_slot_2'])) {
					$canLearn = true;
				}

				$cost = M_Skill::$learnCost[$sLevel];

				if ($sLevel == M_Skill::LEARN_PRIMARY && $payType == T_App::COUPON) {
					$money = $cityInfo['coupon'];
				} else {
					$money = $cityInfo['mil_pay'];
				}

				$err = '';
				if (!$canLearn) {
					$err = T_ErrNo::SKILL_FULL; //技能已满
				} else if ($money < $cost) {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY; //军饷或礼券不足
				}

				if (empty($err)) {
					$skillId = M_Skill::getRandSkillId($sLevel);
					$bCost = $objPlayer->City()->decrCurrency($payType, $cost, B_Log_Trade::E_LearnSkill, $skillId);
					if ($bCost) {
						$learnSkillRate = M_Config::getVal('hero_learn_skill_rate');
						$isSucc = B_Utils::odds($learnSkillRate); //学习技能成功率
						$data = array(
							'IsSucc' => 0
						);
						if ($isSucc) {
							/* 不能有重复的技能 */
							$existIds = array();
							$levelTypeArr = array();
							$levelType = array();
							if (!empty($heroInfo['skill_slot'])) {

								$skillInfo1 = M_Skill::getBaseInfo($heroInfo['skill_slot']);
								if (!empty($skillInfo1['level_type'])) {
									$levelTypeArr = explode('_', $skillInfo1['level_type']);
									$levelType[] = $levelTypeArr[0];
								}
								$existIds[] = $heroInfo['skill_slot'];
							}
							if (!empty($heroInfo['skill_slot_1'])) {
								$skillInfo2 = M_Skill::getBaseInfo($heroInfo['skill_slot_1']);
								if (!empty($skillInfo2['level_type'])) {
									$levelTypeArr = explode('_', $skillInfo2['level_type']);
									$levelType[] = $levelTypeArr[0];
								}
								$existIds[] = $heroInfo['skill_slot_1'];
							}
							if (!empty($heroInfo['skill_slot_2'])) {
								$skillInfo3 = M_Skill::getBaseInfo($heroInfo['skill_slot_2']);
								if (!empty($skillInfo3['level_type'])) {
									$levelTypeArr = explode('_', $skillInfo3['level_type']);
									$levelType[] = $levelTypeArr[0];
								}
								$existIds[] = $heroInfo['skill_slot_2'];
							}
							$skillInfo = M_Skill::getBaseInfo($skillId);
							$levelSkillType = 0;
							if (!empty($skillInfo['level_type'])) {
								$levelTypeArr = explode('_', $skillInfo['level_type']);
								$levelSkillType = $levelTypeArr[0];
							}
							if (!in_array($skillId, $existIds) && ((!empty($levelSkillType) && !in_array($levelSkillType, $levelType)) || empty($levelSkillType))) {
								if (empty($heroInfo['skill_slot_1'])) {
									$heroInfo['skill_slot_1'] = $skillId;
								} else if (empty($heroInfo['skill_slot_2'])) {
									$heroInfo['skill_slot_2'] = $skillId;
								}

								$setArr = array(
									'skill_slot_1' => $heroInfo['skill_slot_1'],
									'skill_slot_2' => $heroInfo['skill_slot_2']
								);
								if (M_Hero::setHeroInfo($heroId, $setArr)) {

									$data = array(
										'IsSucc' => 1,
										'SkillInfo' => array(
											'Id' => $skillInfo['id'],
											'Name' => $skillInfo['name'],
											'FaceId' => $skillInfo['face_id'],
											'Type' => $skillInfo['type'],
											'Level' => $skillInfo['level'],
											//'Desc' 	=> $skillInfo['desc'],
										)
									);
									M_QqShare::check($objPlayer, 'hero_skill', array('id' => $skillInfo['id']));
									if (M_Skill::ID_SKILL_GIFT_LEAD == $skillId) {
										$heroInfo = M_Hero::getCityHeroInfo($cityInfo['id'], $heroId);
										$setArr['max_army_num'] = M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd);
									}
									$bQueue = M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, array($heroId => $setArr)); //同步英雄数据!
								}
							}
						}

						$errNo = '';

						$skillId = $data['IsSucc'] ? $skillId : 0;
						Logger::opHero( $cityInfo['id'], $heroId, Logger::H_ACT_LEARN, $skillId);
					} else {
						$errNo = T_ErrNo::ERR_PAY;
					}
				} else {
					$errNo = $err;
				}
			} else {
				$errNo = T_ErrNo::HERO_NO_EXIST; //军官不存在
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 遗忘技能
	 * @author HeJunyun on 20110615
	 * @param int $heroId 英雄ID
	 * @param int $slotId 技能槽ID（1/2）
	 * @param int $payType 支付类型：1军饷 2消费券
	 */
	public function AForget($heroId, $slotId, $payType) {

		$errNo = T_ErrNo::ERR_ACTION;
		$heroId = intval($heroId);
		$slotId = intval($slotId);
		$payType = intval($payType);
		$numArr = array(1, 2);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($heroId &&
			in_array($slotId, $numArr) &&
			in_array($payType, array(T_App::MILPAY, T_App::COUPON))
		) {
			$cityId = $cityInfo['id'];
			$skill_solt_id = M_Skill::$skillSlot . $slotId;
			//检测英雄是否存在
			$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
			if (isset($heroInfo['id']) && $heroInfo[$skill_solt_id] > 0) {
				$setArr = array($skill_solt_id => 0);
				$res = M_Hero::setHeroInfo($heroId, $setArr);
				if ($res) {

					$errNo = '';
					$syncData = array(
						$heroId => $setArr
					);
					$setArr['nickname'] = $heroInfo['nickname'];
					Logger::opHero( $cityId, $heroId, Logger::H_ACT_FORGET, $heroInfo[$skill_solt_id]);
					M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_HERO, $syncData); //同步数据!
				} else {
					$errNo = T_ErrNo::FORGET_SKILL_FALL;
				}
			} elseif (!isset($heroInfo['id'])) {
				$errNo = T_ErrNo::HERO_NO_EXIST;
			} elseif ($heroInfo[$skill_solt_id] < 1) {
				$errNo = T_ErrNo::HERO_SLOT_NOT_USE;
			}

		} else {
			$errNo = T_ErrNo::ERR_PARAM;
		}


		$data = array();
		return B_Common::result($errNo, $data);
	}
}

?>