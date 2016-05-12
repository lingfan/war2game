<?php
$pageData = B_View::getVal('pageData');
$baseVal = $pageData['baselist']['lottery'];

$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'抽奖配置' => 'System/ConfigLottery',
);
?>

<div class="top-bar">
	<h1>抽奖配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigLottery&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">

			<tr>
				<td width="150">
				</td>
				<td>
					出现次数
					<input type="text" class="text" style="width: 50px;" name="OutRate"
					       value="<?php echo $baseVal['OutRate']; ?>"/>
					<br/>

					出现ID包裹
					<input type="text" class="text" style="width: 300px;" name="OutList"
					       value="<?php echo implode(',', $baseVal['OutList']); ?>"/><br>
				</td>
			</tr>

			<?php for ($i = 1; $i < 18; $i++): ?>
				<tr>
					<td width="150">包裹:<?php echo $i; ?><br/></td>
					<td>
						出现概率:<input type="text" style="width: 100px;" class="text"
						            name="Package[<?php echo $i; ?>][rate]"
						            value="<?php echo $baseVal['Package'][$i]['rate']; ?>"/>
						名称:<input type="text" style="width: 100px;" class="text" name="Package[<?php echo $i; ?>][name]"
						          value="<?php echo $baseVal['Package'][$i]['name']; ?>"/>
						类型:<input type="text" style="width: 100px;" class="text" name="Package[<?php echo $i; ?>][type]"
						          value="<?php echo $baseVal['Package'][$i]['type']; ?>"/>[res, equip, props, hero]
						<br/>

						物品ID,数量,出现概率,抽取次数1_抽取概率1|抽取次数2_抽取概率2|...<br>
						<?php
						$str = '';
						if (isset($baseVal['Package'][$i]['data'])) {

							foreach ($baseVal['Package'][$i]['data'] as $dVal) {
								$tmpStr = array();
								foreach ($dVal['rate2'] as $t => $r) {
									$tmpStr[] = $t . '_' . $r;
								}
								$rate2 = implode("|", $tmpStr);
								$str .= "{$dVal['id']},{$dVal['num']},{$dVal['rate1']},{$rate2}\n";
							}
						}

						?>
						<textarea name="Package[<?php echo $i; ?>][data]" cols='100'
						          rows="10"><?php echo $str; ?></textarea>

					</td>
				</tr>
			<?php endfor; ?>


			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>

		</table>
	</form>
</div>
