<?php

/**
 *
 * 公共类库
 * @author 胡威
 *
 */
class B_Common {
	static public function redirect($url) {
		header('Location:' . $url);
		exit;
	}

	/**
	 * 面包屑导航条
	 * @author huwei
	 * @param array $urlArr
	 * @return string
	 */
	static public function breadcrumb($urlArr) {
		$arr = '';
		if (is_array($urlArr)) {
			foreach ($urlArr as $key => $val) {
				$arr[] = !empty($val) ? "<a href='?r={$val}'>{$key}</a>" : $key;
			}
		}
		return implode('&nbsp;->&nbsp;', $arr);

	}

	/**
	 *
	 * 返回结果控制
	 * @author huwei
	 * @param int $flag 成功0 失败1 其他数字表示其他状态
	 * @param array $data 数据
	 * @return array
	 */
	static public function result($errNo='', $data=array()) {
		$ret['flag'] = empty($errNo) ? T_App::SUCC : T_App::FAIL;
		$ret['data'] = array('ErrNo' => $errNo, 'Data' => $data);
		return $ret;
	}

	static public function outData($c, $a, $data) {
		if (isset($data['city_id'])) {
			$cid = $data['city_id'];
			unset($data['city_id']);
		} else {
			$cid = M_Auth::myCid();
		}

		if (!is_array($data)) {
			$data = array();
		}

		if ($cid) { //获取队列信息

			$syncData = M_Sync::getQueue($cid);
			if (!empty($syncData)) {
				$data['sync'] = $syncData;
			}
		}
		return $data;
	}

	/**
	 *
	 * 返回数组转换成xml格式的结果
	 * @param array $data 数据
	 * @return string xml数据
	 */
	static public function outXml($arr) {
		$XmlConstruct = new B_Xml('data');
		$XmlConstruct->fromArray($arr);
		$XmlConstruct->output();
	}

	/**
	 *
	 * 解析xml返回数组
	 * @param string $xmlUrl xml文件路径
	 * @return array
	 */
	static public function parseXml($xmlUrl) {
		$xmlStr = file_get_contents($xmlUrl);
		$arrObjData = simplexml_load_string($xmlStr);
		return self::_obj2arr($arrObjData);
	}

	static private function _obj2arr($arrObjData, $arrSkipIndices = array()) {
		$arrData = array();

		// if input is object, convert into array
		if (is_object($arrObjData)) {
			$arrObjData = get_object_vars($arrObjData);
		}

		if (is_array($arrObjData)) {
			foreach ($arrObjData as $index => $value) {
				if (is_object($value) || is_array($value)) {
					$value = self::_obj2arr($value, $arrSkipIndices); // recursive call
				}
				if (in_array($index, $arrSkipIndices)) {
					continue;
				}
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}

	/**
	 * 转换字节单位
	 * @param int $size 数据
	 * @return string
	 */
	static public function convert($size) {
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}

	static public function outGate($code, $msg, $params = array()) {
		$arr['ret'] = $code;
		$arr['msg'] = $msg;
		$ret = array_merge($arr, $params);
		echo json_encode($ret);
		exit;

	}

}