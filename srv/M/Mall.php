<?php

/**
 * 商城模块
 */
class M_Mall {
	/** 内政 */
	const CATEGORY_INNER = 1;
	/** 军官 */
	const CATEGORY_HERO = 2;
	/** 宝物 */
	const CATEGORY_TREA = 3;
	/** 战斗 */
	const CATEGORY_WAR = 4;
	/** 图纸商城 */
	const CATEGORY_DRAW = 5;
	/** 点券商城 */
	const CATEGORY_COUPON = 6;
	/** 材料*/
	const CATEGORY_MATERIAL = 7;
	/** 积分*/
	const CATEGORY_POINT = 8;
	/** 活跃度积分*/
	const CATEGORY_ACTIVENESS = 9;
	/** 道具类型 */
	static $category = array(
		self::CATEGORY_INNER      => '内政',
		self::CATEGORY_HERO       => '军官',
		self::CATEGORY_TREA       => '宝物',
		self::CATEGORY_WAR        => '战斗',
		self::CATEGORY_DRAW       => '图纸',
		self::CATEGORY_COUPON     => '点券商城',
		self::CATEGORY_MATERIAL   => '材料',
		self::CATEGORY_POINT      => '突围积分',
		self::CATEGORY_ACTIVENESS => '活跃度积分',
	);
	/** 道具 */
	const ITEM_PROPS = 1;
	/** 军官 */
	const ITEM_HERO = 2;
	/** 装备 */
	const ITEM_EQUIP = 3;
	/** 物品类型 */
	static $itemType = array(
		self::ITEM_PROPS => '道具',
		self::ITEM_HERO  => '军官',
		self::ITEM_EQUIP => '装备',
	);
	/** 军饷 */
	const PAY_MILPAY = 1;
	/** 点券 */
	const PAY_COUPON = 2;
	/** 金钱 */
	const PAY_GOLD = 3;
	/** 突围积分 */
	const PAY_POINT = 4;
	/** 活跃度积分 */
	const PAY_ACTIVENESS = 5;
	/** 消费类型 */
	static $payType = array(
		self::PAY_MILPAY     => '军饷',
		self::PAY_COUPON     => '点券',
		self::PAY_GOLD       => '金钱',
		self::PAY_POINT      => '突围积分',
		self::PAY_ACTIVENESS => '活跃度积分',
	);


	/**
	 * 根据商城ID获取商城基础信息
	 * @author duhuihui    on 20120907
	 * @param int $mall_id 商城ID
	 * @return array 商城基础信息(一维数组)
	 */
	static public function getBaseInfoById($mall_id) {
		$list = M_Base::mallAll();
		return !empty($list[$mall_id]) ? $list[$mall_id] : array();
	}

