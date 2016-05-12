<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'战斗' => 'index/index',
	'副本地图编辑' => '#',
);
?>
<script type="text/javascript" src="styles/adm/js/swfobject.js"></script>
<script type="text/javascript" src="styles/adm/js/CopyMapEditor.js"></script>

<script type="text/javascript">

	CopyScene.setContainer("cnt");
	//设置当前要编辑的swf地图文件地址
	CopyScene.loadRes("<?php echo $resUrl; echo 'swf/battle_map_view/' . $pageData['chapter']['id'] . '/map' .  $pageData['campaign']['campaign_no'];?>.swf");
	//设置地图按钮点击后的js回调函数名
	CopyScene.setCallBackFun("onclk");


	function onclk(id) {
		var campaignId = "<?php echo $pageData['campaign']['id'];?>";

		$.get("?r=War/GetGuanqiaId&id=" + campaignId + "&map_id=" + id, function (data) {
			var op = document.getElementById(data);
			op.selected = 'selected';
		});
		document.getElementById('map_mark').value = id;
		document.getElementById('tc').style.display = '';
	}
	function yinc() {
		document.getElementById('tc').style.display = 'none';
	}
</script>
<div class="top-bar">

	<h1>战斗相关</h1>

	<div class="breadcrumbs"><a href="#">战斗相关</a> / <a href="?r=War/WarFbCateList">副本章节列表</a> / <a href="#">副本地图编辑</a>
        <span id="msg"
              style="color: white;background-color: green; font-weight: bold;padding-left: 10px;padding-right:10px; display: none"></span>
	</div>
</div>
<div class="table" style="height: 600px;">
	<div id="cnt" style="width:100%;height:400px;display:block;">
	</div>
</div>

<div id="tc" style="position: absolute; top: 350px; left: 550px; background-color: lightgray; display: none;">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=War/WarFbMapEdit&id=<?php echo $pageData['campaign']['id']; ?>" method="post" target="iframe">
		章节：<input type="text" name="zj_name" value="<?php echo $pageData['chapter']['name']; ?>"
		          style="border: 0px; background-color: lightgray;" readonly="readonly">
		<br>战役：<input type="text" name="zy_name" value="<?php echo $pageData['campaign']['name']; ?>"
		              style="border: 0px; background-color: lightgray;" readonly="readonly">
		<br>
		标记<input type="text" id="map_mark" name="map_mark" value="" style="border: 0px; background-color: lightgray;"
		         readonly="readonly">
		<br>
		关卡设置：
		<?php
		$gqArr = json_decode($pageData['campaign']['checkpoint_data'], true);
		?>
		<select name="guanqia" id="guanqia">
			<?php foreach ($gqArr as $key => $val) { ?>
				<option value="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo "({$key}){$val[0]}"; ?></option>
			<?php } ?>
		</select>
		<br>
		<br>

		<div style="float: right;">
			<input type="submit" value="保存">
			<input type="button" value="关闭" onclick="yinc();">
		</div>
	</form>
</div>
  
