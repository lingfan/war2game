<?php
$basecfg = M_Config::getVal();
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
);
?>
<div class="top-bar">
	<h1>GM列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=Manger/GMUser" method="post" target="iframe">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100px">GM用户列表</td>
				<td style="float:left;">
					username|password
					<br>
					<textarea name="gm_user" id='gm_user' cols='30'
					          rows='10'><?php echo $basecfg['gm_user']; ?></textarea>
					<br>
					<input id="sub" type="submit" value="提交 ">
				</td>
			</tr>

		</table>
	</form>
</div>