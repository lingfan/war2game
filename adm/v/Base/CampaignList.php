<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'据点列表' => 'Base/CampaignList&page=1',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>兵种数据列表
		<a href="?r=Base/CampaignExport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
		<a href="?r=Base/CampaignImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
		<a href="?r=Base/CampaignCacheUp"
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
			<th width="30px">类型</th>
			<th width="50px">据点名称</th>
			<th width="70px">开放周</th>
			<th width="50px">开始时间</th>
			<th width="50px">结束时间</th>
			<th width="50px">巡逻次数</th>
			<th width="50px">奖励ID</th>
			<th width="50px">是否开放</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php $idStr = strval($val['id']);
					if ($idStr{0} == M_Campaign::CAMP_TYPE_MIL) {
						echo "军事";
					} else if ($idStr{0} == M_Campaign::CAMP_TYPE_RES) {
						echo "资源";
					}
					?></td>
				<td><?php echo $val['title']; ?></td>
				<td><?php
					$weekConst = array('日' => 1, '一' => 2, '二' => 4, '三' => 8, '四' => 16, '五' => 32, '六' => 64);
					$arr = array();
					foreach ($weekConst as $k => $v) {
						if (($val['open_week'] & $v) > 0) {
							$arr[] = $k;
						}
					}
					echo implode(",", $arr);
					?></td>
				<td><?php echo $val['open_start_time']; ?></td>
				<td><?php echo $val['open_end_time']; ?></td>
				<td><?php echo $val['probe_times']; ?></td>
				<td><?php echo $val['award_id']; ?></td>
				<td><?php echo $val['is_open'] ? "是" : "-"; ?></td>
			</tr>
			<tr>
				<td colspan="9">
					<?php
					echo "<b>NPC数据</b>:<br>";
					$i = 0;
					$tmp = 1;
					foreach (M_Campaign::$CampaignBase as $v) {
						$v = strval($v);
						$a = "no_" . $v;
						list($heroId, $mapNo) = explode('|', $val[$a]);
						$i++;
						if ($tmp == $v{0}) {
							echo "&nbsp;&nbsp;&nbsp;";
						} else {
							$tmp = $v{0};
							echo "<br>";
						}
						echo "[{$v}]英雄#{$heroId};战场#{$mapNo}";
					}
					list($add, $unionCoin) = explode("|", $val['effect']);

					echo "<br><b>军团效果</b>:(加成{$add} ,军团资金{$unionCoin} )<br>";
					?>
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
					echo "&nbsp;<a href='?r=Base/CampaignList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>

<div class="table">
	<?php
	foreach ($pageData['campInfo'] as $key1 => $val1) {
		echo "UnionId:" . $key1 . "<br>";
		if (is_array($val1)) {
			foreach ($val1 as $t => $v) {
				$va = ($t == M_Campaign::CAMP_TYPE_RES) ? '资源' : '军事';
				echo "类型#{$va}<br>";
				foreach ($v as $txt) {
					$diff = $txt[1] - time();
					echo "=>效果值{$txt[0]}#期限" . date("Y-m-d H:i:s", $txt[1]) . ";剩" . B_Utils::formatTime($diff) . "s<br>";
				}
			}
		}

		echo "<hr>";
	}
	?>
</div>

