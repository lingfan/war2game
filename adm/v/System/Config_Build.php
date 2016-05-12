<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'建筑配置' => '',
);
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>建筑配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigBuild&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<?php


			$a[1] = '{"1":{"25_14":15},"2":{"40_16":1},"4":{"44_13":1},"6":{"15_22":1,"12_25":1,"40_23":1,"36_25":1,"15_27":1,"18_30":1,"19_25":1,"22_28":1,"27_30":1,"32_27":1,"36_29":1,"39_27":1,"9_22":1,"43_25":1,"31_32":1},"3":{"35_12":1},"5":{"12_20":1,"7_16":1},"7":{"24_9":1},"8":{"15_14":1},"9":{"20_12":1},"10":{"18_8":1},"11":{"10_11":1},"12":{"29_5":1}}';

			$a[2] = '{"1":{"19_10":15},"2":{"12_9":1},"3":{"26_8":1},"4":{"17_7":1},"5":{"5_18":1,"9_21":1},"7":{"18_20":1},"8":{"27_21":1},"9":{"12_17":1},"10":{"30_11":1},"11":{"31_18":1},"12":{"8_14":1},"6":{"34_14":1,"38_12":1,"27_27":1,"31_30":1,"38_17":1,"42_14":1,"34_23":1,"15_24":1,"24_29":1,"31_25":1,"35_27":1,"19_26":1,"42_24":1,"39_25":1,"37_21":1,"41_19":1}}';

			$a[3] = '{"1":{"21_9":11},"6":{"11_9":9,"8_11":1,"14_12":1,"11_13":1,"21_21":11,"17_19":11,"17_23":1,"13_21":1,"30_26":1,"33_24":1,"26_28":1,"28_21":1,"31_19":1,"25_23":1,"21_25":1},"3":{"36_16":1},"4":{"35_20":4},"5":{"16_6":1,"19_8":11},"2":{"40_19":4},"8":{"29_7":8},"10":{"34_6":1},"12":{"38_8":9},"9":{"34_10":1},"7":{"11_16":2},"11":{"22_3":7}}';


			foreach (T_App::$map as $i => $name):

				/**
				 * $arr = array();
				 * $tmpBuildList = json_decode($a[$i], true);
				 * ksort($tmpBuildList);
				 * foreach ($tmpBuildList as $bid => $binfo)
				 * {
				 * foreach ($binfo as $bpos => $blev)
				 * {
				 * $arr[] = array($bpos, $bid, 1);
				 * }
				 * }
				 **/

				$arr = isset($baselist['build_open'][$i]) ? $baselist['build_open'][$i] : array();
				$tmp = array();
				foreach ($arr as $val) {
					$tmp[] = implode(",", $val);
				}
				?>
				<tr>

					<td><strong><?php echo $name; ?></strong></td>
					<td>
						位置,建筑ID,开放等级(城市中心)
						<br>
						<textarea name="build_open[<?php echo $i; ?>]" cols="50"
						          rows="10"><?php echo implode("\n", $tmp); ?></textarea>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
<script>
	$('#rndpwd').click(function () {

		var x = "0123456789qwertyuioplkjhgfdsazxcvbnm";
		var tmp = "";
		for (var i = 0; i < 33; i++) {
			tmp += x.charAt(Math.ceil(Math.random() * 100000000) % x.length);
		}
		$('#pwdkey').val(tmp);
	});
</script>
