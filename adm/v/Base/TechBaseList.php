<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'科技基础列表' => 'Base/TechBaseList&page=1',
);
?>

<div class="top-bar">
	<h1>科技基础数据列表
		<a href="?r=Base/TechBaseAdd"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增基础科技</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">ID</th>
			<th width="50px">科技名</th>
			<th>科技介绍</th>
			<th width="60px">科技类型</th>
			<th width="60px">最大等级</th>
			<th width="70px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td style="text-align: left"><?php echo $val['features']; ?></td>
				<td><?php echo M_Tech::$type[$val['type']]; ?></td>
				<td><?php echo $val['max_level']; ?></td>
				<td>
					<a href="?r=Base/TechBaseAdd&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16" title="编辑" alt="编辑"></a>&nbsp;
					<a href="?r=Base/TechUpgList&page=1&id=<?php echo $val['id']; ?>">
						<img src="styles/adm/images/add-icon.gif" width="16" height="16" title="升级数据" alt="升级数据"></a>&nbsp;
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/DoTechBaseDel&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/del-icon.gif" width="16" height="16" title="删除" alt="删除"></a>
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
					echo "&nbsp;<a href='?r=Base/TechBaseList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>