<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'技能列表' => 'Base/SkillList',
);
?>

<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=Base/SkillDel', {id: id}, function (txt) {
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
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=Base/SkillCacheUp" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">更新缓存</a>
	<a href="?r=Base/SkillImport" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/SkillView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>技能管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="#">技能列表</a> <span id="msg"
	                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
	<?php
	echo '<a href="?r=Base/SkillExport&p=1">导出全部</a>&nbsp;';
	?>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="20">ID</th>
			<th width="60">名称</th>
			<th width="30">类型</th>
			<th width="30">重复</th>
			<th width="20">Lv</th>
			<th>描述</th>
			<th width="30">同类型不同等级</th>
			<th width="30">排序</th>
			<th width="50">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) {
			?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['type'] == 1 ? '普通' : '特殊'; ?></td>
				<td><?php echo $val['is_repeat'] == 1 ? '是' : '-'; ?></td>
				<td><?php echo $val['level']; ?></td>
				<td style="text-align:left"><?php echo $val['desc']; ?></td>
				<td><?php echo $val['level_type']; ?></td>
				<td><?php echo $val['sort']; ?></td>

				<td>
					<a href="?r=Base/SkillView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                              width="16" height="16"
					                                                              alt="edit"/></a>
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

					echo "&nbsp;<a href='?r=Base/SkillList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>