	/**
	 * 获取商城基础信息至前端格式
	 * @author duhuihui    on 20120907
	 * @return array 商城基础信息(一维数组)
	 */
	static public function getList() {
		$arrdata     = array();
		$mallinfoall = M_Base::mallAll();
		if (!empty($mallinfoall) && is_array($mallinfoall)) {
			$arrAward = array();
			foreach ($mallinfoall as $mallinfo) {
				$arrPrice  = json_decode($mallinfo['price'], true); //价格数组
				$showPrice = array();
				!empty($arrPrice[M_Mall::PAY_MILPAY]) && $showPrice[M_Mall::PAY_MILPAY] = $arrPrice[M_Mall::PAY_MILPAY];
				!empty($arrPrice[M_Mall::PAY_COUPON]) && $showPrice[M_Mall::PAY_COUPON] = $arrPrice[M_Mall::PAY_COUPON];
				!empty($arrPrice[M_Mall::PAY_GOLD]) && $showPrice[M_Mall::PAY_GOLD] = $arrPrice[M_Mall::PAY_GOLD];
				!empty($arrPrice[M_Mall::PAY_POINT]) && $showPrice[M_Mall::PAY_POINT] = $arrPrice[M_Mall::PAY_POINT];

				if ($mallinfo['item_type'] == M_Mall::ITEM_PROPS) {
					$propsInfo = M_Props::baseInfo($mallinfo['item_id']);
					if (!empty($propsInfo['name'])) {
						$arrAward[] = array(
							'MailId'    => $mallinfo['id'],
							'Type'      => $mallinfo['category'],
							'Price'     => $showPrice,
							'IsHot'     => $mallinfo['status'],
							'ItemType'  => 'props',
							'PropsId'   => $mallinfo['item_id'],
							'NameDesc'  => $propsInfo['name'],
							'EffectVal' => $propsInfo['effect_val'],
							'Num'       => $mallinfo['num'],
							'Face_id'   => $propsInfo['face_id'],
							'Desc'      => $propsInfo['desc'],
							'Sort'      => $mallinfo['sort'],
							'up_time'   => $mallinfo['up_time'],
							'down_time' => $mallinfo['down_time']
						);
					} else {
						Logger::error(array(__METHOD__ . ':' . __LINE__, $mallinfo['item_id'], $propsInfo));
					}
				} else if ($mallinfo['item_type'] == M_Mall::ITEM_HERO) {
					$heroTplInfo = M_Hero::baseInfo($mallinfo['item_id']);
					if (!empty($heroTplInfo['nickname'])) {
						$heroName   = array(
							'NickName'     => $heroTplInfo['nickname'],
							'Quality'      => $heroTplInfo['quality'],
							'AttrCommand'  => $heroTplInfo['attr_command'],
							'AttrMilitary' => $heroTplInfo['attr_military'],
							'AttrLead'     => $heroTplInfo['attr_lead'],
							'Level'        => $heroTplInfo['level'],
							'SkillSlot'    => $heroTplInfo['skill_slot'],
							'GrowRate'     => $heroTplInfo['grow_rate'],
							'IsLegend'     => $heroTplInfo['is_legend'],
							'AttrEnergy'   => $heroTplInfo['attr_energy']
						);
						$arrAward[] = array(
							'MailId'    => $mallinfo['id'],
							'Type'      => $mallinfo['category'],
							'Price'     => $showPrice,
							'IsHot'     => $mallinfo['status'],
							'ItemType'  => 'hero',
							'HeroId'    => $mallinfo['item_id'],
							'NameDesc'  => $heroName,
							'Num'       => $mallinfo['num'],
							'Face_id'   => $heroTplInfo['face_id'],
							'Desc'      => $heroTplInfo['desc'],
							'Sort'      => $mallinfo['sort'],
							'up_time'   => $mallinfo['up_time'],
							'down_time' => $mallinfo['down_time']
						);
					} else {
						Logger::error(array(__METHOD__ . ':' . __LINE__, $heroTplId, $heroTplInfo));
					}
				} else if ($mallinfo['item_type'] == M_Mall::ITEM_EQUIP) {
					$equiTplInfo = M_Equip::baseInfo($mallinfo['item_id']);
					if (!empty($equiTplInfo['name'])) {
						if ($equiTplInfo['pos'] == 7) {
							$propsName = array(
								'Name'         => $equiTplInfo['name'],
								'Quality'      => $equiTplInfo['quality'],
								'BaseCommand'  => $equiTplInfo['base_command'],
								'BaseMilitary' => $equiTplInfo['base_military'],
								'BaseLead'     => $equiTplInfo['base_lead'],
								'Level'        => $equiTplInfo['level'],
								'NeedLevel'    => $equiTplInfo['need_level'],
								'ExtAttrName'  => $equiTplInfo['ext_attr_name'],
								'ExtAttrRate'  => $equiTplInfo['ext_attr_rate'],
								'Pos'          => $equiTplInfo['pos'],
							);

						} else {
							$propsName = array(
								'Name'         => $equiTplInfo['name'],
								'Quality'      => $equiTplInfo['quality'],
								'BaseCommand'  => $equiTplInfo['base_command'],
								'BaseMilitary' => $equiTplInfo['base_military'],
								'BaseLead'     => $equiTplInfo['base_lead'],
								'Level'        => $equiTplInfo['level'],
								'NeedLevel'    => $equiTplInfo['need_level'],
							);
						}
						$arrAward[] = array(
							'MailId'    => $mallinfo['id'],
							'Type'      => $mallinfo['category'],
							'Price'     => $showPrice,
							'IsHot'     => $mallinfo['status'],
							'ItemType'  => 'equip',
							'Pos'       => $equiTplInfo['pos'],
							'NameDesc'  => $propsName,
							'Num'       => $mallinfo['num'],
							'Face_id'   => $equiTplInfo['face_id'],
							'Desc'      => '',
							'Sort'      => $mallinfo['sort'],
							'up_time'   => $mallinfo['up_time'],
							'down_time' => $mallinfo['down_time'],
						);

					} else {
						Logger::error(array(__METHOD__ . ':' . __LINE__, $mallinfo['item_id'], $equiTplInfo));
					}
				}

			}
		}
		return $arrAward;
	}

	/**
	 * 根据商城ID更新城市商品信息
	 * @author duhuihui on 20120910
	 * @param int $mallId 商城ID
	 * @param array updinfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function setInfo($mallId, $upInfo, $upDB = true) {
		$ret = B_DB::instance('BaseMall')->update($upInfo, $mallId);
		return $ret;
	}


	static public function getNum($mallId) {
		$ret = array();
		$rc  = new B_Cache_RC(T_Key::MALL_NUM);
		if (!$rc->exists()) {
			$list = M_Base::mallAll();

			foreach ($list as $key => $val) {
				$ret[$val['id']] = $val['num'];
			}
			$rc->hmset($ret);
		} else {
			$ret = $rc->hgetall();
		}
		return isset($ret[$mallId]) ? $ret[$mallId] : 0;
	}

	static public function decrNum($mallId, $num) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::MALL_NUM);
		$ret = $rc->hincrby($mallId, -$num);
		return $ret;
	}


}

?>