<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>新手指引管理
		<a href="?r=Base/QuestExport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
		<a href="?r=Base/QuestImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
		<a href="?r=Base/QuestCacheUp"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		   target="iframe">更新缓存</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="30px">前置ID</th>
			<th width="50px">名称</th>
			<th width="50px">类型</th>
			<th width="50px">描述</th>
			<th width="50px">完成条件</th>
			<!-- <th width="50px">前提条件</th> -->
			<th width="10px">等级</th>
			<th width="50px">操作事件</th>
			<th width="150px">奖励ID</th>


		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['prev_id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['type'] == 1 ? '主线' : '支线'; ?></td>
				<td title="<?php echo $val['desc']; ?>"><?php echo $val['guide']; ?></td>
				<td>
					<pre><?php echo $val['cond_pass']; ?></pre>
				</td>
				<!-- <td><?php echo $val['cond_req'];?></td> -->
				<td><?php echo $val['level']; ?></td>
				<td><?php echo $val['event']; ?></td>
				<td><?php echo $val['award_id'] . "<br>" . B_Utils::awardText($val['award_id']); ?></td>
			</tr>
		<?php } ?>
	</table>
</div>


