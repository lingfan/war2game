<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
);
?>
<script>
	function addRow() {
		var str = "<td><input type=\"text\" name=\"wild_refresh_npc[0][]\" value=\"\" style=\"width: 50px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[1][]\" value=\"\" style=\"width: 75px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[2][]\" value=\"\" style=\"width: 75px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[3][]\" value=\"\" style=\"width: 65px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[4][]\" value=\"\" style=\"width: 65px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[5][]\" value=\"\" style=\"width: 30px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[6][]\" value=\"\" style=\"width: 20px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[7][]\" value=\"\" style=\"width: 20px;\">&nbsp;"
			+ "<input type=\"text\" name=\"wild_refresh_npc[8][]\" value=\"\" style=\"width: 200px;\">&nbsp;"
			+ "<a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#wild_refresh_npc').append(newTR);
	}

	function del(a) {
		a.parentNode.parentNode.parentNode.removeChild(a.parentNode.parentNode);
	}
</script>

<div class="top-bar">
	<h1>野外临时NPC配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigTmpNpc&act=showflag" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100px"><strong>野外临时NPC地图显示</strong></td>
				<td>
					<?php
					$baseCfg = $pageData['baseCfg'];
					$wild_refresh_npc_showflag = $baseCfg['wild_refresh_npc_showflag'];
					//var_dump($wild_refresh_npc_showflag);
					$str = array();
					foreach ($wild_refresh_npc_showflag as $key => $val) {
						$str[] = "{$key},{$val}";
					}
					?>
					npcId,地图显示类型(0,255)|npcId,地图显示类型(0,255)<br>
					<input type="text" style="width:500px" name="wild_refresh_npc_showflag"
					       value="<?php echo implode('|', $str); ?>">
					<br><input type="submit" class="button" name="submit" value="保存"/>
				</td>
			</tr>

		</table>
	</form>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigTmpNpc&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td><strong>野外临时NPC刷新设置</strong></td>
				<td>
					<?php
					$wild_refresh_npc = (array)$baseCfg['wild_refresh_npc'];
					?>
					<a href="javascript:void(0);" onclick="addRow();">添加</a>
					<table cellspacing="0px" border="0" style="font-size: 12px;">
						<tr>
							<td>
								开始日期[2012-08-30]/结束日期[2012-09-30]/开始时间[13:00:00]/结束时间[19:00:00]/刷新NPC数量/洲/是否发布广播[0/1]/奖励数组[消灭百分比:奖励;消灭百分比:奖励;消灭百分比:奖励;]
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" name="" value="NPCID" style="width: 50px;">
								<input type="text" name="" value="开始日期" style="width: 75px;">
								<input type="text" name="" value="结束日期" style="width: 75px;">
								<input type="text" name="" value="开始时间" style="width: 65px;">
								<input type="text" name="" value="结束时间" style="width: 65px;">
								<input type="text" name="" value="NPC数量" style="width: 30px;">
								<input type="text" name="" value="洲" style="width: 20px;">
								<input type="text" name="" value="广播" style="width: 20px;">
								<input type="text" name="" value="奖励数组[消灭百分比:奖励;消灭百分比:奖励;消灭百分比:奖励;]"
								       style="width: 200px;">
							</td>
						</tr>
						<tbody id="wild_refresh_npc">
						<?php
						$arr = array();
						foreach ($wild_refresh_npc as $key => $val) {
							$npcInfo = M_NPC::getInfo($key);
							$arr[$key] = !empty($npcInfo['nickname']) ? $npcInfo['nickname'] . '|' . $npcInfo['level'] : 'no exist';
							$t = array();
							foreach ($val[7] as $k => $v) {
								$t[] = "{$k}:{$v}";
							}

							?>
							<tr>

								<td>
									<input type="text" name="wild_refresh_npc[0][]" value="<?php echo $key; ?>"
									       style="width: 50px;">
									<input type="text" name="wild_refresh_npc[1][]" value="<?php echo $val[0]; ?>"
									       style="width: 75px;">
									<input type="text" name="wild_refresh_npc[2][]" value="<?php echo $val[1]; ?>"
									       style="width: 75px;">
									<input type="text" name="wild_refresh_npc[3][]" value="<?php echo $val[2]; ?>"
									       style="width: 65px;">
									<input type="text" name="wild_refresh_npc[4][]" value="<?php echo $val[3]; ?>"
									       style="width: 65px;">
									<input type="text" name="wild_refresh_npc[5][]" value="<?php echo $val[4]; ?>"
									       style="width: 30px;">
									<input type="text" name="wild_refresh_npc[6][]" value="<?php echo $val[5]; ?>"
									       style="width: 20px;">
									<input type="text" name="wild_refresh_npc[7][]" value="<?php echo $val[6]; ?>"
									       style="width: 20px;">
									<input type="text" name="wild_refresh_npc[8][]"
									       value="<?php echo implode(";", $t); ?>" style="width: 200px;">
									<a href="javascript:void(0);" onclick="del(this);">删除</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>


				</td>
			</tr>

			<tr>
				<td></td>
				<td>
					<input type="submit" class="button" name="submit" value="保存"/>
					<font style="color:red">注意: 此处不可在玩家在线时修改,一保存则会删除原游戏中临时NPC,只能停服操作!</font>
					<br>
					<?php
					foreach ($arr as $key => $name) {
						echo "{$key}=>{$name}<br>";
					}
					?>
				</td>
			</tr>
		</table>
	</form>
</div>


<?php
$arr = M_NPC::getRandTempNpcRefreshData();
?>
<div class="table">
	<table width="500px" class="listing form" cellpadding="0" cellspacing="0">
		<?php
		$arr = !empty($arr) ? $arr : array();
		foreach ($arr as $id => $val):?>
			<tr>
				<td width="10px"><?php echo $id; ?></td>
				<td width="10px"><?php echo date('Y-m-d H:i:s', $val['end_time']); ?></td>
				<td>
					<div style="width:600px;word-wrap: break-word;word-break: normal;">
						<?php echo json_encode($val['list']); ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>