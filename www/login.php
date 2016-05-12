<?php
$sTime = microtime(1);
include('common.php');


$args = array(
	'name' => FILTER_SANITIZE_STRING,
	'oid' => FILTER_SANITIZE_NUMBER_INT,
	'sid' => FILTER_SANITIZE_STRING,
	't' => FILTER_SANITIZE_NUMBER_INT,
	'sign' => FILTER_SANITIZE_STRING,
	'adult' => FILTER_SANITIZE_NUMBER_INT,
);

$formVals = filter_var_array($_REQUEST, $args);

header('Content-Type:application/json;charset=utf-8');

if (empty($formVals['name']) ||
	empty($formVals['oid']) ||
	empty($formVals['sid']) ||
	empty($formVals['t']) ||
	empty($formVals['sign'])
) {
	echo json_encode(array('ret' => -1, 'err' => 'args'));
	exit;
}

$difftime = time() - strtotime($formVals['t']);
if (abs($difftime) > 900) {
	echo json_encode(array('ret' => -1, 'err' => 't'));
	exit;
}


$opInfo = M_Consumer::getById($formVals['oid']);
if (empty($opInfo['id'])) {
	echo json_encode(array('ret' => -1, 'err' => 'oid'));
	exit;
}

$key = $opInfo['key'];
$sign = md5("{$formVals['oid']}&{$key}&{$formVals['name']}&{$formVals['sid']}&{$formVals['t']}");
if ($sign != strtolower($formVals['sign'])) {
	echo json_encode(array('ret' => -1, 'err' => 'sign'));
	exit;
}

$maintenance = M_Config::getSvrCfg('maintenance');
$now = time();
$m_start = strtotime($maintenance['start']);
$m_end = strtotime($maintenance['end']);
if (($m_start <= $m_end) && ($m_start < $now) && ($m_end > $now)) {
	echo json_encode(array('ret' => 1, 'err' => $maintenance['msg']));
	exit;
}

M_Auth::render($formVals['name'],$formVals['oid'],$formVals['sid']);