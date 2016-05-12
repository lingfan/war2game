<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'添加奖励' => '',
);
?>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">

	function sb() {
		$('#addForm').submit();
		if ($('#id').val() == '') {
			$("#addForm").each(function () {
				this.reset();
			});
		}
	}

	function addProps() {
		var newRow = $('#row').clone();
		newRow.appendTo('#tb');
	}
	function addTsProps() {
		var newRow = $('#ts_row').clone();
		newRow.appendTo('#ts_tb');
	}

	function addHero() {
		var newRow = $('#row_hero').clone();
		newRow.appendTo('#tb_hero');
	}

	function addEquip() {
		var newRow = $('#equipRow').clone();
		newRow.appendTo('#equipTb');
	}

	function addTsEquip() {
		var newRow = $('#ts_equipRow').clone();
		newRow.appendTo('#ts_equipTb');
	}

	function del(aa) {
		var tb = aa.parentNode.parentNode.parentNode;
		var row = aa.parentNode.parentNode;
		tb.removeChild(row);
	}

	function changeType(val) {
		if (val < 5) {
			var tsRow = document.getElementById('tsRow');
			var resRow = document.getElementById('resRow');
			var tsjlRow = document.getElementById('tsjl');
			tsRow.style.display = '';
			tsjlRow.style.display = '';
			resRow.style.display = 'none';
		}
		else {
			var tsRow = document.getElementById('tsRow');
			var resRow = document.getElementById('resRow');
			var tsjlRow = document.getElementById('tsjl');
			tsRow.style.display = 'none';
			tsjlRow.style.display = 'none';
			resRow.style.display = 'none';
		}
	}

	function _addHero() {
		var obj = document.getElementById('selectHero');
		var index = obj.selectedIndex;
		var txt = obj.options[index].text;
		var id = obj.value;

		var heroTbody = document.getElementById('heroTbody');
		var str = heroTbody.innerHTML;

		var pushStr = "<tr><td><input type='hidden' name='heros[]' value='" + id + "'>" + txt + "</td><td><a href='javascript:void(0)' onclick='del(this);'>删除</a></td></tr>";
		str += pushStr;

		heroTbody.innerHTML = str;
	}

	function _addTplEquip() {
		var id = document.getElementById('tpl_id').value;
		var num = document.getElementById('tpl_num').value;
		var jl = document.getElementById('tpl_jl').value;

		var name = document.getElementById('tpl_id').options[document.getElementById('tpl_id').selectedIndex].text;
		var pushStr = "<tr><td>" + name;
		pushStr += "<input name='jl_equip_id[]' type='hidden' value='" + id + "'>";
		pushStr += "</td><td>" + num;
		pushStr += "<input name='jl_equip_num[]' type='hidden' value='" + num + "'>";
		pushStr += "</td><td>" + jl + "%";
		pushStr += "<input name='jl_equip_jv[]' type='hidden' value='" + jl + "'>";
		pushStr += "</td><td><a href='javascript:void(0)' onclick='del(this);'>删除</a></td>";
		var tb = document.getElementById('tb_tpl_equip');
		var str = tb.innerHTML;
		str += pushStr;
		tb.innerHTML = str;
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
		pushStr += "<input name='sys_equip_level[]' type='hidden' value='" + level + "'>";
		pushStr += "<input name='sys_equip_pos[]' type='hidden' value='" + pos + "'>";
		pushStr += "<input name='sys_equip_quality[]' type='hidden' value='" + quality + "'>";
		pushStr += "</td><td>" + num;
		pushStr += "<input name='sys_equip_num[]' type='hidden' value='" + num + "'>";
		pushStr += "</td><td>" + jl;
		pushStr += "<input name='sys_equip_jl[]' type='hidden' value='" + jl + "'>";
		pushStr += "</td><td><a href='javascript:void(0)' onclick='del(this);'>删除</a></td>";

		var tb = document.getElementById('tb_sys_equip');
		var str = tb.innerHTML;
		str += pushStr;
		tb.innerHTML = str;
	}

	function addProbe() {
		var probeId = document.getElementById('selectProbe').value;
		var probeTitle = document.getElementById('selectProbe').options[document.getElementById('selectProbe').selectedIndex].text;
		var probePro = document.getElementById('probePro').value;

		var pushStr = "<tr><td>" + probeTitle;
		pushStr += "<input name='probeId[]' type='hidden' value='" + probeId + "'>";
		pushStr += "</td><td>" + probePro + "%";
		pushStr += "<input name='probePro[]' type='hidden' value='" + probePro + "'>";
		pushStr += "</td><td><a href='javascript:void(0)' onclick='del(this);'>删除</a></td></tr>";

		var tb = document.getElementById('probe_tb');
		var str = tb.innerHTML;
		str += pushStr;
		tb.innerHTML = str;
	}
</script>
<div class="top-bar">
	<h1>奖励管理</h1>

	<div class="breadcrumbs"><a href="?r=Base/AwardList">奖励列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>奖励</a> <span id="msg"
	                                                                                        style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
