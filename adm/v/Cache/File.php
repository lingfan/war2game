<?php
$urlArr = array(
	'首页' => 'index/index',
	'Bin文件' => '',
);
?>

<div class="top-bar">
	<h1>Bin文件列表</h1>
</div>

<div class="table">
	...
	<br>
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=Cache/FileBin&act=gen" target="iframe">更新Bin文件</a>
</div>
