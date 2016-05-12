<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'道具列表' => 'Base/PropsList&page=1',
);

$propsMaxNum = 20; //礼包包含道具最大数量
$equiMaxNum = 20; //礼包包含装备最大数量
$heroMaxNum = 5; //礼包包含军官最大数量

$packsPropsCount = 0; //道具已有项数
$packsEquiCount = 0; //装备已有项数
$packsHeroCount = 0; //军官已有项数

$info = $pageData['info'];
$beautify = $pageData['beautify'];
$weapon = $pageData['weapon'];
$props = $pageData['props'];
//$baseEquip = $pageData['baseEquip'];
$equipList = $pageData['equipList'];
$baseHero = $pageData['baseHero'];


if (!empty($info) && is_array($info)) {
	$isChkHotV1 = (1 == $info['is_hot']) ? 'checked' : '';
	$isChkHotV0 = (0 == $info['is_hot']) ? 'checked' : '';
	$isChkShV1 = (1 == $info['is_shop']) ? 'checked' : '';
	$isChkShV0 = (0 == $info['is_shop']) ? 'checked' : '';
	$isChkFaV1 = (1 == $info['is_fall']) ? 'checked' : '';
	$isChkFaV0 = (0 == $info['is_fall']) ? 'checked' : '';
	$isChkVipV1 = (1 == $info['is_vip_use']) ? 'checked' : '';
	$isChkVipV0 = (0 == $info['is_vip_use']) ? 'checked' : '';

	$urlArr['修改道具'] = 'Base/PropsAdd&id=' . $info['id'];
	$arrPrice = isset($info['price']) ? json_decode($info['price'], true) : array(T_App::MILPAY => 0, T_App::COUPON => 0);
	$effect_val = $info['effect_val'];
} else {
	$urlArr['新增道具'] = 'Base/PropsAdd';

	$isChkHotV1 = ''; //热卖初始确认值
	$isChkHotV0 = 'checked'; //非热卖初始确认值
	$isChkShV1 = 'checked'; //可商城出售初始默认值
	$isChkShV0 = ''; //不可出售初始默认值
	$isChkFaV1 = 'checked'; //可掉落初始默认值
	$isChkFaV0 = ''; //不可掉落初始默认值
	$isChkVipV1 = ''; //可掉落初始默认值
	$isChkVipV0 = 'checked'; //不可掉落初始默认值
}

?>

<div class="top-bar">
	<h1><?php echo isset($info['id']) ? '修改道具' : '新增道具'; ?></h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>

