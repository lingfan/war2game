<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>

<script type="text/javascript">

	function sb() {
		var id = $('#id').val();
		var name = $('#name').val();
		var pos = document.getElementById('pos').value;
		var type = document.getElementById('type').value;
		var quality = document.getElementById('quality').value;
		var need_level = $('#need_level').val();
		var base_lead = $('#base_lead').val();
		var base_command = $('#base_command').val();
		var base_military = $('#base_military').val();
		var is_locked = document.getElementById('is_locked').value;
		var is_vip_use = $('#is_vip_use').val();
		var suit_id = document.getElementById('suit_id').value;
		var desc_1 = document.getElementById('desc_1').value;
		var desc_2 = document.getElementById('desc_2').value;
		var gold = $('#gold').val();
		var flag = $('#flag').val();

		var act = id < 1 ? 'add' : 'edit';
		$.post('?r=Base/EquipEdit', {id: id, name: name, pos: pos, type: type, quality: quality, need_level: need_level, base_lead: base_lead, base_command: base_command, base_military: base_military, is_locked: is_locked, is_vip_use: is_vip_use, desc_1: desc_1, desc_2: desc_2, gold: gold, suit_id: suit_id, flag: flag, act: act}, function (txt) {
			$('#msg').css('display', '');
			$('#msg').html(txt.msg);
			setTimeout("$('#msg').css('display', 'none')", 3000);
		}, 'json');
	}

</script>
<div class="top-bar">
	<h1>装备管理</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="?r=Base/EquipList">装备列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>装备</a> <span id="msg"
	                                                                                        style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<form id="addForm" name="addForm">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>装备名称<input type="hidden" id="id" name="id"
				               value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>"></td>
				<td>
					<input type="text" name="name" id="name"
					       value="<?php if (isset($pageData['info']['name'])) echo $pageData['info']['name']; ?>">
				</td>
			</tr>

			<tr>
				<td>装备位置</td>
				<td>
					<select name="pos" id="pos">
						<option value="">无</option>
						<?php for ($i = 1; $i < 7; $i++) { ?>
							<option
								value="<?php echo $i; ?>" <?php if (isset($pageData['info']['pos']) && $pageData['info']['pos'] == $i) {
								echo 'selected="selected"';
							} ?>><?php echo T_Word::$EQUIP_QUAL[$i]; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>

			<tr>
				<td>装备图标</td>
				<td>
					<div style="float: left; margin-left: 5px;">
						<img id="faceImg" alt="" src="<?php if (isset($pageData['info']['face_id'])) {
							echo $resUrl . 'imgs/equip/' . $pageData['info']['face_id'] . '.jpg';
						} else {
							echo $resUrl . 'imgs/equip/1.jpg';
						} ?>"/>
					</div>
				</td>
			</tr>

			<tr>
				<td>装备类型</td>
				<td>
					<select name="type" id="type">
						<option
							value="2" <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] == 2) {
							echo 'selected="selected"';
						} ?>>活动装备
						</option>
						<option
							value="1" <?php if (isset($pageData['info']['type']) && $pageData['info']['type'] == 1) {
							echo 'selected="selected"';
						} ?>>系统装备
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>装备品质</td>
				<td>
					<select name="quality" id="quality">
						<option
							value="1" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 1) {
							echo 'selected="selected"';
						} ?>>白
						</option>
						<option
							value="2" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 2) {
							echo 'selected="selected"';
						} ?>>绿
						</option>
						<option
							value="3" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 3) {
							echo 'selected="selected"';
						} ?>>蓝
						</option>
						<option
							value="4" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 4) {
							echo 'selected="selected"';
						} ?>>紫
						</option>
						<option
							value="5" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 5) {
							echo 'selected="selected"';
						} ?>>红
						</option>
						<option
							value="6" <?php if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == 6) {
							echo 'selected="selected"';
						} ?>>金
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>需要等级</td>
				<td>
					<input type="text" name="need_level" id="need_level"
					       value="<?php if (isset($pageData['info']['need_level'])) echo $pageData['info']['need_level']; ?>"
					       style="width: 50px;">
				</td>
			</tr>
			<tr>
				<td>装备属性</td>
				<td>
					防御：<input type="text" name="base_lead" id="base_lead"
					          value="<?php if (isset($pageData['info']['base_lead'])) echo $pageData['info']['base_lead']; ?>"
					          style="width: 30px;">
					攻击：<input type="text" name="base_command" id="base_command"
					          value="<?php if (isset($pageData['info']['base_command'])) echo $pageData['info']['base_command']; ?>"
					          style="width: 30px;">
					生命：<input type="text" name="base_military" id="base_military"
					          value="<?php if (isset($pageData['info']['base_military'])) echo $pageData['info']['base_military']; ?>"
					          style="width: 30px;">
				</td>
			</tr>
			<tr>
				<td>是否绑定</td>
				<td>
					<select name="is_locked" id="is_locked">
						<option
							value="1" <?php if (isset($pageData['info']['is_locked']) && $pageData['info']['is_locked'] == 1) {
							echo 'selected="selected"';
						} ?>>是
						</option>
						<option
							value="0" <?php if (isset($pageData['info']['is_locked']) && $pageData['info']['is_locked'] == 0) {
							echo 'selected="selected"';
						} ?>>否
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>可否用于VIP</td>
				<td>
					<input id="is_vip_use" type="radio" name="is_vip_use"
					       value="1" <?php echo (1 == intval($pageData['info']['is_vip_use'])) ? 'checked' : ''; ?> />可用于VIP&nbsp;&nbsp;&nbsp;&nbsp;
					<input id="is_vip_use" type="radio" name="is_vip_use"
					       value="0" <?php echo (0 == intval($pageData['info']['is_vip_use'])) ? 'checked' : ''; ?> />不可用于VIP
				</td>
			</tr>
			<tr>
				<td>属于哪一个套装ID</td>
				<td>
					<input type="text" name="suit_id" id="suit_id"
					       value="<?php if (isset($pageData['info']['suit_id'])) echo $pageData['info']['suit_id']; ?>">
				</td>
			</tr>
			<tr>
				<td>装备描述1</td>
				<td>
					<textarea name="desc_1" id="desc_1"
					          style="width: 300px; height: 50px;"><?php if (isset($pageData['info']['desc_1'])) echo $pageData['info']['desc_1']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>装备描述2</td>
				<td>
					<textarea name="desc_2" id="desc_2"
					          style="width: 300px; height: 50px;"><?php if (isset($pageData['info']['desc_2'])) echo $pageData['info']['desc_2']; ?></textarea>
				</td>
			</tr>

			<tr>
				<td>出售价格</td>
				<td>
					<input type="text" name="gold" id="gold"
					       value="<?php if (isset($pageData['info']['gold'])) echo $pageData['info']['gold']; ?>">
				</td>
			</tr>
			<tr>
				<td>装备是否可以合成,升级,强化(1合成2升级4强化)</td>
				<td>
					<input type="text" name="flag" id="flag"
					       value="<?php if (isset($pageData['info']['flag'])) echo $pageData['info']['flag']; ?>">
				</td>

			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="button" value=" 保 存 " onclick="sb()"></td>
			</tr>
		</table>
	</form>
</div>


