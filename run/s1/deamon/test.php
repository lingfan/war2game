#!/usr/bin/env php
<?php
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);
while (true) {
	for ($i = 1; $i < 140; $i++) {
		$cityId = $i;
		$info = M_City::getInfo($cityId);

		$info['city_id'] = $cityId;
		$expire = time() + T_App::ONE_DAY; //过期时间1天
		$ip = B_Utils::getIp();
		$sessId = uniqid('war2_');
		$data = array(
			'user_id' => $info['user_id'],
			'sess_id' => $sessId,
			'city_id' => $info['city_id'],
			'ip_addr' => $ip,
			'expire' => $expire,
			'verify' => sha1($info['user_id'] . '|' . $sessId . '|' . $info['city_id'] . '|' . $ip . '|' . $expire . '|' . M_Auth::COOKIE_KEY),
		);


		$cookieStr = B_Crypt::encode(json_encode($data));
		M_Auth::addOnline($info['user_id'], $sessId, $ip);
		$apiurl = "http://www.mswar2.com/dev.php?r=city/info";
		$r = new HttpRequest($apiurl, HttpRequest::METH_GET);

		$r->setOptions(array('cookies' => array('A' => $cookieStr)));
		$out = $r->send()->getBody();
		echo json_encode($out);
		var_dump($i);


	}
	usleep(1000);
}

?>
