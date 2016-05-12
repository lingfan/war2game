<?php
define('IN_DEV', 1);
include('../common.php');
include('auth.php');

if (!empty($_POST['city'])) {
	$cityres = $_POST['cityres'];
	$city = $_POST['city'];
	if ($city['id'] > 0) {
		$cityId = $city['id'];
		unset($city['id']);

		$objPlayer = new O_Player($cityId);

		$city['last_fb_no'] = M_Formula::calcFBNo($city['last_fb_no'][0], $city['last_fb_no'][1], $city['last_fb_no'][2]);
		foreach ($city as $k => $v) {
			$objPlayer->City()->$k = $v;
		}

		foreach ($cityres as $k => $v) {
			$objPlayer->Res()->incr($k, $v, true);
		}
		$objPlayer->save();

		echo $ret ? "城市 $cityId 成功 <br />" : "城市 $cityId 失败 <br />";
		if (!empty($_POST['army'])) {
			$ret = M_Army::setCityArmy($cityId, $_POST['army']);
			echo $ret ? "兵种 $cityId 成功 <br />" : "兵种 $cityId 失败 <br />";
		}

		if (!empty($_POST['breakout'])) {
			M_BreakOut::updateCityBreakOut($cityId, $_POST['breakout'], true);
			echo $ret ? "突击 $cityId 成功 <br />" : "突击 $cityId 失败 <br />";
		}

		if (!empty($_POST['tech'])) {
			$tmp = array();
			foreach ($_POST['tech'] as $id => $lv) {
				$tmp[$id] = intval($lv);
			}

			$up['tech_list'] = json_encode($tmp);
			$ret = M_Extra::setInfo($cityId, $up);
			echo $ret ? "科技 $cityId 成功 <br />" : "科技 $cityId 失败 <br />";
		}

		if (!empty($_POST['build'])) {
			$tmp = array();
			foreach ($_POST['build'] as $id => $val) {
				foreach ($val as $pos => $lv) {
					$tmp[$id][$pos] = intval($lv);
				}
			}
			$up['build_list'] = json_encode($tmp);
			$ret = M_Extra::setInfo($cityId, $up);
			echo $ret ? "建筑 $cityId 成功 <br />" : "建筑 $cityId 失败 <br />";
		}

	}

	exit;

} else {
	$cityId = isset($_GET['city_id']) ? intval($_GET['city_id']) : 0;
	if ($cityId > 0) {
		$cityInfo = M_City::getInfo($cityId);
		if ($cityInfo['id']) {
			$tmp = array();
			$objPlayer = new O_Player($cityId);
			$cityRes = $objPlayer->Res()->get();
			$baseTech = M_Base::techAll();
			$techInfo = $objPlayer->Tech()->get();
			$baseBuild = M_Base::buildAll();

			$buildInfo = $objPlayer->Build()->get();

			$armyInfo = $objPlayer->Army()->toData();
			$cityBout = M_BreakOut::getCityBreakOut($cityId);
		} else {
			exit;
		}

	} else {
		exit;
	}
}

?>

<div class="top-bar">
	<h2>调城市</h2>
