<?php
$_action = isset($_GET['r']) ? $_GET['r'] : 'index/index';
$_parts = explode('/', $_action);

$C = ucfirst($_parts[0]);
$A = ucfirst($_parts[1]);
return array($C, $A);
?>