<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'在线奖励配置' => 'System/ConfigOnline',
);
?>

<div class="top-bar">
	<h1>在线奖励配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigOnline" method="post" target="iframe">
		<input type="hidden" name="act" value="edit">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">在线奖励配置信息 重要内容！请勿随意更改！！！</th>
			</tr>

			<tr>
				<td width="180">在线时长</td>
				<?php
				$str = $awardIds = array();

				foreach($pageData['baseCfg']['config_online_award'] as $val) {
					$str[] = implode(',',$val);
					$awardIds[] = $val[1];
				}
				?>
				<td>
					<textarea rows="10" cols="50" name="config_online_award"><?php echo implode("\n", $str); ?></textarea>
				<br>
					<?php
					foreach($awardIds as $awardId) {
						echo $awardId.':'.B_Utils::awardText($awardId)."<br>";
					}
					?>
				</td>
			</tr>


			<tr>
				<td><strong>日历奖励</strong></td>
				<?php
				$str = '';
				foreach ($pageData['baseCfg']['calender_award'] as $val) {
					list($num, $awardId) = $val;
					$str .= "{$num},{$awardId}\n";
				}
				?>
				<td>
					<div style="float: left;">
						<div style="float: left;">
							累计天数,奖励ID<br>
							<textarea rows="10" cols="20" name="calender_award"><?php echo $str; ?></textarea>
						</div>
						<div style="float: left;">
							<?php
							$text = '';
							foreach ($pageData['baseCfg']['calender_award'] as $val) {
								$text .= $val[1] . B_Utils::awardText($val[1])."<br>";
							}
							echo $text;
							?>
						</div>
					</div>


				</td>

			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
