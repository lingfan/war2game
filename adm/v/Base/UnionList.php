<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'游戏数据' => 'Base/Index',
	'联盟列表' => 'Base/UnionList',
);
?>

<div class="top-bar">
	<h1>游戏联盟管理</h1>

	<div class="breadcrumbs"><a href="#">游戏数据</a> / <a href="#">联盟列表</a> <span id="msg"
	                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none"></iframe>
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width: 30px;">ID</th>
			<th>名称</th>
			<th>等级</th>
			<th>排名</th>
			<th>军团长</th>
			<th>总人数</th>
			<th>总威望</th>
			<th>修正操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['level']; ?></td>
				<td><?php echo $val['rank']; ?></td>
				<td><?php echo $val['boss']; ?></td>
				<td><?php echo $val['total_person']; ?></td>
				<td><?php echo $val['total_renown']; ?></td>
				<td>
					<a href="?r=Base/UnionTotal&id=<?php echo $val['id']; ?>" target="iframe">总人数/威望</a>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>