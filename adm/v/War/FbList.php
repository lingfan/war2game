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
	<a href="?r=War/WarFbCateList"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">副本章节</a>
	<a href="?r=War/WarFbView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>战斗相关</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="#">副本战役列表</a> <span id="msg"
	                                                                             style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>章节</th>
			<th>战役名称</th>
			<th>等级</th>
			<th>关卡数量</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $pageData['cates'][$val['chapter_no']]['name']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['level']; ?></td>
				<td><?php echo count(json_decode($val['checkpoint_data'], true)); ?></td>
				<td style="text-align: center;">
					<a href="?r=War/WarFbView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                             width="16" height="16" alt="edit"/></a>
					<a href="javascript:del(<?php echo $val['id']; ?>)"><img src="styles/adm/images/del-icon.gif"
					                                                         width="16" height="16" alt="del"/></a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<div class="select">
		<strong>
			<?php
			foreach ($pageData['page']['range'] as $val) {
				if ($pageData['page']['curPage'] == $val) {
					echo "&nbsp;{$val}&nbsp;";
				} else {
					$parmStr = '';
					if (!empty($pageData['parms'])) {
						foreach ($pageData['parms'] as $k => $v) {
							$parmStr .= '&' . $k . '=' . $v;
						}
					}

					echo "&nbsp;<a href='?r=War/WarFbList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>