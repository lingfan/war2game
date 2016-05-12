<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">

	function sb() {
		var id = $('#id').val();

		var nickname = $('#nickname').val();
		var gender = document.getElementById('gender').value;
		var quality = document.getElementById('quality').value;
		var face_id = document.getElementById('face_id').value;

		var level = $('#level').val();
		var attr_lead = $('#attr_lead').val();
		var attr_command = $('#attr_command').val();
		var attr_military = $('#attr_military').val();
		var attr_energy = $('#attr_energy').val();
		var attr_mood = document.getElementById('attr_mood').value;

		var equip_arm = document.getElementById('equip_arm').value;
		var equip_cap = document.getElementById('equip_cap').value;
		var equip_uniform = document.getElementById('equip_uniform').value;
		var equip_medal = document.getElementById('equip_medal').value;
		var equip_shoes = document.getElementById('equip_shoes').value;
		var equip_sit = document.getElementById('equip_sit').value;

		var skill_slot_num = document.getElementById('skill_slot_num').value;
		var skill_slot = document.getElementById('skill_slot').value;
		var skill_slot_1 = document.getElementById('skill_slot_1').value;
		var skill_slot_2 = document.getElementById('skill_slot_2').value;

		var army_id = document.getElementById('army_id').value;
		var army_lv = document.getElementById('army_lv').value;
		var army_num = document.getElementById('army_num').value;
		var weapon_id = document.getElementById('weapon_id').value;

		var act = id > 0 ? 'edit' : 'add';
		$.post('?r=War/NpcHeroEdit',
			{
				id: id,
				nickname: nickname,
				gender: gender,
				quality: quality,
				face_id: face_id,
				level: level,
				attr_lead: attr_lead,
				attr_command: attr_command,
				attr_military: attr_military,
				attr_energy: attr_energy,
				attr_mood: attr_mood,
				equip_arm: equip_arm,
				equip_cap: equip_cap,
				equip_uniform: equip_uniform,
				equip_medal: equip_medal,
				equip_shoes: equip_shoes,
				equip_sit: equip_sit,
				skill_slot_num: skill_slot_num,
				skill_slot: skill_slot,
				skill_slot_1: skill_slot_1,
				skill_slot_2: skill_slot_2,
				army_id: army_id,
				army_lv: army_lv,
				army_num: army_num,
				weapon_id: weapon_id,
				act: act
			}, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.msg);
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json');
	}

	function suiji() {
		var sum = $('#sum').val();
		var total = 100;
		//随机数 parseInt(Math.random()*(下限-上限+1)+上限) （随机数不包含上限、下限）
		//alert(Math.round(parseFloat(12.5)*1)/1); 四舍五入
		var a = 10;
		var b = 10;
		//var c = 10;

		var x1 = parseInt(Math.random() * (14 - 41 + 1) + 41);
		a = 10 + x1;
		var x2 = 70 - x1;
		if (70 - x1 > 40) {
			x2 = 40;
		}
		b = 10 + parseInt(Math.random() * (14 - x2 + 1) + x2);
		//var c = 100-a-b;

		var attr_lead = Math.round(parseFloat(sum / total * a) * 1) / 1;
		var attr_command = Math.round(parseFloat(sum / total * b) * 1) / 1;
		var attr_military = sum - attr_lead - attr_command;

		$('#attr_lead').val(attr_lead);
		$('#attr_command').val(attr_command);
		$('#attr_military').val(attr_military);
	}
</script>
<div class="top-bar">
	<h1>NPC管理</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/NpcList">NPC部队列表</a> / <a href="?r=War/NpcHeroList">NPC英雄列表</a>
		/ <a href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>NPC英雄</a> <span id="msg"
		                                                                                        style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
