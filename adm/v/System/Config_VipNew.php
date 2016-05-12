<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'VIP新配置' => 'System/ConfigVipNew',
);

$maxLevel = intval($pageData['MAX_VIP_LEVEL']); //最大VIP等级
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>VIP新配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
<iframe name="iframe" style="display: none;"></iframe>
<form action="?r=System/ConfigVipNew&act=edit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<th class="full" colspan="2">VIP新配置信息 重要内容！请勿随意更改！(各VIP等级配置内容从0级开始)</th>
</tr>

<tr>
	<td width="200"><strong>VIP最大等级值</strong></td>
	<td><input type="text" class="text" name="MAX_VIP_LEVEL" value="<?php echo $maxLevel; ?>"/></td>
</tr>

<tr>
	<td width="150"><strong>到达各级所需军饷数</strong></td>
	<td><input type="text" class="text" name="MIL_PAY_CONF"
	           value="<?php echo implode(',', $pageData['MIL_PAY_CONF']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>增加活力上限值</strong></td>
	<td><input type="text" class="text" name="INCR_ENERGY_LIMIT"
	           value="<?php echo implode(',', $pageData['INCR_ENERGY_LIMIT']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>可购买活力次数</strong></td>
	<td><input type="text" class="text" name="BUY_ENERGY" value="<?php echo implode(',', $pageData['BUY_ENERGY']); ?>"
	           style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>增加军令上限值</strong></td>
	<td><input type="text" class="text" name="INCR_MILORDER_LIMIT"
	           value="<?php echo implode(',', $pageData['INCR_MILORDER_LIMIT']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>可购买军令次数</strong></td>
	<td><input type="text" class="text" name="BUY_MILORDER"
	           value="<?php echo implode(',', $pageData['BUY_MILORDER']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>可开启最大建筑队列序号</strong></td>
	<td><input type="text" class="text" name="BUILD_CD_LISTID"
	           value="<?php echo implode(',', $pageData['BUILD_CD_LISTID']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>可开启最大科技队列序号</strong></td>
	<td><input type="text" class="text" name="TECH_CD_LISTID"
	           value="<?php echo implode(',', $pageData['TECH_CD_LISTID']); ?>" style="width:400px"/></td>
</tr>


<tr>
	<td width="150"><strong>增加战斗后掉宝概率</strong></td>
	<td><input type="text" class="text" name="INCR_AWARD_RATE"
	           value="<?php echo implode(',', $pageData['INCR_AWARD_RATE']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>是否可购买资源</strong></td>
	<td><input type="text" class="text" name="SHOP_RES" value="<?php echo implode(',', $pageData['SHOP_RES']); ?>"
	           style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>装备背包容量</strong></td>
	<td><input type="text" class="text" name="PACK_EQUI" value="<?php echo implode(',', $pageData['PACK_EQUI']); ?>"
	           style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>图纸背包容量</strong></td>
	<td><input type="text" class="text" name="PACK_DRAW" value="<?php echo implode(',', $pageData['PACK_DRAW']); ?>"
	           style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>道具背包容量</strong></td>
	<td><input type="text" class="text" name="PACK_PROPS" value="<?php echo implode(',', $pageData['PACK_PROPS']); ?>"
	           style="width:400px"/></td>
</tr>
<tr>
	<td width="150"><strong>材料背包容量</strong></td>
	<td><input type="text" class="text" name="PACK_MATERIAL"
	           value="<?php echo implode(',', $pageData['PACK_MATERIAL']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td width="150"><strong>可开启最大特殊武器槽序号</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="SPECIAL_SLOTID[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['SPECIAL_SLOTID'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>减少出征时间数组</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="DECR_MARCH_TIME[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['DECR_MARCH_TIME'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>可抽取奖励传奇军官品质数组</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="HERO_AWARD[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['HERO_AWARD'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>可抽取奖励装备数组</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="EQUI_AWARD[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['EQUI_AWARD'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加粮食产量[VIP可购买功能]</strong></td>
	<td>
		<?php echo '每周期几天、每周期总次数、每次增加点数<br />';
		for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="FOOD_INCR_YIELD[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['FOOD_INCR_YIELD'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加石油产量[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="OIL_INCR_YIELD[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['OIL_INCR_YIELD'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加金钱产量[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="GOLD_INCR_YIELD[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['GOLD_INCR_YIELD'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加所有部队攻击[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="ARMY_INCR_ATT[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['ARMY_INCR_ATT'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加所有部队防御[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="ARMY_INCR_DEF[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['ARMY_INCR_DEF'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>增加军官带兵上限[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="HERO_INCR_ARMY[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['HERO_INCR_ARMY'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>复活战后已死兵力[VIP可购买功能]</strong></td>
	<td>
		<?php for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="ARMY_RELIFE[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['ARMY_RELIFE'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>属地开启条件</strong></td>
	<td>
		<?php echo '所处等级能开几个槽填几个值,值是所需军饷<br />';
		for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="COLONY_OPEN[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['COLONY_OPEN'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>
<tr>
	<td width="150"><strong>城市属地开启条件</strong></td>
	<td>
		<?php echo '所处等级能开几个槽填几个值,值是所需军饷<br />';
		for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="CITY_COLONY_OPEN[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['CITY_COLONY_OPEN'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>
<tr>
	<td width="150"><strong>领取VIP宝箱</strong></td>
	<td>
		<?php echo '道具ID_个数<br />';
		for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}"; ?>
			<input type="text" class="text" name="VIP_PACKAGE[<?php echo $lev; ?>]"
			       value="<?php echo $pageData['VIP_PACKAGE'][$lev]; ?>"/><br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td width="150"><strong>VIP商城可出售物品配置</strong></td>
	<td>
		<?php echo '物品类型,物品ID,每天玩家限购,每天系统限购,价格 <br />'; //竖线|隔开
		for ($lev = 0; $lev <= $maxLevel; $lev++) {
			echo "VIP{$lev}";
			?>
			<textarea name="VIP_SHOP[<?php echo $lev; ?>]" cols='90'
			          rows="<?php echo floor($lev * 1.5) + 1; ?>"><?php echo str_replace('|', "\n", $pageData['VIP_SHOP'][$lev]); ?></textarea>
			<br/>
		<?php } ?>
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" class="button" name="submit" value="保存"/></td>
</tr>

</table>
</form>
</div>