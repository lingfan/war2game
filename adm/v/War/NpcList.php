<?php
$pageData = B_View::getVal('pageData');

$typesArr = array(
	'gold' => '黄金',
	'food' => '食物',
	'oil' => '石油'
);
$textArr = array(
	'gold' => '金钱',
	'food' => '食物',
	'oil' => '石油',
	'milpay' => '军饷',
	'coupon' => '礼券',
	'renown' => '威望',
	'warexp' => '功勋',
	'march_num' => '活力',
	'atkfb_num' => '军令',
	'props' => '道具',
	'equip' => '装备',
	'hero' => '英雄',
	'props_weapon' => '图纸',
);
?>

<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=War/NpcDel', {id: id}, function (txt) {
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
	<a href="?r=War/DelNpcCache" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">清除缓存</a>
	<a href="?r=War/NpcListImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=War/NpcView"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>
	<a href="?r=War/NpcHeroList" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">NPC英雄列表</a>

	<h1>NPC管理</h1>

	<div class="breadcrumbs">
		<a href="?r=War/NpcList">全部副本NPC部队</a>&nbsp;&nbsp;
		<a href="?r=War/WildNpcList">野外NPC部队</a>&nbsp;&nbsp;/
		<?php foreach (M_NPC::$NpcType as $npctype => $npcName) { ?>
			<a href="?r=War/NpcList&type=<?php echo $npctype; ?>"><?php echo $npcName; ?></a>&nbsp;&nbsp;
		<?php } ?>
		<br/>
        <span id="msg"
              style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
		<br/>导出数据:
		<a href="?r=War/NpcListExport">全部</a>&nbsp;/
		<?php foreach (M_NPC::$NpcType as $npctype => $npcName) { ?>
			<a href="?r=War/NpcListExport&t=<?php echo $npctype; ?>"><?php echo $npcName; ?></a>&nbsp;&nbsp;
		<?php } ?>
	</div>
</div>
<div class="select-bar">
	<form action="?r=War/NpcList" method="post">
		<label style="margin-left: 10px;">
			名称：
			<input type="text" name="nickname"
			       value="<?php echo isset($pageData['parms']['nickname']) ? $pageData['parms']['nickname'] : ''; ?>">
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
			<th width="50px">NPC英雄</th>
			<th width="15px">等级</th>
			<th width="20px">经验</th>
			<th width="40px">类型</th>
			<th width="80px">军队</th>

			<th width="120px">战斗奖励</th>
			<th width="40px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td width="100"><?php echo $val['nickname']; ?></td>
				<td><?php echo $val['level']; ?></td>
				<td><?php echo $val['exp_num']; ?></td>
				<td><?php echo M_NPC::$NpcType[$val['type']]; ?></td>
				<td>
					<?php
					foreach (json_decode($val['army_data'], true) as $v) {
						$info = B_DB::instance('BaseNpcHero')->get($v);
						echo isset($info['nickname']) ? $info['nickname'] . '<br>' : $v . '不存在<br>';
					}
					?>
				</td>


				<td>
					<?php
					echo B_Utils::awardText($val['award_id'], true);
					?>
				</td>
				<td>
					<a href="?r=War/NpcView&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                           width="16" height="16" alt="edit"/></a>
					&nbsp;
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

					echo "&nbsp;<a href='?r=War/NpcList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>