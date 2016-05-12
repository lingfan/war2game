<?php
$pageData = B_View::getVal('pageData');
$svrcfg = M_Config::getSvrCfg();

$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'服务器配置' => '',
);

?>

<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<div class="top-bar">
	<h1>服务器配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigServer&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">服务器配置信息 重要内容！请勿随意更改！！！</th>
			</tr>
			<tr>
				<td width="150"><strong>服务器ID</strong></td>
				<td><input type="text" class="text" name="server_name" value='<?php echo $svrcfg['server_name']; ?>'/>
				</td>
			</tr>

			<tr>
				<td width="150"><strong>服务器标题</strong></td>
				<td><input type="text" class="text" name="server_title" value='<?php echo $svrcfg['server_title']; ?>'/>
				</td>
			</tr>

			<tr>
				<td width="150"><strong>服务器最大在线人数</strong></td>
				<td><input type="text" class="text" name="max_online_people"
				           value='<?php echo $svrcfg['max_online_people']; ?>'/></td>
			</tr>

			<tr>
				<td width="150"><strong>服务器运营平台KEY</strong></td>
				<td><input type="text" class="text" name="server_api_key"
				           value='<?php echo $svrcfg['server_api_key']; ?>'/></td>
			</tr>
			<tr>
				<td width="150"><strong>日志服务器地址</strong></td>
				<td><input type="text" class="text" name="log_api" value='<?php echo $svrcfg['log_api']; ?>'/>
					<?php
					$flag = false;
					$ret = '';
					if (!empty($svrcfg['log_api'])) {
						list($flag, $ret) = B_Request::call($svrcfg['log_api'], array('m' => 'ver'), '', 'post');
					}

					if ($flag) {
						echo "成功:[{$ret}]";
					} else {
						echo "失败:[{$ret}]";
					}
					?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>服务器资源路径</strong></td>
				<td><input type="text" class="text" name="server_res_url"
				           value='<?php echo $svrcfg['server_res_url']; ?>'/></td>
			</tr>
			<tr>
				<td width="150"><strong>防沉迷开关[1开/0关]</strong></td>
				<td><input type="text" class="text" name="anti_addiction_switch"
				           value='<?php echo $svrcfg['anti_addiction_switch']; ?>'/></td>
			</tr>
			<tr>
				<td width="150"><strong>游戏卡生成密码</strong></td>
				<td><input type="text" class="text" name="city_card_pwd"
				           value='<?php echo $svrcfg['city_card_pwd']; ?>'/></td>
			</tr>

			<tr>
				<td width="150"><strong>停服时间</strong></td>
				<td><input type="text" class="Wdate" name="maintenance[start]"
				           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
				           value='<?php echo $svrcfg['maintenance']['start']; ?>'/>
					--> <input type="text" class="Wdate" name="maintenance[end]"
					           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					           value='<?php echo $svrcfg['maintenance']['end']; ?>'/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					提示语: <input type="text" class="text" name="maintenance[msg]"
					            value='<?php echo $svrcfg['maintenance']['msg']; ?>'/>
					<?php
					$num = M_Client::keepLiveNum();
					echo "总人数:" . $num . '(<a href="?r=Server/CleanOnline" target="iframe">清除在线</a>)';
					?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>QQ调试时间<!--[开发0|正式1]--></strong></td>
				<td><input type="text" class="Wdate" name="qqserverip[start]"
				           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
				           value="<?php echo $svrcfg['qqserverip']['start']; ?>"/>
					--> <input type="text" class="Wdate" name="qqserverip[end]"
					           onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
					           value="<?php echo $svrcfg['qqserverip']['end']; ?>"/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					提示语：<input type="text" class="text" name="qqserverip[msg]"
					           value="<?php echo $svrcfg['qqserverip']['msg']; ?>"/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button" name="submit" value="保存"/></td>
			</tr>
		</table>
	</form>
</div>
<script>
	$('#rndpwd').click(function () {

		var x = "0123456789qwertyuioplkjhgfdsazxcvbnm";
		var tmp = "";
		for (var i = 0; i < 33; i++) {
			tmp += x.charAt(Math.ceil(Math.random() * 100000000) % x.length);
		}
		$('#pwdkey').val(tmp);
	});
</script>
