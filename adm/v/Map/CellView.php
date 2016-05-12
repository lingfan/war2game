<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>

<div class="top-bar">

	<h1>地图编辑器</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="?r=Map/WarMapCellList">地图标记物列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>地图标记物</a> <span id="msg"
	                                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=Map/WarMapCellEdit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>标记物名称：<input type="hidden" id="id" name="id"
				                 value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>">
				</td>
				<td>
					<input type="text" name="name" id="name"
					       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>图标：</td>
				<td>
					<?php $faceArr = array('1' => '图标1', '2' => '图标2'); ?>
					<select name="face_id">
						<?php foreach ($faceArr as $key => $val) { ?>
							<option
								value="<?php echo $key; ?>" <?php if (isset($pageData['info']['face_id']) && $pageData['info']['face_id'] == $key) {
								echo 'selected="selected"';
							} ?>><?php echo $val; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>通行：</td>
				<td>
					不可通过：
					<?php foreach (M_Weapon::$moveType as $key => $val) { ?>
						<input type="checkbox" name="ban[]"
						       value="<?php echo M_MapBattle::$warMapCellBanCrossType[$key]; ?>" <?php if (isset($pageData['info']['ban']) && ($pageData['info']['ban'] & M_MapBattle::$warMapCellBanCrossType[$key]) > 0) {
							echo 'checked="checked"';
						} ?>> <?php echo $val; ?>
					<?php } ?><br>
					不可停留：
					<?php foreach (M_Weapon::$moveType as $key => $val) { ?>
						<input type="checkbox" name="ban[]"
						       value="<?php echo M_MapBattle::$warMapCellBanHoldType[$key]; ?>" <?php if (isset($pageData['info']['ban']) && ($pageData['info']['ban'] & M_MapBattle::$warMapCellBanHoldType[$key]) > 0) {
							echo 'checked="checked"';
						} ?>><?php echo $val; ?>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>标记物类型：</td>
				<td>

					<?php foreach (M_MapBattle::$warMapCellAttr as $key => $val) { ?>
						<input type="radio" name="type"
						       value="<?php echo $key; ?>" <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] == $key) {
							echo 'checked="checked"';
						} ?>><?php echo $val; ?> &nbsp;&nbsp;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>属性</td>
				<td>

					<table border="0" style="font-size: 12px;">
						<tr>
							<td>生命值：</td>
							<td>
								<input type="text" name="life_value"
								       value="<?php if (isset($pageData['info']['life_value'])) echo $pageData['info']['life_value']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>对地攻击力：</td>
							<td>
								<input type="text" name="att_land"
								       value="<?php if (isset($pageData['info']['att_land'])) echo $pageData['info']['att_land']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>对空攻击力：</td>
							<td>
								<input type="text" name="att_sky"
								       value="<?php if (isset($pageData['info']['att_sky'])) echo $pageData['info']['att_sky']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>对海攻击力：</td>
							<td>
								<input type="text" name="att_ocean"
								       value="<?php if (isset($pageData['info']['att_ocean'])) echo $pageData['info']['att_ocean']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>对地防御力：</td>
							<td>
								<input type="text" name="def_land"
								       value="<?php if (isset($pageData['info']['def_land'])) echo $pageData['info']['def_land']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>对空防御力：</td>
							<td>
								<input type="text" name="def_sky"
								       value="<?php if (isset($pageData['info']['def_sky'])) echo $pageData['info']['def_sky']; ?>"
								       style="width: 50px;">
							</td>
						</tr>

						<tr>
							<td>对海防御力：</td>
							<td>
								<input type="text" name="def_ocean"
								       value="<?php if (isset($pageData['info']['def_ocean'])) echo $pageData['info']['def_ocean']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>射击范围：</td>
							<td>
								<input type="text" name="shot_range"
								       value="<?php if (isset($pageData['info']['shot_range'])) echo $pageData['info']['shot_range']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>视野范围：</td>
							<td>
								<input type="text" name="view_range"
								       value="<?php if (isset($pageData['info']['view_range'])) echo $pageData['info']['view_range']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
						<tr>
							<td>移动范围：</td>
							<td>
								<input type="text" name="move_range"
								       value="<?php if (isset($pageData['info']['move_range'])) echo $pageData['info']['move_range']; ?>"
								       style="width: 50px;">
							</td>
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td>排序</td>
				<td>
					<input type="text" name="sort"
					       value="<?php echo isset($pageData['info']['sort']) ? $pageData['info']['sort'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
</div>