</div>
<div class="table">
	<form action="" method="post">
		<table border="1px">
			<tr>
				<td width="150" style="text-align: right">城市名</td>
				<td><?php echo $cityInfo['nickname']; ?></td>
			</tr>
			<tr>
				<td style="text-align: right">城市信息</td>
				<td><input type="hidden" class="text" name="city[id]"
				           value="<?php echo $cityInfo['id']; ?>"/> 金钱<input type="text"
				                                                             size="10" class="text" name="cityres[gold]"
				                                                             value="<?php echo $cityRes['gold']; ?>"/><br>
					食物<input type="text"
					         size="10" class="text" name="cityres[food]"
					         value="<?php echo $cityRes['food']; ?>"/><br> 石油<input type="text"
					                                                                size="10" class="text"
					                                                                name="cityres[oil]"
					                                                                value="<?php echo $cityRes['oil']; ?>"/><br>
					军饷<input type="text"
					         size="10" class="text" name="city[mil_pay]"
					         value="<?php echo $cityInfo['mil_pay']; ?>"/><br> 礼券<input
						type="text" size="10" class="text" name="city[coupon]"
						value="<?php echo $cityInfo['coupon']; ?>"/><br> 活力<input
						type="text" size="10" class="text" name="city[energy]"
						value="<?php echo $cityInfo['energy']; ?>"/><br> 军令<input
						type="text" size="10" class="text" name="city[mil_order]"
						value="<?php echo $cityInfo['mil_order']; ?>"/><br> 威望<input
						type="text" size="10" class="text" name="city[renown]"
						value="<?php echo $cityInfo['renown']; ?>"/><br> 功勋<input
						type="text" size="10" class="text" name="city[mil_medal]"
						value="<?php echo $cityInfo['mil_medal']; ?>"/><br> <?php $fbno = M_Formula::calcParseFBNo($cityInfo['last_fb_no']); ?>
					副本 章节<input type="text" size="3" class="text"
					            name="city[last_fb_no][]" value="<?php echo $fbno[0]; ?>"/> 战役<input
						type="text" size="3" class="text" name="city[last_fb_no][]"
						value="<?php echo $fbno[1]; ?>"/> 关卡<input type="text" size="3"
					                                               class="text" name="city[last_fb_no][]"
					                                               value="<?php echo $fbno[2]; ?>"/><br>
				</td>
			</tr>
			<tr>
				<td style="text-align: right">突击</td>
				<td>可打ID串<input type="text" size="20" class="text"
				                name="breakout[breakout_pass]"
				                value="<?php echo $cityBout['breakout_pass']; ?>"/>(ID组成逗号隔开串)<br>
					当前积分<input type="text" size="10" class="text"
					           name="breakout[point]" value="<?php echo $cityBout['point']; ?>"/><br>
				</td>
			</tr>
			<tr>
				<td style="text-align: right">兵种信息</td>
				<td>步兵(<?php echo $armyInfo[1]['level']; ?>)熟练度<input type="text"
				                                                      size="10" class="text" name="army[1][exp]"
				                                                      value="<?php echo $armyInfo[1]['exp']; ?>"/><br>
					炮兵(<?php echo $armyInfo[2]['level']; ?>)熟练度<input
						type="text" size="10" class="text" name="army[2][exp]"
						value="<?php echo $armyInfo[2]['exp']; ?>"/><br> 装甲(<?php echo $armyInfo[3]['level']; ?>
					)熟练度<input
						type="text" size="10" class="text" name="army[3][exp]"
						value="<?php echo $armyInfo[3]['exp']; ?>"/><br> 航空(<?php echo $armyInfo[4]['level']; ?>
					)熟练度<input
						type="text" size="10" class="text" name="army[4][exp]"
						value="<?php echo $armyInfo[4]['exp']; ?>"/><br>
				</td>
			</tr>
			<tr>
				<td style="text-align: right">科技信息</td>
				<td><?php foreach ($techInfo as $tid => $lv) {
						if (!empty($baseTech[$tid]['name'])) {
							?> <?php echo $baseTech[$tid]['name']; ?><input
							type="text" size="10" class="text" name="tech[<?php echo $tid ?>]"
							value="<?php echo $lv; ?>" /><br> <?php
						}
					}?></td>
			</tr>
			<tr>
				<td style="text-align: right">建筑信息</td>
				<td><?php
					foreach ($buildInfo as $bid => $posArr):?> <?php echo $baseBuild[$bid]['name']; ?>
						<?php foreach ($posArr as $pos => $lv): ?> <?php echo $pos; ?> <input
							type="text" size="10" class="text"
							name="build[<?php echo $bid ?>][<?php echo $pos; ?>]"
							value="<?php echo $lv; ?>"/> <?php endforeach; ?> <br> <?php endforeach; ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="设置"/>
				</td>
			</tr>
		</table>
	</form>
</div>
