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
	/*
	 function sb(){
	 var id = $('#id').val();

	 var nickname = $('#name').val();
	 var face_id = document.getElementById('face_id').value;
	 var gender = $('input:radio[name=gender]:checked').val();
	 var quality = $('input:radio[name=quality]:checked').val();
	 var is_vip_use = $('input:radio[name=is_vip_use]:checked').val();

	 var level = $('#level').val();
	 var attr_lead = $('#attr_lead').val();
	 var attr_command = $('#attr_command').val();
	 var attr_military = $('#attr_military').val();
	 var attr_energy = $('#attr_energy').val();
	 var grow_rate = $('#grow_rate').val();

	 var skill_slot = $('select#skill_slot option:selected').val();
	 var skill_slot_num = $('select#skill_slot_num option:selected').val();
	 var skill_slot_1 = $('select#skill_slot_1 option:selected').val();
	 var skill_slot_2 = $('select#skill_slot_2 option:selected').val();

	 var desc = $('#desc').val();
	 var detail = $('#detail').val();

	 var num = $('#num').val();
	 var start_time = $('#start_time').val();
	 var end_time = $('#end_time').val();
	 var succ_rate = $('#succ_rate').val();
	 var hire_time = $('#hire_time').val();

	 var act = id > 0 ? 'edit' : 'add';
	 $.post('?r=Base/HeroAdd',
	 {
	 id:id,
	 nickname:nickname,
	 gender:gender,
	 quality:quality,
	 is_vip_use:is_vip_use,
	 face_id:face_id,
	 level:level,
	 attr_lead:attr_lead,
	 attr_command:attr_command,
	 attr_military:attr_military,
	 attr_energy:attr_energy,
	 grow_rate:grow_rate,
	 skill_slot_num:skill_slot_num,
	 skill_slot:skill_slot,
	 skill_slot_1:skill_slot_1,
	 skill_slot_2:skill_slot_2,
	 desc:desc,
	 detail:detail,
	 num:num,
	 hire_time:hire_time,
	 start_time:start_time,
	 end_time:end_time,
	 succ_rate:succ_rate,
	 act:act
	 }, function(txt){
	 $('#msg').css('display', '')
	 $('#msg').html(txt.msg);
	 setTimeout("$('#msg').css('display', 'none')",3000);
	 },'json');
	 }
	 */
</script>
<div class="top-bar">
	<h1>英雄管理</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="?r=Base/HeroList">英雄列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>英雄</a> <span id="msg"
	                                                                                        style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
<iframe name="iframe" style="display: none;"></iframe>
<form id="addForm" name="addForm" action="?r=Base/HeroAdd" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<td>军官名称：</td>
	<td>
		<input type="hidden" name="id" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? $pageData['info']['id'] : 0; ?>">
		<input type="hidden" name="act" id="id"
		       value="<?php echo isset($pageData['info']['id']) ? 'edit' : 'add'; ?>">
		<input type="text" name="nickname" id=nickname
		       value="<?php echo isset($pageData['info']['nickname']) ? $pageData['info']['nickname'] : ''; ?>">
	</td>
</tr>
<tr>
	<td>性别：</td>
	<td>
		<?php
		$gender1 = $gender2 = '';
		if (isset($pageData['info']['gender'])) {
			if ($pageData['info']['gender'] == 1) {
				$gender1 = ' checked';
			} else if ($pageData['info']['gender'] == 2) {
				$gender2 = ' checked';
			}
		}
		?>

		男<input id="gender" type="radio" <?php echo $gender1; ?> value="1" name="gender">
		女<input id="gender" type="radio" <?php echo $gender2; ?> value="2" name="gender">

	</td>
</tr>
<tr>
	<td>品质：</td>
	<td>
		<?php
		foreach (T_Hero::$heroQual as $key => $val) {
			$checked = '';
			if (isset($pageData['info']['quality']) && $pageData['info']['quality'] == $key) {
				$checked = ' checked';
			}
			?>
			<?php echo $val; ?><input id="quality" type="radio" <?php echo $checked; ?>
			                          value="<?php echo $key; ?>" name="quality">&nbsp;

		<?php } ?>
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
	<td>头像：</td>
	<td>
		<input type="text" id="face_id" name="face_id"
		       value="<?php echo isset($pageData['info']['face_id']) ? $pageData['info']['face_id'] : ''; ?>">
	</td>
</tr>

<tr>
	<td>属性：</td>
	<td>
		等级：<input type="text" name="level" id="level"
		          value="<?php echo isset($pageData['info']['level']) ? $pageData['info']['level'] : ''; ?>"
		          style="width: 30px;">
		统帅：<input type="text" name="attr_lead" id="attr_lead"
		          value="<?php echo isset($pageData['info']['attr_lead']) ? $pageData['info']['attr_lead'] : ''; ?>"
		          style="width: 30px;">
		指挥：<input type="text" name="attr_command" id="attr_command"
		          value="<?php echo isset($pageData['info']['attr_command']) ? $pageData['info']['attr_command'] : ''; ?>"
		          style="width: 30px;">
		军事：<input type="text" name="attr_military" id="attr_military"
		          value="<?php echo isset($pageData['info']['attr_military']) ? $pageData['info']['attr_military'] : ''; ?>"
		          style="width: 30px;">
		精力：<input type="text" name="attr_energy" id="attr_energy"
		          value="<?php echo isset($pageData['info']['attr_energy']) ? $pageData['info']['attr_energy'] : ''; ?>"
		          style="width: 30px;">
	</td>
</tr>
<tr>
	<td>成长值：</td>
	<td>
		<input type="text" name="grow_rate" id="grow_rate"
		       value="<?php echo isset($pageData['info']['grow_rate']) ? $pageData['info']['grow_rate'] : ''; ?>"
		       style="width: 50px;">(1到2之间)
	</td>
