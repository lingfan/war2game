<?php
if (!empty($_POST)) {
	$args = array(
		'start_idx' => FILTER_VALIDATE_INT, //开始位置
		'num' => FILTER_VALIDATE_INT, //生成数量
		'is_repeat' => FILTER_VALIDATE_INT, //是否重复
		'type' => FILTER_VALIDATE_INT, //类型编号
		'propsId' => FILTER_VALIDATE_INT, //道具编号
		'pwd' => FILTER_SANITIZE_STRING, //密码
	);
	$formVals = filter_var_array($_REQUEST, $args);
	$formVals['type'] = $formVals['is_repeat'] . $formVals['type'];
	$cardNumArr = array();
	$idx = 0;
	for ($i = 0; $i < $formVals['num']; $i++) {
		$idx = $formVals['start_idx'] + $i;
		$tmpBin = @pack('NCn', $idx, $formVals['type'], $formVals['propsId'], $formVals['pwd']); //抑制错误输出
		$hash = substr(md5($tmpBin . $formVals['pwd']), 0, 14);
		$code = base64_encode($tmpBin . $hash);
		//$arr = M_Card::decrypt($code, $formVals['pwd']);
		$cardNumArr[] = $code;
	}

	$filename = "cardlist_{$formVals['type']}_{$formVals['propsId']}_{$formVals['start_idx']}-{$idx}_" . date('Ymd');
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename={$filename}.txt");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo implode("\r\n", $cardNumArr);
	exit;
}
?>
<div class="top-bar">
	<h2>生成新手卡号</h2>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="" method="post" target="iframe" name="card">
		<table>
			<tr>
				<td width="100" style="text-align: right">密码:</td>
				<td><input type="text" class="text" name="pwd" value=""/></td>
			</tr>
			<tr>
				<td style="text-align: right">是否重复:</td>
				<td><input type="radio" name="is_repeat" value="1" checked/>不可重复 <input
						type="radio" name="is_repeat" value="2"/>可重复
				</td>
			</tr>
			<tr>
				<td style="text-align: right">类型编号:</td>
				<td><input type="text" class="text" name="type" value=""/>(范围:1-99)</td>
			</tr>
			<tr>
				<td style="text-align: right">道具ID:</td>
				<td><input type="text" class="text" name="propsId" value=""/>(范围:1-1000)</td>
			</tr>

			<tr>
				<td style="text-align: right">起始数:</td>
				<td><input type="text" class="text" name="start_idx" value=""/>(范围:1-999999)</td>
			</tr>
			<tr>
				<td style="text-align: right">生成数量:</td>
				<td><input type="text" class="text" name="num" value=""/>(范围:1-999999)</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="生成"/>
				</td>
			</tr>
		</table>
	</form>
</div>
