<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'兵种' => 'System/Config&type=army',
	'英雄' => 'System/Config&type=hero',
	'装备' => 'System/Config&type=equip',
);
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>兵种配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<form action="?r=Base/DoBuildBaseAdd" method="post">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">用户信息</th>
			</tr>
			<tr>
				<td width="172"><strong>元首名称</strong></td>
				<td><input type="text" class="text" name="nickname"
				           value="<?php echo !empty($info['nickname']) ? $info['nickname'] : '' ?>"/></td>
			</tr>

			<tr>
				<td><strong>用户昵称</strong></td>
				<td><input type="text" class="text" name="username"
				           value="<?php echo !empty($info['username']) ? $info['username'] : '' ?>"/></td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
