<?php

/**
 * 任务类型:
 * 1    => 升级建筑
 * 2    => 建筑迁移
 * 3    => 升级科技
 * 4    => 升级兵种
 * 5    => 建筑物数量
 * 6    => 副本通关
 * 7    => 攻打副本次数
 * 8    => 配兵操作
 * 9    => 充值
 * 10    => 道具购买
 * 11    => 道具使用
 *
 *
 * 任务完成条件:
 * 升级建筑     => array(build_up, 建筑ID(0任意), 等级)
 * 建筑清CD    => array(build_cd, 次数)
 * 科技升级    => array(tech_up,科技ID(0任意),等级)
 * 科技清CD    => array(tech_cd, 次数)
 * 兵种升级    => array(army_up,兵种ID(0任意),等级)
 * 兵种招募    => array(army_hire,兵种ID(0任意),数量)
 * 兵种配兵    => array(army_fit, 兵种ID(0任意), 数量)
 * 攻打副本    => array(atk_fb, 副本编号, 次数)
 * 攻打玩家    => array(atk_city, 1)
 * 攻打学院    => array(atk_wild, 1)
 * 充值        => array(pay,军饷数)
 * 道具购买    => array(props_buy, 道具ID(0任意), 道具数量)
 * 道具使用    => array(props_use, 道具ID(0任意), 次数)
 * 军官寻找    => array(hero_find, 次数)
 * 军官招募    => array(hero_exp, 次数)
 * 军官培养    => array(hero_train, 次数)
 * 军团申请    => array(union_apply, 次数)
 * 装备强化    => array(equip_strong, 品质(0任意), 等级(0任意), 次数)
 * 装备精炼    => array(equip_refine, 品质(0任意), 等级(0任意), 次数)
 * 武器研究    => array(weapon_study, 次数)
 * 好友邀请    =>array(friend_invite,数量)
 * 完成所有任务
 */
class O_Quest implements O_I {
	//初始状态
	const FLAG_INIT = 0;
	//完成状态
	const FLAG_COMP = 1;
	static $baseTree = array();
	/**
	 * 同步数据
	 * @var array
	 */
	private $_sync = array();
	/**
	 * array(任务ID=>array(任务条件, 任务完成数, 是否完成))
	 * @var array
	 */
	private $_list = array();

	/**
	 * array(任务ID=>array(任务条件, 任务完成数, 是否完成))
	 * @var array
	 */
	private $_data = array();

	/**
	 * 是否有改变
	 * @var bool
	 */
	private $_change = false;
	/**
	 * @var O_Player
	 */
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo        = $objPlayer->getCityExtra();
		$data             = array();
		if (!empty($extraInfo['quest_list'])) {
			$data = json_decode($extraInfo['quest_list'], true);
		}
		if (empty($data)) {
			$data = $this->initList();
		}

