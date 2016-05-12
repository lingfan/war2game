<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'兑换管理' => 'Base/ExchangeList',
);
?>
<style>
	#list tr td {
		background-color: lightgray;
	}
</style>

<iframe name="iframe" style="display: none;"></iframe>
<div class="top-bar">
	<a href="?r=Base/ExchangeExport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/ExchangeImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/ExchangeClean"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" target="iframe">清除缓存</a>

	<h1>兑换管理</h1>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width: 50px;">ID <a href="?r=Base/HeroList" title="按ID升序排序" style="color: white;">↑↑</a></th>
			<th style="width: 30px;">类型</th>
			<th style="width: 50px;">子类型</th>
			<th style="width: 100px;">需要材料</th>
			<th style="width: 50px;">新道具</th>
			<th style="width: 50px;">消耗</th>
			<th style="width: 60px;">开始时间</th>
			<th style="width: 60px;">结束时间</th>
			<th></th>
		</tr>
		<?php
		if (isset($_GET['db'])) {
			$baseList = B_DB::instance('BaseExchange')->getAll();
		} else {
			$tmpList = M_Base::exchangeAll();
			$baseList = array();
			foreach ($tmpList['data'] as $key => $val) {
				$str = array();
				foreach ($val['need_props'] as $k => $v) {
					$str[] = "{$k},{$v}";
				}

				$val['need_props'] = implode("|", $str);

				$str = array();
				foreach ($val['cost_val'] as $k => $v) {
					$str[] = "{$k},{$v}";
				}
				$val['cost_val'] = implode("|", $str);

				$baseList[$key] = $val;
			}


		}


		foreach ($baseList as $key => $val) {
			$type = $val['type'];
			if ($val['type'] == 1) {
				$type = '道具';
			} else if ($val['type'] == 2) {
				$type = '装备';
			}
			?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $type; ?></td>
				<td><?php echo $val['sub_type']; ?></td>
				<td><?php echo $val['need_props']; ?></td>
				<td><?php echo $val['new_props']; ?></td>
				<td><?php echo $val['cost_val']; ?></td>
				<td><?php echo $val['start_time']; ?></td>
				<td><?php echo $val['end_time']; ?></td>
				<td></td>
			</tr>
		<?php } ?>
	</table>
</div>