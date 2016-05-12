<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>

<script type="text/javascript">
	var opstr = '';

	<?php
	foreach ($pageData['npcs'] as $key => $val) {?>
	opstr += '<option value=';
	opstr += <?php echo $val['id'];?>;
	opstr += '>';
	opstr += "<?php echo $val['nickname'];?>";
	opstr += '<\/option>';
	<?php
	}?>


	$(document).ready(function () {
		//$.cleditor.defaultOptions.width = 500;
		//$.cleditor.defaultOptions.height = 100;
		//$.cleditor.defaultOptions.controls = "bold italic underline removeformat color | undo redo | link unlink  | source";
		//$("textarea").cleditor()[0].focus();
	});

	function addRow() {
		$('#num').val(parseInt($('#num').val()) + 1);
		var newRow = $('#row').clone();
		newRow.appendTo('#tb');
	}

	function del(aa) {
		var num = parseInt($('#num').val());
		if (num == 1) {
			alert('不能再删了...');
			return false;
		}
		else {
			$('#num').val(num - 1);
			var tb = aa.parentNode.parentNode.parentNode;
			var row = aa.parentNode.parentNode;
			tb.removeChild(row);
		}
	}

	function getNpcs(id) {
		var obj = document.getElementById(id);
		var val = obj.value;
		obj.innerHTML = opstr;
		for (var i = 0; i < obj.options.length; i++) {
			if (val == obj.options[i].value) {
				obj.options[i].selected = 'selected';
			}
		}
	}

