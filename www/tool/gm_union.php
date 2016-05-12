<?php
define('IN_DEV', 1);
include('../common.php');
include('auth.php');

if (!empty($_POST['union'])) {
	$union = $_POST['union'];
	if ($union['id'] > 0) {
		$unionId = $union['id'];
		unset($union['id']);
		$union['tech_data'] = json_encode($union['tech_data']);
		$ret = M_Union::setInfo($unionId, $union, true);
		echo $ret ? "联盟 $unionId 成功 <br />" : "联盟 $cityId 失败 <br />";
	}

	exit;

} else {
	$unionId = isset($_GET['union_id']) ? intval($_GET['union_id']) : 0;
	if ($unionId > 0) {
		$unionInfo = M_Union::getInfo($unionId);
		$unionTechInfo = json_decode($unionInfo['tech_data'], true);
	} else {
		exit;
	}
}

?>

<div class="top-bar">
	<h2>调联盟</h2>
</div>
<div class="table">
	<form action="" method="post">
		<table border="1px">
			<tr>
				<td width="150" style="text-align: right">联盟名</td>
				<td><?php echo "{$unionInfo['name']}({$unionInfo['level']})"; ?></td>
			</tr>
			<tr>
				<td style="text-align: right">联盟资金</td>
				<td><input type="hidden" class="text" name="union[id]"
				           value="<?php echo $unionInfo['id']; ?>"/> <input type="text"
				                                                            size="10" class="text" name="union[coin]"
				                                                            value="<?php echo $unionInfo['coin']; ?>"/><br>
				</td>
			</tr>
			<tr>
				<td style="text-align: right">联盟科技</td>
				<td><?php
					foreach (M_Union::$unionTechName as $tid => $name):
						$lv = isset($unionTechInfo[$tid]) ? $unionTechInfo[$tid] : 0;
						?> <?php echo $name; ?><input type="text" size="10" class="text"
						                              name="union[tech_data][<?php echo $tid ?>]"
						                              value="<?php echo $lv ?>" /><br>
					<?php endforeach; ?>
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
