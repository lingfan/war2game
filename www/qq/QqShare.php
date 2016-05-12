<?php
include('../common.php');
$params = array();
$args = array(
	'share_id' => FILTER_SANITIZE_STRING,
	'mode' => FILTER_SANITIZE_NUMBER_INT,
);

$formVals = filter_var_array($_REQUEST, $args);

$out = json_encode(array('ret' => 9, 'msg' => 'error_data'));
if (empty($formVals['share_id'])) {
	echo json_encode($out);
	exit;
}

$cityId = M_Auth::myCid();
$objPlayer = new O_Player($cityId);
$cityInfo = $objPlayer->getCityBase();
if (!empty($cityInfo['user_id'])) {
	$cityId = $cityInfo['id'];
	$userId = $cityInfo['user_id'];
	$qqInfo = M_Qq::getQQLive($userId);
	$baseInfo = M_QqShare::getBaseInfoById($formVals['share_id']);
	if (!empty($qqInfo) && !empty($baseInfo['award_id'])) {
		if ($formVals['mode'] == 1) {
			$baseInfo['server_res_url'] = M_Config::getSvrCfg('server_res_url');
			$out = json_encode($baseInfo);
		} else if ($formVals['mode'] == 2) {
			$cityShareInfo = M_QqShare::getInfo($cityId);
			$award = !empty($cityShareInfo['award']) ? json_decode($cityShareInfo['award']) : array();

			$award[] = $formVals['share_id'];
			$data = array('city_id' => $cityId, 'award' => json_encode($award));
			$bUp = M_QqShare::setInfo($data);
			if ($bUp) {
				$awardArr = M_Award::rateResult($baseInfo['award_id']);
				$objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
				$objPlayer->save();

				$rc = new B_Cache_RC(T_Key::SUCCESS_SHARE_TIMES, date('Ymd') . '_' . $cityId . '_' . $qqInfo['pf']);
				$num = $rc->incrby(1);
				$out = json_encode(array('ret' => 0, 'msg' => 'qq_share_succ'));
			}
		}
	}
}

header('Content-Type:text/html;charset=utf-8');
echo $out;
exit;
?>