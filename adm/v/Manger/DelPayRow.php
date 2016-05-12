<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
);
?>
<div class="top-bar">
	<h1>清除特定用户充值记录</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<?php echo isset($pageData['tip']) ? $pageData['tip'] : ''; ?>
	<form action="?r=Manger/DelPayRow" method="post">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100px">城市ID</td>
				<td style="float:left;">
					<input type="text" name="city_id" id="city_id" value="">
					<br>
					<input id="sub" type="submit" value="提交 ">
				</td>
			</tr>
		</table>
	</form>
</div>