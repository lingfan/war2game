<?php

class Q_Auction extends B_DB_Dao {
	protected $_name = 'auction';
	protected $_connType = 'game';
	protected $_primary = 'id';

	public function getSaleList($cityId) {
		$sql  = sprintf('SELECT id FROM %s WHERE sale_city_id = %d AND (auction_status = %d OR auction_status = %d)', $this->_name, $cityId, M_Auction::STATUS_FAIL, M_Auction::STATUS_ING);
		$rows = $this->fetchAll($sql);

		$arrRow = array();
		if (!empty($rows) && is_array($rows)) {
			foreach ($rows as $row) {
				$arrRow[] = $row['id'];
			}
		}

		return $arrRow;
	}

	public function getBuyList($cityId) {
		$sql  = sprintf('SELECT id FROM %s WHERE buy_city_id = %d AND (auction_status = %d OR auction_status = %d)', $this->_name, $cityId, M_Auction::STATUS_FAIL, M_Auction::STATUS_ING);
		$rows = $this->fetchAll($sql);

		$arrRow = array();
		if (!empty($rows) && is_array($rows)) {
			foreach ($rows as $row) {
				$arrRow[] = $row['id'];
			}
		}

		return $arrRow;
	}

	/**
	 * 获取拍卖过期的数据([部分字段][2D数组])
	 * @author chenhui on 20120220
	 * @return false/2D
	 */
	public function getAucExpiredInfo() {
		$nowTime = time();
		$row     = $this->getsBy(array('auction_status' => M_Auction::STATUS_ING, 'auction_expired' => array('<', $nowTime)));
		return $row;
	}

	/**
	 * 获取托管过期的数据(交易ID数组)
	 * @author chenhui on 20120220
	 * @return false/2D
	 */
	public function getKeepExpiredInfo() {
		$nowTime = time();
		$sql     = "SELECT * FROM auction WHERE auction_status <> " . M_Auction::STATUS_DEL . " AND keep_expired < $nowTime ";
		$row     = $this->fetchAll($sql);
		return $row;
	}

	/**
	 * 获取正在拍卖的数据
	 * @author chenhui on 20120117
	 * @param int $goodsType 物品类型
	 * @param int $secVal 物品类型下的小类型(因大类型不同而不同)
	 * @param string $sortField 排序字段
	 * @param int $sortType 排序类型(1升序、2降序)
	 * @param int $rowStart 记录开始ID
	 * @return false/array 2D
	 */
	public function getAucInfoIng($goodsType, $secVal, $sortField, $sortType, $rowStart) {
		$nowTime = time();
		$where   = array(
			'auction_expired' => array('>', $nowTime),
			'ol_time'         => array('<', $nowTime),
		);
		if (!empty($goodsType)) {
			$where['goods_type'] = $goodsType;
			if (!empty($secVal)) {
				if (M_Auction::GOODS_HERO == $goodsType) {
					$where['quality'] = $secVal;
				} else if (M_Auction::GOODS_EQUI == $goodsType) {
					$where['pos'] = $secVal;
				}
			}
		}

		$orderBy = array($sortField=>$sortType);
		$row = $this->getList($rowStart, M_Auction::ING_PAGE_SIZE, $where, $orderBy);
		return $row;
	}

	/**
	 * 根据物品名字模糊查询获取正在拍卖的数据
	 * @author chenhui on 20120228
	 * @param string $goodsName 名字关键字
	 * @param int $rowStart 记录开始ID
	 * @return array 拍卖ID数组 2D
	 */
	public function getAucListByName($goodsName, $rowStart) {
		$nowTime = time();
		$where   = array(
			'auction_expired' => array('>', $nowTime),
			'ol_time'         => array('<', $nowTime),
			'goods_name'       => array('LIKE', "%{$goodsName}%"),
		);
		$row = $this->getList($rowStart, M_Auction::ING_PAGE_SIZE, $where);
		return $row;
	}

	/**
	 * 根据物品类型获取拍卖总条数
	 * @author chenhui on 20120219
	 * @param int $goodsType 物品类型
	 * @return int 数量
	 */
	public function getAucOlSum($goodsType, $secVal) {
		$nowTime = time();
		$where   = array(
			'auction_expired' => array('>', $nowTime),
			'ol_time'         => array('<', $nowTime),
		);
		if (!empty($goodsType)) {
			$where['goods_type'] = $goodsType;
			if (!empty($secVal)) {
				if (M_Auction::GOODS_HERO == $goodsType) {
					$where['quality'] = $secVal;
				} else if (M_Auction::GOODS_EQUI == $goodsType) {
					$where['pos'] = $secVal;
				}
			}
		}
		$num = $this->count($where);
		return intval($num);
	}

	/**
	 * 根据物品名字模糊查询获取拍卖总条数
	 * @author chenhui on 20120228
	 * @param string $goodsName 名字关键字
	 * @return int 总条数
	 */
	public function totalAucListByName($goodsName) {
		$nowTime = time();

		$where   = array(
			'auction_expired' => array('>', $nowTime),
			'ol_time'         => array('<', $nowTime),
			'goods_name'       => array('LIKE', "%{$goodsName}%"),
		);

		$num = $this->count($where);
		return intval($num);
	}


	/**
	 * 根据ID数组删除对应数据
	 * @author chenhui on 20120220
	 * @param array $arrId ID数组
	 * @return bool
	 */
	public function delExpiredInfo($arrId) {
		if (!is_array($arrId) || empty($arrId)) {
			return false;
		}
		$sql = "DELETE FROM auction_ol WHERE id IN (" . implode(',', $arrId) . ") ";
		$ret = $this->execute($sql);
		return $ret;
	}


	/**拍卖行使用统计***************************/
	/**
	 * 根据条件获取拍卖行使用记录
	 * @author chenhui on 20120722
	 * @param int $curPage 当前页码
	 * @param int $offset 每页条数
	 * @param int $cityId 城市ID
	 * @param int $title 1出售/2购买
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	public function getAllAucInfo($curPage, $offset, $cityId, $title, $parms = '') {
		$whereArr = array();

		if (0 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "(`sale_city_id`={$cityId} or `buy_city_id`={$cityId})";
			}
		} else if (1 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "`sale_city_id`={$cityId}";
			}
		} else if (2 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "`buy_city_id`={$cityId}";
			}
		}

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>='{$val}'";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<='{$val}'";
				} else {
					$whereArr[] = "`{$key}`='{$val}'";
				}

			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($curPage - 1) * $offset;
		$sql   = "SELECT * FROM `auction` {$where} ORDER BY `create_at` DESC  LIMIT {$start}, {$offset}";
		$rows  = $this->execute($sql);
		return $rows;
	}

	/**
	 * 根据条件获取拍卖行使用记录
	 * @author chenhui on 20120722
	 * @param int $curPage 当前页码
	 * @param int $offset 每页条数
	 * @param int $cityId 城市ID
	 * @param int $title 1出售/2购买
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	public function getAllAucInfoSum($cityId, $title, $parms = '') {
		$whereArr = array();

		if (0 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "(`sale_city_id`={$cityId} or `buy_city_id`={$cityId})";
			}
		} else if (1 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "`sale_city_id`={$cityId}";
			}
		} else if (2 == $title) {
			if ($cityId > 0) {
				$whereArr[] = "`buy_city_id`={$cityId}";
			}
		}

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>='{$val}'";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<='{$val}'";
				} else {
					$whereArr[] = "`{$key}`='{$val}'";
				}

			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql   = "SELECT count(id) as num FROM `auction` {$where} ";
		$row   = $this->execute($sql);
		return intval($row['num']);
	}

}

?>