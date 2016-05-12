<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'道具导入' => '',
);
?>


<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>道具数据列表
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
	<?php if ($pageData['act'] == 'PropsListImport'): ?>
		<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="r" value="Base/PropsListImport"/>

			<table class="listing form" cellpadding="0" cellspacing="0">

				<tr>
					<td style="width: 100px;">道具：</td>
					<td>
						<input type="file" name="propscsvfile">
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

	<table class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<td style="width: 400px;">
				道具效果值列表:
				<table class="listing form" cellpadding="0" cellspacing="0">
					<?php
					foreach (T_Effect::$Props as $k => $name):?>
						<tr>
							<td style="width: 30px;"><?php echo $k; ?></td>
							<td style="width: 300px;"><?php echo $name; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</td>
			<td align=left>
				道具类型列表:
				<table class="listing form" cellpadding="0" cellspacing="0">
					<?php
					foreach (M_Props::$type as $k => $name):?>
						<tr>
							<td style="width: 30px;"><?php echo $k; ?></td>
							<td><?php echo $name; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</td>
		</tr>
	</table>
</div>

