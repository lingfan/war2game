<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'科技基础列表' => 'Base/TechBaseList&page=1',
);
$info = $pageData['info'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改基础科技'] = 'Base/TechBaseAdd&id=' . $info['id'];
} else {
	$urlArr['新增基础科技'] = 'Base/TechBaseAdd';
}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改科技' : '新增科技'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('name').value == '') {
			window.alert('请填写科技名称!');
			flag = false;
		}
		else if (document.getElementById('features').value == '') {
			window.alert('请填写科技介绍!');
			flag = false;
		}
		else if (document.getElementById('type').value == '') {
			window.alert('请选择科技类型!');
			flag = false;
		}
		else if (document.getElementById('max_level').value == '' || isNaN(document.getElementById('max_level').value)) {
			window.alert('请输入正确的最大等级数!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>

<div class="table">
	<form action="?r=Base/DoTechBaseAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>基础科技</th>
			</tr>
			<tr>
				<td width="100px"><strong>科技名称</strong></td>
				<td width="400px">
					<input type='hidden' name='id' id='id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="text" class="text" name="name" id="name"
					       value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>科技介绍</strong></td>
				<td>
					<textarea name="features" id='features' cols='50'
					          rows='3'><?php echo isset($info['features']) ? $info['features'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>科技类型</strong></td>
				<td>
					<?php foreach (M_Tech::$type as $type_val => $type_desc) { ?>
						<input type="radio" name="type" id="type" value="<?php echo $type_val; ?>"
							<?php echo (isset($info['type']) && $info['type'] == $type_val) ? 'checked' : ''; ?>  />
						<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><strong>最大等级</strong></td>
				<td><input type="text" class="text" name="max_level" id="max_level"
				           value="<?php echo isset($info['max_level']) ? $info['max_level'] : 50; ?>"/></td>
			</tr>
			<tr>
				<td><strong>描述1(备用)</strong></td>
				<td>
					<textarea name="desc_1" id='desc_1' cols='50'
					          rows='3'><?php echo isset($info['desc_1']) ? $info['desc_1'] : ''; ?></textarea>
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