<form id="addForm" name="addForm">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<td>军官名称：</td>
	<td>
		<input type="hidden" name="id" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : 0; ?>">
		<input type="text" name="nickname" id="nickname"
		       value="<?php echo isset($pageData['info']['nickname']) ? $pageData['info']['nickname'] : ''; ?>">
		性别：
		<select name="gender" id="gender">
			<option
				value="1"<?php if (isset($pageData['info']['gender']) && $pageData['info']['gender'] == 1) echo ' selected="selected"'; ?>>
				男
			</option>
			<option
				value="2"<?php if (isset($pageData['info']['gender']) && $pageData['info']['gender'] == 2) echo ' selected="selected"'; ?>>
				女
			</option>
		</select>
		品质：
		<select name="quality" id="quality">
			<?php foreach (T_Hero::$heroQual as $key => $val) { ?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
		头像：
		<input type="text" id="face_id" name="face_id"
		       value="<?php echo isset($pageData['info']['face_id']) ? $pageData['info']['face_id'] : ''; ?>"
		       style="width: 50px;"> <input type="button" value="选择"
		                                    onclick="$('#ceng').css('display', '');">
	</td>
</tr>

<tr>
	<td>等级：</td>
	<td><input type="text" name="level" id="level"
	           value="<?php echo isset($pageData['info']['level']) ? $pageData['info']['level'] : ''; ?>"
	           style="width: 50px;"></td>
</tr>

<tr>
	<td>总属性：：</td>
	<td><input type="text" id="sum" style="width: 50px;"> <input type="button" value="根据总属性随机分配3项属性"
	                                                             onclick="suiji();"></td>
</tr>

<tr>
	<td>属性：</td>
	<td>
		统帅：<input type="text" name="attr_lead" id="attr_lead"
		          value="<?php echo isset($pageData['info']['attr_lead']) ? $pageData['info']['attr_lead'] : ''; ?>"
		          style="width: 30px;">
		指挥：<input type="text" name="attr_command" id="attr_command"
		          value="<?php echo isset($pageData['info']['attr_command']) ? $pageData['info']['attr_command'] : ''; ?>"
		          style="width: 30px;">
		军事：<input type="text" name="attr_military" id="attr_military"
		          value="<?php echo isset($pageData['info']['attr_military']) ? $pageData['info']['attr_military'] : ''; ?>"
		          style="width: 30px;">
	</td>
</tr>
<tr>
	<td>精力：</td>
	<td><input type="text" name="attr_energy" id="attr_energy"
	           value="<?php echo isset($pageData['info']['attr_energy']) ? $pageData['info']['attr_energy'] : ''; ?>"
	           style="width: 50px;"></td>
</tr>
<tr>
	<td>情绪：</td>
	<td>
		<select name="attr_mood" id="attr_mood">
			<?php
			$qxArr = array(1 => '冷静', 2 => '暴躁');
			foreach ($qxArr as $key => $val) {
				?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['attr_mood']) && $pageData['info']['attr_mood'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td>技能：</td>
	<td>
		技能槽数量
		<select name="skill_slot_num" id="skill_slot_num">
			<?php
			$numArr = array(0 => 0, 1 => 1, 2 => 2);
			foreach ($numArr as $key => $val) {
				?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['skill_slot_num']) && $pageData['info']['skill_slot_num'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
		天赋技能：
		<select name="skill_slot" id="skill_slot">
			<option value="0">无</option>
			<?php foreach ($pageData['skill_list'] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['skill_slot']) && $pageData['info']['skill_slot'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>
		技能槽1：
		<select name="skill_slot_1" id="skill_slot_1">
			<option value="0">无</option>
			<?php foreach ($pageData['skill_list'] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['skill_slot_1']) && $pageData['info']['skill_slot_1'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>
		技能槽2：
		<select name="skill_slot_2" id="skill_slot_2">
			<option value="0">无</option>
			<?php foreach ($pageData['skill_list'] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['skill_slot_2']) && $pageData['info']['skill_slot_2'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td>装备：</td>
	<td>
		武器：
		<select name="equip_arm" id="equip_arm">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_WEAPON] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_arm']) && $pageData['info']['equip_arm'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
		军帽：
		<select name="equip_cap" id="equip_cap">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_HAT] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_cap']) && $pageData['info']['equip_cap'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
		军服：
		<select name="equip_uniform" id="equip_uniform">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_UNIFORM] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_uniform']) && $pageData['info']['equip_uniform'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
		<br>
		勋章：
		<select name="equip_medal" id="equip_medal">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_MEDAL] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_medal']) && $pageData['info']['equip_medal'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
		军鞋：
		<select name="equip_shoes" id="equip_shoes">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_SHOES] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_shoes']) && $pageData['info']['equip_shoes'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
		座驾：
		<select name="equip_sit" id="equip_sit">
			<option value="0">--</option>
			<?php foreach ($pageData['equipList'][T_Equip::EQUIP_SIT] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['equip_sit']) && $pageData['info']['equip_sit'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name'] . '-' . T_Word::$EQUIP_POS[$val['quality']]; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td>军队</td>
	<td>
		兵种：
		<select name="army_id" id="army_id">
			<option>--</option>
			<?php foreach (M_Army::$type as $key => $val) { ?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['army_id']) && $pageData['info']['army_id'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
		兵种等级：<input type="text" name="army_lv" id="army_lv"
		            value="<?php echo isset($pageData['info']['army_lv']) ? $pageData['info']['army_lv'] : '0'; ?>"
		            style="width: 30px;">
		兵种数量：<input type="text" name="army_num" id="army_num"
		            value="<?php echo isset($pageData['info']['army_num']) ? $pageData['info']['army_num'] : ''; ?>"
		            style="width: 50px;">
		兵种武器：
		<select name="weapon_id" id="weapon_id">
			<option>--</option>
			<?php foreach ($pageData['weaponList'] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['weapon_id']) && $pageData['info']['weapon_id'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td><input id="sub" type="button" value=" 保 存 " onclick="sb()"></td>
</tr>
</table>
</form>
</div>

<div id="ceng"
     style="border: 0px solid red; position: absolute;left:350px; top: 200px; display: none; text-align: right; ">
	<?php
	$url = $resUrl . 'imgs/avatar/npc/';
	for ($i = 1; $i < 31; $i++) {
		?>
		<img src="<?php echo $url . $i . '.png'; ?>" width="64px" height="64px;"
		     onclick="$('#face_id').val('<?php echo $i; ?>'),$('#ceng').css('display', 'none');"
		     style="cursor: pointer;"/>
		<?php if ($i % 10 == 0) {
			echo "<br>";
		} ?>
	<?php

	}
	?>
	<input type="button" value="取消" onclick="$('#ceng').css('display', 'none');">
</div>
