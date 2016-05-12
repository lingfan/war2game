<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'活动配置' => 'System/ConfigActive',
);
?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>基础配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigActive&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td><strong>3月8日活动 id#3</strong></td>
				<td>
					<table class="listing form" cellpadding="0" cellspacing="0">
						<?php
						$basecfg = M_Config::getVal();
						$activeList = $basecfg['active_list'];
						$award38 = isset($activeList['3']) ? $activeList['3'] : array();
						?>
						<tr>
							<td>起始时间</td>
							<td>
								开始日期：<input type="text" name="active_list[3][0]" class="Wdate"
								            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
								            value="<?php echo isset($award38[0]) ? $award38[0] : ''; ?>">
								截止日期：<input type="text" name="active_list[3][1]" class="Wdate"
								            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
								            value="<?php echo isset($award38[1]) ? $award38[1] : ''; ?>">
							</td>
						</tr>
						<tr>
							<td>奖励内容</td>
							<td>
								奖励ID 用,分割 (1,2,4) 表示3个军官对应的奖励<br>
								<input type="text" class="text" name="active_list[3][2]"
								       value="<?php echo isset($award38[2]) ? $award38[2] : ''; ?>"/>
								<br>
								<?php
								$txt = '';
								$arr = explode(',', $award38[2]);
								foreach ($arr as $id) {
									$txt .= $id . "=>" . B_Utils::awardText($id);
								}
								echo $txt;
								?>
							</td>

						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>

		</table>
	</form>
</div>
