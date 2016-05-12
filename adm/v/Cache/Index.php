<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'缓存信息' => '',
);
?>

<div class="top-bar">
	<h1>缓存信息</h1>
</div>
<div class="select-bar">
	<form action="?r=Cache/CleanKey" method="post">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>KEY名称</td>
				<td>
					索引<input type="text" name="key" value="">&nbsp;&nbsp;&nbsp;&nbsp;
					后缀<input type="text" name="val" value="">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input id="sub" type="submit" value="清除 "></td>
			</tr>
		</table>
	</form>

</div>
<div class="table">

	<?php
	$ips = array_keys($info);
	$fileds = array_keys($info[$ips[0]]);
	?>

	<table id="list" class="listing form" cellpadding="0" cellspacing="0">

		<tr>
			<td></td>
			<?php foreach ($ips as $ip): ?>
				<td>
					<div>
						<?php echo $ip; ?>
					</div>
				</td>
			<?php endforeach; ?>
		</tr>

		<?php foreach ($fileds as $key):
			?>
			<tr>
				<td>
					<div><?php echo $key; ?>:</div>
				</td>

				<?php foreach ($ips as $ip): ?>
					<td>
						<div>
							<?php
							$val = isset($info[$ip][$key]) ? $info[$ip][$key] : '';
							if ($key == 'last_save_time') {
								$tmp1 = date('Y-m-d H:i:s', $val);
								$tmp2 = format_ago(time() - $val);
								$val = "{$tmp1} [{$tmp2}]";
							}
							echo $val;
							?>
						</div>
					</td>
				<?php endforeach; ?>
			</tr>

		<?php endforeach; ?>

	</table>
</div>

<?php

?>
 