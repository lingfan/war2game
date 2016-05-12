<?php
$pageData = B_View::getVal('pageData');
$baseHorse = $pageData['baselist']['horse'];

$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'跑马配置' => 'System/ConfigHorse',
);
?>

<div class="top-bar">
	<h1>越野跑马配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigHorse&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="150"><strong>跑马系统开关</strong></td>
				<td><input type="text" class="text" name="horseswitch" value="<?php echo intval($baseHorse[10]); ?>"/>跑马系统开关[1开
					0关]
				</td>
			</tr>
			<tr>
				<td width="150"><strong>各阶段所需分钟</strong></td>
				<td>投注阶段,等待阶段,比赛阶段,奖励阶段<br>
					<input type="text" class="text" name="costmin" value="<?php echo implode(',', $baseHorse[0]); ?>"/>每阶段的时间(分钟)<br>

					修改时间后需要(<a target="iframe" href="?r=System/HorseCleanCache">清除缓存</a>)
				</td>

			</tr>
			<tr>
				<td width="150"><strong>赔率</strong></td>
				<td><input type="text" class="text" name="payrate" value="<?php echo implode(',', $baseHorse[1]); ?>"/>固定赔率,固定赔率,赔率E最小范围,E最大范围
				</td>
			</tr>
			<tr>
				<td width="150"><strong>状态</strong></td>
				<td>状态ID,描述(一行 不要多余空格)<br/>
					<textarea name="status" cols='42' rows="10"><?php
						foreach ($baseHorse[2] as $statusId => $desc) {
							echo "$statusId,$desc" . "\n";
						}
						?></textarea>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>加减速事件ID</strong></td>
				<td>
					<input type="text" class="text" name="eventid1"
					       value="<?php echo implode(',', $baseHorse[3][0]); ?>"/>1减速事件ID<br/>
					<input type="text" class="text" name="eventid2"
					       value="<?php echo implode(',', $baseHorse[3][1]); ?>"/>2匀速事件ID<br/>
					<input type="text" class="text" name="eventid3"
					       value="<?php echo implode(',', $baseHorse[3][2]); ?>"/>3加速事件ID<br/>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>事件随机</strong></td>
				<td>总事件数,加速事件数随机下限,加速随机上限,加速比减速事件多余数<br/>
					<input type="text" class="text" name="randevent1"
					       value="<?php echo implode(',', $baseHorse[4][0]); ?>"/>第1马<br/>
					<input type="text" class="text" name="randevent2"
					       value="<?php echo implode(',', $baseHorse[4][1]); ?>"/>第2马<br/>
					<input type="text" class="text" name="randevent3"
					       value="<?php echo implode(',', $baseHorse[4][2]); ?>"/>第3马<br/>
					<input type="text" class="text" name="randevent4"
					       value="<?php echo implode(',', $baseHorse[4][3]); ?>"/>第4马<br/>
					<input type="text" class="text" name="randevent5"
					       value="<?php echo implode(',', $baseHorse[4][4]); ?>"/>第5马<br/>
					<input type="text" class="text" name="randevent6"
					       value="<?php echo implode(',', $baseHorse[4][5]); ?>"/>第6马<br/>
					<input type="text" class="text" name="randevent7"
					       value="<?php echo implode(',', $baseHorse[4][6]); ?>"/>第7马<br/>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>投注</strong></td>
				<td><input type="text" class="text" name="betting" value="<?php echo implode(',', $baseHorse[5]); ?>"/>投注最小值,最大值
				</td>
			</tr>
			<tr>
				<td width="150"><strong>打气次数</strong></td>
				<td><input type="text" class="text" name="encourmax" value="<?php echo $baseHorse[6]; ?>"/>一场比赛最多打气次数
				</td>
			</tr>
			<tr>
				<td width="150"><strong>打气ID及军饷</strong></td>
				<td>打气ID,所需军饷(一行 不要多余空格)<br/>
					<textarea name="encourage" cols='42' rows="10"><?php
						foreach ($baseHorse[7] as $encourId => $desc) {
							echo "$encourId,$desc" . "\n";
						}
						?></textarea>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>第一名奖励ID</strong></td>
				<td><input type="text" class="text" name="firstaward" value="<?php echo $baseHorse[8]; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>间隔时间秒数</strong></td>
				<td><input type="text" class="text" name="interval" value="<?php echo $baseHorse[9][0]; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>播放次数</strong></td>
				<td><input type="text" class="text" name="playtimes" value="<?php echo $baseHorse[9][1]; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>参赛马匹对应武器ID</strong></td>
				<td><input type="text" class="text" name="horseweapon"
				           value="<?php echo implode(",", $baseHorse[11]); ?>"/></td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>

		</table>
	</form>
</div>
