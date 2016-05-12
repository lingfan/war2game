<?php
$pageData = B_View::getVal('pageData');
$info = $pageData['info'];
$urlArr = array(
	'首页' => 'index/index',
	'管理员列表' => 'Manger/List',
	isset($info['id']) ? '修改密码' : '添加' => '',
);

?>

<div class="top-bar">
	<h1>用户管理</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none"></iframe>
	<form action="?r=Manger/update" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<input type="hidden" name='id' value='<?php echo isset($info['id']) ? $info['id'] : '' ?>'>
			<tr>
				<td width="172"><strong>用户账号</strong></td>
				<td><input type="text" class="text" name="username"
				           value="<?php echo isset($info['username']) ? $info['username'] : '' ?>" <?php echo isset($info['username']) ? 'readonly="readonly"' : ''; ?>/>
				</td>
			</tr>

			<tr>
				<td><strong>用户昵称</strong></td>
				<td><input type="text" class="text" name="nickname"
				           value="<?php echo isset($info['nickname']) ? $info['nickname'] : '' ?>" <?php echo isset($info['nickname']) ? 'readonly="readonly"' : ''; ?>/>
				</td>
			</tr>

			<tr>
				<td><strong>权限组</strong></td>
				<td>
					<select name="group_id" <?php echo isset($info['group_id']) ? 'disabled="disabled"' : ''; ?>>
						<?php foreach ($pageData['groupList'] as $key => $val) { ?>
							<option
								value="<?php echo $val['id']; ?>" <?php echo $info['group_id'] == $val['id'] ? 'selected="selected"' : ''; ?>><?php echo $val['name']; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<?php if (isset($info['id'])) { ?>
				<tr>
					<td><strong>旧密码</strong></td>
					<td><input type="password" class="text" name="oldpwd" value=""/></td>
				</tr>

				<tr>
					<td><strong>新密码</strong></td>
					<td><input type="password" class="text" name="password2" value=""/></td>
				</tr>

				<tr>
					<td><strong>确认新密码</strong></td>
					<td><input type="password" class="text" name="password" value=""/></td>
				</tr>
			<?php } else { ?>
				<tr>
					<td><strong>密码</strong></td>
					<td><input type="password" class="text" name="password" value=""/></td>
				</tr>
			<?php } ?>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>