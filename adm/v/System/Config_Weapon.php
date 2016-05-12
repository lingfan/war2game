<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
);
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>武器配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigWeapon&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td><strong>租借武器设置</strong></td>
				<td>
					武器ID,租借时间(小时),军饷,军械所等级
					<br>
					<?php
					$arr = $baselist['temp_weapon'];
					$tmp = array();
					foreach ($arr as $id => $val) {
						array_unshift($val, $id);
						$tmp[] = implode(",", $val);
					}
					?>
					<textarea name="temp_weapon" cols="50" rows="10"><?php echo implode("\n", $tmp); ?></textarea>
				</td>
			</tr>

			<tr>
				<td></td>
				<td>
					<input type="submit" class="button" name="submit" value="保存"/>
				</td>
			</tr>
		</table>
	</form>
</div>