</tr>

<tr>
	<td>天赋技能：</td>
	<td>
		<select name="skill_slot" id="skill_slot">
			<option value="0">无</option>
			<?php foreach ($pageData['skill_list'] as $key => $val) { ?>
				<option
					value="<?php echo $val['id']; ?>"<?php if (isset($pageData['info']['skill_slot']) && $pageData['info']['skill_slot'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>


<tr>
	<td>普通技能：</td>
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
	<td>英雄描述：</td>
	<td>
		<textarea id="desc" rows="3" cols="50"
		          name="desc"><?php echo isset($pageData['info']['desc']) ? $pageData['info']['desc'] : ''; ?></textarea>
	</td>
</tr>
<tr>
	<td>详细介绍：</td>
	<td>
		<textarea id="detail" rows="3" cols="50"
		          name="detail"><?php echo isset($pageData['info']['detail']) ? $pageData['info']['detail'] : ''; ?></textarea>
	</td>
</tr>

<tr>
	<td>招募：</td>
	<td>
		可分配数量：<input type="text" name="num" id="num"
		             value="<?php echo isset($pageData['info']['num']) ? $pageData['info']['num'] : ''; ?>"
		             style="width: 40px;">剩余:<?php echo isset($pageData['info']['id']) ? M_Hero::getBaseTplHeroNum($pageData['info']['id']) : 0; ?>
		分配开始<input type="text" name="start_time" id="start_time"
		           value="<?php echo isset($pageData['info']['start_time']) ? date('Y-m-d H:i:s', $pageData['info']['start_time']) : ''; ?>"
		           type="text" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">
		分配结束<input type="text" name="end_time" id="end_time"
		           value="<?php echo isset($pageData['info']['end_time']) ? date('Y-m-d H:i:s', $pageData['info']['end_time']) : ''; ?>"
		           type="text" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">

	</td>
</tr>

<tr>
	<td>招募成功率：</td>
	<td>
		<input type="text" name="succ_rate" id="succ_rate"
		       value="<?php echo isset($pageData['info']['succ_rate']) ? $pageData['info']['succ_rate'] : ''; ?>"
		       style="width: 40px;">%
		招募时间(秒)：<input type="text" name="hire_time" id="hire_time"
		               value="<?php echo isset($pageData['info']['hire_time']) ? $pageData['info']['hire_time'] : ''; ?>"
		               style="width: 80px;">
	</td>

</tr>

<tr>
	<td>招募条件：</td>
	<td>
		<?php $need = isset($pageData['info']['hire_need']) ? json_decode($pageData['info']['hire_need'], true) : array(); ?>
		<input type="checkbox" name="props" <?php if (isset($need['props'][0]) && $need['props'][0] == 1) {
			echo 'checked="checked"';
		} ?> value="1"> 道具
		<select name="props_id">
			<?php foreach ($pageData['props_list'] as $val) { ?>
				<option
					value="<?php echo $val['id']; ?>" <?php if (isset($need['props']) && $need['props'][0] == 1 && $need['props'][1] == $val['id']) {
					echo 'selected="selected"';
				} ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select> x <input type="text" name="props_num" style="width: 50px;"
		                   value="<?php echo isset($need['props'][2]) ? $need['props'][2] : ''; ?>"> <br>
		<input type="checkbox"
		       name="milpay" <?php if (isset($need['milpay'][0]) && $need['milpay'][0] == 1) {
			echo 'checked="checked"';
		} ?> value="1"> 军饷 x <input type="text" name="milpay_num" style="width: 50px;"
		                            value="<?php echo isset($need['milpay'][1]) ? $need['milpay'][1] : ''; ?>"><br>
		<input type="checkbox"
		       name="coupon" <?php if (isset($need['coupon'][0]) && $need['coupon'][0] == 1) {
			echo 'checked="checked"';
		} ?> value="1"> 礼券 x <input type="text" name="coupon_num" style="width: 50px;"
		                            value="<?php echo isset($need['coupon'][1]) ? $need['coupon'][1] : ''; ?>"><br>
		<input type="checkbox" name="gold" <?php if (isset($need['gold'][0]) && $need['gold'][0] == 1) {
			echo 'checked="checked"';
		} ?> value="1"> 金钱 x <input type="text" name="gold_num" style="width: 50px;"
		                            value="<?php echo isset($need['gold'][1]) ? $need['gold'][1] : ''; ?>"><br>
	</td>

</tr>

<tr>
	<td>&nbsp;</td>
	<td><input id="sub" type="submit" value=" 保 存 "></td>
</tr>
</table>
</form>
</div>
<div id="ceng"
     style="border: 0px solid red; position: absolute;left:150px; top: 200px; display: none; text-align: right; ">
	<?php
	$url = $resUrl . 'imgs/hero/';
	for ($i = 1; $i < 3; $i++) {
		for ($j = 1; $j < 3; $j++) {
			for ($k = 1; $k < 16; $k++) {
				$a = $i == 1 ? 'normal' : 'small';
				?>
				<img src="<?php echo $url . $a . '/' . $i . '_' . $j . '_' . $k . '.png'; ?>" width="64px"
				     height="64px;"
				     onclick="$('#face_id').val('<?php echo $i . '_' . $j . '_' . $k; ?>'),$('#ceng').css('display', 'none');"
				     style="cursor: pointer;"/>
				<?php //if ($k % 10 == 0) { echo "<br>";}?>
			<?php
			}
			echo "<br>";
		}
	}
	?>
	<input type="button" value="取消" onclick="$('#ceng').css('display', 'none');">
</div>