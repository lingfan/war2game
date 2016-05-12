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
			$.post('?r=Map/WarMapCellDel', {id: id}, function (txt) {
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
	<a href="?r=Map/WarMapCellView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>

	<h1>地图编辑器</h1>

	<div class="breadcrumbs"><a href="#">地图编辑器</a> / <a href="#">地图标记物列表</a> <span id="msg"
	                                                                               style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>

<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>标记名称</th>
			<th>类型</th>

			<th>禁止通行</th>
			<th>禁止停留</th>

			<th>生命值</th>
			<th>攻击力</th>

			<th>防御力</th>

			<th>射程</th>
			<th>视野</th>
			<th>移动</th>
			<th>排序</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo M_MapBattle::$warMapCellAttr[$val['type']]; ?></td>

				<td>
					<?php
					$a = 0;
					foreach (M_Weapon::$moveType as $k => $v) {
						if (($val['ban'] & M_MapBattle::$warMapCellBanCrossType[$k]) > 0) {
							echo $v . '<br>';
							$a++;
						}
					}
					if ($a < 1) echo '无';
					unset($a);
					?>
				</td>
				<td>
					<?php
					$a = 0;
					foreach (M_Weapon::$moveType as $k => $v) {
						if (($val['ban'] & M_MapBattle::$warMapCellBanHoldType[$k]) > 0) {
							echo $v . '<br>';
							$a++;
						}
					}
					if ($a < 1) echo '无';
					unset($a);
					?>
				</td>

				<td><?php echo $val['life_value']; ?></td>
				<td>
					对地：<?php echo $val['att_land']; ?><br>
					对空：<?php echo $val['att_sky']; ?><br>
					对海：<?php echo $val['att_ocean']; ?>
				</td>

				<td>
					对地：<?php echo $val['def_land']; ?><br>
					对空：<?php echo $val['def_sky']; ?><br>
					对海：<?php echo $val['def_ocean']; ?>
				</td>

				<td><?php echo $val['shot_range']; ?></td>
				<td><?php echo $val['view_range']; ?></td>
				<td><?php echo $val['move_range']; ?></td>
				<td><?php echo $val['sort']; ?></td>
				<td>
					<a href="?r=Map/WarMapCellView&id=<?php echo $val['id']; ?>"><img
							src="styles/adm/images/edit-icon.gif" width="16" height="16" alt="edit"/></a>
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

					echo "&nbsp;<a href='?r=Map/WarMapCellList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>