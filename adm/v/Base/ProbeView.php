<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);

$textArr = array(
	'gold' => '金钱',
	'food' => '食物',
	'oil' => '石油',
	'milpay' => '军饷',
	'coupon' => '礼券',
	'renown' => '威望',
	'warexp' => '功勋',
	'march_num' => '活力',
	'atkfb_num' => '军令',
	'props' => '道具',
	'equip' => '装备',
	'hero' => '英雄',
	'props_weapon' => '图纸',
);

?>
<script type="text/javascript">

	function addProps() {
		var newRow = $('#row').clone();
		newRow.appendTo('#tb');
	}
	function addProps2() {
		var newRow = $('#row2').clone();
		newRow.appendTo('#tb2');
	}
	function del(aa) {
		var tb = aa.parentNode.parentNode.parentNode;
		var row = aa.parentNode.parentNode;
		tb.removeChild(row);
	}


	<?php //@todo?>
	function _addSysEquip() {
		var level = document.getElementById('sys_level').value;
		var pos = document.getElementById('sys_pos').value;
		var quality = document.getElementById('sys_qual').value;
		var num = document.getElementById('sys_num').value;
		var jl = document.getElementById('sys_jl').value;

		var name = document.getElementById('sys_level').options[document.getElementById('sys_level').selectedIndex].text;
		name += document.getElementById('sys_pos').options[document.getElementById('sys_pos').selectedIndex].text;
		name += "(" + document.getElementById('sys_qual').options[document.getElementById('sys_qual').selectedIndex].text + ")";
		//alert(name);
		var pushStr = "<tr><td>" + name;
		pushStr += "<input name='award1_equip_level[]' type='hidden' value='" + level + "'>";
		pushStr += "<input name='award1_equip_pos[]' type='hidden' value='" + pos + "'>";
		pushStr += "<input name='award1_equip_quality[]' type='hidden' value='" + quality + "'>";
		pushStr += "</td><td>" + num;
		pushStr += "<input name='award1_equip_num[]' type='hidden' value='" + num + "'>";
		pushStr += "</td><td>" + jl;
		pushStr += "<input name='award1_equip_jl[]' type='hidden' value='" + jl + "'>";
		pushStr += "</td><td><a href='javascript:void(0)' onclick='del(this);'>删除</a></td>";

		var tb = document.getElementById('tb_sys_equip');
		var str = tb.innerHTML;
		str += pushStr;
		tb.innerHTML = str;
	}

</script>
<div class="top-bar">
	<h1>事件管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="?r=Base/ProbeList">事件列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>事件</a></div>
</div>
<div class="table">
	<iframe name="iframe" id="iframe" style="display: none;"></iframe>
	<form id="probeForm" name="probeForm" action="?r=Base/ProbeEdit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 100px;">事件问题：</td>
				<td>
					<input type="hidden" name="id" id="id"
					       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : ''; ?>">
					<textarea name="title"
					          style="width: 400px;"><?php echo isset($pageData['info']['title']) ? $pageData['info']['title'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td style="width: 100px;">类型：</td>
				<td></td>
			</tr>

			<tr>
				<td>奖励ID：</td>
				<td>
					<input type="text" name="award_id"
					       value="<?php echo isset($pageData['info']['award_id']) ? $pageData['info']['award_id'] : ''; ?>">
					<?php
					echo B_Utils::awardText($pageData['info']['award_id']);
					?>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
</div>