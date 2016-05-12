<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'突围列表' => 'Base/ProbeList',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>

	<a href="?r=Base/BoutExport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/BoutImport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/BoutCacheUp" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">更新缓存</a>

	<h1>事件列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="15px">ID</th>
			<th width="25px">是否开放</th>
			<th width="20px">开放周</th>
			<th width="25px">开始时间</th>
			<th width="25px">结束时间</th>
			<th width="50px">下一个突围ID</th>
			<th width="15px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['is_open']; ?></td>
				<td><?php echo $val['open_week']; ?></td>
				<td><?php echo $val['open_start_time']; ?></td>
				<td><?php echo $val['open_end_time']; ?></td>
				<td><?php echo $val['next_boutid']; ?></td>
				<td>
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/BoutDel&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
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
					echo "&nbsp;<a href='?r=Base/BoutList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>