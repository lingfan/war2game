<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'指引导入' => '#',
);
?>


<div class="top-bar">
	<h1>QQ分享管理</h1>

	<div class="breadcrumbs"><a href="?r=Base/QqShareList">分享奖励列表</a></div>
</div>
<div class="table">
	<?php
	$i = 1;
	if (!empty($pageData['tip'])) {
		foreach ($pageData['tip'] as $k => $v):
			echo "{$k}#{$v}&nbsp;&nbsp;&nbsp;&nbsp;";
			if ($i % 7 == 0) {
				echo "<br>";
			}
			$i++;
		endforeach;
	}

	?>
	<form id="addForm" name="addForm" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="r" value="Base/QqShareImport"/>
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 100px;">指引列表：</td>
				<td>
					<input type="file" name="csvfile">
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value="提交 "></td>
			</tr>
		</table>
	</form>
<pre>

 * 任务类型:
 * 1 => 升级建筑
 * 2 => 科技升级
 * 3 => 装备强化
 * 4 => 装备合成
 * 5 => 学习技能
 * 6 => 军团每日领取奖励
 * 7 => 每日购买任何道具
 * 8 => 玩家每日使用道具
 * 9 => 每日抽奖得到道具
 * 10=>军官培养
 * 11=>打副本
 * 12=>打突击
 * 13=>攻打学院
 * 14=>占领玩家
 * 15=>军团贡献
 * 16军团每日领取奖励
 
 
 * 任务完成条件:
 * 升级建筑 => array(build_up, 建筑ID, 等级)
 * 科技升级 => array(tech_up,等级)
 * 装备强化=> array(equip_strong,等级(0任意))
 * 装备合成=> array(equip_mix,需求等级, 品质,)
 * 学习技能=> array(hero_skill,技能id)
 * 军团每日领取奖励=> array(union_getaward)
 * 每日购买任何道具 => array(props_buy)
 * 玩家每日使用道具 => array(props_use)
 * 每日抽奖得到道具 => array(props_award)
 * 军官培养=> array(hero_train, 点数数)
 * 打副本=>array(fb_atk,副本编号)
 * 打突击=>break_out(break_out,宝箱ID)
 * 攻打学院=>array(atk_wildnpc,类型，等级)
 * 占领玩家=>array(occupied_city,等级)
 * 军团贡献=>array(union_contribution)
  * 军团每日领取奖励=>array(union_getaward)


</pre>
</div>

