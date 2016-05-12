<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'英雄列表' => 'Base/HeroList',
);
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
			$.post('?r=Base/HeroDel', {id: id}, function (txt) {
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
	<a href="?r=Base/HeroAddTpl" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">添加</a>
	<a href="?r=Base/HeroListExport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/HeroTplImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/DelHeroCache" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">清除缓存</a>

	<h1>英雄管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="#">英雄列表</a> <span id="msg"
	                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="select-bar">
	<form action="?r=Base/HeroList" method="post">
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
			<th style="width: 30px;">ID <a href="?r=Base/HeroList" title="按ID升序排序" style="color: white;">↑↑</a></th>
			<th>名字</th>
			<th>品质 <a href="?r=Base/HeroList&order=quality" title="按品质降序排序" style="color: white;">↓↓</a></th>
			<th width="30">槽数</th>
			<th>天赋</th>
			<th>兵种</th>
			<th width="60">防御</th>
			<th width="60">攻击</th>
			<th width="60">生命</th>
		</tr>
		<?php
		$skillList = B_DB::instance('BaseSkill')->getAll();
		foreach ($pageData['list'] as $key => $val) {
			?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><strong
						style="color: <?php echo $pageData['color'][$val['quality']]; ?>"><?php echo $val['nickname']; ?></strong>
				</td>
				<td><?php echo T_Hero::$heroQual[$val['quality']]; ?></td>
				<td><?php echo $val['skill_slot_num']; ?></td>
				<td>
					<?php
					$name = '无';
					if ($val['skill_slot'] > 0) {
						$skillInfo = isset($skillList[$val['skill_slot']]) ? $skillList[$val['skill_slot']] : '';
						if (!empty($skillInfo)) {
							$name = $skillInfo['name'];
						}

					}
					echo $name;
					?>
				</td>
				<td><?php echo M_Army::$type[$val['army_id']]; ?></td>
				<td><?php echo $val['attr_lead']; ?></td>
				<td><?php echo $val['attr_command']; ?></td>
				<td><?php echo $val['attr_military']; ?></td>
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

					echo "&nbsp;<a href='?r=Base/HeroList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>