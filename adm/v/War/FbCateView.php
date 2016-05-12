<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>
<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=War/WarFbDel', {id: id}, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.msg);
				if (txt.flag == 1) {
					$('#list #' + id).remove();
				}
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json');
		}
	}

</script>
<div class="top-bar">

	<h1>战斗相关</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/WarFbCateList">副本章节列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>副本章节</a> <span id="msg"
	                                                                                          style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=War/WarFbCateEdit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>章节名称：<input type="hidden" id="id" name="id"
				                value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>"></td>
				<td>
					<input type="text" name="name"
					       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
				</td>
			</tr>
			<tr>
				<td>章节描述：</td>
				<td>
					<textarea name="desc"
					          style="width: 500px; height: 60px;"><?php echo isset($pageData['info']['desc']) ? $pageData['info']['desc'] : ''; ?></textarea>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
	<?php if (isset($pageData['info']['id'])) { ?>
		<div class="table" style="margin-top: 20px;">
			<span style="color: blue"><?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?> : 战役列表</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="?r=War/WarFbView&cate=<?php echo $pageData['info']['id']; ?>">添加战役</a>

			<form action="?r=War/WarFbListEdit" method="post" target="iframe">
				<table id="list" class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th style="width: 60px;">战役编号</th>
						<th>章节</th>
						<th>战役名称</th>
						<th>等级</th>
						<th>关卡数量</th>
						<th>操作</th>
					</tr>
					<?php foreach ($pageData['list'] as $key => $val) { ?>
						<tr id="<?php echo $val['id']; ?>">
							<td style="text-align: center;">
								<input type="text" name="campaign_no[]" value="<?php echo $val['campaign_no']; ?>"
								       style="width: 50px;text-align: center;">
								<input type="hidden" name="ids[]" value="<?php echo $val['id']; ?>">
							</td>
							<td><?php echo $pageData['cates'][$val['chapter_no']]['name']; ?></td>
							<td><?php echo $val['name']; ?></td>
							<td><?php echo $val['level']; ?></td>
							<td><?php echo count(json_decode($val['checkpoint_data'], true)); ?></td>
							<td style="text-align: center;">
								<a href="?r=War/WarFbMapView&chapter=<?php echo $val['chapter_no']; ?>&campaign=<?php echo $val['campaign_no']; ?>">地图</a>
								<a href="?r=War/WarFbView&id=<?php echo $val['id']; ?>"><img
										src="styles/adm/images/edit-icon.gif" width="16" height="16" alt="edit"/></a>
								<a href="javascript:del(<?php echo $val['id']; ?>)"><img
										src="styles/adm/images/del-icon.gif" width="16" height="16" alt="del"/></a>
							</td>
						</tr>
					<?php } ?>
				</table>
				<input type="submit" value="保存">
			</form>
		</div>
	<?php } ?>
</div>
