<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'权限组列表' => 'Manger/UserGroup',
);
?>
<div class="top-bar">
	<a href="?r=Manger/GroupView" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>权限组列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">

	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>权限组名称</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val): ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td>
					<a href="#"><img src="styles/adm/images/del-icon.gif" width="16" height="16" alt="删除"/></a>
					<a href="?r=Manger/GroupView&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16" alt="编辑"/></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

</div>