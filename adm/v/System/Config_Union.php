<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'联盟配置' => 'System/ConfigUnion',
);
$baselist = $pageData['baselist'];
?>

<div class="top-bar">
	<h1>联盟配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigUnion&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td width="150"><strong>创建联盟消耗黄金</strong></td>
				<td>
					<input type="text" class="text" name="union_create_cost"
					       value="<?php echo $baselist['union_create_cost']; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>修改旗帜消耗金钱</strong></td>
				<td>

					<input type="text" class="text" name="union_up_face_cost"
					       value="<?php echo $baselist['union_up_face_cost']; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>创建联盟所需功勋</strong></td>
				<td>
					<input type="text" class="text" name="union_create_need_medal"
					       value="<?php echo $baselist['union_create_need_medal']; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>联盟贡献所需功勋 </strong></td>
				<td>
					<input type="text" class="text" name="union_donation_need_medal"
					       value="<?php echo $baselist['union_donation_need_medal']; ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>升级</strong></td>
				<td>
					金钱_军饷_容纳人数|金钱_军饷_容纳人数<br>
					<?php
					$tmp = $baselist['union_up'];
					$str = array();
					foreach ($tmp as $key => $val1) {
						$str[] = implode("_", $val1);
					}
					echo "最高等级(" . count($str) . ")";
					?>
					<textarea rows="1" cols="100" name="union_up"><?php echo implode('|', $str); ?></textarea><br>
				</td>
			</tr>
			<tr>
				<td><strong>科技</strong></td>
				<td>
					联盟等级_加成_资金|联盟等级_加成_资金<br>
					<?php
					$tmp = $baselist['union_tech'];

					foreach (M_Union::$unionTechName as $key => $val2):
						$str = array();
						if (!empty($tmp[$key])) {
							foreach ($tmp[$key] as $v) {
								$str[] = implode('_', $v);
							}
						}

						echo "{$val2}(" . count($str) . ")";
						?>
						<textarea rows="2" cols="100"
						          name="union_tech[<?php echo $key; ?>]"><?php echo implode('|', $str); ?></textarea>
						<br>

					<?php

					endforeach;?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>玩家加入军团冷却时间</strong></td>
				<td>
					<input type="text" class="text" name="cd_apply_union" style="width: 50px;"
					       value="<?php echo $baselist['cd_apply_union']; ?>"/>小时
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>

		</table>
	</form>
</div>
