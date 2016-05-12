<?php

class M_Award {
	/** 其他 */
	const TYPE_OTHER = 0;
	/** npc掉落 */
	const TYPE_DROP = 1;
	/** 任务奖励 */
	const TYPE_TASK = 2;
	/** 探索奖励 */
	const TYPE_PROBE = 3;
	/** 道具包 */
	const TYPE_PROPS = 4;

	static $Type = array(
		self::TYPE_OTHER => '其他',
		self::TYPE_TASK  => '任务',
		self::TYPE_DROP  => 'NPC',
		self::TYPE_PROBE => '探索',
		self::TYPE_PROPS => '道具',
	);


	/**
	 * 转化奖励结果 变成 前端解析格式
	 *
	 * @param array $awardResult 奖励结果数组
	 * array(类型,数量,ID)
	 * ...
	 * @param bool $isDetail
	 * @return array
	 *    array(
	 *        [res, gold, {LANG_RES_GOLD}, num],            //资源 金钱
	 *        [res, oil, {LANG_RES_OIL}, num],            //资源 石油
	 *        [res, food, {LANG_RES_FOOD}, num],            //资源 食物
	 *        [money, milpay, {LANG_MILPAY}, num],        //货币 军饷
	 *        [money, coupon, {LANG_COUPON}, num],        //货币 礼券
	 *        [item, renown, {LANG_RENOWN}, num],            //其他 威望
	 *        [item, warexp, {LANG_WAREXP}, num],            //其他 功勋
	 *        [item, march_num, {LANG_MARCH_NUM}, num],    //其他 活力
	 *        [item, atkfb_num, {LANG_ATKFB_NUM}, num],    //其他 军令
	 *
	 *        [props, id, {xxxx}, num],            //道具
	 *        [equip, 位置, {xxxx}, num],            //装备
	 *        [hero, id, {xxxx}, num],            //英雄
	 *    )
	 */
	static public function toText($awardResult, $isDetail = false) {
		$arrAward = array();
		if (empty($awardResult)) {
			return $arrAward;
		}
		foreach ($awardResult as $val) {
			list($type, $num, $id) = $val;
			switch ($type) {
				case 'gold':
					$arrAward[] = array('res', 'gold', array(T_Lang::RES_GOLD_NAME), $num);
					break;
				case 'food':
					$arrAward[] = array('res', 'oil', array(T_Lang::RES_OIL_NAME), $num);
					break;
				case 'oil':
					$arrAward[] = array('res', 'food', array(T_Lang::RES_FOOD_NAME), $num);
					break;
				case 'milpay':
					$arrAward[] = array('money', 'milpay', array(T_Lang::MILPAY), $num);
					break;
				case 'coupon':
					$arrAward[] = array('money', 'coupon', array(T_Lang::COUPON), $num);
					break;
				case 'renown':
					$arrAward[] = array('item', 'renown', array(T_Lang::RENOWN), $num);
					break;
				case 'exploit':
					$arrAward[] = array('item', 'warexp', array(T_Lang::WAREXP), $num);
					break;
				case 'energy':
					$arrAward[] = array('item', 'atkfb_num', array(T_Lang::ATKFB_NUM), $num);
					break;
				case 'props':
					$propsInfo = M_Props::baseInfo($id);
					if ($isDetail) {
						if ('EQUI_INCR_STRONG' == $propsInfo['effect_txt']) {
							$tmpVal   = explode(',', $propsInfo['effect_val']);
							$frontVal = $tmpVal[0];
						} else if ('WEAPON_PIECE' == $propsInfo['effect_txt']) {
							$frontVal = explode(',', $propsInfo['effect_val']); //array(对应图纸ID,合成需要数量)
						} else {
							$frontVal = $propsInfo['effect_val'];
						}
						$tmp        = array(
							'PropsId'    => $propsInfo['id'],
							'Name'       => $propsInfo['name'],
							'Desc'       => $propsInfo['desc'],
							'FaceId'     => $propsInfo['face_id'],
							'Type'       => $propsInfo['type'],
							'EffectTxt'  => $propsInfo['effect_txt'],
							'EffectVal'  => $frontVal, //格式不统一
							'EffectTime' => $propsInfo['effect_time'],
							'DirectUse'  => isset(M_Props::$EffectUse[$propsInfo['effect_txt']]) ? 1 : 0, //是否可以直接使用

						);
						$arrAward[] = array('props', $num, $tmp);
					} else {
						if ($propsInfo['effect_txt'] == 'WEAPON_CREATE') {
							$arrAward[] = array('props_weapon', $id, $propsInfo['name'], $num, $propsInfo['face_id']);
						} else {
							$arrAward[] = array('props', $id, $propsInfo['name'], $num, $propsInfo['face_id']);
						}
					}

					break;
				case 'equip':
					$equipTplInfo = M_Equip::baseInfo($id);
					if ($isDetail) {
						if ('EQUI_INCR_STRONG' == $equipTplInfo['effect_txt']) {
							$tmpVal   = explode(',', $equipTplInfo['effect_val']);
							$frontVal = $tmpVal[0];
						} else if ('WEAPON_PIECE' == $equipTplInfo['effect_txt']) {
							$frontVal = explode(',', $equipTplInfo['effect_val']); //array(对应图纸ID,合成需要数量)
						} else {
							$frontVal = $equipTplInfo['effect_val'];
						}
						$tmp        = array(
							'PropsId'    => $equipTplInfo['id'],
							'Name'       => $equipTplInfo['name'],
							'Desc'       => $equipTplInfo['desc'],
							'FaceId'     => $equipTplInfo['face_id'],
							'Type'       => $equipTplInfo['type'],
							'EffectTxt'  => $equipTplInfo['effect_txt'],
							'EffectVal'  => $frontVal, //格式不统一
							'EffectTime' => $equipTplInfo['effect_time'],
							'DirectUse'  => isset(M_Props::$EffectUse[$equipTplInfo['effect_txt']]) ? 1 : 0, //是否可以直接使用

						);
						$arrAward[] = array('props', $num, $tmp);
					} else {
						$propsName  = array(T_Lang::EQUIP_NAME, $equipTplInfo['name'], array(T_Lang::$EQUIP_QUAL[$equipTplInfo['quality']]), $equipTplInfo['quality']);
						$arrAward[] = array('equip', $equipTplInfo['pos'], $propsName, $num, $equipTplInfo['face_id']);
					}

					break;
				case 'hero':
					$heroTplInfo = M_Hero::baseInfo($id);
					if ($isDetail) {
						$tmp = array(
							'Id'           => $heroTplInfo['id'],
							'Name'         => $heroTplInfo['name'],
							'Pos'          => $heroTplInfo['pos'],
							'FaceId'       => $heroTplInfo['face_id'],
							'NeedLevel'    => $heroTplInfo['need_level'],
							'Level'        => $heroTplInfo['level'],
							'MaxLevel'     => $heroTplInfo['max_level'],
							'Quality'      => $heroTplInfo['quality'],
							'BaseLead'     => $heroTplInfo['base_lead'],
							'BaseCommand'  => $heroTplInfo['base_command'],
							'BaseMilitary' => $heroTplInfo['base_military'],
							'IsLocked'     => isset($heroTplInfo['is_locked']) ? $heroTplInfo['is_locked'] : 0,
							'ExtAttrName'  => $heroTplInfo['ext_attr_name'],
							'ExtAttrRate'  => $heroTplInfo['ext_attr_rate'],
							'ExtAttrSkill' => $heroTplInfo['ext_attr_skill'],
							'IsUse'        => 0,
							'OnSale'       => 0,
							'SuitId'       => $heroTplInfo['suit_id'],
							'Desc1'        => $heroTplInfo['desc_1'],
							'Desc2'        => $heroTplInfo['desc_2'],
							'CreateAt'     => 0,
							'Flag'         => isset($heroTplInfo['flag']) ? $heroTplInfo['flag'] : 7,
							'HeroName'     => 0,
							'HeroQuality'  => 0,
						);

						$arrAward[] = array('equip', $num, $tmp);
					} else {
						$heroName   = array(T_Lang::HERO_NAME, $heroTplInfo['nickname'], array(T_Lang::$HERO_QUAL[$heroTplInfo['quality']]), $heroTplInfo['quality']);
						$arrAward[] = array('hero', $id, $heroName, $num, $heroTplInfo['face_id']);
					}

					break;
			}
		}
		return $arrAward;
	}


