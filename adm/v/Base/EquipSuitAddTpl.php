<?php
$pageData = B_View::getVal('pageData');
$info = isset($pageData['info']) ? $pageData['info'] : array();
$effect = isset($info['effect']) ? $info['effect'] : '';
$typeArr = array_merge(array('0' => '所有'), M_Army::$type, array('5' => '没有兵种'));
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
$suitEquip = array_merge(T_Effect::$SuitBaseType, T_Effect::$SuitBattleType);
?>

<script type="text/javascript">
	$(document).ready(function () {
		$("#submit").click(function () {
			var data = $("#addForm").serialize();

			$.post('?r=Base/EquipSuitAdd', data, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.err);
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json')
		});
	});


</script>
<div class="top-bar">
	<h1>装备管理</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="?r=Base/EquipList">装备列表</a> / <a href="?r=Base/EquipSuit">套装列表</a>
		/ <a href="#"><?php echo isset($pageData['info']

			['id']) ? '修改' : '添加' ?>套装</a> <span id="msg"
		                                         style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">

<form id="addForm" name="addForm" method="post" action="?r=Base/EquipSuitAdd">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<td width="100px;">套装名称：</td>
	<td>
		<input type="text" name="name" id="name"
		       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
		<input type="hidden" name="id" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : ''; ?>">
	</td>
</tr>
<tr>
	<td colspan='1'>2件加成：</td>
	<td> 效果类型
		<select name="select2" id="select2">

			<option>选择</option>
			<?php foreach ($suitEquip as $key => $val) { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
			<?php } ?>
		</select>
		效果值
		<input type="text" name="val2" id="val2" value="">

		<div>
			使用兵种
			<select id="self_army_type2" name="self_army_type2">
				<?php
				foreach ($typeArr as $key => $val):?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			目标兵种
			<select id="aim_army_type2" name="self_army_type2">
				<?php
				foreach ($typeArr as $key => $val) :?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			攻击类型
			<select id="effect_type2" name="effect_type2">
				<?php
				foreach (T_Effect::$SkillAimType as $key => $val) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</div>
		<a id="effadd2">添加</a>
	</td>

</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td></td>
</tr>
<tr>
	<td>3件加成：</td>
	<td>效果类型
		<select name="select3" id="select3">
			<option>选择</option>
			<?php foreach ($suitEquip as $key => $val) { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
			<?php } ?>
		</select>
		效果值
		<input type="text" name="val3" id="val3" value="">

		<div>
			使用兵种
			<select id="self_army_type3" name="self_army_type3">
				<?php
				foreach ($typeArr as $key => $val):?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			目标兵种
			<select id="aim_army_type3" name="aim_army_type3">
				<?php
				foreach ($typeArr as $key => $val) :?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			攻击类型
			<select id="effect_type3" name="effect_type3">
				<?php
				foreach (T_Effect::$SkillAimType as $key => $val) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</div>
		<a id="effadd3">添加</a>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td></td>
</tr>
<tr>
	<td>4件加成：</td>
	<td>效果类型
		<select name="select4" id="select4">
			<option>选择</option>
			<?php foreach ($suitEquip as $key => $val) { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
			<?php } ?>
		</select>
		效果值
		<input type="text" name="val4" id="val4" value="">

		<div>
			使用兵种
			<select id="self_army_type4" name="self_army_type4">
				<?php
				foreach ($typeArr as $key => $val):?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			目标兵种
			<select id="aim_army_type4" name="aim_army_type4">
				<?php
				foreach ($typeArr as $key => $val) :?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			攻击类型
			<select id="effect_type4" name="effect_type4">
				<?php
				foreach (T_Effect::$SkillAimType as $key => $val) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</div>
		<a id="effadd4">添加</a>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td></td>
</tr>
<tr>
	<td>5件加成：</td>
	<td>效果类型
		<select name="select5" id="select5">
			<option>选择</option>
			<?php foreach ($suitEquip as $key => $val) { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
			<?php } ?>
		</select>
		效果值
		<input type="text" name="val5" id="val5" value="">

		<div>
			使用兵种
			<select id="self_army_type5" name="self_army_type5">
				<?php
				foreach ($typeArr as $key => $val):?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			目标兵种
			<select id="aim_army_type5" name="aim_army_type5">
				<?php
				foreach ($typeArr as $key => $val) :?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			攻击类型
			<select id="effect_type5" name="effect_type5">
				<?php
				foreach (T_Effect::$SkillAimType as $key => $val) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</div>
		<a id="effadd5">添加</a>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td></td>
