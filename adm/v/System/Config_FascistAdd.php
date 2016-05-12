<?php
$urlArr = array(
	'首页' => 'index/index',
	'固定法西斯配置' => '',
);
$pageData = B_View::getVal('pageData');
$list = $pageData['list'];
$id = !empty($pageData['id']) ? $pageData['id'] : 0;
$t = array();
if (isset($list['npc_awardArr'])) {
	foreach ($list['npc_awardArr'] as $k => $v) {
		$t[] = "{$k}:{$v}";
	}
}
$npcPos = isset($list['npc_pos']) ? explode(',', $list['npc_pos']) : array();
?>

<script type="text/javascript">
	$(document).ready(function () {
		if (<?php echo isset($list['npc_zone']) ?$list['npc_zone'] : 0;?>) {
			$('input[name="npc_zone"][value=<?php echo $list['npc_zone'];?>]').attr("checked", "checked");

		}
		$("#submit").click(function () {
			var data = $("#fascistEditForm").serialize();
			$.post('?r=System/FascistAdd&act=1', data, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.err);
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json')
		});
	});


</script>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>固定法西斯配置</h1>

	<div class="breadcrumbs">
		<a href="#">系统管理</a> / <a href="?r=System/ConfigFascist">固定法西斯配置</a> /
		<a href="#"><?php echo isset($info['id']) ? '修改' : '添加' ?>固定法西斯</a> <span
			id="msg"
			style="color: white; background-color: green; font-weight: bold; padding-left: 10px; padding-right: 10px; display: none"></span>
	</div>
</div>
<div class="table">

	<form id="fascistEditForm" name="fascistEditForm" method="post" action="?r=System/FascistAdd">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td><strong>广播时间</strong></td>
				<td>
					起始时间：<input type="text" name="broadcast_start" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($list['broadcast_start']) ? $list['broadcast_start'] : ''; ?>">
					截止时间：<input type="text" name="broadcast_end" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($list['broadcast_end']) ? $list['broadcast_end'] : ''; ?>">
					广播间隔时间：<input type="text" name="Interval_broadcast" name="Interval_broadcast" size=5
					              value="<?php echo isset($list['Interval_broadcast']) ? $list['Interval_broadcast'] : ''; ?>"/>分钟
				</td>
			</tr>
			<tr>
				<td><strong>广播内容</strong></td>
				<td><input class="required" id="broadcast" name="broadcast" size=50
				           value="<?php echo isset($list['broadcast']) ? $list['broadcast'] : ''; ?>"></input>
				</td>
			</tr>
			<tr>
				<td><strong>世界频道内容</strong></td>
				<td><input class="required" id="channel" name="channel" size=50
				           value="<?php echo isset($list['channel']) ? $list['channel'] : ''; ?>"></input>
				</td>
			</tr>
			<tr>
				<td><strong>部队出现时间</strong></td>
				<td>
					起始时间：<input type="text" name="npc_start" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($list['npc_start']) ? $list['npc_start'] : ''; ?>">
					截止时间：<input type="text" name="npc_end" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($list['npc_end']) ? $list['npc_end'] : ''; ?>">
				</td>
			<tr>
				<td><strong>出现时广播内容</strong></td>
				<td><input class="required" id="out_broadcast" name="out_broadcast" size=50
				           value="<?php echo isset($list['out_broadcast']) ? $list['out_broadcast'] : ''; ?>"></input>
				</td>
			</tr>
			<tr>
				<td><strong>部队出现洲</strong></td>
				<td>
					<div style="width: 200px; float: left;">
						<input type="radio" id="npc_zone" name="npc_zone" checked value="1"/>亚洲
						<input type="radio" id="npc_zone" name="npc_zone" value="2"/>欧洲
						<input type="radio" id="npc_zone" name="npc_zone" value="3"/>非洲
					</div>
				</td>
			</tr>
			<tr>
				<td>输入坐标</td>
				<td><input id="npc_pos1" name="npc_pos1" type="text" size="10" value="<?php echo $npcPos[0]; ?>"/>-
					<input id="npc_pos2" name="npc_pos2" type="text" size="10" value="<?php echo $npcPos[1]; ?>"/>
				</td>
			</tr>
			<tr>
				<td>NPC部队ID</td>
				<td><input class="textInput digits" type="text" name="npc_id"
				           id="npc_id" maxlength="7"
				           value="<?php echo isset($list['npc_id']) ? $list['npc_id'] : ''; ?>">
				</td>
			</tr>
			<tr>
				<td>奖励数组</td>
				<td><input class="textInput digits" type="text" name="npc_awardArr"
				           value="<?php echo isset($list['npc_awardArr']) ? implode(";", $t) : ''; ?>"
				           id="npc_awardArr">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input id="submit" type="button" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
</div>
