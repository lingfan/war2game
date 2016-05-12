<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'代码更新' => '',
);
?>


<div class="top-bar">
	<iframe name="iframe" style="display: none;"></iframe>
	<h1>代码更新
	</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
		<br>
	</div>
</div>

<div class="table">
	<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="r" value="Server/UploadCode"/>

		<table class="listing form">

			<tr>
				<td style="width: 100px;">ZIP代码：</td>
				<td>
					<input type="file" name="zipfile">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value="提交 "></td>
			</tr>
		</table>
	</form>
</div>
