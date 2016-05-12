<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'任务列表' => 'Base/TaskList&page=1',
);

$propsMaxNum = 20; //奖励包含道具最大数量
$equiMaxNum = 20; //奖励包含装备最大数量
$heroMaxNum = 5; //奖励包含军官最大数量

$packsPropsCount = 0; //道具已有项数
$packsEquiCount = 0; //装备已有项数
$packsHeroCount = 0; //军官已有项数

$info = $pageData['info'];
$props = $pageData['props'];
$equipList = $pageData['equipList'];
$baseHero = $pageData['baseHero'];

if (!empty($info) && is_array($info)) {
	$urlArr['修改任务'] = 'Base/TaskAdd&id=' . $info['id'];

	$packsInfo = json_decode($info['award'], true);
	$packsRes = isset($packsInfo['res']) ? $packsInfo['res'] : array(); //资源
	$packsProps = isset($packsInfo['props']) ? $packsInfo['props'] : array(); //道具
	$packsEquip = isset($packsInfo['equip']) ? $packsInfo['equip'] : array(); //装备
	$packsHero = isset($packsInfo['hero']) ? $packsInfo['hero'] : array(); //军官
	$packsMoney = isset($packsInfo['money']) ? $packsInfo['money'] : array(); //军饷点券
	$packsItem = isset($packsInfo['item']) ? $packsInfo['item'] : array(); //项目

	$packsPropsCount = count($packsProps);
	$packsEquiCount = count($packsEquip);
	$packsHeroCount = count($packsHero);

} else {
	$urlArr['新增任务'] = 'Base/TaskAdd';

}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改任务' : '新增任务'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('title').value == '') {
			window.alert('请填写任务名称!');
			flag = false;
		}
		else if (document.getElementById('features').value == '') {
			window.alert('请填写任务描述!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>

<div class="table">
	<form action="?r=Base/DoTaskAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>任务</th>
			</tr>
			<tr>
				<td width="100px"><strong>任务名称</strong></td>
				<td width="400px">
					<input type='hidden' name='id' id='id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="text" class="text" name="title" id="title"
					       value="<?php echo isset($info['title']) ? $info['title'] : ''; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>任务类型</strong></td>
				<td>
					<?php foreach (M_Task::$type as $type_val => $type_desc) { ?>
						<input type="radio" name="type" id="type" value="<?php echo $type_val; ?>"
							<?php echo (isset($info['type']) && $info['type'] == $type_val) ? 'checked' : ''; ?>  />
						<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><strong>目标说明描述</strong></td>
				<td>
					<textarea name="desc_aim" id='desc_aim' cols='50'
					          rows='3'><?php echo isset($info['desc_aim']) ? $info['desc_aim'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>任务介绍(目标)</strong></td>
				<td>
					<textarea name="desc_intro" id='desc_intro' cols='50'
					          rows='3'><?php echo isset($info['desc_intro']) ? $info['desc_intro'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>任务操作描述</strong></td>
				<td>
					<textarea name="desc_guide" id='desc_guide' cols='50'
					          rows='3'><?php echo isset($info['desc_guide']) ? $info['desc_guide'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>任务完成描述</strong></td>
				<td>
					<textarea name="desc_finish" id='desc_finish' cols='50'
					          rows='3'><?php echo isset($info['desc_finish']) ? $info['desc_finish'] : ''; ?></textarea>
				</td>
			</tr>

			<tr>
				<td><strong>任务奖励ID</strong></td>
				<td>
					<input type="text" class="text" name="award_id" id="award_id"
					       value="<?php echo isset($info['award_id']) ? $info['award_id'] : 0; ?>"/>

					<?php
					echo B_Utils::awardText($info['award_id']);
					?>
				</td>
			</tr>

			<tr>
				<td><strong>排序</strong></td>
				<td>
					<input type="text" class="text" name="sort" id="sort"
					       value="<?php echo isset($info['sort']) ? $info['sort'] : 0; ?>"/>
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