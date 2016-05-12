<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'据点导入' => '#',
);
?>


<div class="top-bar">
	<h1>据点管理</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=Base/CampaignList">据点列表</a></div>
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
		<input type="hidden" name="r" value="Base/CampaignImport"/>
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 100px;">据点列表：</td>
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

