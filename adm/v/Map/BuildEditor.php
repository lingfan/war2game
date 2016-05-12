<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
$pageData = B_View::getVal('pageData');
//var_dump($pageData);
$urlArr = array(
	'首页' => 'index/index',
	'城内建筑编辑器' => 'map/buildEditor',
);


$info = isset($pageData['info']) ? $pageData['info'] : '';

$zone = $pageData['zhou'];
$area = json_decode($pageData['info']['area'], true);
if ($zone == T_App::MAP_ASIA) {
	$name = 'swf/buildres/asia.swf';
} else if ($zone == T_App::MAP_EUROPE) {
	$name = 'swf/buildres/europ.swf';
} else if ($zone == T_App::MAP_AFRICA) {
	$name = 'swf/buildres/afric.swf';
}

$postUrl = urlencode('adm.php?r=Build/SetArea&zhou=' . $zone);
$resBuildUrl = urlencode($resUrl . $name);
?>


<div class="top-bar">
	<h1>城内建筑编辑器</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<script>
	function changeId() {
		//var nowURL=document.location.href;
		//alert(nowURL);
		var id = document.getElementById('id').value;
		var zhou = document.getElementById('zhou').value;

		var urlStr = '<?php echo ROOT_URL;?>adm.php?r=Map/BuildEditor&id=' + id + '&zhou=' + zhou;
		location = urlStr;
	}
</script>
<div class="select-bar">
	<form action='?r=Map/BuildEditor' method='post'>
		请选择要编辑建筑&nbsp;
		<select id='id' name='id' onchange="changeId();">
			<?php foreach ($pageData['list'] as $key => $val) { ?>
				<option value='<?php echo $val['id']; ?>' <?php if ($val['id'] == $pageData['info']['id']) {
					echo "selected='selected'";
				} ?>><?php echo $val['name']; ?></option>
			<?php } ?>
		</select>&nbsp;&nbsp;
		请选择建筑所在洲&nbsp;
		<select name="zhou" id="zhou" onchange="changeId();" style="width: 50px;">
			<?php foreach (T_App::$map as $key => $val) { ?>
				<option value="<?php echo $key; ?>" <?php if ($key == $zone) {
					echo "selected='selected'";
				} ?>><?php echo $val; ?></option>
			<?php } ?>
		</select>
	</form>
</div>


<script>
	$(document).ready(
		function () {
			$('#editordiv').flash({
				id: 'build_editor',
				swf: '<?php echo ROOT_URL;?>styles/adm/images/adm_flash/build_editor.swf?res=<?php echo $resBuildUrl; ?>',
				width: 800,
				height: 600,
				flashvars: { id: '<?php echo $pageData['info']['id'];?>', area: '<?php echo $area[$zone];?>', postUrl: '<?php echo $postUrl;?>' }
			});
		}
	);


</script>


<div class="table" id="editordiv">

</div>




