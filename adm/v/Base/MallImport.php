<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'商城物品导入' => '',
);
?>


<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>商城物品数据列表
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
	<?php if ($pageData['act'] == 'MallListImport'): ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="Base/MallListImport"/>

			<table class="listing form" cellpadding="0" cellspacing="0">

				<tr>
					<td style="width: 100px;">商城物品：</td>
					<td>
						<input type="file" name="mallcsvfile">
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
	商城栏目[1内政道具,2军官道具,3宝物道具,4战斗道具,5图纸,6点券商城,7材料]
</div>

