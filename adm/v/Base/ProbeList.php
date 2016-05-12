<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'事件列表' => 'Base/ProbeList',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=Base/ProbeView" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加事件</a>
	<a href="?r=Base/ProbeExport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/ProbeImport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/ProbeCacheUp" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">更新缓存</a>

	<h1>事件列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="200px">事件内容</th>
			<th width="50px">类型</th>
			<th width="60px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td style="text-align: left;"><?php echo $val['title']; ?></td>
				<td><?php echo $val['type']; ?></td>
				<td>
					<a href="?r=Base/ProbeView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                              width="16" height="16" title="编辑"
					                                                              alt="编辑"></a>&nbsp;
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/ProbeDel&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
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
					echo "&nbsp;<a href='?r=Base/ProbeList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>