<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
);
?>


<div class="top-bar">
	<h1>登录操作</h1>
</div>

<div class="table">
	<form action="?r=Index/CheckLogin" method="post">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<th colspan="2">登录操作</th>
			</tr>
			<tr>
				<td style="width: 100px;">用户名称</td>
				<td style="text-align: left;">
					<input type="text" name="username" value="">
				</td>
			</tr>
			<tr>
				<td style="width: 100px;">用户密码</td>
				<td style="text-align: left;">
					<input type="password" name="password" value="">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td style="text-align: left;">
					<input type="submit" value="登录">
				</td>
			</tr>
		</table>
	</form>

</div>