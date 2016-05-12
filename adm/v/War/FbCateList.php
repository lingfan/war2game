<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'装备列表列表' => 'Base/EquipList',
);
?>

<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=War/WarFbCateDel', {id: id}, function (txt) {
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
<iframe name="iframe" style="display: none;"></iframe>
<div class="top-bar">
	<a href="?r=War/FBCleanCache" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">清除缓存</a>
	<a href="?r=War/WarFbCateView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>
	<a href="?r=War/WarFbImport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">关卡对话导入</a>

	<h1>战斗相关</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="#">副本章节列表</a> <span id="msg"
	                                                                             style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th width="140px">章节名称</th>
			<th>描述</th>
			<th width="50px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td style="text-align: left;"><?php echo isset($val['desc'][90]) ? mb_substr($val['desc'], 0, 90, 'utf-8') . '..' : $val['desc']; ?></td>
				<td style="text-align: center;">
					<a href="?r=War/WarFbExport&id=<?php echo $val['id']; ?>">关卡导出</a>
					<a href="?r=War/WarFbCateView&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16" alt="edit"/></a>
					&nbsp;&nbsp;<a href="javascript:del(<?php echo $val['id']; ?>)"><img
							src="styles/adm/images/del-icon.gif" width="16" height="16" alt="del"/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>