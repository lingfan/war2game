<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统公告列表' => '',
);
$baselist = $pageData['baselist'];
?>
<script>
	function delMsg(id) {
		$.post('?r=System/NoticeDel', {id: id}, function (txt) {
			$('#msg').css('display', '')
			$('#msg').html(txt.msg);
			if (txt.flag == 1) {
				$('#list #' + id).remove();
			}
			setTimeout("$('#msg').css('display', 'none')", 3000);
		}, 'json');
	}

</script>
<div class="top-bar">
	<a href="?r=System/ViewEdit" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>公告管理</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="#">公告列表</a> <span id="msg"
	                                                                         style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="select-bar">
	<label style="margin-left: 10px;">
		<input type="text" name="textfield"/>
	</label>
	<label style="margin-right: 10px;">
		<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"
		       name="Submit" value="Search"/>
	</label>
</div>
<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>系统公告内容</th>
			<th>开始时间</th>
			<th>间隔时间/秒</th>
			<th>结束时间</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td width="272"><strong><?php echo mb_substr($val['title'], 0, 18, 'utf-8') . '..'; ?></strong></td>
				<td><?php echo date('Y-m-d H:i:s', $val['start_time']); ?></td>
				<td><?php echo $val['interval_time']; ?></td>
				<td><?php echo date('Y-m-d H:i:s', $val['end_time']); ?></td>
				<td>
					<a href="?r=System/ViewEdit&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                               width="16" height="16"
					                                                               alt="edit"/></a>
					<a href="javascript:delMsg(<?php echo $val['id']; ?>)"><img src="styles/adm/images/del-icon.gif"
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
					echo "&nbsp;<a href='?r=System/Notice&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>
