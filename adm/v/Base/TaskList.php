<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'任务列表' => 'Base/TaskList&page=1',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>任务数据列表
		<a href="?r=Base/TaskAdd" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增任务</a>
		<a href="?r=Base/TaskCacheUp"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		   target="iframe">更新缓存</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="80px">任务名</th>
			<th width="50px">类型</th>
			<th>介绍</th>
			<th width="30px">排序</th>
			<th width="60px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) {
			?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['title']; ?></td>
				<td><?php echo M_Task::$type[$val['type']]; ?></td>
				<td style="text-align: left"><?php echo $val['desc_intro']; ?></td>
				<td><?php echo $val['sort']; ?></td>
				<td>
					<a href="?r=Base/TaskAdd&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                            width="16" height="16" title="编辑"
					                                                            alt="编辑"></a>&nbsp;
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/DoTaskDel&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
					                                                              width="16" height="16" title="删除"
					                                                              alt="删除"></a>
				</td>
			</tr>
		<?php } ?>
	</table>

	<div class="select">
		<strong>
			<?php
			foreach ($pageData['page']['range'] as $val) {
				if ($pageData['page']['curPage'] == $val) {
					echo "&nbsp;{$val}&nbsp;";
				} else {
					echo "&nbsp;<a href='?r=Base/TaskList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>