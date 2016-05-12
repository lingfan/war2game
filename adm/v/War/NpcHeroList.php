<?php
$pageData = B_View::getVal('pageData');
?>
<style>
	#list tr td {
		background-color: lightgray;
	}
</style>
<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=War/NpcHeroDel', {id: id}, function (txt) {
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
	<a href="?r=War/DelNpcHeroCache"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" target="iframe">清除缓存</a>
	<a href="?r=War/NpcHeroListImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=War/NpcHeroView" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>


	<h1>NPC管理</h1>

	<div class="breadcrumbs">
		<a href="#">战斗相关</a> / <a href="?r=War/NpcList">NPC部队列表</a> / <a href="#">NPC英雄列表</a> <span id="msg"
		                                                                                            style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
		<br>导出每页1000记录：
		<?php
		$num = $pageData['total'];
		$p = ceil($num / 1000);
		for ($i = 1; $i <= $p; $i++) {
			echo '<a href="?r=War/NpcHeroListExport&offset=1000&p=' . $i . '">' . $i . '</a>&nbsp;';
		}
		echo '<a href="?r=War/NpcHeroListExport&offset=' . $num . '&p=1">全部</a>&nbsp;';
		echo "<br>类型导出:";
		$t = B_DB::instance('BaseNpcHero')->getTypeArr();
		foreach ($t as $v) {
			if ($v['type']) {
				echo '<a href="?r=War/NpcHeroListExport&t=' . $v['type'] . '">' . $v['type'] . '</a>&nbsp;';
			}

		}
		?>


	</div>
</div>
<div class="select-bar">
	<form action="?r=War/NpcHeroList" method="post">
		<label style="margin-left: 10px;">
			名称：
			<input type="text" name="nickname"
			       value="<?php echo isset($pageData['parms']['nickname']) ? $pageData['parms']['nickname'] : ''; ?>">
		</label>
		<label style="margin-left: 10px;">
			品质：
			<select name="quality">
				<option value="">--</option>
				<?php foreach (T_Hero::$heroQual as $key => $val) { ?>
					<option
						value="<?php echo $key; ?>" <?php if (isset($pageData['parms']['quality']) && $pageData['parms']['quality'] == $key) {
						echo 'selected="selected"';
					} ?>><?php echo $val; ?></option>
				<?php } ?>
			</select>
		</label>
		<label style="margin-right: 10px;">
			<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="Submit"/>
		</label>
	</form>

</div>

<div class="table">

	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>NPC英雄</th>
			<th>详细</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td width="100"><strong
						style="color: <?php echo $pageData['color'][$val['quality']]; ?>"><?php echo $val['nickname']; ?></strong>
				</td>
				<td style="text-align: left;">
					<?php
					echo '性别:';
					echo $val['gender'] == 1 ? '男' : '女';
					echo '&nbsp;&nbsp;&nbsp;';
					echo '品质:' . T_Hero::$heroQual[$val['quality']];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '等级:' . $val['level'];
					echo '&nbsp;&nbsp;&nbsp;';
					//echo '<br>';
					echo '技能槽数量:' . $val['skill_slot_num'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '天赋技能:';
					echo $val['skill_slot'] > 0 ? $pageData['skill_list'][$val['skill_slot']]['name'] : '无';
					echo '&nbsp;&nbsp;&nbsp;';
					echo '技能1:';
					echo $val['skill_slot_1'] > 0 ? $pageData['skill_list'][$val['skill_slot_1']]['name'] : '无';
					echo '&nbsp;&nbsp;&nbsp;';
					echo '技能2:';
					echo $val['skill_slot_2'] > 0 ? $pageData['skill_list'][$val['skill_slot_2']]['name'] : '无';
					echo '<br>';
					echo '统帅:' . $val['attr_lead'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '指挥:' . $val['attr_command'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '军事:' . $val['attr_military'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '精力:' . $val['attr_energy'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '情绪:' . $val['attr_mood']; //@todo
					echo '<br>';
					echo '武器:';
					if (isset($pageData['equipList'][$val['equip_arm']]['name'])) {
						echo $pageData['equipList'][$val['equip_arm']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_arm']]['quality']] . ')';
					} else {
						echo '无';
					}
					echo '&nbsp;&nbsp;&nbsp;';
					echo '军帽:';
					if (isset($pageData['equipList'][$val['equip_cap']]['name'])) {
						echo $pageData['equipList'][$val['equip_cap']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_cap']]['quality']] . ')';
					} else {
						echo '无';
					}
					echo '&nbsp;&nbsp;&nbsp;';
					echo '军服:';
					if (isset($pageData['equipList'][$val['equip_uniform']]['name'])) {
						echo $pageData['equipList'][$val['equip_uniform']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_uniform']]['quality']] . ')';
					} else {
						echo '无';
					}
					//echo '<br>';
					echo '勋章:';
					if (isset($pageData['equipList'][$val['equip_medal']]['name'])) {
						echo $pageData['equipList'][$val['equip_medal']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_medal']]['quality']] . ')';
					} else {
						echo '无';
					}
					echo '&nbsp;&nbsp;&nbsp;';
					echo '军鞋:';
					if (isset($pageData['equipList'][$val['equip_shoes']]['name'])) {
						echo $pageData['equipList'][$val['equip_shoes']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_shoes']]['quality']] . ')';
					} else {
						echo '无';
					}
					echo '&nbsp;&nbsp;&nbsp;';
					echo '座驾:';
					if (isset($pageData['equipList'][$val['equip_sit']]['name'])) {
						echo $pageData['equipList'][$val['equip_sit']]['name'] . '(' . T_Word::$EQUIP_QUAL[$pageData['equipList'][$val['equip_sit']]['quality']] . ')';
					} else {
						echo '无';
					}
					echo '<br>';
					echo '配备兵种:' . M_Army::$type[$val['army_id']];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '兵种等级:' . $val['army_lv'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '兵种数量:' . $val['army_num'];
					echo '&nbsp;&nbsp;&nbsp;';
					echo '兵种武器:' . $pageData['weaponList'][$val['weapon_id']]['name'];

					?>
				</td>

				<td>
					<a href="?r=War/NpcHeroView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
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

					echo "&nbsp;<a href='?r=War/NpcHeroList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>