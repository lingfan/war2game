<?php
$pageData = B_View::getVal('pageData');

$typesArr = array(
	'gold' => '黄金',
	'food' => '食物',
	'oil' => '石油'
);
?>

<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=Base/AwardDel', {id: id}, function (txt) {
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
	<a href="?r=Base/AwardListExport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导出</a>
	<a href="?r=Base/AwardListImport"
	   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	<a href="?r=Base/AwardCacheUp" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
	   target="iframe">更新缓存</a>

	<h1>奖励数管理</h1>

	<div class="breadcrumbs"><span id="msg"
	                               style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="select-bar">
	<form action="?r=Base/AwardList" method="post">
		<label style="margin-left: 10px;">
			名称：
			<input type="text" name="parms[name]"
			       value="<?php echo isset($pageData['parms']['name']) ? $pageData['parms']['name'] : ''; ?>">
		</label>

		<label style="margin-right: 10px;">
			<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="Submit"/>
		</label>
	</form>

</div>
<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="40px">ID</th>
			<th width="150px">名称</th>
			<th width="40px">类型</th>
			<th width="40px">掉落数</th>
			<th>描述</th>
			<th width="60px">操作</th>

		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr id="<?php echo $val['id']; ?>">
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['type']; ?></td>
				<td><?php echo $val['num']; ?></td>
				<td><?php echo $val['desc']; ?></td>
				<td>
					<a href="javascript:del(<?php echo $val['id']; ?>)"><img src="styles/adm/images/del-icon.gif"  width="16" height="16" alt="del"/></a>
				</td>

			</tr>
			<tr>
				<td>
					奖励:
				</td>
				<td colspan="5">
					<?php
					echo B_Utils::awardText($val['id']);
					?>
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

					echo "&nbsp;<a href='?r=Base/AwardList&page={$val}{$parmStr}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>












						