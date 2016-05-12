<?php
include('common.php');
include('./tool/auth.php');

$pageData = array();
$args = array(
	'username' => FILTER_SANITIZE_STRING,
	'cid' => FILTER_SANITIZE_NUMBER_INT,
	'isadult' => FILTER_SANITIZE_NUMBER_INT,
	'server' => FILTER_SANITIZE_NUMBER_INT,
);
$formVals = filter_var_array($_REQUEST, $args);

if ($formVals['username'] && $formVals['cid'] && $formVals['server']) {
	$adult = $formVals['isadult'];
	$username = trim($formVals['username']);
	$cid = trim($formVals['cid']);
	$cInfo = M_Consumer::getById($cid);
	if (!empty($cInfo['key'])) {
		$now = date('YmdHis');
		$sign = md5("{$cid}&{$cInfo['key']}&{$username}&{$formVals['server']}&{$now}");
		B_Common::redirect("login.php?oid={$cid}&name={$username}&sid={$formVals['server']}&t={$now}&sign={$sign}&adult={$adult}");
	} else {
		echo 'err consumer'; //检查pm系统中[服务器&运营商]是否正确对应
		exit;
	}
}
?>

	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<HTML>
	<HEAD>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<TITLE></TITLE>
	</HEAD>
	<BODY>
	<form action='' method='get' name='login'>
		<input type="hidden" name="r" value='test/login'/>
		<input type="hidden" name="cid" value="1"/>
		用户名:
		<input type="text" name="username" value="" style="width: 100px"/>&nbsp;&nbsp;
		服务器ID:
		<input type="text" name="server" value="1" style="width: 50px"/> &nbsp;&nbsp;
		成年:
		<input type="radio" name="isadult" value="1" checked/>是&nbsp;&nbsp;
		<input type="radio" name="isadult" value="0"/>否
		<input name="submit" value="登录" type="submit"/>
	</form>
	</BODY>
	</HTML>

<?php
// }
?>