<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'基础数据' => 'Base/Index',
	'运营商列表' => 'Base/ConsumerList',
);
?>
<script type="text/javascript">
	function del(id) {
		var res = confirm('此操作不可返回，确认删除?');
		if (res == true) {
			$.post('?r=Base/ConsumerDel', {id: id}, function (txt) {
				$('#msg').css('display', '')
				$('#msg').html(txt.msg);
				if (txt.flag == 1) {
					$('#list #' + id).remove();
				}
				setTimeout("$('#msg').css('display', 'none')", 3000);
			}, 'json');
		}
	}
</script>
<div class="top-bar">
	<h1>运营商管理</h1>

	<div class="breadcrumbs"><a href="#">基础数据</a> / <a href="#">运营商列表</a> <span id="msg"
	                                                                            style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th style="width: 30px;">ID</th>
			<th>运营商账号</th>
			<th>运营商密码</th>
			<th>运营商域名</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['name']; ?></td>
				<td><?php echo $val['key']; ?></td>
				<td><?php echo $val['domain']; ?></td>
			</tr>
		<?php } ?>
	</table>
</div>