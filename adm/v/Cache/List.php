<?php
$urlArr = array(
	'首页' => 'index/index',
	'缓存KEY列表' => '',
);
?>

<div class="top-bar">
	<h1>缓存KEY列表</h1>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>KEY名</th>
			<th>KEY名</th>
			<th>操作</th>
		</tr>
		<?php foreach ($list as $key => $val) { ?>
			<tr id="<?php echo $key; ?>">
				<td width="100px"><?php echo $key; ?></td>
				<td width="300px"><?php echo $val[0]; ?></td>
				<td width="300px"><?php echo $val[2]; ?></td>
				<td>
					<a href="?r=Cache/InfoList&key=<?php echo $key; ?>">查看</a>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
