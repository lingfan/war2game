<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'NPC导入' => '',
);
?>


<div class="top-bar">
	<h1>NPC管理</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/NpcHeroList">NPC英雄列表</a></div>
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
	<?php if ($pageData['act'] == 'NpcHeroListImport'): ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="War/NpcHeroListImport"/>

			<table class="listing form" cellpadding="0" cellspacing="0">

				<tr>
					<td style="width: 100px;">NPC英雄：</td>
					<td>
						<input type="file" name="npcherocsvfile">
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td><input id="sub" type="submit" value="提交 "></td>
				</tr>
			</table>
		</form>
	<?php else: ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="War/NpcListImport"/>
			<table class="listing form" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width: 100px;">NPC部队：</td>
					<td>
						<input type="file" name="npccsvfile">
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td><input id="sub" type="submit" value=" 提交 "></td>
				</tr>

				<tr>
					<td>NPC部队类型</td>
					<td>1步兵学院 2炮兵学院 3装甲兵学院 4航空兵学院 5道具NPC 6图纸NPC 7副本NPC 8据点NPC 9金钱资源NPC 10食物资源NPC 11石油资源NPC 12临时NPC
						13突围NPC
					</td>
				</tr>
			</table>
		</form>
	<?php endif; ?>
</div>

