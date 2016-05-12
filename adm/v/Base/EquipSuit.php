<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'装备列表列表' => '',
);
?>
<script>
	function del(id) {
		$.post('?r=Base/EquipSuitDel', {id: id}, function (txt) {
			$('#msg').css('display', '')
			$('#msg').html(txt.msg);
			setTimeout("$('#msg').css('display', 'none')", 3000);
		}, 'json');
	}

	function show(a, b, c) {
		var div = document.createElement('div');
	}


</script>
<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=Base/EquipSuitCacheUp"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" target="iframe">更新缓存</a>
	<a href="?r=Base/EquipSuitImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/EquipSuitAddView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加套装</a>

	<h1>套装管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="?r=Base/EquipList">装备列表</a> / <a href="#">套装列表</a> <span
			id="msg"
			style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
		<?php
		echo '<a href="?r=Base/EquipSuitExport&p=1">导出全部</a>&nbsp;';
		?>
	</div>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>名称</th>
			<th>描述</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td width="20"><?php echo $val['id']; ?></td>
				<td width="100"><?php echo $val['name']; ?></td>
				<td style="text-align: left;"><?php echo $val['desc']; ?></td>
				<td><a href="?r=Base/EquipSuitAddView&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16"
							alt="edit"/> </a> <a
						href="javascript:del(<?php echo $val['id']; ?>)"><img
							src="styles/adm/images/del-icon.gif" width="16" height="16"
							alt="del"/> </a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<!-- 	<div class="select"> -->
	<!-- 		<strong> -->
	<?php
	// 		foreach($pageData['page']['range'] as $val)
	// 		{
	// 			if ($pageData['page']['curPage'] == $val)
	// 			{
	// 				echo "&nbsp;{$val}&nbsp;";
	// 			}
	// 			else
	// 			{
	// 				$parmStr = '';
	// 				if (!empty($pageData['parms']))
	// 				{
	// 					foreach ($pageData['parms'] as $k => $v)
	// 					{
	// 						$parmStr .= '&' . $k . '=' . $v;
	// 					}
	// 				}

	// 				echo "&nbsp;<a href='?r=Base/EquipList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
	// 			}

	// 		}
	// 		echo "&nbsp;&nbsp;".$pageData['page']['curPage'].'/'.$pageData['page']['totalPage'];
	?>

	<!-- 		</strong> -->
	<!-- 	</div> -->
</div>
