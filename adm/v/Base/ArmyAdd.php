<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'兵种列表' => 'Base/ArmyList&page=1',
);
$info = $pageData['info'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改兵种'] = 'Base/ArmyAdd&id=' . $info['id'];
} else {
	$urlArr['新增兵种'] = 'Base/ArmyAdd';
}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改兵种' : '新增兵种'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('name').value == '') {
			window.alert('请填写兵种名称!');
			flag = false;
		}
		else if (document.getElementById('features').value == '') {
			window.alert('请填写兵种描述!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>

<div class="table">
	<form action="?r=Base/DoArmyAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>兵种</th>
			</tr>
			<tr>
				<td width="100px"><strong>兵种名称</strong></td>
				<td width="400px">
					<input type='hidden' name='id' id='id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="text" class="text" name="name" id="name"
					       value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>兵种介绍</strong></td>
				<td>
					<textarea name="features" id='features' cols='50'
					          rows='3'><?php echo isset($info['features']) ? $info['features'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>生命值</strong></td>
				<td><input type="text" class="text" name="life_value" id="life_value"
				           value="<?php echo isset($info['life_value']) ? $info['life_value'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对地攻击</strong></td>
				<td><input type="text" class="text" name="att_land" id="att_land"
				           value="<?php echo isset($info['att_land']) ? $info['att_land'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对空攻击</strong></td>
				<td><input type="text" class="text" name="att_sky" id="att_sky"
				           value="<?php echo isset($info['att_sky']) ? $info['att_sky'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对海攻击</strong></td>
				<td><input type="text" class="text" name="att_ocean" id="att_ocean"
				           value="<?php echo isset($info['att_ocean']) ? $info['att_ocean'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对地防御</strong></td>
				<td><input type="text" class="text" name="def_land" id="def_land"
				           value="<?php echo isset($info['def_land']) ? $info['def_land'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对空防御</strong></td>
				<td><input type="text" class="text" name="def_sky" id="def_sky"
				           value="<?php echo isset($info['def_sky']) ? $info['def_sky'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>对海防御</strong></td>
				<td><input type="text" class="text" name="def_ocean" id="def_ocean"
				           value="<?php echo isset($info['def_ocean']) ? $info['def_ocean'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>招募金钱消耗</strong></td>
				<td><input type="text" class="text" name="cost_gold" id="cost_gold"
				           value="<?php echo isset($info['cost_gold']) ? $info['cost_gold'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>招募粮食消耗</strong></td>
				<td><input type="text" class="text" name="cost_food" id="cost_food"
				           value="<?php echo isset($info['cost_food']) ? $info['cost_food'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>招募石油消耗</strong></td>
				<td><input type="text" class="text" name="cost_oil" id="cost_oil"
				           value="<?php echo isset($info['cost_oil']) ? $info['cost_oil'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>招募人口消耗</strong></td>
				<td><input type="text" class="text" name="cost_people" id="cost_people"
				           value="<?php echo isset($info['cost_people']) ? $info['cost_people'] : 0; ?>"/></td>
			</tr>
			<tr>
				<td><strong>描述1(备用)</strong></td>
				<td>
					<textarea name="desc_1" id='desc_1' cols='50'
					          rows='3'><?php echo isset($info['desc_1']) ? $info['desc_1'] : ''; ?></textarea>
				</td>
			</tr>

		</table>
	</form>
</div>