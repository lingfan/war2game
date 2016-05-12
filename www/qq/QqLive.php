<?php
include('../common.php');

$info = M_Auth::getLoginCookie();
$cityId = !empty($info['city_id'])?$info['city_id']:0;
echo M_Qq::keepQQLive($cityId);
?>