<?php
$basecfg = M_Config::getVal();
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
);
?>
<div class="top-bar">
	<h1>调试IP列表</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=Manger/DebugIp" method="post" target="iframe">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100px">ip列表</td>
				<td style="float:left;"><?php echo B_Utils::getIp(); ?><br>
					192.168.0.1<br>
					192.168.*.*
					<br>
					<textarea name="debug_ip" id='debug_ip' cols='30'
					          rows='10'><?php echo $basecfg['debug_ip']; ?></textarea>
					<br>
					<input id="sub" type="submit" value="提交 ">
				</td>
			</tr>

		</table>
	</form>
</div>