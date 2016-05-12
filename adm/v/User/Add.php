<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'用户列表' => 'user/list',
	'用户添加' => '',
);

$info = $pageData['info'];
?>

<div class="top-bar">
	<h1>用户管理</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<form action="?r=user/update" method="post">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<input type="hidden" name='id' value='<?php echo !empty($info['id']) ? $info['id'] : '' ?>'>
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