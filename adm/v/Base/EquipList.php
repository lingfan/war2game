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
			$.post('?r=Base/EquipTplDel', {id: id, act: 'del'}, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.msg);
				if (txt.flag == 1) {
					$('#list #' + id).remove();
				}
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json');
		}
	}

	function toSub(id, suit_id) {
		$.post('?r=Base/SetTplSuit', {id: id, suit_id: suit_id}, function (txt) {
			$('#msg').css('display', '')
			$('#msg').html(txt.msg);
			setTimeout("$('#msg').css('display', 'none')", 3000);
		}, 'json');
	}
</script>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<a href="?r=Base/EquipCacheUp" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">更新缓存</a>
	<a href="?r=Base/EquipListImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/EquipView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>
	<a href="?r=Base/EquipSuit" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">套装编辑</a>

	<h1>装备管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="#">装备列表</a> <span id="msg"
	                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
		<?php
		echo '<a href="?r=Base/EquipListExport&p=1">导出全部</a>&nbsp;';
		?>
	</div>
</div>

<div class="select-bar">
	<form action="?r=Base/EquipList" method="post">
		<label style="margin-left: 10px;">
			名称：
			<select name="equip_id" id="equip_id" style="width: 100px;">
				<option value="0">----</option>
				<?php
				$baselist = !empty($pageData['baseList']) ? $pageData['baseList'] : array();
				foreach ($baselist as $key => $val) {
					?>
					<option
						value="<?php echo $val['id']; ?>" <?php if (isset($pageData['parms']['equip_id']) && $pageData['parms']['equip_id'] == $val['id']) {
						echo 'selected="selected"';
					} ?>><?php echo $val['name']; ?></option>
				<?php } ?>
			</select>
		</label>
		<label style="margin-left: 10px;">
			品质：
			<select name="quality" id="quality" style="width: 50px;">
				<option value="0">--</option>

				<?php foreach (T_Word::$EQUIP_QUAL as $key => $val) { ?>
					<option
						value="<?php echo $key; ?>" <?php if (isset($pageData['parms']['quality']) && $pageData['parms']['quality'] == $key) {
						echo 'selected="selected"';
					} ?>><?php echo $val; ?></option>
				<?php } ?>

			</select>
		</label>
		<label style="margin-left: 10px;">
			所属套装：
			<select name="suit_id" id="suit_id" style="width: 100px;">
				<option value="0">--</option>
				<?php foreach ($pageData['suit'] as $value) { ?>
					<option
						value="<?php echo $value['id']; ?>" <?php if (isset($pageData['parms']['suit_id']) && $pageData['parms']['suit_id'] == $value['id']) {
						echo 'selected="selected"';
					} ?>><?php echo $value['name']; ?></option>
				<?php } ?>
			</select>
		</label>
		<label style="margin-left: 90px;">
			<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="Submit"
			       name="Submit" value="Search"/>
		</label>
	</form>

</div>
<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width: 30px;">ID <a href="?r=Base/EquipList" title="按ID升序排序" style="color: white;">↑↑</a></th>
			<th>名称</th>
			<th>品质 <a href="?r=Base/EquipList&order=quality" title="按品质降序排序" style="color: white;">↓↓</a></th>
			<th>所属套装</th>
			<th>防御</th>
			<th>攻击</th>
			<th>生命</th>
			<th>需要等级</th>
			<th>装备类型</th>
			<th>出售价格</th>
			<th>VIP</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><strong style="color: <?php echo $val['color']; ?>"><?php echo $val['name']; ?></strong></td>
				<td><?php echo $val['quality']; ?></td>
				<td style="width: 100px;">
					<select <?php echo 'onchange="toSub(' . $val['id'] . ',this.value)"' ?> style="width: 100%">
						<option value="0">无</option>
						<?php foreach ($pageData['suit'] as $value) { ?>
							<option
								value="<?php echo $value['id']; ?>" <?php if ($val['suit_id'] == $value['id']) echo 'selected="selected"'; ?>><?php echo $value['name']; ?></option>
						<?php } ?>
					</select>
				</td>
				<td><?php echo $val['base_lead']; ?></td>
				<td><?php echo $val['base_command']; ?></td>
				<td><?php echo $val['base_military']; ?></td>
				<td><?php echo $val['need_level']; ?></td>
				<td><?php echo $val['type'] == 1 ? '系统装备' : '活动装备'; ?> </td>
				<td><?php echo $val['gold']; ?></td>
				<td><?php echo $val['is_vip_use'] ? '是' : '否'; ?></td>
				<td>
					<a href="?r=Base/EquipView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
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
					if (!empty($pageData['order'])) {
						$parmStr .= '&order=' . $pageData['order'];
					}
					echo "&nbsp;<a href='?r=Base/EquipList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>