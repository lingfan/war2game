<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'装备配置' => 'System/ConfigEquip',
);
$baselist = $pageData['baselist'];
?>
<script>
	function addHeroExpRow() {
		var str = "<td><input type=\"text\" name=\"hero_exp_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_exp_val[]\" style=\"width: 80px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_exp_tb').append(newTR);
	}

	function addHeroMoodRow() {
		var str = "<td><input type=\"text\" name=\"hero_attr_mood_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_attr_mood_val[]\" style=\"width: 35px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_mood_tb').append(newTR);
	}

	function addHeroEnergyRow() {
		var str = "<td><input type=\"text\" name=\"hero_attr_energy_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_attr_energy_val[]\" style=\"width: 35px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_energy_tb').append(newTR);
	}

	function del(a) {
		a.parentNode.parentNode.parentNode.removeChild(a.parentNode.parentNode);
	}
</script>

<div class="top-bar">
	<h1>装备配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigEquip&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">装备配置信息 重要内容！请勿随意更改！！！</th>
			</tr>
			<tr>
				<td width="200"><strong>装备最大强化等级</strong></td>
				<td><input type="text" class="text" name="strong_equip_max_level"
				           value="<?php echo $baselist['strong_equip_max_level']; ?>"/></td>
			</tr>

			<tr>
				<td><strong>强化所需基础资源(金)</strong></td>
				<td><input type="text" class="text" name="strong_equip_base_gold"
				           value="<?php echo $baselist['strong_equip_base_gold']; ?>"/></td>
			</tr>

			<tr>
				<td><strong>强化所需基础资源系数</strong></td>
				<td><input type="text" class="text" name="strong_equip_gold_rate"
				           value="<?php echo $baselist['strong_equip_gold_rate']; ?>"/></td>
			</tr>

			<tr>
				<td><strong>强化所需资源计算参数a</strong></td>
				<td><input type="text" class="text" name="strong_equip_rate_a"
				           value="<?php echo $baselist['strong_equip_rate_a']; ?>"/></td>
			</tr>

			<tr>
				<td><strong>强化所需资源计算参数b</strong></td>
				<td><input type="text" class="text" name="strong_equip_rate_b"
				           value="<?php echo $baselist['strong_equip_rate_b']; ?>"/></td>
			</tr>

			<tr>
				<td><strong>强化所需资源计算参数s(分品质)</strong></td>
				<td>
					<table cellspacing="0px" border="0" style="font-size: 12px;">
						<?php foreach (T_Word::$EQUIP_QUAL as $key => $val) { ?>
							<tr>
								<td><?php echo $val; ?></td>
								<td><input type="text" name="strong_equip_rate_s[<?php echo $key; ?>]"
								           value="<?php $arr = $baselist['strong_equip_rate_s'];
								           echo $arr[$key]; ?>"></td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>

			<tr>
				<td><strong>各品质装备成长值</strong></td>
				<td>
					<table cellspacing="0px" border="0" style="font-size: 12px;">
						<tr>
							<td>装备品质</td>
							<?php foreach (T_Word::$EQUIP_QUAL as $key => $val) { ?>
								<td><?php echo $val; ?></td>
							<?php } ?>
						</tr>
						<?php foreach (T_Equip::$equipLevel as $level) { ?>
							<tr>
								<td>等级:<?php echo $level; ?></td>
								<?php foreach (T_Word::$EQUIP_QUAL as $qual => $val) { ?>
									<td><input type="text"
									           name="strong_equip_attr_add_rate[<?php echo $level; ?>][<?php echo $qual; ?>]"
									           value="<?php $arr = $baselist['strong_equip_attr_add_rate'];
									           echo isset($arr[$level][$qual]) ? $arr[$level][$qual] : ''; ?>"
									           style="width: 25px;"></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
			<tr>
				<td><strong>各品质套装装备成长值</strong></td>
				<td>
					<table cellspacing="0px" border="0" style="font-size: 12px;">
						<tr>
							<td>装备品质</td>
							<?php foreach (T_Word::$EQUIP_QUAL as $key => $val) { ?>
								<td><?php echo $val; ?></td>
							<?php } ?>
						</tr>
						<?php foreach (T_Equip::$equipLevel as $level) { ?>
							<tr>
								<td>等级:<?php echo $level; ?></td>
								<?php foreach (T_Word::$EQUIP_QUAL as $qual => $val) { ?>
									<td><input type="text"
									           name="strong_suit_equip_attr_add_rate[<?php echo $level; ?>][<?php echo $qual; ?>]"
									           value="<?php $arr = $baselist['strong_suit_equip_attr_add_rate'];
									           echo isset($arr[$level][$qual]) ? $arr[$level][$qual] : ''; ?>"
									           style="width: 25px;"></td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
			<!--        <tr> -->
			<!-- 			<td><strong>套装装备是否可以合成</strong></td> -->
			<!-- 			<td><input type="text" class="text" name="is_synthesis" value=" -->
			<?php //echo $baselist['is_synthesis'];?>
			<!-- 			" /></td> -->
			<!-- 		</tr> -->
			<!-- 		<tr> -->
			<!-- 			<td><strong>套装装备是否可以升级</strong></td> -->
			<!-- 			<td><input type="text" class="text" name="is_upgrades" value=" -->
			<?php //echo $baselist['is_upgrades'];?>
			<!-- 			" /></td> -->
			<!-- 		</tr> -->
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