<iframe name="iframe" id="iframe" style="display: none;"></iframe>
<form id="addForm" name="addForm" action="?r=Base/AwardEdit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<td style="width: 100px;">奖励名称：</td>
	<td>
		<input type="hidden" name="id" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : ''; ?>">
		<input type="text" name="name" id=""
		       value="<?php echo isset($pageData['info']["name"]) ? $pageData['info']['name'] : ''; ?>">
	</td>
</tr>

<tr>
	<td>奖励类型：</td>
	<td>
		<select name="type" id="type" onchange="changeType(this.value)">
			<?php foreach (M_Award::$Type as $key => $val) { ?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['type']) && $pageData['info']['type'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
<td>奖励数据：</td>
<td>
<?php
$jlResArr = array();

if (isset($pageData['info']['award_text'])) {
	$jlArr = json_decode($pageData['info']['award_text'], true);
	$jlResArr = isset($jlArr['res']) ? $jlArr['res'] : array();
	$jsPropsArr = isset($jlArr['props']) ? $jlArr['props'] : array();
	$jsEquipArr = isset($jlArr['equip']) ? $jlArr['equip'] : array();
	$sysEquipArr = isset($jlArr['sys_equip']) ? $jlArr['sys_equip'] : array();
}
?>
资源：<br>
<input type="checkbox" name="jl_res_type[]" value="gold" <?php if (isset($jlResArr['gold'])) {
	echo 'checked="checked"';
} ?>>
<?php
if (isset($jlResArr['gold'])) {
	$gold = explode(',', $jlResArr['gold']);
}
?>
金钱x<input type="text" name="jl_gold_num" id="jl_gold_num" value="<?php echo isset($gold) ? $gold[0] : ''; ?>"
          style="width: 50px;">
几率：<input type="text" name="jl_gold_jv" id="jl_gold_jv" value="<?php echo isset($gold) ? $gold[1] : ''; ?>"
          style="width: 50px;">%
<br>
<input type="checkbox" name="jl_res_type[]" value="food" <?php if (isset($jlResArr['food'])) {
	echo 'checked="checked"';
} ?>>
<?php
if (isset($jlResArr['food'])) {
	$food = explode(',', $jlResArr['food']);
}
?>
食物x<input type="text" name="jl_food_num" id="jl_food_num" value="<?php echo isset($food) ? $food[0] : ''; ?>"
          style="width: 50px;">
几率：<input type="text" name="jl_food_jv" id="jl_food_jv" value="<?php echo isset($food) ? $food[1] : ''; ?>"
          style="width: 50px;">%
<br>
<input type="checkbox" name="jl_res_type[]" value="oil" <?php if (isset($jlResArr['oil'])) {
	echo 'checked="checked"';
} ?>>
<?php
if (isset($jlResArr['oil'])) {
	$oil = explode(',', $jlResArr['oil']);
}
?>
石油x<input type="text" name="jl_oil_num" id="jl_oil_num" value="<?php echo isset($oil) ? $oil[0] : ''; ?>"
          style="width: 50px;">
几率：<input type="text" name="jl_oil_jv" id="jl_oil_jv" value="<?php echo isset($oil) ? $oil[1] : ''; ?>"
          style="width: 50px;">%
<br>

道具：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:addProps();">添加</a><br>
<table border="0" style="font-size: 12px;">
	<tbody id="tb">
	<?php
	if (isset($jsPropsArr) && count($jsPropsArr) > 0) {
		foreach ($jsPropsArr as $k => $v) {
			$v = explode(',', $v);
			?>
			<tr id="row">
				<td>
					<select name="jl_props_id[]">
						<?php foreach ($pageData['props_list'] as $key => $val) { ?>
							<option
								value="<?php echo $val['id']; ?>"<?php if ($k == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
						<?php } ?>
					</select>
					x<input type="text" name="jl_props_num[]" value="<?php echo $v[0]; ?>" style="width: 50px;">
					几率：<input type="text" name="jl_props_jv[]" id="jl_props_jv[]" value="<?php echo $v[1]; ?>"
					          style="width: 50px;">%
					<a href="javascript:void(0)" onclick="del(this);">删除</a>
				</td>
			</tr>
		<?php
		}
	} else {
		?>
		<tr id="row">
			<td>
				<select name="jl_props_id[]">
					<?php foreach ($pageData['props_list'] as $key => $val) { ?>
						<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
					<?php } ?>
				</select>
				x<input type="text" name="jl_props_num[]" value="" style="width: 50px;">
				几率：<input type="text" name="jl_props_jv[]" value="" style="width: 50px;">%
				<a href="javascript:void(0)" onclick="del(this);">删除</a>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

添加活动装备：<?php //@todo?>
<select id="tpl_id">
	<?php foreach ($pageData['equipList'] as $key => $val) { ?>
		<option
			value="<?php echo $val['id']; ?>"><?php echo $val['name'] . '(' . T_Word::$EQUIP_QUAL[$val['quality']] . ')'; ?></option>
	<?php } ?>
</select>
数量：<input id="tpl_num" style="width: 50px;">
几率：<input id="tpl_jl" style="width: 50px;">%
<input type="button" value="添加" onclick="_addTplEquip();">
<br>
活动装备列表：
<table style="font-size: 12px;">
	<tr>
		<td>装备</td>
		<td>数量</td>
		<td>几率(%)</td>
		<td>操作</td>
	</tr>
	<tbody id="tb_tpl_equip">
	<?php
	if (isset($jsEquipArr) && count($jsEquipArr) > 0) {
		foreach ($jsEquipArr as $k => $v) {
			$v = explode(',', $v);
			?>
			<tr>
				<td>
					<?php echo $pageData['equipList'][$k]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$k]['quality']] . ')'; ?>
					<input name="jl_equip_id[]" type="hidden"
					       value="<?php echo $pageData['equipList'][$k]['id']; ?>">
				</td>
				<td>
					<?php echo $v[0]; ?>
					<input name="jl_equip_num[]" type="hidden" value="<?php echo $v[0]; ?>">
				</td>
				<td>
					<?php echo $v[1]; ?>
					<input name="jl_equip_jv[]" type="hidden" value="<?php echo $v[1]; ?>">
				</td>
				<td>
					<a href="javascript:void(0);" onclick="del(this);">删除</a>
				</td>
			</tr>
		<?php
		}
	}
	?>
	</tbody>
</table>
<br>
添加系统装备：
<select id="sys_level">
	<?php foreach (T_Word::$EQUIP_NAME as $level => $name) { ?>
		<option value="<?php echo $level; ?>"><?php echo $name; ?></option>
	<?php } ?>
</select>
<select id="sys_pos">
	<?php foreach (T_Word::$EQUIP_POS as $pos => $name) { ?>
		<option value="<?php echo $pos; ?>"><?php echo $name; ?></option>
	<?php } ?>
</select>
<select id="sys_qual">
	<?php foreach (T_Word::$EQUIP_QUAL as $quality => $name) { ?>
		<option value="<?php echo $quality; ?>"><?php echo $name; ?></option>
	<?php } ?>
</select>
数量：<input id="sys_num" style="width: 50px;">
几率：<input id="sys_jl" style="width: 50px;">%
<input type="button" value="添加" onclick="_addSysEquip();">
<br>
系统装备列表：
<table style="font-size: 12px;">
	<tr>
		<td>装备</td>
		<td>数量</td>
		<td>几率(%)</td>
		<td>操作</td>
	</tr>
	<tbody id="tb_sys_equip">
	<?php
	if (isset($sysEquipArr) && count($sysEquipArr) > 0) {
		foreach ($sysEquipArr as $k => $v) {
			$v = explode(',', $v);
			?>
			<tr>
				<td>
					<?php echo M_Equip::getSysEquipName($v[0], $v[1]) . '(' . T_Word::$EQUIP_QUAL[$v[2]] . ')'; //@todo?>
					<input name="sys_equip_level[]" type="hidden" value="<?php echo $v[0]; ?>">
					<input name="sys_equip_pos[]" type="hidden" value="<?php echo $v[1]; ?>">
					<input name="sys_equip_quality[]" type="hidden" value="<?php echo $v[2]; ?>">
				</td>
				<td>
					<?php echo $v[3]; ?>
					<input name="sys_equip_num[]" type="hidden" value="<?php echo $v[3]; ?>">
				</td>
				<td>
					<?php echo $v[4]; ?>
					<input name="sys_equip_jl[]" type="hidden" value="<?php echo $v[4]; ?>">
				</td>
				<td>
					<a href="javascript:void(0);" onclick="del(this);">删除</a>
				</td>
			</tr>
		<?php
		}
	}
	?>
	</tbody>
</table>

军饷X<input type="text" name="jl_mil_pay_num"
          value="<?php echo isset($jlArr['mil_pay']) ? $jlArr['mil_pay'][0] : ''; ?>" style="width: 50px;">
几率：<input type="text" name="jl_mil_pay_jv"
          value="<?php echo isset($jlArr['mil_pay']) ? $jlArr['mil_pay'][1] : ''; ?>" style="width: 50px;">%
<br>
点券X<input type="text" name="jl_coupon_num"
          value="<?php echo isset($jlArr['coupon']) ? $jlArr['coupon'][0] : ''; ?>" style="width: 50px;">
几率：<input type="text" name="jl_coupon_jv"
          value="<?php echo isset($jlArr['coupon']) ? $jlArr['coupon'][1] : ''; ?>" style="width: 50px;">%
</td>
</tr>

<tr>
	<td>奖励描述：</td>
	<td>
		<textarea rows="" cols="" name="award_desc"
		          style="width: 500px; height: 60px;"><?php echo isset($pageData['info']['award_desc']) ? $pageData['info']['award_desc'] : ''; ?></textarea>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input id="sub" type="button" value=" 保 存 " onclick="sb()"></td>
</tr>
</table>
</form>
</div>