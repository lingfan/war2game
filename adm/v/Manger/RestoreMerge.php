<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
);
?>
<div class="top-bar">
	<h1>合服重新迁移</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>


<div class="table">
	<form action="?r=Manger/RestoreMerge" method="post">
		<table class="listing" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100px">重新迁移的用户</td>
				<td style="float:left;">
					merge_80031_ww2,战神之风杀
					<br>
					<textarea name="data" id='data' cols='30' rows='10'></textarea>
					<br>
					是否删除
					<input type="checkbox" name="del" id='del'>
					<br>
					<input id="sub" type="submit" value="提交 ">
				</td>
			</tr>

		</table>
	</form>
</div>

<div class="table">
	<?php
	echo "合服" . ($pageData['mergeConf']['status'] ? '开启' : '关闭');
	echo "<br>";
	echo "合服列表";
	foreach ($pageData['mergeConf']['db']['database'] as $k => $v) {
		echo "[" . $k . '=>' . $v . '],';
	}
	echo "<br>";
	$info = M_Props::baseInfo($pageData['mergeConf']["compensate_props_id"]);
	echo "玩家补偿ID:" . $pageData['mergeConf']["compensate_props_id"] . "=>{$info['name']}<br>";
	$info = M_Props::baseInfo($pageData['mergeConf']["union_props_id"]);
	echo "联盟补偿ID:" . $pageData['mergeConf']["union_props_id"] . "=>{$info['name']}<br>";
	?>
</div>