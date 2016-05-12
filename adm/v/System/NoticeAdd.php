<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统公告列表' => '',
);
$baselist = $pageData['baselist'];
?>
<script type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">

	function sb() {
		var id = $('#id').val();
		var title = document.getElementById('title').value;
		var start = $('#start').val();
		var end = $('#end').val();
		var interval = $('#interval').val();

		var act = id < 1 ? 'add' : 'set';
		$.post('?r=System/NoticeAdd', {id: id, title: title, start: start, end: end, interval: interval, act: act}, function (txt) {
			$('#msg').css('display', '')
			$('#msg').html(txt.msg);
			setTimeout("$('#msg').css('display', 'none')", 3000);
		}, 'json');
	}

</script>
<div class="top-bar">
	<h1>公告管理</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>
			公告</a> <span id="msg"
	                     style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<form action="?r=System/NoticeAdd" id="addForm" name="addForm">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>消息内容：<input type="hidden" id="id" name="id"
				                value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>"></td>
				<td><textarea name="title" rows="" cols="" style="width: 600px; height: 100px;"
				              id="title"><?php if (isset($pageData['info']['title'])) echo $pageData['info']['title']; ?></textarea>
				</td>
			</tr>
			<tr>
				<td>开始时间</td>
				<td><input name="start" id="start" type="text" class="Wdate"
				           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
				           value="<?php if (isset($pageData['info']['start_time'])) echo date('Y-m-d H:i:s', $pageData['info']['start_time']); ?>"/>
				</td>
			</tr>
			<tr>
				<td>结束时间</td>
				<td><input name="end" id="end" type="text" class="Wdate"
				           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
				           value="<?php if (isset($pageData['info']['end_time'])) echo date('Y-m-d H:i:s', $pageData['info']['end_time']); ?>">
				</td>
			</tr>
			<tr>
				<td>间隔时间</td>
				<td><input name="interval" id="interval" type="text"
				           value="<?php echo isset($pageData['info']['interval_time']) ? $pageData['info']['interval_time'] : 3600; ?>">(秒)
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="button" value=" 保 存 " onclick="sb()"></td>
			</tr>
		</table>
	</form>
</div>
