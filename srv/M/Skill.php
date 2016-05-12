<?php

class M_Skill {
	/** 技能槽 */
	static $skillSlot = 'skill_slot_';

	/** 初级技能 */
	const SKILL_PRIMARY = 1;
	/** 中级技能 */
	const SKILL_MIDDLE = 2;
	/** 高级技能 */
	const SKILL_SENIOR = 3;
	/** 技能等级 */
	static $skillGrade = array(
		self::SKILL_PRIMARY => '初级技能',
		self::SKILL_MIDDLE  => '中级技能',
		self::SKILL_SENIOR  => '高级技能',
	);

	/** 初级学习 */
	const LEARN_PRIMARY = 1;
	/** 中级学习 */
	const LEARN_MIDDLE = 2;
	/** 高级学习 */
	const LEARN_SENIOR = 3;
	/** 学习等级 */
	static $learnGrade = array(
		self::LEARN_PRIMARY => '初级学习',
		self::LEARN_MIDDLE  => '中级学习',
		self::LEARN_SENIOR  => '高级学习',
	);

	/** 学习技能花费军饷/礼券(技能等级=>花费) */
	static $learnCost = array(
		self::LEARN_PRIMARY => 15,
		self::LEARN_MIDDLE  => 30,
		self::LEARN_SENIOR  => 50
	);

	/** 技能 天才指挥 的ID */
	const ID_SKILL_GIFT_LEAD = 12;

	/**
	 * 插入技能记录
	 * @author huwei
	 * @param array $data
	 * @return bool
	 */
	static public function insert($data) {
		$data['effect']  = array(
			array('type' => 'ATT_FREEZE', 'val' => 1),
			array('type' => 'DEF_INCR_DEF_AIR', 'val' => 1),
		);
		$data['trigger'] = array($trigger['type'], $trigger['val']);

		$effect  = json_encode($data['effect']);
		$trigger = json_encode($data['trigger']);
		if (!T_Hero::$skillType[$data['type']]) {
			$data['type'] = 1;
		}

		$filedArr = array(
			'type'        => T_Hero::$skillType[$data['type']],
			'level'       => $data['level'],
			'name'        => $data['name'],
			'desc'        => $data['desc'],
			'odds'        => $data['odds'],
			'cost_energy' => $data['cost_energy'],
			'trigger'     => $trigger,
			'effect'      => $effect,
			'effect_bout' => $data['effect_bout'],
			'sort'        => $data['sort'],
			'create_at'   => time(),
		);
		return B_DB::instance('BaseSkill')->insert($filedArr);
	}

