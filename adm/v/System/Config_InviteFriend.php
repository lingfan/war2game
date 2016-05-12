<?php
$pageData = B_View::getVal('pageData');

$live = $pageData['friend_live_award'];
$invite = $pageData['friend_invite_award'];

$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'好友邀请奖励配置' => 'System/ConfigPayAward',
);


?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>好友邀请奖励配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigInviteFriend" method="post" target="iframe">
		<input type="hidden" name="act" value="edit">
		<table class="listing form" cellpadding="0" cellspacing="0">

			<tr>
				<td width="100">好友活跃次数</td>
				<td style="text-align: left;">
					起始时间：<input type="text" name="friend_live_award[start_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($live['start']) ? $live['start'] : ''; ?>"><br/>
					截止时间：<input type="text" name="friend_live_award[end_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($live['end']) ? $live['end'] : ''; ?>">
					<br>
					最小值_最大值_奖励ID<br/>
					<?php
					$str = array();
					foreach ($live['data'] as $v) {
						$str[] = implode('_', $v);
					}
					?>
					<textarea name="friend_live_award[data]" cols="30"
					          rows="8"><?php echo implode("\n", $str); ?></textarea>
				</td>
				<td>
					<?php
					$text = '';
					foreach ($live['data'] as $val) {
						$text .= $val[2] . B_Utils::awardText($val[2]);
					}
					echo $text;
					?>
				</td>
			</tr>

		</table>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100">好友邀请次数</td>
				<td style="text-align: left;">
					起始时间：<input type="text" name="friend_invite_award[start_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($invite['start']) ? $invite['start'] : ''; ?>"><br/>
					截止时间：<input type="text" name="friend_invite_award[end_time]" class="Wdate"
					            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					            value="<?php echo isset($invite['end']) ? $invite['end'] : ''; ?>">
					<br>
					数量_奖励ID<br/>
					<?php
					$str = array();
					foreach ($invite['data'] as $v) {
						$str[] = implode('_', $v);
					}
					?>
					<textarea name="friend_invite_award[data]" cols="30"
					          rows="8"><?php echo implode("\n", $str); ?></textarea>
				</td>
				<td>

					<?php
					$text = '';
					foreach ($invite['data'] as $val) {
						$text .= $val[1] . B_Utils::awardText($val[1]);
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
