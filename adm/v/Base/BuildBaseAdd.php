<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'建筑基础列表' => 'Base/BuildBaseList&page=1',
);
$info = $pageData['info'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改基础建筑'] = 'Base/BuildBaseAdd&id=' . $info['id'];

	$isChkMvV1 = (1 == $info['is_moved']) ? 'checked' : '';
	$isChkMvV0 = (0 == $info['is_moved']) ? 'checked' : '';
	$isChkMuV1 = (1 == $info['is_multi']) ? 'checked' : '';
	$isChkMuV0 = (0 == $info['is_multi']) ? 'checked' : '';
	$isChkBeV1 = (1 == $info['is_beautify']) ? 'checked' : '';
	$isChkBeV0 = (0 == $info['is_beautify']) ? 'checked' : '';
} else {
	$urlArr['新增基础建筑'] = 'Base/BuildBaseAdd';

	$isChkMvV1 = 'checked'; //可移动初始确认值
	$isChkMvV0 = ''; //不可移动初始确认值
	$isChkMuV1 = ''; //可多建初始默认值
	$isChkMuV0 = 'checked'; //不可多建初始默认值
	$isChkBeV1 = ''; //是装饰初始默认值
	$isChkBeV0 = 'checked'; //不是装饰初始默认值
}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改建筑' : '新增建筑'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('name').value == '') {
			window.alert('请填写建筑名称!');
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
	<form action="?r=Base/DoBuildBaseAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>基础建筑</th>
			</tr>
			<tr>
				<td width="100px"><strong>建筑名称</strong></td>
				<td width="400px">
					<input type='hidden' name='id' id='id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="text" class="text" name="name" id="name"
					       value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>建筑介绍</strong></td>
				<td>
					<textarea name="features" id='features' cols='50'
					          rows='3'><?php echo isset($info['features']) ? $info['features'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>是否可移动</strong></td>
				<td>
					<input type="radio" name="is_moved" value="1" <?php echo $isChkMvV1; ?> />可移动&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_moved" value="0" <?php echo $isChkMvV0; ?> />不可移动
				</td>
			</tr>
			<tr>
				<td><strong>是否可多建</strong></td>
				<td>
					<input type="radio" name="is_multi" value="1" <?php echo $isChkMuV1; ?> />可多建&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_multi" value="0" <?php echo $isChkMuV0; ?> />不可多建
				</td>
			</tr>
			<tr>
				<td><strong>是否装饰建筑</strong></td>
				<td>
					<input type="radio" name="is_beautify" value="1" <?php echo $isChkBeV1; ?> />是装饰&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_beautify" value="0" <?php echo $isChkBeV0; ?> />不是装饰
				</td>
			</tr>
			<tr>
				<td><strong>最大等级</strong></td>
				<td><input type="text" class="text" name="max_level" id="max_level"
				           value="<?php echo isset($info['max_level']) ? $info['max_level'] : 50; ?>"/></td>
			</tr>
			<tr>
				<td><strong>默认排序</strong></td>
				<td><input type="text" class="text" name="sort" id="sort"
				           value="<?php echo isset($info['sort']) ? $info['sort'] : 1; ?>"/></td>
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