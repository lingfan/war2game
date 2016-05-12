#!/usr/bin/env php
<?php
//合服后数据校正操作
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

M_Merge::verfiyData();
?>