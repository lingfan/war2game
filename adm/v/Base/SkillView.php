<?php
$pageData = B_View::getVal('pageData');
$info = isset($pageData['info']) ? $pageData['info'] : array();
$effect = isset($info['effect']) ? $info['effect'] : '';
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>

<script type="text/javascript">
	$(document).ready(function () {
		$("#submit").click(function () {
			var data = $("#skillEditForm").serialize();
			$.post('?r=Base/SkillEdit', data, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.err);
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json')
		});
	});


</script>
<div class="top-bar">
	<h1>技能管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="?r=Base/SkillList">技能列表</a> / <a
			href="#"><?php echo isset($info['id']) ? '修改' : '添加' ?>技能</a> <span id="msg"
	                                                                            style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">

	<form id="skillEditForm" name="skillEditForm" method="post" action="?r=Base/SkillEdit">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>技能名称：<input type="hidden" name="id" value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>">
				</td>
				<td>
					<input type="text" name="name" value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>图标选择</td>
				<td>
					<input type="text" name="face_id"
					       value="<?php echo isset($info['face_id']) ? $info['face_id'] : ''; ?>" style="width: 50px;">
				</td>
			</tr>
			<tr>
				<td>技能类型</td>
				<td>
					<?php
					$t = isset($info['type']) ? $info['type'] : 1;
					foreach (T_Hero::$skillType as $key => $val) {
						?>
						<input name="type" id="type" value="<?php echo $key; ?>" <?php if ($t == $key) {
							echo 'checked="checked"';
						} ?> type="radio"><?php echo $val; ?>
						&nbsp;&nbsp;&nbsp;
					<?php } ?>

				</td>
			</tr>
			<tr>
				<td>是否重复学习</td>
				<td>
					<?php
					$isRep = array('1' => '是', '0' => '否');
					$rep = isset($info['is_repeat']) ? $info['is_repeat'] : 0;
					foreach ($isRep as $key => $val) {
						?>
						<input name="is_repeat" id="is_repeat" value="<?php echo $key; ?>" <?php if ($rep == $key) {
							echo 'checked="checked"';
						} ?> type="radio"><?php echo $val; ?>
						&nbsp;&nbsp;&nbsp;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>等级</td>
				<td>
					<input type="text" style="width: 30px;" name="level"
					       value="<?php echo isset($info['level']) ? $info['level'] : ''; ?>">
				</td>
			</tr>
			<tr>
				<td>描述</td>
				<td>
					<input type="text" name=desc value="<?php echo isset($info['desc']) ? $info['desc'] : ''; ?>"
					       style="width: 500px;">
				</td>
			</tr>
			<tr>
				<td>同类型不同等级</td>
				<td>
					<input type="text" style="width: 50px;" name="level_type"
					       value="<?php echo isset($info['level_type']) ? $info['level_type'] : ''; ?>"
					       style="width: 500px;">
				</td>
			</tr>
			<tr>
				<td>排序</td>
				<td>
					<input type="text" style="width: 30px;" name="sort"
					       value="<?php echo isset($info['sort']) ? $info['sort'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>技能属性</td>
				<td>

					技能效果
					<select id="effect_key" name="effect_key">
						<?php
						foreach ($pageData['allSkill'] as $key => $val) {
							?>
							<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
						<?php } ?>
					</select>
					技能值
					<input type="text" id="effect_val" style="width: 150px;" name="effect_val" value="">

					<div>
						触发几率
						<input type="text" id="trigger_odds" style="width: 30px;" name="trigger_odds" value="100">%
						精力消耗
						<input type="text" id="cost_energy" style="width: 30px;" name="cost_energy" value="0">
						影响回合
						<input type="text" id="effect_bout" style="width: 30px;" name="effect_bout" value="0">
					</div>
					<div>
						触发类型
						<input type="radio" name="trigger_type" value="ATK" class="intro"/>攻击
						&nbsp;&nbsp;&nbsp;
						<input type="radio" name="trigger_type" value="DEF"/>反击
						&nbsp;&nbsp;&nbsp;
						<input type="radio" name="trigger_type" value="ATK&DEF" checked="checked"/>攻击和反击
					</div>
					<div>
						使用兵种
						<select id="self_army_type" name="self_army_type">
							<?php
							$typeArr = array_merge(array('0' => '所有'), M_Army::$type);
							foreach ($typeArr as $key => $val):?>
								<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
							<?php endforeach; ?>
						</select>
						目标兵种
						<select id="aim_army_type" name="aim_army_type">
							<?php
							foreach ($typeArr as $key => $val) :?>
								<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
							<?php endforeach; ?>
						</select>
						攻击类型
						<select id="effect_type" name="effect_type">
							<?php
							foreach (T_Effect::$SkillAimType as $key => $val) {
								?>
								<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
							<?php } ?>
						</select>
					</div>
					<a id="effadd">添加</a>

				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td id="txt">

					<?php
					$type = array_merge(T_Effect::$SkillBaseType, T_Effect::$SkillBattleType);

					$effect = json_decode($effect, true);
					if (!empty($effect)) {
						foreach ($effect as $k => $v) {
							list($trigger_odds, $effect_val, $trigger_type, $self_army_type, $aim_army_type, $effect_type, $cost_energy, $effect_bout) = explode('|', $v);
							//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
							echo "<div id='effect_{$k}'><input type='hidden' name='effect[$k]' value='{$v}'>";

							echo "<span>{$type[$k]},<br>触发概率[{$trigger_odds}%],技能值[{$effect_val}],回合数[{$effect_bout}";
							$effType = T_Effect::$SkillAimType[$effect_type];
							echo "],使用兵种[{$typeArr[$self_army_type]}],目标兵种[{$typeArr[$aim_army_type]}],攻击类型[{$effType}],消耗精力[{$cost_energy}],触发类型[{$trigger_type}]</span>&nbsp;&nbsp;";
							echo "</span>&nbsp;&nbsp;<a onclick='del_effect(\"{$k}\")'>删除</a></div>";
						}
					}
					?>
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
	function del_effect(val) {
		var k = "#effect_" + val;
		var b = $(k).html('');
	}

	$('#effadd').click(
		function () {
			var trigger_odds = $('#trigger_odds').val();
			var trigger_type = $('input[name="trigger_type"]:checked').val();//$('input:radio:checked').val(); $('input[@name="testradio"][checked]'); $('input[name="testradio"]').filter(':checked');
			//$('input[name="testradio"]').each(function(){alert(this.value);循环遍历
			//$('input[name="testradio"]:eq(1)').val() 取具体某个值
			var cost_energy = $('#cost_energy').val();
			var effect_bout = $('#effect_bout').val();
			var self_army_type = $('#self_army_type').val();
			var aim_army_type = $('#aim_army_type').val();

			var self_army_type_txt = $('#self_army_type').find("option:selected").text();
			var aim_army_type_txt = $('#aim_army_type').find("option:selected").text();


			var effect_key = $('#effect_key').val();
			var effect_val = $('#effect_val').val();
			var effect_type = $('#effect_type').val();
			var effect_key_txt = $('#effect_key').find("option:selected").text();
			var effect_type_txt = $('#effect_type').find("option:selected").text();

			var str = '';
			if (effect_key.length > 0 && effect_val.length > 0 && effect_type.length > 0) {
				//几率|技能值|触发类型|使用兵种|目标兵种|攻击类型|消耗精力|影响回合数
				var d = trigger_odds + "|" + effect_val + "|" + trigger_type + "|" + self_army_type + "|" + aim_army_type + "|" + effect_type + "|" + cost_energy + "|" + effect_bout;
				str = "<div id=effect_" + effect_key + "><input type='hidden' name='effect[" + effect_key + "]' value='" + d + "'>";
				str += "<span>" + effect_key_txt + ",<br>触发概率[" + trigger_odds + "%],技能值[" + effect_val + "],回合数[" + effect_bout;
				str += "],使用兵种[" + self_army_type_txt + "],目标兵种[" + aim_army_type_txt + "],攻击类型[" + effect_type_txt + "],消耗精力[" + cost_energy + "],触发类型[" + trigger_type + "]</span>&nbsp;&nbsp;";
				str += "<a onclick='del_effect(\"" + effect_key + "\")'>删除</a></div>";
				$('#txt').append(str);
			}
			else {
				alert('错误数据');
			}

		}
	);
</script>