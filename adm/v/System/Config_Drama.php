<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'剧情任务奖励配置' => 'System/ConfigDrama',
);

$dramaAward = $pageData['info'];
$dramaNum = $pageData['dramaNum'];
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>剧情任务奖励配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<form action="?r=System/ConfigDrama&act=edit" method="post">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">服务器配置信息 重要内容！请勿随意更改！！！</th>
			</tr>

			<tr>
				<td width="150"><strong>章节编号(从1开始)</strong></td>
				<td>奖励内容(填奖励ID N个战役填N个值 用逗号,隔开)</td>
			</tr>

			<?php for ($chap = 1; $chap <= $dramaNum; $chap++) { ?>
				<tr>
					<td width="150"><strong>第 <?php echo $chap; ?> 章</strong></td>
					<td><input type="text" class="text" style="width:500px" name="drama_award[<?php echo $chap; ?>]"
					           value="<?php echo implode(',', $dramaAward[$chap]); ?>"/></td>
				</tr>
			<?php } ?>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