</script>
<div class="top-bar">

	<h1>战斗相关</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/WarFbCateList">副本章节列表</a> / <a
			href="?r=War/WarFbCateView&id=<?php if ($pageData['cate'] > 0) {
				echo $pageData['cate'];
			} elseif (isset($pageData['info']['chapter_no'])) {
				echo $pageData['info']['chapter_no'];
			} ?>">修改副本章节</a> / <a href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>副本战役</a> <span
			id="msg"
			style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=War/WarFbEdit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>战役名称：<input type="hidden" id="id" name="id"
				                value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>"></td>
				<td>
					<input type="text" name="name" id="name"
					       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
					编号：<input type="text" name="campaign_no" id="campaign_no"
					          value="<?php echo isset($pageData['info']['campaign_no']) ? $pageData['info']['campaign_no'] : ''; ?>"
					          style="width: 30px;">
				</td>
			</tr>

			<tr>
				<td>所属章节：</td>
				<td>
					<select name="type">
						<?php foreach ($pageData['cates'] as $val) { ?>
							<option
								value="<?php echo $val['id']; ?>" <?php if (isset($pageData['info']['chapter_no']) && $pageData['info']['chapter_no'] == $val['id']) echo ' selected="selected"'; ?>><?php echo $val['name']; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>等级：</td>
				<td>
					<input type="text" name="level"
					       value="<?php echo isset($pageData['info']['level']) ? $pageData['info']['level'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>关卡设置：</td>
				<td>
					<table border="0" style="font-size: 12px; width: 100%">
						<tr>
							<td colspan="6">
								关卡数量：<input type="text" id="num" name="checkpoint_num"
								            value="<?php echo isset($pageData['info']['checkpoint_data']) ? count(json_decode($pageData['info']['checkpoint_data'], true)) : '1'; ?>"
								            readonly="readonly" style="width: 20px;">
								<a href="javascript:void(0);" onclick="addRow();">添加</a>
							</td>
						</tr>
						<tbody id="tb">
						<?php
						$checkpoint_data = isset($pageData['info']['checkpoint_data']) ? json_decode($pageData['info']['checkpoint_data'], true) : array();
						if ($checkpoint_data) {
							foreach ($checkpoint_data as $nkey => $value) {
								?>
								<tr id="row">
									<td>关卡名：
										<input type="text" name="gname[]" value="<?php echo $value[0]; ?>"
										       style="width: 80px;">

										地形：
										<select name="dixing[]">
											<?php foreach (T_App::$terrain as $key => $val) { ?>
												<option
													value="<?php echo $key; ?>" <?php if ($key == $value[1]) echo 'selected="selected"'; ?>><?php echo $val; ?></option>
											<?php } ?>
										</select>


										天气：
										<select name="tianqi[]">
											<?php foreach (T_App::$weather as $key => $val) { ?>
												<option
													value="<?php echo $key; ?>" <?php if ($key == $value[2]) echo 'selected="selected"'; ?>><?php echo $val; ?></option>
											<?php } ?>
										</select>


										地图：
										<select name="ditu[]">
											<?php foreach ($pageData['maps'] as $key => $val) { ?>
												<option
													value="<?php echo $val['id']; ?>" <?php if ($val['id'] == $value[3]) echo 'selected="selected"'; ?>><?php echo $val['name']; ?></option>
											<?php } ?>
										</select>
										<br>
										NPC选择：
										<select id="npc_<?php echo $nkey ?>" name="npc[]">
											<?php foreach ($pageData['npcs'] as $key => $val) { ?>
												<?php
												if ($val['id'] == $value[4]) {
													echo '<option value="' . $val['id'] . '">' . $val['nickname'] . '</option>';
													break;
												}
												?>
											<?php } ?>
										</select>
										<input type="button" onclick="getNpcs('npc_<?php echo $nkey ?>');" value="编辑">
										动画：
										<input type="text" name="donghua[]"
										       value="<?php echo isset($value[5]) ? $value[5] : ''; ?>">
										<br>
										场景对话：<br>
										<textarea name="duihua[]"
										          style="width: 540px;height: 80px;"><?php echo isset($value[6]) ? implode("\n", $value[6]) : ''; ?></textarea>
										<br>
										描述：<br>
										<textarea name="gq_desc[]"
										          style="width: 540px;height: 80px;"><?php echo isset($value[7]) ? $value[7] : ''; ?></textarea>
									</td>
									<td><a href="javascript:void(0);" onclick="del(this);">删除</a></td>
								</tr>

							<?php
							}
						} else {
							?>

							<tr id="row">
								<td>关卡名：
									<input type="text" name="gname[]" style="width: 100px;">

									地形：
									<select name="dixing[]">
										<?php foreach (T_App::$terrain as $key => $val) { ?>
											<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
										<?php } ?>
									</select>
									天气：
									<select name="tianqi[]">
										<?php foreach (T_App::$weather as $key => $val) { ?>
											<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
										<?php } ?>
									</select>
									地图：
									<select name="ditu[]">
										<?php foreach ($pageData['maps'] as $key => $val) { ?>
											<option
												value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
										<?php } ?>
									</select>
									<br>
									NPC选择：
									<select name="npc[]">
										<?php foreach ($pageData['npcs'] as $key => $val) { ?>
											<option
												value="<?php echo $val['id']; ?>"><?php echo $val['nickname']; ?></option>
										<?php } ?>
									</select>
									动画：
									<input type="text" name="donghua[]" value="">
									<br>
									场景对话：<br>
									<textarea name="duihua[]" style="width: 540px;height: 80px;"></textarea>
									<br>
									描述：<br>
									<textarea name="gq_desc[]" style="width: 540px;height: 80px;"></textarea>
								</td>
								<td><a href="javascript:void(0);" onclick="del(this);">删除</a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr>
				<td>副本描述：</td>
				<td>
					<textarea name="desc"
					          style="width: 500px; height: 80px;"><?php echo isset($pageData['info']['desc']) ? $pageData['info']['desc'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>通关奖励：</td>
				<td>
					<textarea name="award"
					          style="width: 500px; height: 80px;"><?php echo isset($pageData['info']['award']) ? $pageData['info']['award'] : ''; ?></textarea>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
</div>
