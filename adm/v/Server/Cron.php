<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>分</th>
			<th>时</th>
			<th>日</th>
			<th>月</th>
			<th>星期</th>
			<th>脚本</th>
		</tr>
		<?php
		$pageData = B_View::getVal('pageData');
		foreach ($pageData['list'] as $key => $val) {
			?>
			<tr>
				<td><?php echo $val[0]; ?></td>
				<td><?php echo $val[1]; ?></td>
				<td><?php echo $val[2]; ?></td>
				<td><?php echo $val[3]; ?></td>
				<td><?php echo $val[4]; ?></td>
				<td><?php echo $val[5]; ?> <?php echo isset($val[6]) ? $val[6] : ''; ?></td>
			</tr>
		<?php } ?>
	</table>

</div>
