<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'装备列表' => '',
);
?>

<div class="top-bar">

	<h1>地图编辑器</h1>

	<div class="breadcrumbs"><a href="#">首页</a> / <a href="?r=Map/WarMapSecneList">战场装饰物列表</a> / <a
			href="#"><?php echo isset($pageData['info']['id']) ? '修改' : '添加' ?>战场装饰物</a> <span id="msg"
	                                                                                           style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=Map/WarMapSecneEdit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>标记物名称：<input type="hidden" id="id" name="id"
				                 value="<?php if (isset($pageData['info']['id'])) echo $pageData['info']['id']; ?>">
				</td>
				<td>
					<input type="text" name="name" id="name"
					       value="<?php echo isset($pageData['info']['name']) ? $pageData['info']['name'] : ''; ?>">
				</td>
			</tr>

			<tr>
				<td>图标：</td>
				<td>
					<input type="text" name="face_id" id="face_id"
					       value="<?php echo isset($pageData['info']['face_id']) ? $pageData['info']['face_id'] : ''; ?>">
				</td>
			</tr>


			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value=" 保 存 "></td>
			</tr>
		</table>
	</form>
</div>
