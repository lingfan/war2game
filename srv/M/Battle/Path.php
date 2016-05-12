<?php

class M_Battle_Path {
	/** 坐标类型 禁止移动*/
	const POS_TYPE_BAN_MOVE = 1;
	/** 坐标类型 禁止停留*/
	const POS_TYPE_BAN_HOLD = 2;

	/** 寻路最大范围 */
	const MAX_RANGE = 20;
	/** 计算最大次数 */
	const MAX_TRY = 5000;
	/** 寻路最大距离 */
	const MAX_DISTANCE = 1000;

	private $Road = 0;
	private $Block = 1;
	private $_maxW = 20;
	private $_maxH = 30;
	private $_startPos = '';
	private $_mapData = array();
	private $_openList = array();
	private $_roadList = array();
	private $_chkList = array();
	private $_banCell = array();

	/**
	 *
	 * 构建地图数据
	 * @param array $mapSize 地图尺寸array(x,y)
	 * @param array $mapData 地图标记数据  array(坐标'x_y'=>标志物array(cell_id))
	 */
	public function __construct($mapSize, $mapData) {
		list($this->_maxW, $this->_maxH) = $mapSize;
		$this->_mapData = $mapData;
		$this->_banCell = M_Base::warmapcellAll();
	}

	/**
	 * 可移动的范围
	 * @param int $moveType 移动类型
	 * @param string $sPos 开始点 x_y
	 * @param int $range 范围
	 * @return array
	 */
	public function getMoveRange($moveType, $sPos, $range = 10) {
		if (empty($moveType) || empty($sPos)) {
			return false;
		}
		$this->_startPos = $sPos;
		list($sX, $sY) = explode('_', $sPos);
		return $this->_getMoveRangePos($moveType, $sX, $sY, $range);
	}

	/**
	 * 获取距离目标的最近坐标
	 * @param int $moveNum
	 * @param array $moveList array('x_y',move)
	 * @param string $ePos
	 */
	public function getMoveLastPos($moveNum, $moveList, $ePos) {
		$maxDist = self::MAX_DISTANCE;
		$minPos  = '';
		if (!empty($moveNum) && is_array($moveList)) {
			$bugLog = '';
			foreach ($moveList as $curPos => $val) {
				$dist = M_Formula::aiCalcDistance($curPos, $ePos);
				//需要移动多少 并且 离目标最近
				if ($moveNum == $val[1] && $dist < $maxDist) {
					$maxDist = $dist;
					$minPos  = $curPos;
				}
			}
		}
		return $minPos;
	}

	/**
	 * 可移动的路径
	 * @param string $ePos 结束点 x_y
	 * @param array $moveList 移动范围
	 * @return array
	 */
	public function getMovePath($ePos, $moveList) {
		$path = array();
		if (isset($moveList[$ePos][0])) {
			$path[] = $ePos;
			$pPos   = $ePos;
			$i      = 0;
			while ($pPos != $this->_startPos) {
				$pPos = isset($moveList[$pPos][0]) ? $moveList[$pPos][0] : '';
				($pPos != $this->_startPos) && !empty($pPos) && array_unshift($path, $pPos);
				if ($i > self::MAX_RANGE) {
					break;
				}
				$i++;
			}
		}
		return $path;

	}

	/**
	 * 检测坐标列表是否有效通过
	 * @author huwei
	 * @param string $heroPos 英雄坐标
	 * @param array $moveList 移动坐标
	 * @param int $moveType 移动类型
	 * @param int $moveNum 移动力
	 * @return bool
	 */
	public function checkMovePath($heroPos, $moveList, $moveType, $moveNum) {
		$ok  = array();
		$num = count($moveList);
		if ($num > 0 && $num < $moveNum + 1) {
			$i = 0;
			foreach ($moveList as $val) {
				$prevPos = ($i == 0) ? $heroPos : $moveList[$i - 1];
				$diffDis = M_Formula::aiCalcDistance($prevPos, $val);
				$cellId  = isset($this->_mapData[$val][0]) ? $this->_mapData[$val][0] : '';
				//移动坐标是否可以穿越
				if ($diffDis == 1) {
					if ($i != $num && $this->okCell($moveType, $cellId, self::POS_TYPE_BAN_MOVE)) {
						//坐标可以穿越
						$ok[] = $val;
					} else if ($i == $num && $this->okCell($moveType, $cellId, self::POS_TYPE_BAN_HOLD)) {
						//最后一个坐标可以停留
						$ok[] = $val;
					}
				}
				$i++;
			}
		}

		$ret = (count($ok) == $num) ? true : false;
		return $ret;
	}

