<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
);
$baselist = $pageData['baselist'];
?>
<div class="top-bar">
	<h1>爬楼配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigFloor&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180"><strong>开放时间</strong></td>
				<td><input type="text" class="text" name="floor_open"
				           value="<?php echo implode(",", $baselist['floor_open']); ?>"/>(开始时间,关闭时间)
				</td>
			</tr>
			<tr>
				<td><strong>爬楼消费</strong></td>
				<td><input type="text" value="<?php echo implode(',', $baselist['floor_cost']); ?>" name="floor_cost"
				           class="text">(免费次数,初始花费,累加值,最大花费,最大次数)
				</td>
			</tr>
			<tr>
				<td><strong>消耗系数</strong></td>
				<td><input type="text" value="<?php echo $baselist['floor_rate']; ?>" name="floor_rate" class="text">
				</td>
			</tr>

			<tr>
				<td width="180"><strong>爬楼数据</strong></td>
				<td>
					<table>
						<tr>
							<td colspan="5">关卡(npcId,地图,奖励ID)</td>
						</tr>
						<tr>
							<td>编号:<br>
								<?php $size = count($baselist['floor_data'][1]); ?>
								<textarea name="no" cols="1"
								          rows="<?php echo $size; ?>"><?php echo implode("\n", range(1, $size)); ?></textarea>
							</td>
							<?php
							for ($i = 1; $i <= 4; $i++):

								$tmpArr = $baselist['floor_data'][$i];
								$t = array();
								foreach ($tmpArr as $key => $v) {
									$t[] = implode(",", $v);
								}
								$dataval = implode("\n", $t);

								$num = count($baselist['floor_data'][$i]);
								?>
								<td>级别<?php echo $i; ?><br><textarea name="floor_data[<?php echo $i; ?>]" cols="20"
								                                     rows="<?php echo $size; ?>"><?php echo $dataval; ?></textarea>
								</td>
							<?php endfor; ?>
						</tr>
					</table>
					<br>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
