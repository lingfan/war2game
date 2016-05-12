<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'科技基础列表' => 'Base/TechBaseList&page=1',
	'科技升级列表' => 'Base/TechUpgList&id=' . $pageData['id'] . '&page=1',
);
?>

<div class="top-bar">
	<h1><?php echo $pageData['techBase'][$pageData['id']]['name']; ?> 升级数据列表
		<a href="?r=Base/TechUpgAdd&id=<?php echo $pageData['id']; ?>"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增科技升级</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th>等级</th>
			<th>金钱</th>
			<th>粮食</th>
			<th>石油</th>
			<th>时间</th>
			<th>前提建筑</th>
			<th>前提科技</th>
			<th>科技效果</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) {
			$arrNeedBuild = json_decode($val['need_build'], true);
			$arrNeedTech = json_decode($val['need_tech'], true);
			$arrEffecat = json_decode($val['effect'], true);
			?>
			<tr>
				<td><?php echo $val['level']; ?></td>
				<td><?php echo $val['cost_gold']; ?></td>
				<td><?php echo $val['cost_food']; ?></td>
				<td><?php echo $val['cost_oil']; ?></td>
				<td><?php echo $val['cost_time']; ?></td>
				<td><?php echo (count($arrNeedBuild) > 0) ? $pageData['buildBase'][key($arrNeedBuild)]['name'] . '=' . current($arrNeedBuild) : '_'; ?></td>
				<td><?php echo (count($arrNeedTech) > 0) ? $pageData['techBase'][key($arrNeedTech)]['name'] . '=' . current($arrNeedTech) : '_'; ?>
				<td><?php echo (count($arrEffecat) > 0) ? T_Effect::$Tech[key($arrEffecat)] . '=' . current($arrEffecat) . ' %' : '_'; ?>
				<td>
					<a href="?r=Base/TechUpgAdd&id=<?php echo $val['tech_id']; ?>&level=<?php echo $val['level']; ?>">
						<img src="styles/adm/images/edit-icon.gif" width="16" height="16" title="编辑" alt="编辑"></a>
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/DoTechUpgDel&id=<?php echo $val['tech_id']; ?>&level=<?php echo $val['level']; ?>">
						<img src="styles/adm/images/del-icon.gif" width="16" height="16" title="删除" alt="删除"></a>
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
					echo "&nbsp;<a href='?r=Base/TechUpgList&page={$val}&id={$pageData['id']}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>