	/**
	 * 获取技能的基本信息
	 * @author huwei
	 * @return array
	 */
	static public function getBaseList() {
		static $list = array();
		if (empty($list)) {
			$apcKey = T_Key::BASE_SKILL; //自定义Memcached中存储城市兵种数据的键名
			$info   = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseSkill')->getAll();
				Logger::base(__METHOD__);
				APC::set($apcKey, $info);
			}
			$list = $info;
		}
		return $list;
	}

	/**
	 * 获取技能通过技能等级
	 * @author huwei
	 * @return array
	 */
	static public function getSkillIdByLevel() {
		static $info = null;
		if (is_null($info)) {
			$apcKey = T_Key::BASE_SKILL . '_IDS';
			$info   = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$skillList = M_Base::skillAll();
				foreach ($skillList as $key => $sinfo) {
					if ($sinfo['level'] == M_Skill::SKILL_PRIMARY) {
						$info[M_Skill::SKILL_PRIMARY][] = $sinfo['id'];
					} elseif ($sinfo['level'] == M_Skill::SKILL_MIDDLE) {
						$info[M_Skill::SKILL_MIDDLE][] = $sinfo['id'];
					} else {
						$info[M_Skill::SKILL_SENIOR][] = $sinfo['id'];
					}
				}
				Logger::base(__METHOD__);
				APC::set($apcKey, $info);
			}
		}
		return $info;
	}

	/**
	 * 随机抽取一个技能
	 * @author huwei
	 * @param int $sLevel 技能等级
	 * @return int
	 */
	static public function getRandSkillId($sLevel) {
		//学习成功
		$skillIds = M_Skill::getSkillIdByLevel(); //所有技能

		/**
		 * 30军饷30%成功学习技能(成功后概率：一级技能30%，二级60%，三级10%)
		 * 分配概率
		 * 各级学习获得各级技能概率
		 * 初级学习:1级技能100%;
		 * 中级学习:1级技能30%、2级技能60%、3级技能10%;
		 * 高级学习:1级技能15%、2级技能25%、3级技能60%;
		 */
		$learnSkillList = M_Config::getVal('hero_learn_skill');
		$skillLv        = B_Utils::dice($learnSkillList[$sLevel]);

		//随机一个技能ID
		$skillIdsList = $skillIds[$skillLv];
		$i            = array_rand($skillIdsList);
		$skillId      = $skillIdsList[$i];
		return $skillId;
	}

	/**
	 * 清除技能缓存
	 * @author huwei
	 */
	static public function cleanBaseSkill() {
		APC::del(T_Key::BASE_SKILL);
		APC::del(T_Key::BASE_SKILL . '_IDS');
	}

	/**
	 * 获取技能的信息
	 * @author huwei
	 * @param int $skillId 技能ID
	 * @return array
	 */
	static public function getBaseInfo($skillId) {
		$listData = M_Base::skillAll();
		return isset($listData[$skillId]) ? $listData[$skillId] : array();
	}

	/**
	 * 学习技能
	 * @author HeJunyun on 20110614
	 * @param int $cityId 城市ID
	 * @param int $heroId 英雄ID
	 * @param int $skill_solt_id 技能槽字段
	 * @param int $proId 道具ID
	 * @return int $errNo 错误编号
	 */
	static public function learnSkill($cityId, $heroId, $skill_solt_id, $proId) {
		$errNo = T_ErrNo::ERR_ACTION;
		//根据道具技能书获得技能
		$skillId = M_Props::getSkillFromBook($proId);
		if ($skillId > 0) {
			if ($objPlayer->Pack()->decrNumByPropId($proId, 1)) //扣除道具
			{
				$setInfo = array(
					$skill_solt_id => $skillId
				);
				$res     = M_Hero::setHeroInfo($heroId, $setInfo);
				$res && $errNo = '';
			}
		} else {
			$errNo = T_ErrNo::SKILL_BOOK_USE_FALL;
		}
		return $errNo;
	}

	/**
	 * 获取英基础雄技能加成
	 * @author huwei
	 * @param array $heroInfo
	 * @return void
	 */
	static public function getBaseEffectByHero(&$heroInfo) {
		//$skillIds = array(0,$heroInfo['skill_slot_1'],$heroInfo['skill_slot_2']);
		$skillIds = array($heroInfo['skill_slot'], $heroInfo['skill_slot_1'], $heroInfo['skill_slot_2']);
		$armyId   = isset($heroInfo['army_id']) ? $heroInfo['army_id'] : '';

		$data = M_Skill::getEffect($skillIds);

		$effect = $data['base'];

		$battleeffect = $data['battle'];

		$heroInfo['skill_lead']     = 0;
		$heroInfo['skill_command']  = 0;
		$heroInfo['skill_military'] = 0;
		$heroInfo['skill_energy']   = 0;
		$heroInfo['skill_army_num'] = array(M_Army::ID_FOOT => 0, M_Army::ID_GUN => 0, M_Army::ID_ARMOR => 0, M_Army::ID_AIR => 0);

		//0几率|1技能值|2触发类型|3使用兵种|4目标兵种|攻击类型|消耗精力|影响回合数

		foreach ($effect as $k => $val) {
			switch ($k) {
				case 'INCR_LEA':
					$heroInfo['skill_lead'] = M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_lead'], $val[1]);
					break;
				case 'INCR_COM':
					$heroInfo['skill_command'] = M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_command'], $val[1]);
					break;
				case 'INCR_MIL':
					$heroInfo['skill_military'] = M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_military'], $val[1]);
					break;
				case 'INCR_VIM':
					$heroInfo['skill_energy'] = M_Formula::calcHeroBaseSkillAdd($heroInfo['attr_energy'], $val[1]);
					break;
				case 'INCR_AN':

					foreach ($heroInfo['skill_army_num'] as $armyIdKey => $v) {
						$heroInfo['skill_army_num'][$armyIdKey] = $val[1][$armyIdKey];
					}
					break;
			}
		}
		return true;
	}

	static public function getBattleEffectByHero($heroInfo) {
		$skillIds = array($heroInfo['skill_slot'], $heroInfo['skill_slot_1'], $heroInfo['skill_slot_2']);
		$data     = M_Skill::getEffect($skillIds);
		$armyId   = isset($heroInfo['army_id']) ? $heroInfo['army_id'] : '';
		$effect   = array();
		foreach ($data['battle'] as $key => $val) {
			//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
			if (empty($val[3])) {
				$effect[$key] = $val;
			} else if (!empty($val[3]) && $val[3] == $armyId) {
				$effect[$key] = $val;
			}
		}

		return $effect;
	}

	static public function getEffect($skillIds) {
		$baseEffects   = array();
		$battleEffects = array();
		foreach ($skillIds as $skillId) {
			if (!empty($skillId)) {
				$info = M_Skill::getBaseInfo($skillId);
				if (!empty($info['effect'])) {
					$effectArr = json_decode($info['effect'], true);

					if (!empty($effectArr) && is_array($effectArr)) {
						foreach ($effectArr as $effectTxt => $val) {
							//0几率|1技能值|2触发类型|3使用兵种|4目标兵种|5攻击类型|6消耗精力|7影响回合数
							$valArr = explode('|', $val);

							if ($effectTxt == 'DECR_AN' || $effectTxt == 'INCR_AN') {
								$tmp = array();
								if ($effectTxt == 'DECR_AN') { //计算 所有兵种 减少数量
									foreach (M_Army::$type as $armyId => $name) {
										$tmp[$armyId] = 0;

										if (empty($valArr[3]) || $valArr[3] == $armyId) {
											$tmp[$armyId] = 0 - $valArr[1];
										}
									}
								} else { //计算 所有兵种 增长数量
									foreach (M_Army::$type as $armyId => $name) {
										$tmp[$armyId] = 0;

										if (empty($valArr[3]) || $valArr[3] == $armyId) {
											$tmp[$armyId] = $valArr[1];
										}
									}
								}

								foreach (M_Army::$type as $armyId => $name) {
									if (isset($baseEffects['INCR_AN'][1][$armyId])) {
										$baseEffects['INCR_AN'][1][$armyId] += $tmp[$armyId];
									} else {
										$baseEffects['INCR_AN'][1][$armyId] = $tmp[$armyId];
									}
								}
							} else if (isset(T_Effect::$SkillBaseType[$effectTxt])) //基础技能效果
							{
								if (isset($baseEffects[$effectTxt])) {
									//是否可以叠加
									if (in_array($effectTxt, T_Effect::$SkillOverlayType) && $valArr[1]) {
										$baseEffects[$effectTxt][1] += $valArr[1];
									}
								} else {
									$baseEffects[$effectTxt] = $valArr;
								}
							} else if (isset(T_Effect::$SkillBattleType[$effectTxt])) //战斗技能效果
							{
								//战斗技能不可以叠加
								$battleEffects[$effectTxt] = $valArr;
							}
						}

					}
				}
			}

		}
		$effects['base']   = $baseEffects;
		$effects['battle'] = $battleEffects;
		return $effects;
	}

	/**
	 * 基础属性加成
	 * @author huwei
	 * @param array $baseEffect 效果基础类型定义
	 * @return array
	 */
	static public function clacHeroInfo($baseEffect) {
		$effectKey = array(
			'INCR_LEA' => 'attr_lead',
			'INCR_COM' => 'attr_command',
			'INCR_MIL' => 'attr_military',
			'INCR_VIM' => 'attr_energy',
			'INCR_AN'  => 'army_num',
		);

		$ret = array(
			'attr_lead'     => 0,
			'attr_command'  => 0,
			'attr_military' => 0,
			'attr_energy'   => 0,
			'army_num'      => 0,
		);

		if (!empty($heroInfo) && !empty($baseEffect) && is_array($baseEffect)) {
			foreach ($baseEffect as $effTxt => $effVal) {
				if (isset($effectKey[$effTxt])) {
					$keyName       = $effectKey[$effTxt];
					$ret[$keyName] = $effVal;
				}
			}
		}

		return $ret;
	}

}

?>