<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
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
	<h1>NPC管理</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/NpcList">NPC部队列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>NPC部队</a> <span id="msg"
	                                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
<iframe name="iframe" id="iframe" style="display: none;"></iframe>
<form id="addForm" name="addForm" action="?r=War/NpcEdit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<td style="width: 100px;">NPC部队名称：</td>
	<td>
		<input type="hidden" name="id" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : ''; ?>">
		<input type="text" name="nickname" id="nickname"
		       value="<?php echo isset($pageData['info']['nickname']) ? $pageData['info']['nickname'] : ''; ?>">
	</td>
</tr>

<tr>
	<td>图标：</td>
	<td>
		<input type="text" id="face_id" name="face_id"
		       value="<?php echo isset($pageData['info']['face_id']) ? $pageData['info']['face_id'] : ''; ?>"
		       style="width: 50px;"> <input type="button" value="选择" onclick="$('#ceng').css('display', '');">
	</td>
</tr>

<tr>
	<td>等级：</td>
	<td>
		<input type="text" name="level" id="level"
		       value="<?php echo isset($pageData['info']['level']) ? $pageData['info']['level'] : ''; ?>">
	</td>
</tr>
<tr>
	<td>经验：</td>
	<td>
		<input type="text" name="exp_num" id="" exp_num""
		value="<?php echo isset($pageData['info']['"exp_num"']) ? $pageData['info']['"exp_num"'] : ''; ?>">
	</td>
</tr>
<tr>
	<td>NPC类型：</td>
	<td>
		<select name="type" id="type" onchange="changeType(this.value)">
			<?php foreach (M_NPC::$NpcType as $key => $val) { ?>
				<option
					value="<?php echo $key; ?>"<?php if (isset($pageData['info']['type']) && $pageData['info']['type'] == $key) echo ' selected="selected"'; ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td>选择NPC英雄：</td>
	<td>
		请选择：

		<select id="selectHero">
			<?php
			$tmpHeroArr = array();
			if (isset($pageData['info']['army_data'])) {
				$tmpHeroArr = json_decode($pageData['info']['army_data'], true);
			}
			?>
			<?php foreach ($pageData['hero_list'] as $key => $val) { ?>
				<option value="<?php echo $val['id']; ?>"><?php echo $val['nickname']; ?></option>
			<?php } ?>
		</select>
		<a href="javascript:_addHero();">添加</a>
		<table style="font-size: 12px;">
			<tr>
				<td colspan="2">部队英雄：</td>
			</tr>
			<tbody id="heroTbody">
			<?php
			if (count($tmpHeroArr) > 0) {
				foreach ($tmpHeroArr as $v) {
					?>
					<tr>
						<td><input type="hidden" name="heros[]" value="<?php echo $v; ?>">
							<?php echo $pageData['hero_list'][$v]['nickname']; ?>
						</td>
						<td><a href="javascript:void(0)" onclick="del(this);">删除</a></td>
					</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>
	</td>
</tr>

<tr id="tsRow"  <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] > 4) {
	echo 'style="display: none;"';
} ?>>
	<td>探索需要：</td>
	<td>
		<?php
		$tmpArr = array();
		if (isset($pageData['info']['probe_cost_data'])) {
			$tmpArr = json_decode($pageData['info']['probe_cost_data'], true);
		}
		?>
		<input type="checkbox" name="ts_res_type[]" value="gold" <?php if (isset($tmpArr['gold'])) {
			echo 'checked="checked"';
		} ?>>
		金钱x<input type="text" name="ts_gold_num"
		          value="<?php echo isset($tmpArr['gold']) ? $tmpArr['gold'] : ''; ?>"><br>
		<input type="checkbox" name="ts_res_type[]" value="food" <?php if (isset($tmpArr['food'])) {
			echo 'checked="checked"';
		} ?>>
		食物x<input type="text" name="ts_food_num"
		          value="<?php echo isset($tmpArr['food']) ? $tmpArr['food'] : ''; ?>"><br>
		<input type="checkbox" name="ts_res_type[]" value="oil" <?php if (isset($tmpArr['oil'])) {
			echo 'checked="checked"';
		} ?>>
		石油x<input type="text" name="ts_oil_num" value="<?php echo isset($tmpArr['oil']) ? $tmpArr['oil'] : ''; ?>">
	</td>
</tr>

<tr id="tsRow"  <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] > 4) {
	echo 'style="display: none;"';
} ?>>
	<td>每10分钟给预备兵：</td>
	<td>
		<input type="text" name="res_data"
		       value="<?php echo isset($pageData['info']['res_data']) ? $pageData['info']['res_data'] : 0; ?>">个
	</td>
