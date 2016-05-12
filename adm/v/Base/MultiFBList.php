<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'多人副本数据' => 'Base/MultiFBList',
);
?>
<style>
	#list tr td {
		background-color: lightgray;
	}
</style>

<iframe name="iframe" style="display: none;"></iframe>
<div class="top-bar">
	<a href="?r=Base/MultiFBExport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/MultiFBImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/MultiFBClean" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">清除缓存</a>

	<h1>多人副本管理</h1>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width: 10px;">ID <a href="?r=Base/HeroList" title="按ID升序排序" style="color: white;">↑↑</a></th>
			<th style="width: 30px;">类型</th>
			<th style="width: 30px;">副本编号</th>
			<th style="width: 30px;">城市等级</th>
			<th style="width: 30px;">军官数</th>
			<th style="width: 30px;">威望</th>
			<th style="width: 30px;">人数</th>

			<th style="width: 100px;">积分奖励</th>
			<th style="width: 100px;">时间奖励</th>


			<th></th>
		</tr>
		<?php
		$baseList = B_DB::instance('BaseMultiFB')->all();
		foreach ($baseList as $key => $val) {
			//单人副本进度,城市等级,军官数量,威望,最少参与人数
			list($fbNo, $cityLv, $heroNum, $renow, $cityNum) = explode(',', $val['join_rule']);
			?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['type']; ?></td>
				<td><?php echo $fbNo; ?></td>
				<td><?php echo $cityLv; ?></td>
				<td><?php echo $heroNum; ?></td>
				<td><?php echo $renow; ?></td>
				<td><?php echo $cityNum; ?></td>
				<td><?php echo $val['award_list']; ?></td>
				<td><?php echo $val['time_limit']; ?></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="10">关卡数据(关卡编号,npcId, 地图编号, 战功):<br>
					<?php
					$arr = explode("|", $val['def_line']);
					$tmp = 1;
					foreach ($arr as $k => $tmpVal) {
						list($lineNo, $npcId, $mapNo, $point) = explode('_', $tmpVal);

						$npcInfo = M_NPC::getInfo($npcId);
						if ($npcInfo['id']) {
							$a = $npcId . '(' . $npcInfo['nickname'] . '),' . $mapNo . ',' . $point;
						} else {
							$a = $npcId . '(not exist),' . $mapNo;
						}

						if ($lineNo{0} != $tmp) {
							$tmp = $lineNo{0};
							echo "<br>";
						}
						echo $lineNo . ':' . $a . "&nbsp;|&nbsp;";
					}
					?></td>

			</tr>
		<?php } ?>
	</table>
</div>