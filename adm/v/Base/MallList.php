<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'商城物品列表' => 'Base/MallList&page=1',
);
?>

<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>商城物品列表
		<!-- 	<a href="?r=Base/MallAdd" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">新增道具</a> -->
		<a href="?r=Base/DelMallCache"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		   target="iframe">清除缓存</a>
		<a href="?r=Base/DelMallNumCache"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" target="iframe">清除商品数量缓存</a>
		<a href="?r=Base/MallListImport"
		   class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">导入</a>
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
		<br>
		<?php
		echo '<a href="?r=Base/MallListExport&p=1">全部</a>&nbsp;';
		?>

	</div>
</div>

<div class="table">
	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th width="30px">ID</th>
			<th width="60px">商城栏目</th>
			<th width="60px">物品类型</th>
			<th width="80px">物品ID/物品名称</th>
			<th width="30px">热卖</th>
			<th width="80px">军饷/点券/金钱</th>
			<th width="30px">数量</th>
			<th width="30px">上架时间</th>
			<th width="30px">下架时间</th>
			<th width="30px">排序</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) {
			$arrPrice = isset($val['price']) ? json_decode($val['price'], true) : array(T_App::MILPAY => 0, T_App::COUPON => 0, T_App::RES_GOLD => 0);
			$milpayVal = isset($arrPrice[T_App::MILPAY]) ? $arrPrice[T_App::MILPAY] : '0';
			$couponVal = isset($arrPrice[T_App::COUPON]) ? $arrPrice[T_App::COUPON] : '0';
			$moneyVal = isset($arrPrice[T_App::RES_GOLD]) ? $arrPrice[T_App::RES_GOLD] : '0';
			?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo M_Mall::$category[$val['category']]; ?></td>
				<td><?php echo M_Mall::$itemType[$val['item_type']]; ?></td>
				<td><?php echo $val['item_id'] . '/' . $val['name']; ?></td>
				<td><?php echo $val['status'] ? '是' : '-'; ?></td>
				<td><?php echo $milpayVal . '/' . $couponVal . '/' . $moneyVal; ?></td>
				<td><?php echo $val['num']; ?></td>
				<td><?php echo date('Y-m-d H:i:s', $val['up_time']); ?></td>
				<td><?php echo date('Y-m-d H:i:s', $val['down_time']); ?></td>
				<td><?php echo $val['sort']; ?></td>
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
					echo "&nbsp;<a href='?r=Base/MallList&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>