<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'道具列表' => 'Base/PropsList&page=1',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>道具数据列表
		<a href="?r=Base/PropsAdd"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增道具</a>
		<a href="?r=Base/DelPropsCache"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		   target="iframe">清除缓存</a>
		<a href="?r=Base/PropsListImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
		<br>
		<?php
		echo '<a href="?r=Base/PropsListExport&p=1">全部</a>&nbsp;';
		?>

	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="120px">道具名</th>
			<th width="30px">类型</th>
			<th width="40px" style="font-size: 12px">持续</th>
			<th>描述</th>
			<th width="40px">操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) {
			$arrPrice = isset($val['price']) ? json_decode($val['price'], true) : array(T_App::MILPAY => 0, T_App::COUPON => 0);
			$milpayVal = isset($arrPrice[T_App::MILPAY]) ? $arrPrice[T_App::MILPAY] : '0';
			$couponVal = isset($arrPrice[T_App::COUPON]) ? $arrPrice[T_App::COUPON] : '0';
			?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo M_Props::$type[$val['type']]; ?></td>
				<td><?php echo !empty($val['effect_time']) ? B_Utils::formatTime($val['effect_time']) : '-'; ?></td>
				<td style="text-align: left"><span><?php echo $val['desc']; ?></span></td>
				<td>
					<a href="?r=Base/PropsAdd&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                             width="16" height="16" title="编辑"
					                                                             alt="编辑"></a>&nbsp;
					<a onclick="javascript:return confirm('删除后数据不可恢复,您确定删除吗?');"
					   href="?r=Base/DoPropsDel&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
					                                                               width="16" height="16" title="删除"
					                                                               alt="删除"></a>
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
					echo "&nbsp;<a href='?r=Base/PropsList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>