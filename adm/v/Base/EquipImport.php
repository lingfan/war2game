<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备导入' => '',
);
?>


<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>装备数据列表
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
		<br>
	</div>
</div>
<div class="table">
	<?php
	$i = 1;
	if (!empty($pageData['tip'])) {
		foreach ($pageData['tip'] as $k => $v):
			echo "{$k}#{$v}&nbsp;&nbsp;&nbsp;&nbsp;";
			if ($i % 7 == 0) {
				echo "<br>";
			}
			$i++;
		endforeach;
	}

	?>
	<?php if ($pageData['act'] == 'EquipListImport'): ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="Base/EquipListImport"/>

			<table class="listing form" cellpadding="0" cellspacing="0">

				<tr>
					<td style="width: 100px;">装备：</td>
					<td>
						<input type="file" name="equipcsvfile">
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td><input id="sub" type="submit" value="提交 "></td>
				</tr>
			</table>
		</form>
	<?php else: ?>
	<?php endif; ?>
</div>

