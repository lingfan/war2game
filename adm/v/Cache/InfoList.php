<?php
$urlArr = array(
	'首页' => 'index/index',
	'缓存KEY列表' => '',
);
?>

<div class="top-bar">
	<h1>缓存KEY信息列表</h1>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>KEY名 共 <?php echo count($list); ?>个</th>
			<th>操作 <a href="?r=Cache/CleanKey&key=<?php echo $val; ?>">清空</a></th>
		</tr>
		<?php
		foreach ($list as $key => $val) {
			?>
			<tr id="<?php echo $key; ?>">
				<td width="500px"><?php echo $val; ?></td>
				<td>
					<!--
				<a href="?r=Cache/Info&key=<?php echo $val; ?>&suffix=">查看</a>
				<a href="?r=Cache/DelKey&key=<?php echo $val; ?>">删除</a>
				 -->
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
