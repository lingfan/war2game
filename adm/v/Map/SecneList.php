<?php
$pageData = B_View::getVal('pageData');
$name = ($pageData['type'] == 'war') ? '战场装饰物列表' : '城外装饰物列表';
$urlArr = array(
	'地图编辑器' => '',
	$name => '',
);
?>

<div class="top-bar">
	<h1>地图编辑器</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div style="clear: both;"></div>
<div>
	<ul>
		<?php foreach ($pageData['list'] as $key => $val) { ?>
			<li style="float:left;height:150px;width:100px;margin-right:10px;overflow:hidden;padding: 11px 0 0;text-align: center;">
				<img width="100px" height="100px" src="<?php echo $val['face_id']; ?>"><br>
				<?php echo $val['name']; ?><br>
			</li>
		<?php } ?>
	</ul>
</div>
<div style="clear: both;"></div>