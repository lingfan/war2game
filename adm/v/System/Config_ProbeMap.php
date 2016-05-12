<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'野外NPC系统默认生成数量' => 'System/DefaultProbeMap',
);
$baselist = $pageData['baselist'];
?>
<div class="top-bar">
	<h1>野外NPC系统默认生成数量</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/DefaultProbeMap" method="post" onsubmit="javascript:return checkInput()" target="iframe">
		<input type="hidden" name="act" value="edit">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100">NPC等级</td>
				<td width="100">每个洲分配数量</td>
			</tr>
			<?php
			for ($i = 1; $i < 6; $i++) {
				?>
				<tr>
					<td width="100"><?php echo $i; ?>级NPC部队</td>
					<td width="100"><input type="text" name="npcNum[<?php echo $i; ?>]"
					                       value="<?php echo isset($pageData['info']['npc_num'][$i]) ? $pageData['info']['npc_num'][$i] : 0; ?>"
					                       style="width: 50px;"></td>
				</tr>
			<?php
			}
			?>
		</table>
		<input type="submit" value="保存">
	</form>
</div>


<?php

$urlArr = array(
	'亚洲' => 'War/ProbeMap&zone=1',
	'欧洲' => 'War/ProbeMap&zone=2',
	'非洲' => 'War/ProbeMap&zone=3',
);

$zoneId = array(
	1 => 91,
	2 => 92,
	3 => 93,
);

?>

<?php
$npcList = M_NPC::$WILD_NPC_IDS;
//50 7,8   350 378
//40 6,12  280 288
//30 4,22 210 222
//20 5,25 140 145
//10 8,30 70 72
?>


<div class="table">

	<table class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<?php foreach ($npcList as $zone => $npcIds): ?>
				<td style="text-align:center"><?php echo T_App::$map[$zone]; ?></td>
			<?php endforeach; ?>
		</tr>
		<tr>
			<?php foreach ($npcList as $zone => $npcIds): ?>
				<td>
					<table class="listing form" cellpadding="0" cellspacing="0">
						<?php
						foreach ($npcIds as $npcId) :
							$npcInfo = M_NPC::getInfo($npcId);
							?>
							<tr>
								<td width="100"><?php echo isset($npcInfo['nickname']) ? $npcInfo['nickname']."(".$npcInfo['level'].")" : "no exist ({$npcId})"; ?> </td>
								<?php $num = B_DB::instance('WildMap')->totalNpcNum($npcId); ?>
								<td width="50"><?php echo $num; ?></td>

							</tr>
						<?php endforeach;; ?>
					</table>

				</td>
			<?php endforeach; ?>
		</tr>

	</table>

</div>

