<?php
include('../common.php');
Logger::pay(json_encode($_REQUEST), 'req');


$args = array(
	'openid' => FILTER_SANITIZE_STRING,
	'appid' => FILTER_SANITIZE_STRING,
	'ts' => FILTER_SANITIZE_STRING,
	'payitem' => FILTER_SANITIZE_STRING,
	'token' => FILTER_SANITIZE_STRING,
	'billno' => FILTER_SANITIZE_STRING,
	'amt' => FILTER_SANITIZE_STRING,
	'sig' => FILTER_SANITIZE_STRING,
	'payamt_coins' => FILTER_SANITIZE_STRING,
	'pubacct_payamt_coins' => FILTER_SANITIZE_STRING,
	'zoneid' => FILTER_SANITIZE_STRING,
);
$formVals = filter_var_array($_REQUEST, $args);
$data = array('ret' => 4, 'msg' => "param err：[" . $formVals['sig'] . "]");

if (!empty($formVals['openid']) && !empty($formVals['payitem']) && $formVals['billno']) {
	$newUsername = md5($formVals['openid'] . $formVals['zoneid']);
	$userInfo = M_User::getInfoByUsername($newUsername);

	if ($formVals['zoneid'] != $userInfo['server_id']) {
		$data = array('ret' => 4, 'msg' => "zoneid err {$formVals['zoneid']}=>{$userInfo['server_id']}：[" . $formVals['sig'] . "]");
		echo json_encode($data);
		exit;
	}

	if (!empty($userInfo['id'])) {
		$cityId = M_City::getCityIdByUserId($userInfo['id']);
		if ($cityId > 0) { //用户名|时间|IP|订单编号|人民币|军饷比例|礼券比例|服务器ID|运营商名称|校验码
			//执行添加游戏货币i
			$arr = explode('*', $formVals['payitem']);
			//Logger::qq(array(111,$arr));
			if (!empty($arr[1]) && !empty($arr[2])) {
				$milpay = $arr[1] * $arr[2];
				$rmb = (intval($formVals['amt'] / 10) + $formVals['payamt_coins']) / 10;
				$orderInfo = array(
					'city_id' => $cityId,
					'server_id' => $formVals['zoneid'],
					'username' => $userInfo['username'],
					'username_ext' => $userInfo['username_ext'],
					'consumer_id' => $userInfo['consumer_id'],
					'order_no' => $formVals['billno'],
					'rmb' => $rmb,
					'mil_pay' => $milpay,
					'coupon' => 0,
					'ip' => B_Utils::getIp(),
					'pay_time' => $formVals['ts'],
				);
				$flag = false;
				$prefix = strval($arr[0]);
				//Logger::qq(array(222,$prefix));
				if ($prefix[0] == 'G') {
					$orderInfo['data'] = json_encode(array('qq' => $prefix));
					$flag = M_Pay::call($cityId, $orderInfo);
				} else if ($prefix[0] == 'P') {
					$pid = intval(substr($arr[0], 1));
					$propsInfo = M_Props::baseInfo($pid);
					$ret = $objPlayer->Pack()->incr($propsInfo['id'], $arr[2]);
					if ($ret) {
						M_Task_Action::PropsBuyProps($cityId); //完成任务(购买道具完成)

						$orderInfo['data'] = json_encode(array('qq' => $prefix));
						$flag = M_Pay::call($cityId, $orderInfo, true);
					}
				} else if ($prefix[0] == 'E') {
					$ret = array();
					$eid = intval(substr($arr[0], 1));
					$tplInfo = M_Equip::baseInfo($eid);
					//Logger::qq(array(222,$prefix));
					if ($tplInfo['id']) {
						for ($i = 0; $i < $arr[2]; $i++) {
							$ret[] = M_Equip::makeEquip($cityId, $tplInfo);
						}
					}
					if (count($ret) == $arr[2]) {
						$orderInfo['data'] = json_encode(array('qq' => $prefix));
						$flag = M_Pay::call($cityId, $orderInfo, true);
					} else {
						Logger::qq(array('Msg' => 'Error buy_EQUIP', 'Data' => $cityId, 'Params' => array($eid, $arr[2], $ret)));
					}
				} else if ($prefix[0] == 'H') {
					$ret = array();
					$hid = intval(substr($arr[0], 1));
					if ($tplInfo['id']) {
						for ($i = 0; $i < $arr[2]; $i++) {
							$ret[] = M_Hero::moveTplHeroToCityHero($cityId, $hid, B_Logger::H_MALL_BUY);
						}
					}
					if (count($ret) == $arr[2]) {
						$orderInfo['data'] = json_encode(array('qq' => $prefix));
						$flag = M_Pay::call($cityId, $orderInfo, true);
					} else {
						Logger::qq(array('Msg' => 'Error buy_HERO', 'Data' => $cityId, 'Params' => array($hid, $arr[2], $ret)));
					}
				}

				if ($flag) {

					$data = array('ret' => 0, 'msg' => 'ok');
				}
			}
		}
	}
}

header('Content-Type:text/html;charset=utf-8');
echo json_encode($data);
exit;
?>