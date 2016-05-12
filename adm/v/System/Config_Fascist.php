<?php
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
);
$basecfg = M_Config::getVal();
$wild_fixed_npc = isset($basecfg['wild_fixed_npc']) ? $basecfg['wild_fixed_npc'] : array();
$arr = array();
$t = array();
foreach ($wild_fixed_npc as $key => $val) {

	$npcInfo = M_NPC::getInfo($key);
	$arr[$key] = !empty($npcInfo['nickname']) ? $npcInfo['nickname'] . '|' . $npcInfo['level'] : 'no exist';

	foreach ($val['npc_awardArr'] as $k => $v) {
		$t[$key][] = "{$k}:{$v}";
	}
}


?>
<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=System/FascistAddCacheUp"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" target="iframe">更新缓存</a>
	<a href="?r=System/FascistAdd"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>固定法西斯配置</h1>

	<div class="breadcrumbs"><a href="#">系统管理</a> / <a href="#">固定法西斯配置</a> <span id="msg"
	                                                                              style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<table id="list" class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th>NPC部队</th>
			<th>开始时间</th>
			<th>结束时间</th>
			<th>所在洲</th>
			<th>坐标</th>
			<th>广播</th>
			<th>世界频道内容</th>
			<th>广播时间</th>
			<th>出现时广播内容</th>
			<th>奖励数组</th>
			<th>操作</th>
		</tr>
		<?php foreach ($wild_fixed_npc as $key => $val) {
			?>
			<tr id="<?php echo $key; ?>">
				<td><?php echo $key; ?></td>
				<td><?php echo $val['npc_start']; ?></td>
				<td><?php echo $val['npc_end']; ?></td>
				<td><?php echo $val['npc_zone']; ?></td>
				<td><?php echo $val['npc_pos']; ?></td>
				<td><?php echo $val['broadcast']; ?></td>
				<td><?php echo $val['channel']; ?></td>
				<td><?php echo $val['broadcast_start'] . ' - ' . $val['broadcast_end']; ?></td>
				<td><?php echo $val['out_broadcast']; ?></td>
				<td><?php echo implode(";", $t[$key]); ?></td>
				<td>
					<a href="?r=System/FascistAdd&act=1&id=<?php echo $key; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16" alt="edit"/></a>
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=System/FascistDelete&id=<?php echo $key; ?>"><img src="styles/adm/images/del-icon.gif"
					                                                              width="16" height="16" alt="del"/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<table class="listing form" cellpadding="0" cellspacing="0">
		<?php
		foreach ($arr as $key => $name) {
			$awardArr = $wild_fixed_npc[$key]['npc_awardArr'];
			?>
			<tr>
				<td><?php echo "{$key}=>{$name}<br>";
					?>
				</td>

				<td>
					<?php
					foreach ($awardArr as $awardId) {
						echo $awardId;
						echo B_Utils::awardText($awardId);
					}
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
</div>