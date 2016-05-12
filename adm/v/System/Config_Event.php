<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'活动开启配置' => 'System/ConfigEvent',
);
?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>活动开启配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigEvent&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td><strong>爬楼</strong></td>
				<td>
					<?php
					$floor = $pageData['event_floor'];
					?>
					通过副本编号<input type="text" style="width: 100px;" class="text" name="event_floor[fbno]" value="<?php echo isset($floor['fbno']) ? $floor['fbno'] : ''; ?>"/>
					开始日期：<input type="text" name="event_floor[stime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($floor['stime']) ? $floor['stime'] : ''; ?>">
					截止日期：<input type="text" name="event_floor[etime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($floor['etime']) ? $floor['etime'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td><strong>突击</strong></td>
				<td>
					<?php
					$breakout = $pageData['event_breakout'];
					?>
					通过副本编号<input type="text" style="width: 100px;" class="text" name="event_breakout[fbno]" value="<?php echo isset($breakout['fbno']) ? $breakout['fbno'] : ''; ?>"/>
					开始日期：<input type="text" name="event_breakout[stime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($breakout['stime']) ? $breakout['stime'] : ''; ?>">
					截止日期：<input type="text" name="event_breakout[etime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($breakout['etime']) ? $breakout['etime'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td><strong>据点</strong></td>
				<td>
					<?php
					$campaign = $pageData['event_campaign'];
					?>
					通过副本编号<input type="text" style="width: 100px;" class="text" name="event_campaign[fbno]" value="<?php echo isset($campaign['fbno']) ? $campaign['fbno'] : ''; ?>"/>
					开始日期：<input type="text" name="event_campaign[stime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($campaign['stime']) ? $campaign['stime'] : ''; ?>">
					截止日期：<input type="text" name="event_campaign[etime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($campaign['etime']) ? $campaign['etime'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td><strong>问答</strong></td>
				<td>
					<?php
					$answer = $pageData['event_answer'];
					?>
					通过副本编号<input type="text" style="width: 100px;" class="text" name="event_answer[fbno]" value="<?php echo isset($answer['fbno']) ? $answer['fbno'] : ''; ?>"/>
					开始日期：<input type="text" name="event_answer[stime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($answer['stime']) ? $answer['stime'] : ''; ?>">
					截止日期：<input type="text" name="event_answer[etime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($answer['etime']) ? $answer['etime'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td><strong>挑战</strong></td>
				<td>
					<?php
					$challenge = $pageData['event_challenge'];
					?>
					通过副本编号<input type="text" style="width: 100px;" class="text" name="event_challenge[fbno]" value="<?php echo isset($challenge['fbno']) ? $challenge['fbno'] : ''; ?>"/>
					开始日期：<input type="text" name="event_challenge[stime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($challenge['stime']) ? $challenge['stime'] : ''; ?>">
					截止日期：<input type="text" name="event_challenge[etime]" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" value="<?php echo isset($challenge['etime']) ? $challenge['etime'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>

		</table>
	</form>
</div>
