<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'指引导入' => '#',
);
?>


<div class="top-bar">
	<h1>新手指引管理</h1>

	<div class="breadcrumbs"><a href="?r=Base/QuestList">指引列表</a></div>
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
		<input type="hidden" name="r" value="Base/QuestImport"/>
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
 * 2 => 建筑迁移
 * 3 => 升级科技
 * 4 => 升级兵种
 * 5 => 建筑物数量
 * 6 => 副本通关
 * 7 => 攻打副本次数
 * 8 => 配兵操作
 * 9 => 充值
 * 10=>道具购买
 * 11=>道具使用
 * 
 * 
 * 任务完成条件:
 * 升级建筑 => array(build_up, 建筑ID, 等级)
 * 建筑迁移 => array(build_move, 建筑ID, 次数)
 * 建筑清CD => array(build_cd, 次数)
 * 科技升级 => array(tech_up,科技ID,等级)
 * 科技清CD => array(tech_cd, 次数)
 * 兵种升级 => array(army_up,兵种ID,等级)
 * 兵种招募 => array(army_hire,兵种ID,数量)
 * 兵种配兵=> array(army_fit, 兵种ID, 数量)
 * 副本次数=> array(fb_times, 副本编号, 次数)
 * 充值=> array(pay,军饷数)
 * 道具购买 => array(props_buy, 道具ID, 道具数量)
 * 道具使用 => array(props_use, 道具ID, 次数)
 * 军官寻找=> array(hero_find, 次数)
 * 传奇招募=> array(hero_hire_s,次数)
 * 军官招募=> array(hero_hire, 次数)
 * 军官培养=> array(hero_train, 次数)
 * 军团申请=> array(union_apply, 次数)
 * 装备强化=> array(equip_strong, 品质(0任意), 等级(0任意), 次数)
 * 装备升级=> array(equip_up, 品质(0任意), 等级(0任意), 次数)
 * 装备合成=> array(equip_mix, 品质(0任意), 等级(0任意), 次数)
 * 特殊武器研究=> array(weapon_study_s, 第几个)
 * 普通武器研究=> array(weapon_study,	武器ID)
 * 好友邀请=>array(friend_invite,数量)
 * 攻打玩家=>array(atk_player,1)
 * 攻打学院=>array(atk_wildnpc,1)
 * 完成所有任务


</pre>
</div>

