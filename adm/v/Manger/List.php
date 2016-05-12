<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'管理员列表' => '',
);
?>
<div class="top-bar">
	<a href="?r=Manger/View"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>管理员列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="select-bar">
	<label>
		<input type="text" name="textfield"/>
	</label>
	<label>
		<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit"
		       name="Submit" value="Search"/>
	</label>
</div>
<div class="table">
	<iframe name="iframe" style="display: none"></iframe>
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>用户账号</th>
			<th>用户呢称</th>
			<th>权限组</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val): ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['username']; ?></td>
				<td><?php echo $val['nickname'] ? $val['nickname'] : '--'; ?></td>

				<td><?php echo $val['group_id']; ?></td>
				<td>
					<a href="?r=Manger/del&id=<?php echo $val['id']; ?>" target="iframe"
					   onclick="return confirm('删除后无法恢复,确定要删除吗')"><img src="styles/adm/images/del-icon.gif" width="16"
					                                                   height="16" alt="删除"/></a>
					<a href="?r=Manger/View&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                           width="16" height="16" alt="编辑"/></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="select" style="width: 550px;">
		<strong>
			<?php
			foreach ($pageData['page']['range'] as $val) {
				if ($pageData['page']['curPage'] == $val) {
					echo "&nbsp;{$val}&nbsp;";
				} else {
					echo "&nbsp;<a href='?r=Manger/List&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>