		$this->_data = $data;
		foreach ($data as $qId => $v) {
			list($rule, $num, $flag) = $v;
			$ruleKey = $rule[0];
			if ($flag == self::FLAG_INIT) { //未完成
				$this->_list[$ruleKey][] = $qId;
			}
		}
	}

	/**
	 * 初始玩家任务基础树
	 * @author huwei
	 * @return array
	 */
	public function initList() {
		$tmp  = array();
		$nextList = M_Task::getQuestIdsByPrevId(0);

		foreach ($nextList as $id) {
			$row = M_Base::questInfo($id);
			//array(完成数量,已完成[0未|1已]) 领取完跳到下一个删除当前任务
			$tmp[$id] = array($row['cond_pass'], 0, 0);
			$this->_change = true;
		}
		return $tmp;
	}



	public function get() {
		return $this->_data;
	}

	/**
	 * 是否有数据改变
	 * @return boolean
	 */
	public function isChange() {
		return $this->_change;
	}

	/**
	 * 完成任务
	 * @param int $qId
	 * @return array
	 */
	public function finish($qId) {
		$ret = false;
		if (isset($this->_data[$qId]) && $this->_data[$qId][2] == self::FLAG_COMP) {
			unset($this->_data[$qId]);
			$this->_sync[$qId] = M_Sync::DEL;

			$nextList = M_Task::getQuestIdsByPrevId($qId);

			foreach ($nextList as $subId) {
				$row = M_Base::questInfo($subId);
				//array(完成数量,已完成[0未|1已]) 领取完跳到下一个删除当前任务
				$this->_data[$subId] = array($row['cond_pass'], 0, self::FLAG_INIT);
				//效果值
				$ruleKey = $row['cond_pass'][0];
				//检查ID
				$checkId = $row['cond_pass'][1];
				//更新进行中任务
				$this->_list[$ruleKey][] = $subId;

				$cKey = '_params_' . $ruleKey;
				if (method_exists($this, $cKey)) {
					$params = $this->$cKey($this->_objPlayer, $checkId);
					//Logger::debug(array(__METHOD__, $cKey, $ruleKey, $checkId, $params));
					$this->check($ruleKey, $params);
				}

				$arg                 = isset($this->_sync[$subId]['DescArgs']) ? $this->_sync[$subId]['DescArgs'] : array(0);
				$ok                  = isset($this->_sync[$subId]['IsOk']) ? $this->_sync[$subId]['IsOk'] : self::FLAG_INIT;
				$this->_sync[$subId] = array(
					'_0'       => M_Sync::ADD,
					'Id'       => $subId,
					'Name'     => $row['name'],
					'Desc'     => $row['desc'],
					'DescArgs' => $arg,
					'IsOk'     => $ok
				);
			}

			$ret           = true;
			$this->_change = true;

		}
		return $ret;
	}

	public function check($ruleKey, $params = array()) {
		Logger::debug(array(__METHOD__, func_get_args(), $this->_list));
		if (isset($this->_list[$ruleKey]) && method_exists($this, $ruleKey)) {
			foreach ($this->_list[$ruleKey] as $qId) {
				$this->$ruleKey($qId, $params);
			}
		}
	}

	public function getSync() {
		$ret         = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_build_up(O_Player $objPlayer, $id) {
		$bList = $objPlayer->Build()->get();

		$bArr  = isset($bList[$id]) ? $bList[$id] : array();
		$lvArr = array();
		foreach ($bArr as $pos => $vLv) {
			$lvArr[] = $vLv;
		}
		$params['id'] = $id;
		$params['lv'] = !empty($lvArr) ? max($lvArr) : 0;
		return $params;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_tech_up(O_Player $objPlayer, $id) {
		$params['id'] = $id;
		$list         = $objPlayer->Tech()->get();
		$params['lv'] = isset($list[$id]) ? $list[$id] : 0;
		return $params;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_army_up(O_Player $objPlayer, $id) {
		$params['id'] = $id;
		$armyList     = $objPlayer->Army()->get();
		list($num, $lv, $exp) = $armyList[$id];
		$params['lv'] = $lv;
		return $params;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_army_hire(O_Player $objPlayer, $id) {
		$params['id'] = $id;
		$armyList     = $objPlayer->Army()->get();
		list($num, $lv, $exp) = $armyList[$id];
		$params['num'] = $num;
		return $params;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_fb_pass(O_Player $objPlayer, $id) {
		$params['id'] = $objPlayer->City()->last_fb_no;
		return $params;
	}

	/**
	 *
	 * @param O_Player $objPlayer
	 * @param int $id
	 */
	private function _params_weapon_study(O_Player $objPlayer, $id) {
		$isExist       = $objPlayer->Weapon()->inBaseWeapon($id);
		$params['val'] = $isExist ? $id : 0;
		return $params;
	}

	/**
	 * 建筑升级
	 * @param int $qId
	 * @param array $params array('id'=>建筑ID, 'lv'=>等级)
	 */
	private function build_up($qId, $params) {
		//完成条件array(build_up, ID(0任意), 等级)
		$this->_commonLv($qId, $params);
	}

	/**
	 * 公共等级检测
	 * @param int $qId
	 * @param array $params [id:ID,lv:等级]
	 */
	private function _commonLv($qId, $params) {
		//array(完成条件, 等级, 是否完成)
		list($ruleArr, $tmpVal, $isOk) = $this->_data[$qId];

		//完成条件array(效果值, ID(0任意), 等级)
		list($key, $checkId, $checkLv) = $ruleArr;

		$curId = isset($params['id']) ? $params['id'] : 0;
		$curLv = isset($params['lv']) ? $params['lv'] : 0;

		//Logger::debug(array($checkId, $curId, $curLv, $checkLv));

		if (empty($checkId) || $checkId == $curId) {
			$tmpVal = min($curLv, $checkLv);
			if ($tmpVal >= $checkLv) {
				$isOk = 1;
			}

			$this->_sync[$qId] = array(
				'_0'       => M_Sync::SET,
				'DescArgs' => array($tmpVal),
				'IsOk'     => $isOk
			);
			$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
			$this->_change     = true;
		}
	}

	/**
	 * 科技升级
	 * @param int $qId
	 * @param array $params array('id'=>科技ID, 'lv'=>等级)
	 */
	private function tech_up($qId, $params) {
		//完成条件array(tech_up, ID(0任意), 等级)
		$this->_commonLv($qId, $params);
	}

	/**
	 * 攻击副本
	 * @param int $qId
	 * @param array $params array('id'=>副本编号int, 'num'=>攻击次数)
	 */
	private function atk_fb($qId, $params) {
		//array(完成条件, 次数, 是否完成)
		list($ruleArr, $curVal, $isOk) = $this->_data[$qId];


		//完成条件array(atk_fb, 副本编号, 次数)
		list($key, $checkNoVal, $checkTimes) = $ruleArr;
		list($a,$b,$c) = explode('_',$checkNoVal);
		$checkNo = M_Formula::calcFBNo($a,$b,$c);

		$curNo  = isset($params['id']) ? $params['id'] : 0;
		$tmpNum = isset($params['num']) ? $params['num'] : 0;
		$curVal += $tmpNum;

		if ($checkTimes == 1) {//1次表示 通过即可
			if ($curNo >= $checkNo) {
				$isOk = 1;
				$this->_sync[$qId] = array(
					'_0'       => M_Sync::SET,
					'DescArgs' => array(1),
					'IsOk'     => $isOk
				);
				$this->_data[$qId] = array($ruleArr, 1, $isOk);
				$this->_change     = true;
			}
		} else {
			if ($curNo == $checkNo) { //关卡检查
				$tmpVal = min($curVal, $checkTimes);
				if ($tmpVal >= $checkTimes) { //完成
					$isOk = 1;
				}
				$this->_sync[$qId] = array(
					'_0'       => M_Sync::SET,
					'DescArgs' => array($tmpVal),
					'IsOk'     => $isOk
				);
				$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
				$this->_change     = true;
			}
		}



	}

	/**
	 * 攻打玩家
	 *
	 * @param int $qId
	 * @param array $params [num:次数]
	 */
	private function atk_player($qId, $params) {
		//完成条件array(atk_player, 次数)
		$this->_commonTimes($qId, $params);
	}

	/**
	 *
	 * 公共次数检测
	 * @param $qId
	 * @param $params [num:数量]
	 * @param $add        是否累加模式[true:是|false:否]
	 */
	private function _commonTimes($qId, $params, $add = true) {
		//array(完成条件, 已完成数量, 是否完成)
		list($ruleArr, $curVal, $isOk) = $this->_data[$qId];

		//完成条件array(效果值, 次数)
		list($key, $checkNum) = $ruleArr;

		$tmpNum = isset($params['num']) ? $params['num'] : 0;

		if ($add) { //累加模式
			$curVal += $tmpNum;
		} else {
			$curVal = $tmpNum;
		}

		$tmpVal = min($curVal, $checkNum);

		if ($tmpVal >= $checkNum) {
			$isOk = 1;
		}

		$this->_sync[$qId] = array(
			'_0'       => M_Sync::SET,
			'DescArgs' => array($tmpVal),
			'IsOk'     => $isOk
		);

		$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
		$this->_change     = true;
	}

	/**
	 * 攻打学院
	 *
	 * @param int $qId
	 * @param array $params
	 */
	private function atk_wildnpc($qId, $params) {
		//攻打学院=>array(atk_wildnpc,1)
		$this->_commonTimes($qId, $params);
	}

	/**
	 * 清理建筑cd
	 *
	 * @param int $qId
	 * @param array $params
	 */
	private function build_cd($qId, $params) {
		//完成条件array(build_cd, 次数)
		$this->_commonTimes($qId, $params);
	}

	/**
	 * 清理科技cd
	 *
	 * @param int $qId
	 * @param array $params
	 */
	private function tech_cd($qId, $params) {
		//完成条件array(tech_cd, 次数)
		$this->_commonTimes($qId, $params);
	}

	/**
	 * 兵种升级
	 *
	 * @param int $qId
	 * @param array $params [id:兵种,lv:等级]
	 */
	private function army_up($qId, $params) {
		//array(army_up,兵种ID(0任意),等级)
		$this->_commonLv($qId, $params);
	}

	/**
	 * 兵种招兵
	 *
	 * @param int $qId
	 * @param array $params [id:兵种,num:数量]
	 */
	private function army_hire($qId, $params) {
		//array(army_hire,兵种ID(0任意),数量)
		$this->_commonNum($qId, $params);
	}

	/**
	 * 公共数量检测
	 * @param int $qId
	 * @param array $params [id:ID,num:数量]
	 */
	private function _commonNum($qId, $params) {
		//array(完成条件, 已完成数量, 是否完成)
		list($ruleArr, $tmpVal, $isOk) = $this->_data[$qId];

		//array(效果值,兵种ID(0任意),数量)
		list($key, $checkId, $checkNum) = $ruleArr;


		$curId  = isset($params['id']) ? $params['id'] : 0;
		$addNum = isset($params['num']) ? $params['num'] : 0;


		if (empty($checkId) || $checkId == $curId) {
			$tmpVal += $addNum;

			//Logger::debug(array($checkId, $curId, $addNum, $checkNum, $tmpVal));

			$tmpVal = min($tmpVal, $checkNum);
			if ($tmpVal >= $checkNum) {
				$isOk = 1;
			}

			$this->_sync[$qId] = array(
				'_0'       => M_Sync::SET,
				'DescArgs' => array($tmpVal),
				'IsOk'     => $isOk
			);

			$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
			$this->_change     = true;
		}
	}

	/**
	 * 兵种配兵
	 *
	 * @param int $qId
	 * @param array $params [id:兵种,num:数量]
	 */
	private function army_fit($qId, $params) {
		//array(army_fit, 兵种ID(0任意), 数量)
		$this->_commonNum($qId, $params);
	}

	/**
	 * 充值检测
	 *
	 * @param int $qId
	 * @param array $params [num:当前玩家总军饷]
	 */
	private function pay($qId, $params) {
		//array(pay,军饷数)
		$this->_commonTimes($qId, $params, false);
	}

	/**
	 * 商城购买
	 *
	 * @param int $qId
	 * @param array $params [id:物品ID,num:数量]
	 */
	private function mall_buy($qId, $params) {
		//array(mall_buy, 物品ID(0任意), 物品数量)
		$this->_commonNum($qId, $params);
	}

	/**
	 * 道具使用
	 *
	 * @param int $qId
	 * @param array $params [id:物品ID,num:数量]
	 */
	private function props_use($qId, $params) {
		//array(props_use, 道具ID(0任意), 次数)
		$this->_commonNum($qId, $params);
	}

	private function hero_find($qId, $params) {
		//军官寻找=> array(hero_find, 次数)
		$this->_commonTimes($qId, $params);
	}

	private function hero_hire($qId, $params) {
		//普通军官招募=> array(hero_hire, 次数)
		$this->_commonTimes($qId, $params);
	}

	private function hero_train($qId, $params) {
		//军官培养=> array(hero_train, 次数)
		$this->_commonTimes($qId, $params);
	}

	private function union_apply($qId, $params) { //军团申请=> array(union_apply, 次数)
		$this->_commonTimes($qId, $params);
	}

	private function equip_strong($qId, $params) { //装备强化=> array(equip_strong, 品质(0任意), 等级(0任意), 次数)
		$this->_commonEquip($qId, $params);
	}

	/**
	 * 公共装备检测
	 *
	 * @param $qId
	 * @param $params [qual:品质,lv:等级,num:次数]
	 */
	private function _commonEquip($qId, $params) {
		//array(完成条件, 等级, 是否完成)
		list($ruleArr, $tmpVal, $isOk) = $this->_data[$qId];

		//array(效果值, 品质(0任意), 等级(0任意), 次数)
		list($key, $checkQual, $checkLv, $checkNum) = $ruleArr;

		$curQual = isset($params['qual']) ? $params['qual'] : 0;
		$curLv   = isset($params['lv']) ? $params['lv'] : 0;
		$addNum  = isset($params['num']) ? $params['num'] : 0;

		if (empty($checkQual) || $checkQual == $curQual) {
			if (empty($checkLv) || $checkLv == $curLv) {
				$tmpVal += $addNum;
				$tmpVal = min($tmpVal, $checkNum);
				if ($tmpVal >= $checkNum) {
					$isOk = 1;
				}
				$this->_sync[$qId] = array(
					'_0'       => M_Sync::SET,
					'DescArgs' => array($tmpVal),
					'IsOk'     => $isOk
				);
				$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
				$this->_change     = true;
			}
		}
	}

	private function equip_up($qId, $params) { //装备升级=> array(equip_up, 品质(0任意), 等级(0任意), 次数)
		$this->_commonEquip($qId, $params);
	}

	private function equip_mix($qId, $params) { //装备合成=> array(equip_mix, 品质(0任意), 等级(0任意), 次数)
		$this->_commonEquip($qId, $params);
	}

	/**
	 * 特殊武器研究
	 *
	 * @param int $qId
	 * @param array $params [val:第几个]
	 */
	private function weapon_study_s($qId, $params) { //完成条件array(weapon_study_s, 第几个)
		$this->_commonEq($qId, $params);
	}

	/**
	 * 任务相等检查
	 *
	 * @param int $qId
	 * @param array $params [val:任务值]
	 */
	private function _commonEq($qId, $params) {
		//array(完成条件, 等级, 是否完成)
		list($ruleArr, $tmpVal, $isOk) = $this->_data[$qId];

		//完成条件array(任务标签, 任务值)
		list($key, $checkVal) = $ruleArr;

		$curVal = isset($params['val']) ? $params['val'] : 0;

		if (empty($checkVal) || $checkVal == $curVal) {
			$tmpVal = $curVal;
			$isOk   = 1;

			$this->_sync[$qId] = array(
				'_0'       => M_Sync::SET,
				'DescArgs' => array($tmpVal),
				'IsOk'     => $isOk
			);

			$this->_data[$qId] = array($ruleArr, $tmpVal, $isOk);
			$this->_change     = true;
		}
	}

	/**
	 * 普通武器研究
	 *
	 * @param int $qId
	 * @param array $params [val:武器ID]
	 */
	private function weapon_study($qId, $params) { //普通武器研究=> array(weapon_study,	武器ID)
		$this->_commonEq($qId, $params);
	}



}

?>