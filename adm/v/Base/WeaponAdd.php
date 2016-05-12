<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'武器列表' => 'Base/WeaponList&page=1',
);
$info = $pageData['info'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改武器'] = 'Base/WeaponAdd&id=' . $info['id'];

	$arrNeedBuild = !empty($info['need_build']) ? $info['need_build'][0] : array();
	$arrNeedTech = !empty($info['need_tech']) ? $info['need_tech'][0] : array();

	$isChkSpV1 = (1 == $info['is_special']) ? 'checked' : '';
	$isChkSpV0 = (0 == $info['is_special']) ? 'checked' : '';
	$isChkNpcV1 = (1 == $info['is_npc']) ? 'checked' : '';
	$isChkNpcV0 = (0 == $info['is_npc']) ? 'checked' : '';
	$isChkStV1 = (1 == $info['shot_type']) ? 'checked' : '';
	$isChkStV2 = (2 == $info['shot_type']) ? 'checked' : '';
} else {
	$urlArr['新增武器'] = 'Base/WeaponAdd';

	$isChkSpV1 = ''; //特殊武器初始确认值
	$isChkSpV0 = 'checked'; //非特殊武器初始确认值
	$isChkNpcV1 = ''; //是NPC武器初始默认值
	$isChkNpcV0 = 'checked'; //非NPC武器初始默认值
	$isChkStV1 = 'checked'; //直线型射程初始默认值
	$isChkStV2 = ''; //弧线型射程初始默认值
}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改武器' : '新增武器'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('name').value == '') {
			window.alert('请填写武器名称!');
			flag = false;
		}
		else if (document.getElementById('army_id').value == '') {
			window.alert('请选择可装备兵种!');
			flag = false;
		}
		else if (document.getElementById('march_type').value == '') {
			window.alert('请选择出征系!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>

<div class="table">
<form action="?r=Base/DoWeaponAdd" method="post" onsubmit="javascript:return checkInput()">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>武器</th>
</tr>
<tr>
	<td width="100px"><strong>武器名称</strong></td>
	<td width="400px">
		<input type='hidden' name='id' id='id' value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
		<input type="text" class="text" name="name" id="name"
		       value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>"/>
	</td>
</tr>
<tr>
	<td><strong>武器描述</strong></td>
	<td>
		<textarea name="features" id='features' cols='50'
		          rows='3'><?php echo isset($info['features']) ? $info['features'] : ''; ?></textarea>
	</td>
</tr>
<tr>
	<td><strong>武器详细介绍</strong></td>
	<td>
		<textarea name="detail" id='detail' cols='50'
		          rows='3'><?php echo isset($info['detail']) ? $info['detail'] : ''; ?></textarea>
	</td>
</tr>
<tr>
	<td><strong>对应部队名称</strong></td>
	<td><input type="text" class="text" name="army_name" id="army_name"
	           value="<?php echo isset($info['army_name']) ? $info['army_name'] : 'XXX部队'; ?>"/></td>
</tr>
<tr>
	<td><strong>可装备兵种</strong></td>
	<td>
		<?php foreach (M_Army::$type as $type_val => $type_desc) { ?>
			<input type="radio" name="army_id" id="army_id" value="<?php echo $type_val; ?>"
				<?php echo (isset($info['army_id']) && $info['army_id'] == $type_val) ? 'checked' : ''; ?>  />
			<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
		<?php } ?>
	</td>
</tr>
<tr>
	<td><strong>需要兵种等级</strong></td>
	<td>
		<select name='need_army_lv' id='need_army_lv'>
			<option value='0'>--0--</option>
			<?php for ($i = 1; $i < 11; $i++) { ?>
				<option
					value="<?php echo $i; ?>" <?php echo (isset($info['id']) && $i == $info['need_army_lv']) ? 'selected' : ''; ?> ><?php echo $i; ?></option>
			<?php } ?>
		</select>&nbsp;级
	</td>
</tr>
<tr>
	<td><strong>出征系</strong></td>
	<td>
		<?php foreach (M_War::$marchType as $type_val => $type_desc) { ?>
			<input type="radio" name="march_type" id="march_type" value="<?php echo $type_val; ?>"
				<?php echo (isset($info['march_type']) && $info['march_type'] == $type_val) ? 'checked' : ''; ?>  />
			<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
		<?php } ?>
	</td>
</tr>
<tr>
	<td><strong>战场展示形式</strong></td>
	<td>
		<?php foreach (M_Weapon::$showType as $type_val => $type_desc) { ?>
			<input type="radio" name="show_type" id="show_type" value="<?php echo $type_val; ?>"
				<?php echo (isset($info['show_type']) && $info['show_type'] == $type_val) ? 'checked' : ''; ?>  />
			<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
		<?php } ?>
	</td>
</tr>
<tr>
	<td><strong>是否特殊武器</strong></td>
	<td>
		<input type="radio" name="is_special" value="1" <?php echo $isChkSpV1; ?> />特殊武器&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="is_special" value="0" <?php echo $isChkSpV0; ?> />非特殊武器
	</td>
</tr>
<tr>
	<td><strong>是否NPC武器</strong></td>
	<td>
		<input type="radio" name="is_npc" value="1" <?php echo $isChkNpcV1; ?> />NPC武器&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="is_npc" value="0" <?php echo $isChkNpcV0; ?> />非NPC武器
	</td>
</tr>
<tr>
	<td><strong>武器默认排序</strong></td>
	<td><input type="text" class="text" name="sort" id="sort"
	           value="<?php echo isset($info['sort']) ? $info['sort'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>生命值</strong></td>
	<td><input type="text" class="text" name="life_value" id="life_value"
	           value="<?php echo isset($info['life_value']) ? $info['life_value'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对地攻击力</strong></td>
	<td><input type="text" class="text" name="att_land" id="att_land"
	           value="<?php echo isset($info['att_land']) ? $info['att_land'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对空攻击力</strong></td>
	<td><input type="text" class="text" name="att_sky" id="att_sky"
	           value="<?php echo isset($info['att_sky']) ? $info['att_sky'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对海攻击力</strong></td>
	<td><input type="text" class="text" name="att_ocean" id="att_ocean"
	           value="<?php echo isset($info['att_ocean']) ? $info['att_ocean'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对地防御力</strong></td>
	<td><input type="text" class="text" name="def_land" id="def_land"
	           value="<?php echo isset($info['def_land']) ? $info['def_land'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对空防御力</strong></td>
	<td><input type="text" class="text" name="def_sky" id="def_sky"
	           value="<?php echo isset($info['def_sky']) ? $info['def_sky'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>对海防御力</strong></td>
	<td><input type="text" class="text" name="def_ocean" id="def_ocean"
	           value="<?php echo isset($info['def_ocean']) ? $info['def_ocean'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>速度</strong></td>
	<td><input type="text" class="text" name="speed" id="speed"
	           value="<?php echo isset($info['speed']) ? $info['speed'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>移动范围</strong></td>
	<td><input type="text" class="text" name="move_range" id="move_range"
	           value="<?php echo isset($info['move_range']) ? $info['move_range'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>移动类型</strong></td>
	<td>
		<?php foreach (M_Weapon::$moveType as $type_val => $type_desc) { ?>
			<input type="radio" name="move_type" id="move_type" value="<?php echo $type_val; ?>"
				<?php echo (isset($info['move_type']) && $info['move_type'] == $type_val) ? 'checked' : ''; ?>  />
			<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
		<?php } ?>
	</td>
</tr>
<tr>
	<td><strong>射程最小值</strong></td>
	<td><input type="text" class="text" name="shot_range_min" id="shot_range_min"
	           value="<?php echo isset($info['shot_range_min']) ? $info['shot_range_min'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>射程最大值</strong></td>
	<td><input type="text" class="text" name="shot_range_max" id="shot_range_max"
	           value="<?php echo isset($info['shot_range_max']) ? $info['shot_range_max'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>射程类型</strong></td>
	<td>
		<input type="radio" name="shot_type" value="1" <?php echo $isChkStV1; ?> />直线型&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="shot_type" value="2" <?php echo $isChkStV2; ?> />弧线型
	</td>
</tr>
<tr>
	<td><strong>视野范围</strong></td>
	<td><input type="text" class="text" name="view_range" id="view_range"
	           value="<?php echo isset($info['view_range']) ? $info['view_range'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>掠夺量</strong></td>
	<td><input type="text" class="text" name="carry" id="carry"
	           value="<?php echo isset($info['carry']) ? $info['carry'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>攻击次数</strong></td>
	<td><input type="text" class="text" name="att_num" id="att_num"
	           value="<?php echo isset($info['att_num']) ? $info['att_num'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>研发金钱消耗</strong></td>
	<td><input type="text" class="text" name="cost_gold" id="cost_gold"
	           value="<?php echo isset($info['cost_gold']) ? $info['cost_gold'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>研发粮食消耗</strong></td>
	<td><input type="text" class="text" name="cost_food" id="cost_food"
	           value="<?php echo isset($info['cost_food']) ? $info['cost_food'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>研发石油消耗</strong></td>
	<td><input type="text" class="text" name="cost_oil" id="cost_oil"
	           value="<?php echo isset($info['cost_oil']) ? $info['cost_oil'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>研发冷却时间(秒)</strong></td>
	<td><input type="text" class="text" name="cost_time" id="cost_time"
	           value="<?php echo isset($info['cost_time']) ? $info['cost_time'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>出征石油消耗</strong></td>
	<td><input type="text" class="text" name="march_cost_oil" id="march_cost_oil"
	           value="<?php echo isset($info['march_cost_oil']) ? $info['march_cost_oil'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>出征食物消耗</strong></td>
	<td><input type="text" class="text" name="march_cost_food" id="march_cost_food"
	           value="<?php echo isset($info['march_cost_food']) ? $info['march_cost_food'] : 0; ?>"/></td>
</tr>
<tr>
	<td><strong>前提科技</strong></td>
	<td>
		<select name='need_tech_id' id='need_tech_id'>
			<option value='0'>--请选择科技--</option>
			<?php foreach ($pageData['techBase'] as $techId => $techInfo) {
				$tmp = isset($arrNeedTech[0]) ? $arrNeedTech[0] : '';
				?>
				<option
					value="<?php echo $techId; ?>" <?php echo (isset($info['id']) && $techId == $tmp) ? 'selected' : ''; ?> >
					<?php echo $techInfo['name']; ?></option>
			<?php } ?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;等级
		<select name='need_tech_level' id='need_tech_level'>
			<option value='0'>--请选择等级--</option>
			<?php for ($i = 1; $i < 51; $i++) {
				$tmp = isset($arrNeedTech[1]) ? $arrNeedTech[1] : '';
				?>
				<option
					value="<?php echo $i; ?>" <?php echo (isset($info['id']) && $i == $tmp) ? 'selected' : ''; ?> ><?php echo $i; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<td><strong>前提建筑</strong></td>
	<td>
		<select name='need_build_id' id='need_build_id'>
			<option value='0'>--请选择建筑--</option>
			<?php
			foreach ($pageData['buildBase'] as $buildId => $buildInfo) {
				?>
				<option
					value="<?php echo $buildId; ?>" <?php echo (isset($info['id']) && !empty($arrNeedBuild[0]) && $buildId == $arrNeedBuild[0]) ? 'selected' : ''; ?> >
					<?php echo $buildInfo['name']; ?></option>
			<?php } ?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;等级
		<select name='need_build_level' id='need_build_level'>
			<option value='0'>--请选择等级--</option>
			<?php for ($i = 1; $i < 51; $i++) { ?>
				<option
					value="<?php echo $i; ?>" <?php echo (isset($info['id']) && !empty($arrNeedBuild[1]) && $i == $arrNeedBuild[1]) ? 'selected' : ''; ?> ><?php echo $i; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" class="button" name="submit" value="<?php echo isset($info['id']) ? '修改' : '新增'; ?>"/></td>
</tr>
</table>
</form>
</div>