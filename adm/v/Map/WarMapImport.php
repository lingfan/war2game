<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'地图导入' => '',
);
?>


<div class="top-bar">
	<h1>地图管理</h1>
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

	<br>导出每页100记录：
	<?php
	$p = $pageData['page'];
	for ($i = 1; $i <= $p; $i++) {
		echo '<a href="?r=Map/WarMapExport&p=' . $i . '">' . $i . '</a>&nbsp;';
	}
	?>
	<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="r" value="Map/WarMapImport"/>

		<table class="listing form" cellpadding="0" cellspacing="0">

			<tr>
				<td style="width: 100px;">战场地图：</td>
				<td>
					<input type="file" name="csvfile">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value="提交 "></td>
			</tr>
		</table>
	</form>
</div>

