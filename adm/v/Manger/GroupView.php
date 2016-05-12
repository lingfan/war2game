<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'权限组列表' => 'Manger/UserGroup',
);
?>
<script type="text/javascript">
	function bt(the, a) {
		//alert($("#"+a).is(":checked"));
		if (the.checked) {
			$("#" + a + ">li>input").attr('checked', 'checked');
		}
		else {
			$("#" + a + ">li>input").attr('checked', '');
		}
	}

</script>

<div class="top-bar">
	<h1>权限组操作</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=Manger/EditFlagGroup" method="post" target="iframe">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="2">添加/修改权限组</th>
			</tr>
			<tr>
				<td style="width: 100px;">权限组名称</td>
				<td style="text-align: left;">
					<input type="hidden" name="id"
					       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : ''; ?>">
					<input type="text" name="name"
					       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
				</td>
			</tr>
			<tr>
				<td>模块选择</td>
				<td style="text-align: left;">
					<?php
					$DataMenu = require('../DataMenu.php');
					$list = isset($pageData['info']['flag']) ? json_decode($pageData['info']['flag'], true) : array();
					?>
					<?php foreach ($DataMenu as $key => $val) { ?>
						<input type="checkbox" name="flag[]"
						       value="<?php echo $key; ?>" <?php if (in_array($key, $list)) echo 'checked="checked"'; ?>
						       onclick="bt(this,'<?php echo $key; ?>')"> <?php echo $val['name']?><!--  <a href="#">全选/全不选</a> -->
						<ul id="<?php echo $key ?>">
							<?php foreach ($val['sub'] as $k => $v) { ?>
								<li><input type="checkbox" name="flag[]"
								           value="<?php echo $k; ?>" <?php if (in_array($k, $list)) echo 'checked="checked"'; ?>><?php echo $v; ?>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" value="保存">
				</td>
			</tr>
		</table>
	</form>

</div>