</tr>

<tr id="resRow" <?php if (!isset($pageData['info']['type']) || $pageData['info']['type'] != 5) {
	echo 'style="display: none;"';
} ?>>
	<td>资源数据：</td>
	<td>
		<?php
		$tmpResArr = array();
		if (isset($pageData['info']['res_data'])) {
			$tmpResArr = json_decode($pageData['info']['res_data'], true);
		}
		?>
		<select name="res_data_type" id="res_data_type">
			<?php
			$resArr = array(
				'gold' => '金钱',
				'food' => '食物',
				'oil' => '石油',
			);
			foreach ($resArr as $k => $v) {
				?>
				<option value="<?php echo $k; ?>" <?php if (!empty($tmpResArr)) {
					foreach ($tmpResArr as $key => $val) {
						if ($key == $k) {
							echo ' selected="selected"';
						}
					}
				}?>><?php echo $v; ?></option>
			<?php } ?>
		</select>
		产量：<input type="text" name="res_data_num" id="res_data_num" value="<?php
		if (!empty($tmpResArr) && is_array($tmpResArr)) {
			foreach ($tmpResArr as $key => $val) {
				echo $val;
			}
		}
		?>" style="width: 50px;">/h
	</td>
</tr>

<tr id="tsjl" <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] > 4) {
	echo 'style="display: none;"';
} ?>>
	<td>探索事件：</td>
	<td>
		<select id="selectProbe">
			<?php foreach ($pageData['probeList'] as $key => $val) { ?>
				<option value="<?php echo $val['id']; ?>"><?php echo $val['title'] ?></option>
			<?php } ?>
		</select>
		触发几率 <input type="text" id="probePro" style="width: 50px;">%
		<input type="button" value="添加" onclick="addProbe();">
		<table style="font-size: 12px;">
			<tr>
				<td>事件内容</td>
				<td>触发概率</td>
				<td>删除</td>
			</tr>
			<tbody id="probe_tb">
			<?php
			$probe_event_data = array();
			if (isset($pageData['info']['probe_event_data']) && $pageData['info']['probe_event_data']) {
				$probe_event_data = json_decode($pageData['info']['probe_event_data'], true);
			}
			if ($probe_event_data) {
				foreach ($probe_event_data as $key => $val) {
					?>
					<tr>
						<td><?php echo $pageData['probeList'][$key]['title']; ?><input name="probeId[]" type="hidden"
						                                                               value="<?php echo $key; ?>"></td>
						<td><?php echo $val; ?>%<input name="probePro[]" type="hidden" value="<?php echo $val; ?>"></td>
						<td><a href="javascript:void(0)" onclick="del(this);">删除</a></td>
					</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>
	</td>
</tr>


<tr>
	<td>奖励ID：</td>
	<td>
		<input type="text" name="award_id"
		       value="<?php echo isset($pageData['info']['award_id']) ? $pageData['info']['award_id'] : ''; ?>">
	</td>
</tr>

<tr>
	<td>奖励内容：</td>
	<td>
		<?php
		if (isset($pageData['info']['award_id']) && $pageData['info']['award_id']) {

			$awardArr = M_Award::allResult($pageData['info']['award_id']);
			$text = M_Award::toText($awardArr);

			foreach ($text as $val) {
				if ($val[0] == 'res' || $val[0] == 'money' || $val[0] == 'item') {
					echo $textArr[$val[1]], 'x', $val[3], ' 概率:', $val[4], '%', '<br>';
				} elseif ($val[0] == 'equip') {
					echo $textArr[$val[0]], ':', $val[2][1], 'x', $val[3], ' 概率:', $val[4], '%', '<br>';
				} else {
					echo $textArr[$val[0]], ':', $val[2], 'x', $val[3], ' 概率:', $val[4], '%', '<br>';
				}
			}
		}
		?>
	</td>
</tr>

<tr>
	<td>奖励描述：</td>
	<td>
		<textarea rows="" cols="" name="award_remark"
		          style="width: 500px; height: 60px;"><?php echo isset($pageData['info']['award_remark']) ? $pageData['info']['award_remark'] : ''; ?></textarea>
	</td>
</tr>

<tr>
	<td>部队描述：</td>
	<td>
		<textarea rows="" cols="" name="feature"
		          style="width: 500px; height: 60px;"><?php echo isset($pageData['info']['feature']) ? $pageData['info']['feature'] : ''; ?></textarea>
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