</tr>
<tr>
	<td>6件加成：</td>
	<td>效果类型
		<select name="select6" id="select6">
			<option>选择</option>
			<?php foreach ($suitEquip as $key => $val) { ?>
				<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
			<?php } ?>
		</select>
		效果值
		<input type="text" name="val6" id="val6" value="">

		<div>
			使用兵种
			<select id="self_army_type6" name="self_army_type6">
				<?php
				foreach ($typeArr as $key => $val):?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			目标兵种
			<select id="aim_army_type6" name="aim_army_type6">
				<?php
				foreach ($typeArr as $key => $val) :?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php endforeach; ?>
			</select>
			攻击类型
			<select id="effect_type6" name="effect_type6">
				<?php
				foreach (T_Effect::$SkillAimType as $key => $val) {
					?>
					<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</div>
		<a id="effadd6">添加</a>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td id="txt">

		<?php
		$type = array_merge(T_Effect::$SuitBaseType, T_Effect::$SuitBattleType);
		if (!empty($effect)) {
			foreach ($effect as $key => $value) {
				if (!empty($value)) {
					foreach ($value as $k => $v) {
						list($effect_val, $self_army_type, $aim_army_type, $effect_type) = explode('|', $v);
						//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
						echo "<div id='effect{$key}_{$k}'><input type='hidden' name='effect{$key}[$k]' value='{$v}'>";

						echo "<span>{$key}件{$type[$k]},<br>技能值[{$effect_val}";
						$effType = T_Effect::$SkillAimType[$effect_type];
						echo "],使用兵种[{$typeArr[$self_army_type]}],目标兵种[{$typeArr[$aim_army_type]}],攻击类型[{$effType}]</span>&nbsp;&nbsp;";
						echo "</span>&nbsp;&nbsp;<a onclick='del_effect{$key}(\"{$k}\")'>删除</a></div>";
					}
				}
			}
		}
		?>
	</td>
<tr>
	<td>套装描述</td>
	<td>
		<input type="text" name="desc" id="desc"
		       value="<?php echo isset($pageData['info']['desc']) ? $pageData['info']['desc'] : ''; ?>"
		       style="width: 500px;">
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input id="submit" type="button" value=" 保 存 "></td>
</tr>
</table>
</form>
</div>
<script>
	function del_effect2(val) {
		var k = "#effect2_" + val;
		var b = $(k).html('');
	}
	function del_effect3(val) {
		var k = "#effect3_" + val;
		var b = $(k).html('');
	}
	function del_effect4(val) {
		var k = "#effect4_" + val;
		var b = $(k).html('');
	}
	function del_effect5(val) {
		var k = "#effect5_" + val;
		var b = $(k).html('');
	}
	function del_effect6(val) {
		var k = "#effect6_" + val;
		var b = $(k).html('');
	}
	$('#effadd2').click(
		function () {
			var self_army_type = $('#self_army_type2').val();
			var aim_army_type = $('#aim_army_type2').val();
			var effect_key = $('#select2').val();
			var effect_val = $('#val2').val();
			var effect_type = $('#effect_type2').val();
			var self_army_type_txt = $('#self_army_type2').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type2').find("option:selected").text();
			var effect_key_txt = $('#select2').find("option:selected").text();
			var effect_type_txt = $('#effect_type2').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = effect_val + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type;
				str = "<div id=effect2_" + effect_key + "><input type='hidden' name='effect2[" + effect_key + "]' value='" + d + "'>";
				str += "<span>2件" + effect_key_txt + "技能值[" + effect_val;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect2(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}

		}
	);
	$('#effadd3').click(
		function () {
			var self_army_type = $('#self_army_type3').val();
			var aim_army_type = $('#aim_army_type3').val();
			var effect_key = $('#select3').val();
			var effect_val = $('#val3').val();
			var effect_type = $('#effect_type2').val();
			var self_army_type_txt = $('#self_army_type3').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type3').find("option:selected").text();
			var effect_key_txt = $('#select2').find("option:selected").text();
			var effect_type_txt = $('#effect_type2').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = effect_val + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type;
				str = "<div id=effect3_" + effect_key + "><input type='hidden' name='effect3[" + effect_key + "]' value='" + d + "'>";
				str += "<span>3件" + effect_key_txt + "技能值[" + effect_val;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect3(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}


		}
	);
	$('#effadd4').click(
		function () {
			var self_army_type = $('#self_army_type4').val();
			var aim_army_type = $('#aim_army_type4').val();
			var effect_key = $('#select4').val();
			var effect_val = $('#val4').val();
			var effect_type = $('#effect_type4').val();
			var self_army_type_txt = $('#self_army_type4').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type4').find("option:selected").text();
			var effect_key_txt = $('#select4').find("option:selected").text();
			var effect_type_txt = $('#effect_type4').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = effect_val + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type;
				str = "<div id=effect4_" + effect_key + "><input type='hidden' name='effect4[" + effect_key + "]' value='" + d + "'>";
				str += "<span>4件" + effect_key_txt + "技能值[" + effect_val;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect4(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}


		}
	);
	$('#effadd5').click(
		function () {
			var self_army_type = $('#self_army_type5').val();
			var aim_army_type = $('#aim_army_type5').val();
			var effect_key = $('#select5').val();
			var effect_val = $('#val5').val();
			var effect_type = $('#effect_type5').val();
			var self_army_type_txt = $('#self_army_type5').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type5').find("option:selected").text();
			var effect_key_txt = $('#select5').find("option:selected").text();
			var effect_type_txt = $('#effect_type5').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = effect_val + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type;
				str = "<div id=effect5_" + effect_key + "><input type='hidden' name='effect5[" + effect_key + "]' value='" + d + "'>";
				str += "<span>5件" + effect_key_txt + "技能值[" + effect_val;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect5(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}

		}
	);
	$('#effadd6').click(
		function () {
			var self_army_type = $('#self_army_type6').val();
			var aim_army_type = $('#aim_army_type6').val();
			var effect_key = $('#select6').val();
			var effect_val = $('#val6').val();
			var effect_type = $('#effect_type6').val();
			var self_army_type_txt = $('#self_army_type6').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type6').find("option:selected").text();
			var effect_key_txt = $('#select6').find("option:selected").text();
			var effect_type_txt = $('#effect_type6').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = effect_val + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type;
				str = "<div id=effect6_" + effect_key + "><input type='hidden' name='effect6[" + effect_key + "]' value='" + d + "'>";
				str += "<span>6件" + effect_key_txt + "技能值[" + effect_val;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect6(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}


		}
	);
</script>
