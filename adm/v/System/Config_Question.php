<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'问答题库' => 'Base/QuestionImport',
);
$baselist = $pageData['baselist'];
?>
<div class="top-bar">
	<h1>问答配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigQuestion&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="180"><strong>开放时间(开始时间,关闭时间)</strong></td>
				<td><input type="text" class="text" name="question_open"
				           value="<?php echo implode(",", $baselist['question_open']); ?>"/></td>
			</tr>

			<tr>
				<td width="180"><strong>答对分数</strong></td>
				<td><input style="width: 50px;" type="text" class="text" name="question_point"
				           value="<?php echo $baselist['question_point']; ?>"/></td>
			</tr>
			<tr>
				<td width="180"><strong>每题时间(秒)</strong></td>
				<td><input style="width: 50px;" type="text" class="text" name="question_time"
				           value="<?php echo $baselist['question_time']; ?>"/></td>
			</tr>
			<tr>
				<td width="180"><strong>每轮问题数量</strong></td>
				<td><input style="width: 50px;" type="text" class="text" name="question_num"
				           value="<?php echo $baselist['question_num']; ?>"/></td>
			</tr>
			<tr>
				<td><strong>重新答题</strong></td>
				<td><input type="text" value="<?php echo implode(',', $baselist['question_cost']); ?>"
				           name="question_cost" class="text">(免费次数,初始花费,累加值,最大值)
				</td>
			</tr>

			<tr>
				<td><strong>积分兑换道具</strong></td>
				<td>(道具ID,消耗积分)<br>
					<?php
					$tmpArr = $baselist['question_props'];
					$t = array();
					foreach ($tmpArr as $key => $v) {
						$t[] = implode(",", array($key, $v));
					}
					$str = implode("\n", $t);
					?>
					<textarea name="question_props" cols="50" rows="10"><?php echo $str; ?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
