<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'充值奖励奖励配置' => 'System/ConfigPay',
);

$payAward = $pageData['list'];
unset($payAward[1]);
unset($payAward[2]);

$str = $text = '';
foreach ($payAward as $key => $val) {
	$text .= '<br />' . $val[2] . B_Utils::awardText($val[2]);
	$str .= implode('_', $val) . "\n";
}
?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>充值奖励配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigPay" method="post" target="iframe">
		<input type="hidden" name="act" value="edit">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">充值奖励配置信息 重要内容！请勿随意更改！！！</th>
			</tr>
			<tr>
				<td width="150">起止时间</td>
				<td>
					起始时间：<input type="text" name="start_time" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($pageData['list'][1]) ? $pageData['list'][1] : ''; ?>"><br/>
					截止时间：<input type="text" name="end_time" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($pageData['list'][2]) ? $pageData['list'][2] : ''; ?>">
				</td>
			</tr>
			<tr>
				<td>充值金额奖励(平台币RMB)</td>
				<td>
					最小值_最大值_奖励ID<br/>
					<textarea name="pay_award" cols="50" rows="15"><?php echo $str; ?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
			<tr>
				<td>奖励ID及内容</td>
				<td><?php echo $text; ?></td>
			</tr>
		</table>
	</form>
</div>
