<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'非NPC武器列表' => 'Base/WeaponList&page=1&is_npc=' . M_Weapon::NOTNPC,
	'NPC武器列表' => 'Base/WeaponList&page=1&is_npc=' . M_Weapon::NPC,
);
$is_npc = $pageData['is_npc'];
$shotType = array('1' => '直线型', '2' => '弧线型');

?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>武器数据列表

		<a href="?r=Base/WeaponAdd"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增武器</a>
		<a href="?r=Base/WeaponListImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
		<a href="?r=Base/WeaponCacheUp"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		   target="iframe">更新缓存</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
		<br/>
		<?php
		echo '<a href="?r=Base/WeaponListExport&p=1">全部</a>&nbsp;';
		?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="70px">武器名</th>
			<th width="50px">可装备兵种</th>
			<th width="30px">兵种需要等级</th>
			<th width="40px">出征系</th>
			<th width="40px">移动类型</th>
			<th width="40px">是否特殊</th>
			<th width="40px">是否NPC</th>
			<th width="40px">射击类型</th>
			<th width="60px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo M_Army::$type[$val['army_id']]; ?></td>
				<td><?php echo $val['need_army_lv']; ?></td>
				<td><?php echo M_War::$marchType[$val['march_type']]; ?></td>
				<td><?php echo M_Weapon::$moveType[$val['move_type']]; ?></td>
				<td><?php echo $val['is_special'] ? '是' : '-'; ?></td>
				<td><?php echo $val['is_npc'] ? '是' : '-'; ?></td>
				<td><?php echo $shotType[$val['shot_type']]; ?></td>
				<td>
					<a href="?r=Base/WeaponAdd&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                              width="16" height="16" title="编辑"
					                                                              alt="编辑"></a>&nbsp;
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/DoWeaponDel&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
					                                                                width="16" height="16" title="删除"
					                                                                alt="删除"></a>
				</td>
			</tr>
		<?php } ?>
	</table>


</div>