<?php
$pageData = B_View::getVal('pageData');

$payadd = $pageData['config_pay_add_award'];
$payonce = $pageData['config_pay_once_award'];

$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'充值奖励配置' => 'System/ConfigPayAward',
);


?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>一次充值奖励配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigPayAward" method="post" target="iframe">
		<input type="hidden" name="act" value="edit">
		<table class="listing form" cellpadding="0" cellspacing="0">

			<tr>
				<td><strong>首次充值奖励ID</strong></td>
				<td><input type="text" class="text" name="first_recharge_id" value="<?php echo $pageData['first_recharge_id']; ?>"/>
				</td>
			</tr>
			<tr>
				<td width="100">一次性<br>充值金额奖励<br>(平台币RMB)</td>
				<td style="text-align: left;">
					起始时间：<input type="text" name="pay_once_award[start_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($payonce['start']) ? $payonce['start'] : ''; ?>"><br/>
					截止时间：<input type="text" name="pay_once_award[end_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($payonce['end']) ? $payonce['end'] : ''; ?>">
					<br>
					最小值_最大值_奖励ID<br/>
					<?php
					$str = array();
					foreach ($payonce['data'] as $v) {
						$str[] = implode('_', $v);
					}
					?>
					<textarea name="pay_once_award[data]" cols="30"
					          rows="8"><?php echo implode("\n", $str); ?></textarea>
				</td>
				<td>
					<?php
					$text = '';
					foreach ($payonce['data'] as $val) {
						$text .= $val[2] .':'. B_Utils::awardText($val[2])."<br>";
					}
					echo $text;
					?>
				</td>
			</tr>

		</table>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100">累计<br>充值金额奖励<br>(平台币RMB)</td>
				<td style="text-align: left;">
					起始时间：<input type="text" name="pay_add_award[start_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($payadd['start']) ? $payadd['start'] : ''; ?>"><br/>
					截止时间：<input type="text" name="pay_add_award[end_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($payadd['end']) ? $payadd['end'] : ''; ?>">
					<br>
					数量_奖励ID<br/>
					<?php
					$str = array();
					foreach ($payadd['data'] as $v) {
						$str[] = implode('_', $v);
					}
					?>
					<textarea name="pay_add_award[data]" cols="30"
					          rows="8"><?php echo implode("\n", $str); ?></textarea>
				</td>
				<td>

					<?php
					$text = '';
					foreach ($payadd['data'] as $val) {
						$text .= $val[1] .':'. B_Utils::awardText($val[1])."<br>";
					}
					echo $text;
					?>
				</td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
				<td></td>
			</tr>

		</table>
	</form>
</div>