	/**
	 * 获取移动范围坐标
	 * @author huwei
	 * @param int $moveType 移动类型
	 * @param int $sX 开始点X坐标
	 * @param int $sY 开始点y坐标
	 * @param int $eX 结束点X坐标
	 * @param int $eY 结束点y坐标
	 * @return vod
	 */
	private function _getMoveRangePos1($moveType, $sX, $sY, $eX, $eY) {
		$step           = 0;
		$num            = 1;
		$moveList       = array();
		$openList[$num] = array((int)$sX, (int)$sY, $step);

		while (!empty($openList)) {
			$num++;
			$x = count($openList);

			if ($num > self::MAX_TRY) {
				break;
			}

			$tmp = array_shift($openList);
			$pos = $tmp[0] . '_' . $tmp[1];

			if (!isset($chkList[$pos])) {
				$roundPoints   = self::_getRound($tmp[0], $tmp[1], $tmp[2] + 1);
				$chkList[$pos] = 1;

				for ($i = 0; $i < count($roundPoints); $i++) {
					$tmpX    = $roundPoints[$i][0];
					$tmpY    = $roundPoints[$i][1];
					$tmpMove = $roundPoints[$i][2];
					$tmpPos  = $tmpX . '_' . $tmpY;

					$cellId = isset($this->_mapData[$tmpPos][0]) ? $this->_mapData[$tmpPos][0] : '';

					if ($tmpX > -1 &&
						$tmpY > -1 &&
						$tmpX < $this->_maxW &&
						$tmpY < $this->_maxH &&
						$this->okCell($moveType, $cellId, self::POS_TYPE_BAN_MOVE)
					) //检测是否可以通过改点
					{
						//保存是否可以停留的点
						if (!isset($moveList[$tmpPos]) && $this->okCell($moveType, $cellId, self::POS_TYPE_BAN_MOVE)) {
							$moveList[$tmpPos] = array($tmp[0] . '_' . $tmp[1], $tmpMove);
							if ($tmpX == $eX && $tmpY == $eY) {
								break;
							}
						}

						if (!isset($chkList[$tmpPos])) {
							$openList[++$num] = array($tmpX, $tmpY, $tmpMove);
						}
					}
				}
			}
		}

		return $moveList;
	}


	/**
	 * 获取移动范围坐标
	 * @author huwei
	 * @param int $moveType 移动类型
	 * @param int $sX 开始点X坐标
	 * @param int $sY 开始点y坐标
	 * @param int $range 范围
	 * @return vod
	 */
	private function _getMoveRangePos($moveType, $sX, $sY, $range) {
		$step           = 0;
		$num            = 1;
		$moveList       = array();
		$openList[$num] = array((int)$sX, (int)$sY, $step);
		$range          = $range + 1;

		while (!empty($openList)) {
			$num++;
			$x = count($openList);

			if ($num > self::MAX_TRY) {
				break;
			}

			$tmp = array_shift($openList);
			$pos = $tmp[0] . '_' . $tmp[1];

			if (!isset($chkList[$pos])) {
				$roundPoints   = self::_getRound($tmp[0], $tmp[1], $tmp[2] + 1);
				$chkList[$pos] = 1;

				for ($i = 0; $i < count($roundPoints); $i++) {
					$tmpX    = $roundPoints[$i][0];
					$tmpY    = $roundPoints[$i][1];
					$tmpMove = $roundPoints[$i][2];
					$tmpPos  = $tmpX . '_' . $tmpY;

					$cellId = isset($this->_mapData[$tmpPos][0]) ? $this->_mapData[$tmpPos][0] : '';

					if ($tmpX > -1 &&
						$tmpY > -1 &&
						$tmpX < $this->_maxW &&
						$tmpY < $this->_maxH &&
						$this->okCell($moveType, $cellId, self::POS_TYPE_BAN_MOVE) && //检测是否可以通过改点
						$tmpMove < $range
					) {
						//保存是否可以停留的点
						if (!isset($moveList[$tmpPos]) && $this->okCell($moveType, $cellId, self::POS_TYPE_BAN_HOLD)) {
							$moveList[$tmpPos] = array($tmp[0] . '_' . $tmp[1], $tmpMove);
						}

						if (!isset($chkList[$tmpPos])) {
							$openList[++$num] = array($tmpX, $tmpY, $tmpMove);
						}
					}
				}
			}
		}

		return $moveList;
	}

