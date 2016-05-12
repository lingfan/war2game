<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'套装装备导入' => '',
);
?>


<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>套装装备数据列表
	</h1>

	<div class="breadcrumbs">

		<a href="#">首页</a> / <a href="?r=Base/EquipList">装备列表</a> / <a href="?r=Base/EquipSuit">套装列表</a> / <a
			href="?r=Base/EquipSuitImport">套装装备导入</a> <span id="msg"
		                                                    style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
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
	<?php if ($pageData['act'] == 'EquipSuitImport'): ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="Base/EquipSuitImport"/>

			<table class="listing form" cellpadding="0" cellspacing="0">

				<tr>
					<td style="width: 100px;">装备：</td>
					<td>
						<input type="file" name="equipsuitcsvfile">
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