	/**
	 * 奖励结果
	 *
	 * @param int $id
	 * @return  array array(类型, 数量, ID)
	 *
	 */
	static public function rateResult($id) {
		$award     = array();
		$awardInfo = M_Base::award($id);
		if (!empty($awardInfo['id'])) {
			$num = $awardInfo['num'];

			if (!empty($awardInfo['data']['fix'])) {
				foreach ($awardInfo['data']['fix'] as $val) {
					$award[] = $val;
					$num--;
				}
			}


			$tmp = array();
			if (!empty($awardInfo['data']['rnd'])) {
				foreach ($awardInfo['data']['rnd'] as $key => $val) {
					$tmp[$key] = $val[0];
				}
			}


			while ($num > 0) {
				$k = B_Utils::dice($tmp);
				unset($tmp[$k]);
				$num--;
				$newVal = $awardInfo['data']['rnd'][$k];
				array_shift($newVal);
				$award[] = $newVal;
			}
		}


		return $award;
	}

	/**
	 * 奖励内容
	 * @param int $id
	 * @param array
	 * 随机array(
	 * 金钱(概率_gold_数量)
	 * 食物(概率_food_数量)
	 * 食物(概率_oil_数量)
	 * 军饷(概率_milpay_数量)
	 * 礼券(概率_coupon_数量)
	 * 军令(概率_energy_数量)
	 * 军功(概率_exploit_数量)
	 * 威望(概率_renown_数量)
	 * 道具(概率_props_数量_ID)
	 * 装备(概率_equip_数量_ID)
	 * 军官(概率_hero_数量_ID)
	 * )
	 * @return  array<br>
	 * array(类型,数量,ID)
	 * ...
	 */
	static public function allResult($id) {
		$award     = array();
		$awardInfo = M_Base::award($id);
		if (!empty($awardInfo['id'])) {

			if (!empty($awardInfo['data']['fix'])) {
				foreach ($awardInfo['data']['fix'] as $val) {
					$award[] = $val;
				}
			}

			if (!empty($awardInfo['data']['rnd'])) {
				foreach ($awardInfo['data']['rnd'] as $val) {
					array_shift($val);
					$award[] = $val;
				}
			}

		}

		return $award;
	}

}

?>