	/**
	 * 获取周围坐标位置的差
	 * @author huwei
	 * @param int $x
	 * @param int $y
	 * @param int $pNode
	 * @return array
	 */
	private function _getRound($x, $y, $pNode) {
		$evenArr = array(
			array(-1, -1),
			array(1, -1),
			array(2, 0),
			array(1, 0),
			array(-1, 0),
			array(-2, 0)

		);
		$oddArr  = array(
			array(-1, 0),
			array(1, 0),
			array(2, 0),
			array(1, 1),
			array(-1, 1),
			array(-2, 0)
		);
		$tmp     = ($x % 2) == 0 ? $evenArr : $oddArr;
		$result  = array();
		foreach ($tmp as $val) {
			$tmpx = $x + $val[0];
			$tmpx = $y + $val[1];
			if ($tmpx >= 0 && $tmpx >= 0) {
				$result[] = array($x + $val[0], $y + $val[1], $pNode);
			}
		}
		return $result;

	}

	/**
	 * 获取攻击范围
	 * @author huwei
	 * @param string $myPos 坐标(X_Y)
	 * @param array $rangeArr 攻击范围array(min, max)
	 * @return array  array(15_1, 17_1, 17_2, 14_2)
	 */
	public function getAtkRange($myPos, $rangeArr) {
		$posList = array();
		if (!empty($myPos) && is_array($rangeArr)) {
			list($min, $max) = $rangeArr;
			$min = max(1, $min);
			$max = max($min, $max);
			$arr = $this->_getRangeList($myPos, $max);

			for ($i = $min; $i < $max + 1; $i++) {
				$posList = array_merge($posList, array_keys($arr[$i]));
			}
		}

		return $posList;
	}

	/**
	 * 获取无障碍物的范围
	 * @author huwei
	 * @param string $sPos
	 * @param array $range
	 * @return array array(x_y)
	 */
	private function _getRangeList($sPos, $range) {
		$range = min($range, self::MAX_RANGE);

		$move           = 0;
		$num            = 1;
		$data           = array();
		$addList[$sPos] = array($sPos, $move);
		$openList[$num] = array($sPos, $move);

		while (!empty($openList)) {
			$num++;
			$x = count($openList);

			if ($num > 5000) {
				break;
			}
			$tmp = array_shift($openList);

			$tmpPos = $tmp[0];

			if (!isset($chkList[$tmpPos])) {
				list($sx, $sy) = explode('_', $tmp[0]);
				$roundPoints = self::_getRound($sx, $sy, $tmp[1] + 1);
				//遍历过的节点数组
				$chkList[$tmpPos] = 1;

				for ($i = 0; $i < count($roundPoints); $i++) {
					$tmpX    = $roundPoints[$i][0];
					$tmpY    = $roundPoints[$i][1];
					$tmpMove = $roundPoints[$i][2];
					$newPos  = $tmpX . '_' . $tmpY;

					if ($tmpX > -1 &&
						$tmpY > -1 &&
						$tmpX < $this->_maxW &&
						$tmpY < $this->_maxH &&
						$tmpMove < $range + 1
					) {
						//添加过的路径数组
						if (!isset($addList[$newPos])) {
							$addList[$newPos]        = 1;
							$data[$tmpMove][$newPos] = array($tmp[0] . '_' . $tmp[1]);
						}

						if (!isset($chkList[$newPos])) {
							$openList[++$num] = array($newPos, $tmpMove);
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * 是否禁止通过
	 * @author huwei on 20110617
	 * @access public
	 * @param int $moveType 移动类型
	 * @param int $cellId
	 * @return bool  [true:可以通过 ; false:禁止通过]
	 */
	public function okCell($moveType, $cellId, $banType) {
		$ret = false;
		if (!empty($cellId)) {
			$ban = isset($this->_banCell[$cellId]['ban']) ? $this->_banCell[$cellId]['ban'] : 0;
			if ($banType & self::POS_TYPE_BAN_MOVE) {
				$num = M_MapBattle::$warMapCellBanCrossType[$moveType] & $ban;
				if ($num == 0) { //可以通过
					$ret = true;
				}
			} else if ($banType & self::POS_TYPE_BAN_HOLD) {
				$num = M_MapBattle::$warMapCellBanHoldType[$moveType] & $ban;
				if ($num == 0) { //可以通过
					$ret = true;
				}
			}
		} else {
			$ret = true;
		}
		return $ret;
	}

	public function dist($x1, $y1, $x2, $y2) {
		return max(abs($x1 - $x2), abs($y1 - $y2), abs(($x1 + $y1) - ($x2 + $y2)));

		$dx = abs(x1 - x2);
		$dy = abs(y2 - y1);
		return sqrt(($dx * $dx) + ($dy * $dy));

	}
}

?>