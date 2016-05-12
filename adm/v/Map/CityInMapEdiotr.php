<?php
$resUrl = M_Config::getSvrCfg('server_res_url');
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'城内地图编辑器' => 'map/cityInMapEdiotr',
);

$info = isset($pageData['info']) ? $pageData['info'] : '';
?>

<div class="top-bar">
	<h1>城内地图编辑器</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<form action='?r=Map/CityInMapEdiotr' method='post'>
	<select name='id'>
		<option value='1'>亚洲</option>
		<option value='2'>欧洲</option>
		<option value='3'>非洲</option>
	</select>
	<select name='level'>
		<option value='1'>1 级</option>
		<option value='2'>2 级</option>
		<option value='3'>3 级</option>
		<option value='4'>4 级</option>
		<option value='5'>5 级</option>
	</select>
	<input type="submit" value="提交"/>
</form>
<?php
$arr = array('1' => '亚洲', '2' => '欧洲', '3' => '非洲');
$id = 1;
$level = 1;
if (!empty($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
}
if (!empty($_REQUEST['level'])) {
	$level = $_REQUEST['level'];
}
echo "编辑 <font style='color:red'>  $arr[$id] $level 级 </font> 城内地图数据 <hr />";
$url = $resUrl . 'imgs/cityin/m' . $id . '_lv' . $level . '.jpg';
$url = urlencode($url);
$postUrl = ROOT_URL . '/adm.php?r=admin/UpdateCityMapBlock';
$postUrl = urlencode($postUrl);
$getUrl = ROOT_URL . '/adm.php?r=admin/GetCityMapBlock';
$getUrl = urlencode($getUrl);
$vars = "id={$id}&level={$level}&url={$url}&postUrl={$postUrl}&getUrl={$getUrl}";
?>

<script>
	$(document).ready(
		function () {

			$('#editordiv').flash({
				swf: '<?php echo ROOT_URL;?>styles/adm/images/adm_flash/city_map_editor.swf?<?php echo $vars; ?>',
				width: 800,
				height: 600
			});

		}
	);
</script>

<div class="table" id="editordiv">

</div>