<?php
include('../common.php');
$args = array(
	'num' => FILTER_SANITIZE_NUMBER_INT,
	'mode' => FILTER_SANITIZE_NUMBER_INT,
);

$formVals = filter_var_array($_REQUEST, $args);

$out = json_encode(array('ret' => 1002, 'msg' => '请先登录'));
if (empty($formVals['num'])) {
	echo json_encode($out);
	exit;
}

$cityId = M_Auth::myCid();
$objPlayer = new O_Player($cityId);
$cityInfo = $objPlayer->getCityBase();
if (!empty($cityInfo['user_id'])) {
	$userId = $cityInfo['user_id'];
	$qqInfo = M_Qq::getQQLive($userId);
	if ($qqInfo) {
		$basecfg = M_Config::getVal();
		$appid = $basecfg['appid'];
		$appkey = $basecfg['appkey'];
		$openid = $qqInfo['openid'];
		$openkey = $qqInfo['openkey'];

		//@todo test qqlogin


		$propsId = isset($formVals['num']) ? $formVals['num'] : '';
		$mode = isset($formVals['mode']) ? $formVals['mode'] : '';

		if (!empty($propsId)) {
			$params = array(
				'openid' => $openid,
				'openkey' => $openkey,
				'pf' => $qqInfo['pf'],
				'pfkey' => $qqInfo['pfkey'],
				'ts' => time(),
				'zoneid' => intval($qqInfo['serverid']),
			);

			$url = M_Config::getSvrCfg('server_res_url') . "imgs/";
			if ($mode == 0) {
				//商城里的军饷包
				$milpayArr = array(
					1 => 100,
					2 => 200,
					3 => 500,
					4 => 1000,
					5 => 2000,
					6 => 3000,
					7 => 5000,
					8 => 10000,
					9 => 20000,
				);

				$num = 100;
				$no = 1;
				if (isset($milpayArr[$propsId])) {
					$num = $milpayArr[$propsId];
					$no = $propsId;
				}

				$params['payitem'] = "G001*{$num}*1";
				$params['amt'] = $num;
				$params['goodsmeta'] = base64_encode("{$num}军饷包*{$num}");
				$params['goodsurl'] = $url . "props/qqcoupe{$no}.jpg";
			} else if ($mode == 1) {
				//VIP图纸
				$info = M_Props::baseInfo($propsId);
				if (!empty($info['id'])) {
					$arrPrice = json_decode($info['price'], true);
					$price = isset($arrPrice[T_App::MILPAY]) ? $arrPrice[T_App::MILPAY] : 1;
					$propsNo = str_pad($propsId, 3, "0", STR_PAD_LEFT);
					$name = str_replace('图纸', '', $info['name']);
					$params['payitem'] = "P{$propsNo}*{$price}*1";
					$params['amt'] = $price;
					$params['goodsmeta'] = base64_encode("{$name}*1");
					$faceId = !empty($info['face_id']) ? $info['face_id'] : $info['id'];
					$params['goodsurl'] = $url . "props/{$faceId}.jpg";
				}
			} else if ($mode == 2) {
				//VIP装备
				$vipLevel = $cityInfo['vip_level'];
				$equipInfo = M_Vip::getShopItemInfo($vipLevel, M_Vip::SHOP_EQUI, $propsId);
				$baseInfo = M_Equip::baseInfo($propsId);
				if (!empty($equipInfo[2]) && !empty($baseInfo['name'])) {
					$price = $equipInfo[2];
					$propsNo = str_pad($propsId, 3, "0", STR_PAD_LEFT);

					$params['payitem'] = "E{$propsNo}*{$price}*1";
					$params['amt'] = $price;
					$params['goodsmeta'] = base64_encode("{$baseInfo['name']}*1");
					$params['goodsurl'] = $url . "equip/{$baseInfo['face_id']}.jpg";
				}
			} else if ($mode == 3) {
				//商城里除军饷包外所有东东
				$basemallinfo = M_Mall::getBaseInfoById($propsId); //商城基础数据
				$item_id = intval($basemallinfo['item_id']);
				$arrPrice = json_decode($basemallinfo['price'], true); //商城物品价格数组
				$price = $arrPrice[1];
				$params['amt'] = $price;
				$propsNo = str_pad($item_id, 3, "0", STR_PAD_LEFT);

				if (!empty($arrPrice[1])) {
					switch ($basemallinfo['item_type']) {
						case M_Mall::ITEM_PROPS :
							$baseInfo = M_Props::baseInfo($item_id); //道具
							$name = str_replace('图纸', '', $baseInfo['name']);
							$params['payitem'] = "P{$propsNo}*{$price}*1";
							$params['goodsmeta'] = base64_encode("{$name}*1");
							$faceId = !empty($baseInfo['face_id']) ? $baseInfo['face_id'] : $baseInfo['id'];
							$params['goodsurl'] = $url . "props/{$faceId}.jpg";
							break;
						case M_Mall::ITEM_HERO :
							$baseInfo = M_Hero::baseInfo($item_id); //军官
							$params['payitem'] = "H{$propsNo}*{$price}*1";
							$params['goodsmeta'] = base64_encode("{$baseInfo['nickname']}*1");
							$params['goodsurl'] = $url . "hero/normal/{$baseInfo['face_id']}.png";
							break;
						case M_Mall::ITEM_EQUIP :
							$baseInfo = M_Equip::baseInfo($item_id); //装备
							$params['payitem'] = "E{$propsNo}*{$price}*1";
							$params['goodsmeta'] = base64_encode("{$baseInfo['name']}*1");
							$params['goodsurl'] = $url . "equip/{$baseInfo['face_id']}.jpg";
							break;
						default :
							break;
					}
				}
			}
		}

		$qqUserData = M_Qq::instance()->api('/v3/pay/buy_goods', $params, 'post', 'https');

		if ($qqUserData['ret'] != 0) {
			Logger::qq(array('Msg' => 'Error buy_goods', 'Data' => $qqUserData, 'Params' => $params));
		}
		$out = json_encode($qqUserData);
	}
}

header('Content-Type:text/html;charset=utf-8');
echo $out;
exit;
?>