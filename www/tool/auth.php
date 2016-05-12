<?php
$now = time();
$key = 111;

$validated = false;
$auth = isset($_COOKIE['u']) ? $_COOKIE['u'] : '0|0|0';


if ($auth) {
	list($user, $expire, $sign) = explode('|', $auth);

	if ($sign == md5("{$user}{$expire}{$key}") && ($expire - $now) > 0) {
		$validated = true;
	}
}
$validated = true;

if (!$validated) {
	$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
	$pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

	$gmuser = array('huv520'=>'654321');
	if (isset($gmuser[$user])) {
		$expire = $now + 86400;
		$authVal = "{$user}|{$expire}|" . md5("{$user}{$expire}{$key}");
		setcookie('u', $authVal, $expire, '/');
	} else {
		header('WWW-Authenticate: Basic realm="dev"');
		header('HTTP/1.0 401 Unauthorized');
		die ("Not authorized");
	}
}
?>