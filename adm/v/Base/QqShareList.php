<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>新手指引管理
		<a href="?r=Base/QqShareExport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
		<a href="?r=Base/QqShareImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
		<a href="?r=Base/QqShareCacheUp"
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
			<th width="50px">名称</th>
			<th width="50px">类型</th>
			<th width="50px">描述</th>
			<th width="50px">完成条件</th>
			<th width="150px">奖励ID</th>
			<th width="150px">分享的标题</th>
			<th width="150px">显示的应用图片URL</th>
			<th width="150px">故事摘要</th>
			<th width="150px">分享内容</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['type']; ?></td>
				<td><?php echo $val['desc']; ?></td>
				<td>
					<pre><?php echo $val['cond_pass']; ?></pre>
				</td>
				<td><?php echo $val['award_id'] . "<br>" . B_Utils::awardText($val['award_id']); ?></td>
				<td><?php echo $val['title']; ?></td>
				<td><?php echo $val['img']; ?></td>
				<td><?php echo $val['summary']; ?></td>
				<td><?php echo $val['msg']; ?></td>
			</tr>
		<?php } ?>
	</table>
</div>


