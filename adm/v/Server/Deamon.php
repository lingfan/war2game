<a href="?r=Server/ResetDeamon">重启进程</a>&nbsp;
<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>PID</th>
			<th width="30px">用户</th>
			<th width="30px">CPU占用率</th>
			<th width="30px">内存使用率</th>
			<th width="30px">虚拟内存</th>
			<th width="30px">占用内存</th>
			<th width="10px">状态</th>
			<th width="30px">开始时间</th>
			<th width="30px">运行时间</th>
			<th>脚本</th>
		</tr>
		<?php
		$i = 0;
		$pageData = B_View::getVal('pageData');
		foreach ($pageData['list'] as $key => $val) {
			if (!empty($val[1])) {
				$i++;
				?>
				<tr id="<?php echo $key; ?>">
					<td><?php echo $val[1]; ?></td>
					<td><?php echo $val[0]; ?></td>
					<td><?php echo $val[2]; ?></td>
					<td><?php echo $val[3]; ?></td>
					<td><?php echo ceil($val[4] / 1024); ?>M</td>
					<td><?php echo ceil($val[5] / 1024); ?>M</td>
					<td title="<?php echo get_script_stat($val[7]); ?>"><?php echo $val[7]; ?></td>
					<td><?php echo $val[8]; ?></td>
					<td><?php echo $val[9]; ?></td>
					<td><?php
						$extstr = !empty($val[11]) ? $val[11] : '';
						echo $val[10] . ' ' . $extstr; ?>
					</td>
				</tr>
			<?php
			}
		} ?>
	</table>
	共有 <?php echo $i; ?> 个进程

</div>

<div class="table">
	<table id="list1" class="listing form" cellpadding="0" cellspacing="0"
	       style="table-layout:fixed;word-break: break-all; word-wrap: break-word;">
		<tr>
			<th style="width: 30px;">模块</th>
			<th>数量</th>
		</tr>

		<tr>
			<td>战斗</td>
			<td>
				<?php
				$w = new M_Battle_QueueHandler();
				for ($i = 1; $i <= M_Battle_QueueHandler::NUM; $i++) {
					$arr = $w->get($i);
					echo "队列#{$i}:数量[" . count($arr) . "];数据:" . json_encode($arr) . "<br>";
				}
				?>
			</td>
		</tr>
		<tr>
			<td>AI</td>
			<td>
				<?php
				$w = new M_Battle_QueueAI();
				for ($i = 1; $i <= M_Battle_QueueAI::NUM; $i++) {
					$num = $w->aiSize($i);
					$arr = $w->aiGetAll($i);
					echo "队列#{$i}:数量[" . $num . "];数据:" . json_encode($arr) . "<br>";
				}
				?>
			</td>
		</tr>
		<tr>
			<td>行军</td>
			<td>
				<?php
				for ($i = 1; $i <= M_March_Queue::NUM; $i++) {
					$num = M_March_Queue::size($i);
					$arr = M_March_Queue::get($i);
					echo "队列#{$i}:数量[" . $num . "];数据:" . json_encode($arr) . "<br>";
				}
				?>
			</td>
		</tr>
		<tr>
			<td>被占领的坐标</td>
			<td>
				<?php
				$posNoArr = M_March_Hold::get();
				$a = $a1 = array();

				foreach ($posNoArr as $posNo) {
					$mapInfo = M_MapWild::getWildMapInfo($posNo);
					$a[$mapInfo['type']][] = $posNo;

				}
				if (!empty($a[T_Map::WILD_MAP_CELL_NPC])) {
					echo "NPC:" . implode(",", $a[T_Map::WILD_MAP_CELL_NPC]);
					echo "<br>";
				}
				if (!empty($a[T_Map::WILD_MAP_CELL_CITY])) {
					echo "城市:" . implode(",", $a[T_Map::WILD_MAP_CELL_CITY]);
				}
				?>
			</td>
		</tr>


	</table>

</div>

<?php
function get_script_stat($str) {
	$ret = '';
	$n = strlen($str);

	$tip = array(
		'D' => '不可中断的静止',
		'R' => '正在执行中',
		'S' => '静止状态',
		'T' => '暂停执行',
		'Z' => '不存在但暂时无法消除',
		'W' => '没有足够的记忆体分页可分配',
		'<' => '高优先序的行程',
		'N' => '低优先序的行程',
		'L' => '有记忆体分页分配并锁在记忆体内',
	);


	for ($i = 0; $i < $n; $i++) {
		$s = $str[$i];
		$ret .= isset($tip[$s]) ? $tip[$s] : '';
	}
	return $ret;
}

?>