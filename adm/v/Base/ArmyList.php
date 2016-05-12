<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'兵种列表' => 'Base/ArmyList&page=1',
);
?>

<div class="top-bar">
	<h1>兵种数据列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="50px">兵种名</th>
			<th width="70px">描述</th>
			<th width="50px">金钱</th>
			<th width="50px">粮食</th>
			<th width="50px">石油</th>
			<th width="50px">人口</th>
			<th width="60px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['features']; ?></td>
				<td><?php echo $val['cost_gold']; ?></td>
				<td><?php echo $val['cost_food']; ?></td>
				<td><?php echo $val['cost_oil']; ?></td>
				<td><?php echo $val['cost_people']; ?></td>
				<td>
					<a href="?r=Base/ArmyAdd&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                            width="16" height="16" title="编辑"
					                                                            alt="编辑"></a>&nbsp;
				</td>
			</tr>
		<?php } ?>
	</table>
</div>