<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
);
$baselist = $pageData['baselist'];
?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<script>
	document.getElementById('start_time').innerText =<?php echo isset($pageData['list'][1]) ? $pageData['list'][1] : '';?>;
</script>
<div class="top-bar">
	<h1>基础配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
<iframe name="iframe" style="display: none;"></iframe>
<form action="?r=System/ConfigLiveness&act=edit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">

	<tr>
		<td><strong>积分兑换活动起始时间</strong></td>
		<td>
			起始时间：<input type="text" name="activeness[start]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
			            value="<?php echo isset($baselist['activeness_list']['start']) ? date('Y-m-d H:i:s', $baselist['activeness_list']['start']) : ''; ?>">

			截止时间：<input type="text" name="activeness[end]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
			            value="<?php echo isset($baselist['activeness_list']['end']) ? date('Y-m-d H:i:s', $baselist['activeness_list']['end']) : ''; ?>">

		</td>
	</tr>
<tr>
	<td><strong>积分兑换活动</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<?php
			foreach (M_Liveness::$category as $key => $val) {
				if (isset($baselist['activeness_list'][$key][0]) && is_array($baselist['activeness_list'][$key][0])) {

					foreach ($baselist['activeness_list'][$key][0] as $k => $v) {
						$arr[] = $k . ':' . $v;
					}
					$baselist['activeness_list'][$key][0] = implode(',', $arr);
				}
				?>
				<tr>
					<td><?php echo $val; ?>
						<input name="activeness[<?php echo $key ?>][0]" type="text" style="width: 150px;"
						       value="<?php echo isset($baselist['activeness_list'][$key][0]) ? $baselist['activeness_list'][$key][0] : ''; ?>">分
					</td>
					<td>每日获取上限
						<input name="activeness[<?php echo $key ?>][1]" type="text" style="width: 100px;"
						       value="<?php echo isset($baselist['activeness_list'][$key][1]) ? $baselist['activeness_list'][$key][1] : ''; ?>">分
						<?php
						if ($key == M_Liveness::GET_POINT_EXPLORE) {

							?>
							获得几率
							<input name="activeness[<?php echo $key ?>][2]" type="text" style="width: 50px;"
							       value="<?php echo isset($baselist['activeness_list'][$key][2]) ? $baselist['activeness_list'][$key][2] : ''; ?>">%

						<?php } ?>
					</td>

				</tr>
			<?php } ?>
		</table>

	</td>
</tr>


	<tr>
		<td><strong>积分兑换物品</strong></td>
		<td>

			<?php
			$tmpArr = $baselist['activeness_item'];
			$t = array();
			foreach ($tmpArr as $v) {
				$t[] = implode(",", $v);
			}

			$str = implode("\n", $t);
			?>
			物品类型(1道具|3装备),物品ID,需要积分<br>
			<textarea name="activeness_item" cols="50" rows="10"><?php echo $str;?></textarea>

		</td>
	</tr>
<tr>
	<td></td>
	<td><input type="submit" class="button" name="submit" value="保存"/></td>
</tr>

</table>
</form>
</div>
