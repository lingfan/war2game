<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'亚洲' => 'War/ProbeMap&zone=1',
	'欧洲' => 'War/ProbeMap&zone=2',
	'非洲' => 'War/ProbeMap&zone=3',
);

$zoneId = array(
	1 => 91,
	2 => 92,
	3 => 93,
);
$data = $baselist;
$default = $data['config_probe_map'];
?>
<div class="top-bar">
	<h1>野外NPC生成</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<?php
foreach (T_App::$map as $zone => $name)
	$zone = isset($_GET['zone']) ? $_GET['zone'] : 1;
?>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<input type="hidden" id="zone" name="zone" value="<?php echo $zone ?>" readonly>
	<?php echo $name ?>
	<table class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100">NPC名称</td>
			<td width="100">每区块分配数量</td>
			<td width="50">已分配数量</td>
			<td width="150">操作</td>
		</tr>
		<?php
		$npcList = B_DB::instance('BaseNpcTroop')->getAllMapNpc();
		foreach ($npcList as $key => $val) {

			$num = B_DB::instance('WildMap')->totalNpcNum($val['id']);
			?>
			<tr>
				<td width="100"><?php echo $val['nickname']; ?> <input type="hidden" name="npcId"
				                                                       value="<?php echo $val['id']; ?>"></td>
				<td width="100"><input type="text" id="npcNum_<?php echo $val['id']; ?>" name="npcNum[]"
				                       value="<?php echo isset($default['npc_num'][$val['level']]) ? $default['npc_num'][$val['level']] : ''; ?>"
				                       style="width: 50px;"></td>
				<td width="50"><span id="curNum_<?php echo $val['id']; ?>"><?php echo $num; ?></span></td>
				<td width="150">
					<span id="status_<?php echo $val['id']; ?>"></span>
				</td>
			</tr>
		<?php

		}
		?>
	</table>
</div>
<script>

	function makeNpcPos(npcId) {

		var posX = $("#x").val();
		var posY = $("#y").val();
		var zone = $("#zone").val();
		var num = $("#npcNum_" + npcId).val();
		if (posX < 10 || posY < 10 || zone.length < 1 || num < 1 || num > posX * posY) {
			alert("参数错误");
			return false;
		}

		//$(".button").attr("disabled",true);
		$("#button_" + npcId).attr("disabled", true);
		$("#status_" + npcId).html("坐标生成中...");

		$.ajax({
			type: 'POST',
			url: "?r=War/ProbeMap",
			data: {'act': 'save', 'num': num, 'z': zone, 'npcId': npcId, 'x': posX, 'y': posY},
			success: function (data) {
				if (data.succ == 1) {
					//alert('操作成功');
					//$(".button").attr("disabled",false);
					$("#button_" + npcId).attr("disabled", false);
					$("#curNum_" + npcId).html(data.num);
					$("#status_" + npcId).html("");
				}
				else {
					//alert('操作失败');
					//$(".button").attr("disabled",false);
					$("#button_" + npcId).attr("disabled", false);
					$("#status_" + npcId).html("");
				}
			},
			dataType: "json"
		});
		return false;
	}

</script>