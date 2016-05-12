<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'科技升级列表' => 'Base/TechUpgList&page=1&id=' . $pageData['id'],
);
$info = $pageData['info'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改升级科技'] = 'Base/TechUpgAdd&id=' . $info['tech_id'] . '&level=' . $info['level'];
	$arrNeedBuild = !empty($info['need_build']) ? $info['need_build'][0] : array();
	$arrNeedTech = !empty($info['need_tech']) ? $info['need_tech'][0] : array();
	$arrEffect = !empty($info['effect']) ? json_decode($info['effect'], true) : array();

} else {
	$urlArr['新增升级科技'] = 'Base/TechUpgAdd&id=' . $pageData['id'];

}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改科技升级' : '新增科技升级'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('id').value == '') {
			window.alert('请填写科技ID!');
			flag = false;
		}
		else if (document.getElementById('level').value < 1 || isNaN(document.getElementById('level').value)) {
			window.alert('请填写正确的科技等级!');
			flag = false;
		}
		else if (document.getElementById('cost_gold').value == '' || isNaN(document.getElementById('cost_gold').value)) {
			window.alert('请输入正确的消耗金钱数!');
			flag = false;
		}
		else if (document.getElementById('cost_food').value == '' || isNaN(document.getElementById('cost_food').value)) {
			window.alert('请输入正确的消耗粮食数!');
			flag = false;
		}
		else if (document.getElementById('cost_oil').value == '' || isNaN(document.getElementById('cost_oil').value)) {
			window.alert('请输入正确的消耗石油数!');
			flag = false;
		}
		else if (document.getElementById('cost_time').value == '' || isNaN(document.getElementById('cost_time').value)) {
			window.alert('请输入正确的消耗时间数!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>

<div class="table">
	<form action="?r=Base/DoTechUpgAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td class="full" colspan="2">
					<font style="color:red"><?php echo isset($info['id']) ? '修改 ' : '新增 ';
						echo $pageData['techBase'][$pageData['id']]['name']; ?></font> 升级数据
				</td>
			</tr>

			<tr>
				<td width="100px"><strong>等级</strong></td>
				<td width="400px">
					<input type='hidden' name='record_id' id='record_id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="hidden" name="id" id="id"
					       value="<?php echo isset($pageData['id']) ? $pageData['id'] : 0; ?>"/>
					<input type="text" class="text" name="level" id="level"
					       value="<?php echo isset($info['level']) ? $info['level'] : ''; ?>" <?php echo isset($info['level']) ? 'readonly' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td><strong>消耗金钱</strong></td>
				<td>
					<input type="text" class="text" name="cost_gold" id="cost_gold"
					       value="<?php echo isset($info['cost_gold']) ? $info['cost_gold'] : 0; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>消耗粮食</strong></td>
				<td>
					<input type="text" class="text" name="cost_food" id="cost_food"
					       value="<?php echo isset($info['cost_food']) ? $info['cost_food'] : 0; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>消耗石油</strong></td>
				<td>
					<input type="text" class="text" name="cost_oil" id="cost_oil"
					       value="<?php echo isset($info['cost_oil']) ? $info['cost_oil'] : 0; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>消耗时间</strong></td>
				<td>
					<input type="text" class="text" name="cost_time" id="cost_time"
					       value="<?php echo isset($info['cost_time']) ? $info['cost_time'] : 0; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>建筑前提</strong></td>
				<td>
					<select name='need_build_id' id='need_build_id'>
						<option value='0'>--请选择建筑--</option>
						<?php foreach ($pageData['buildBase'] as $buildId => $buildInfo) { ?>
							<option
								value="<?php echo $buildId; ?>" <?php echo (isset($info['id']) && $buildId == $arrNeedBuild[0]) ? 'selected' : ''; ?> >
								<?php echo $buildInfo['name']; ?></option>
						<?php } ?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;等级
					<select name='need_build_level' id='need_build_level'>
						<option value='0'>--请选择等级--</option>
						<?php for ($i = 1; $i < 101; $i++) { ?>
							<option
								value="<?php echo $i; ?>" <?php echo (isset($info['id']) && $i == $arrNeedBuild[1]) ? 'selected' : ''; ?> ><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><strong>科技前提</strong></td>
				<td>
					<select name='need_tech_id' id='need_tech_id'>
						<option value='0'>--请选择科技--</option>
						<?php foreach ($pageData['techBase'] as $techId => $techInfo) { ?>
							<option
								value="<?php echo $techId; ?>" <?php echo (isset($info['id']) && $techId == $arrNeedTech[0]) ? 'selected' : ''; ?> >
								<?php echo $techInfo['name']; ?></option>
						<?php } ?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;等级
					<select name='need_tech_level' id='need_tech_level'>
						<option value='0'>--请选择等级--</option>
						<?php for ($i = 1; $i < 101; $i++) { ?>
							<option
								value="<?php echo $i; ?>" <?php echo (isset($info['id']) && $i == $arrNeedTech[1]) ? 'selected' : ''; ?> ><?php echo $i; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><strong>科技效果</strong></td>
				<td>
					<select name='effect_code' id='effect_code'>
						<option value='0'>--请选择效果--</option>
						<?php foreach (T_Effect::$Tech as $effect_code => $effect_desc) { ?>
							<option
								value="<?php echo $effect_code; ?>" <?php echo (isset($info['id']) && $effect_code == key($arrEffect)) ? 'selected' : ''; ?> >
								<?php echo $effect_desc; ?></option>
						<?php } ?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;效果值
					<input type="text" class="text" name="effect_val" id="effect_val"
					       value="<?php echo !empty($arrEffect) ? current($arrEffect) : 0; ?>" style="width: 100px"/> %
				</td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit"
				           value="<?php echo isset($info['id']) ? '修改' : '新增'; ?>"/></td>
			</tr>
		</table>
	</form>
</div>