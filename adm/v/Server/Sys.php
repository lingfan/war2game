<?php
$pageData = B_View::getVal('pageData');
$arr = $list = array();
foreach ($pageData['list'] as $key => $val) {
	$list[$key] = $val;
	$arr[$val[3]]['cpu'] = isset($arr[$val[3]]['cpu']) ? $arr[$val[3]]['cpu'] + $val[1] : $val[1];
	$arr[$val[3]]['mem'] = isset($arr[$val[3]]['mem']) ? $arr[$val[3]]['mem'] + $val[2] : $val[2];
	$arr[$val[3]]['num'] = isset($arr[$val[3]]['num']) ? $arr[$val[3]]['num'] + 1 : 1;
}

?>
<style>
	td {
		word-break: break-all;
		width: 200px;
	}
</style>
<div class="table">
	进程概况:
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">CMD</th>
			<th width="50px">CPU VAL</th>
			<th width="50px">MEM VAL</th>
			<th width="50px">NUM</th>
		</tr>
		<?php
		foreach ($arr as $key => $val) {
			?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $val['cpu']; ?></td>
				<td><?php echo $val['mem']; ?></td>
				<td><?php echo $val['num']; ?></td>
			</tr>
		<?php } ?>
	</table>

	进程详细:
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">PID</th>
			<th width="50px">CPU</th>
			<th width="50px">MEM</th>
			<th width="50px">CMD</th>
		</tr>
		<?php
		$i = 0;
		foreach ($list as $key => $val) {
			$i++;
			?>
			<tr>
				<td><?php echo $val[0]; ?></td>
				<td><?php echo $val[1]; ?></td>
				<td><?php echo $val[2]; ?></td>
				<td><?php echo $val[3]; ?></td>
			</tr>
		<?php } ?>
	</table>
	共有 <?php echo $i; ?> 个进程

</div>

