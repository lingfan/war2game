<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'战场地图编辑器' => 'Map/WarMapEditor',
);

$info = isset($pageData['info']) ? $pageData['info'] : '';
?>

<div class="top-bar">
	<h1>战场地图编辑器</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<script>
	$(document).ready(
		function () {
			$('#editordiv').flash({
				swf: '<?php echo ROOT_URL;?>styles/adm/images/adm_flash/war_map_editor.swf',
				flashvars: {
					apiurl: '<?php echo ROOT_URL;?>adm.php',
				},
				width: 800,
				height: 600
			});
		}
	);
</script>

<div class="table" id="editordiv">

</div>