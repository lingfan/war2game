<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'突围导入' => '#',
);
?>


<div class="top-bar">
	<h1>突围管理</h1>

	<div class="breadcrumbs"><a href="?r=Base/BoutList">突围列表</a></div>
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
	<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="r" value="Base/BoutImport"/>
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 100px;">突围列表：</td>
				<td>
					<input type="file" name="boutcsvfile">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value="提交 "></td>
			</tr>
		</table>
	</form>
</div>

