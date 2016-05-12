<?php
define('IN_DEV', 1);
include('../common.php');
include('auth.php');

if (!empty($_POST['h'])) {
	foreach ($_POST['h'] as $id => $v) {
		$nickname = $v['nickname'];
		unset($v['nickname']);
		$ret = M_Hero::setHeroInfo($id, $v, true); //给军官调经验
		echo $ret ? "军官 $nickname 成功 <br />" : "军官 $nickname 失败 <br />";
	}
	exit;
} else {
	$cityId = isset($_GET['city_id']) ? intval($_GET['city_id']) : 0;
	if ($cityId > 0) {
		$cityInfo = M_City::getInfo($cityId);
		if ($cityInfo['id']) {
			$tmp = array();
			$heroIds = M_Hero::getCityHeroList($cityId);
			foreach ($heroIds as $heroId) {
				$heroInfo = M_Hero::getHeroInfo($heroId);
				$tmp[$heroInfo['id']] = array(
					'id' => $heroInfo['id'],
					'nickname' => $heroInfo['nickname'],
					'exp' => $heroInfo['exp'],
					'level' => $heroInfo['level'],
					'training_lead' => $heroInfo['training_lead'],
					'training_command' => $heroInfo['training_command'],
					'training_military' => $heroInfo['training_military'],
					'skill_slot' => $heroInfo['skill_slot'],
					'skill_slot_1' => $heroInfo['skill_slot_1'],
					'skill_slot_2' => $heroInfo['skill_slot_2'],
				);
			}
		} else {
			exit;
		}
	} else {
		exit;
	}
}

?>

<div class="top-bar">
	<h2>调军官经验值</h2>
</div>
<div class="table">
	<form action="" method="post">
		<table border="1px">
			<tr>
				<td width="150" style="text-align: right">城市名</td>
				<td><?php echo $cityInfo['nickname']; ?></td>
			</tr>
			<?php foreach ($tmp as $id => $val): ?>
				<tr>
					<td style="text-align: right"><?php echo $val['nickname']; ?>(<?php echo $val['level']; ?>)</td>
					<td><input type="hidden" size="10" class="text"
					           name="h[<?php echo $id; ?>][nickname]"
					           value="<?php echo $val['nickname']; ?>"/> 经验值<input type="text"
					                                                               size="10" class="text"
					                                                               name="h[<?php echo $id; ?>][exp]"
					                                                               value="<?php echo $val['exp']; ?>"/>
						培养统帅<input type="text"
						           size="5" class="text" name="h[<?php echo $id; ?>][training_lead]"
						           value="<?php echo $val['training_lead']; ?>"/> 培养指挥<input
							type="text" size="5" class="text"
							name="h[<?php echo $id; ?>][training_command]"
							value="<?php echo $val['training_command']; ?>"/> 培养军事<input
							type="text" size="5" class="text"
							name="h[<?php echo $id; ?>][training_military]"
							value="<?php echo $val['training_military']; ?>"/> 技能1 <?php
						$name = "h[$id][skill_slot]";
						echo skill_option($name, $val['skill_slot']);
						?> 技能2 <?php
						$name = "h[$id][skill_slot_1]";
						echo skill_option($name, $val['skill_slot_1']);
						?> 技能3 <?php
						$name = "h[$id][skill_slot_2]";
						echo skill_option($name, $val['skill_slot_2']);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="设置"/>
				</td>
			</tr>
		</table>
	</form>
	<?php
	$n = 0;
	$skillInfo = M_Base::skillAll();
	foreach ($skillInfo as $id => $val) {
		if ($n % 5 == 0) {
			echo "<br>";
		}
		$n++;
		echo "{$id}:{$val['name']};&nbsp;";
	}

	?>
</div>

<?php
function skill_option($name, $id) {
	$skillInfo = M_Base::skillAll();
	echo "<select name={$name}>";
	echo "<option value=''>空</option>";
	foreach ($skillInfo as $k => $val) {
		$selected = ($id == $k) ? 'selected' : '';
		echo " <option value='{$k}' {$selected}>{$val['name']}</option>";
	}
	echo "</select>";
}

?>
