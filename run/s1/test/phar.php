<?php

// 要打包是需要在php.ini设置phar.readonly = off的，默认是on


$str = date('Ymd');
$str = 'v1';
$phar = new Phar('/opt/ww2/srv/' . $str . '.phar', 0, $str . '.phar');
$phar->buildFromDirectory('/opt/ww2/srv/v1');
?>