<div class="table">
	<form action="?r=Base/DoPropsAdd" method="post" onsubmit="javascript:return checkInput()">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2"><?php echo isset($info['id']) ? '修改' : '新增'; ?>道具</th>
			</tr>
			<tr>
				<td width="100px"><strong>道具名称</strong></td>
				<td width="400px">
					<input type='hidden' name='id' id='id'
					       value="<?php echo isset($info['id']) ? $info['id'] : ''; ?>"/>
					<input type="text" class="text" name="name" id="name"
					       value="<?php echo isset($info['name']) ? $info['name'] : ''; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>道具描述</strong></td>
				<td>
					<textarea name="desc" id='desc' cols='50'
					          rows='3'><?php echo isset($info['desc']) ? $info['desc'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>道具功能</strong></td>
				<td>
					<textarea name="feature" id='feature' cols='50'
					          rows='3'><?php echo isset($info['feature']) ? $info['feature'] : ''; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><strong>道具类型</strong></td>
				<td>
					<?php foreach (M_Props::$type as $type_val => $type_desc) { ?>
						<input type="radio" name="type" id="type" value="<?php echo $type_val; ?>"
							<?php echo (isset($info['type']) && $info['type'] == $type_val) ? 'checked' : ''; ?>  />
						<?php echo $type_desc; ?>&nbsp;&nbsp;&nbsp;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><strong>道具价格</strong></td>
				<td>
					<input type="text" class="text" name="price1" id="price1"
					       value="<?php echo isset($arrPrice[T_App::MILPAY]) ? $arrPrice[T_App::MILPAY] : 0; ?>"/>军饷<br/>
					<input type="text" class="text" name="price2" id="price2"
					       value="<?php echo isset($arrPrice[T_App::COUPON]) ? $arrPrice[T_App::COUPON] : 0; ?>"/>点券<br/>
				</td>
			</tr>
			<tr>
				<td><strong>系统出售价格值</strong></td>
				<td>
					<input type="text" class="text" name="sys_price" id="sys_price"
					       value="<?php echo isset($info['sys_price']) ? $info['sys_price'] : 0; ?>"/>
				</td>
			</tr>
			<tr>
				<td><strong>是否热卖</strong></td>
				<td>
					<input type="radio" name="is_hot" value="1" <?php echo $isChkHotV1; ?> />热卖&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_hot" value="0" <?php echo $isChkHotV0; ?> />非热卖
				</td>
			</tr>
			<tr>
				<td><strong>是否商城出售</strong></td>
				<td>
					<input type="radio" name="is_shop" value="1" <?php echo $isChkShV1; ?> />可出售&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_shop" value="0" <?php echo $isChkShV0; ?> />不可出售
				</td>
			</tr>
			<tr>
				<td><strong>是否可掉落</strong></td>
				<td>
					<input type="radio" name="is_fall" value="1" <?php echo $isChkFaV1; ?> />可掉落&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_fall" value="0" <?php echo $isChkFaV0; ?> />不可掉落
				</td>
			</tr>
			<tr>
				<td><strong>可否用于VIP</strong></td>
				<td>
					<input type="radio" name="is_vip_use" value="1" <?php echo $isChkVipV1; ?> />可用于VIP&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="is_vip_use" value="0" <?php echo $isChkVipV0; ?> />不可用于VIP
				</td>
			</tr>
			<tr>
				<td><strong>道具效果标签</strong></td>
				<td>
					<select name='effect_txt' id='effect_txt'
					        onchange="javascript:changeTxt(this.options[this.options.selectedIndex].value)">
						<?php foreach (T_Effect::$Props as $txt => $desc) { ?>
							<option
								value="<?php echo $txt; ?>" <?php echo (isset($info['id']) && $txt == $info['effect_txt']) ? 'selected' : ''; ?> >
								<?php echo $desc; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>

			<tr>
				<td><strong>效果值(多种数据格式)</strong></td>
				<td>
					<div>
						<input type="text" class="text" name="effect_val" id="effect_val"
						       value="<?php echo !empty($effect_val) ? $effect_val : 0; ?>"/>&nbsp;
					</div>
				</td>
			</tr>

			<tr>
				<td><strong>效果持续时间</strong></td>
				<td><input type="text" class="text" name="effect_time" id="effect_time"
				           value="<?php echo isset($info['effect_time']) ? $info['effect_time'] : 0; ?>"/>&nbsp;秒
				</td>
			</tr>
			<tr>
				<td><strong>道具排序序号</strong></td>
				<td><input type="text" class="text" name="sort" id="sort"
				           value="<?php echo isset($info['sort']) ? $info['sort'] : 0; ?>"/></td>
			</tr>

			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit"
				           value="<?php echo isset($info['id']) ? '修改' : '新增'; ?>"/></td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
	function checkInput() {
		var flag = true;
		if (document.getElementById('name').value == '') {
			window.alert('请填写道具名称!');
			flag = false;
		}
		else if (document.getElementById('type').value == '') {
			window.alert('请填写道具类型!');
			flag = false;
		}
		else if (document.getElementById('effect_txt').value == '') {
			window.alert('请填写道具效果标签!');
			flag = false;
		}
		else if (!window.confirm('您确定操作吗？')) {
			flag = false;
		}
		return flag;
	}
</script>