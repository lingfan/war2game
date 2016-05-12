<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'服务器配置' => '',
);

?>

<div class="top-bar">
	<h1>QQ服务器配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
	<iframe name="iframe" style="display: none;"></iframe>
	<form action="?r=System/ConfigQQ&act=edit" method="post" target="iframe">
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">服务器配置信息 重要内容！请勿随意更改！！！</th>
			</tr>
			<tr>
				<td width="150"><strong>QQ平台ID</strong></td>
				<td><input type="text" class="text" name="appid" value="<?php echo M_Config::getVal('appid'); ?>"/></td>
			</tr>

			<tr>
				<td width="150"><strong>QQ平台key</strong></td>
				<td><input type="text" class="text" name="appkey" value="<?php echo M_Config::getVal('appkey'); ?>"/>
				</td>
			</tr>

			<tr>
				<td width="150"><strong>黄钻贵族新手礼包（限领一次）ID</strong></td>
				<td><input type="text" class="text" name="yellow_vip_one"
				           value="<?php echo M_Config::getVal('yellow_vip_one'); ?>"/>
					<?php
					echo B_Utils::awardText(M_Config::getVal('yellow_vip_one'));
					?>
				</td>

			</tr>
			<tr>
				<td width="150"><strong>年费黄钻贵族奢华礼包ID</strong></td>
				<td><input type="text" class="text" name="yellow_year_vip"
				           value="<?php echo M_Config::getVal('yellow_year_vip'); ?>"/>
					<?php
					echo B_Utils::awardText(M_Config::getVal('yellow_year_vip'));
					?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>黄钻贵族每日至尊礼包ID</strong></td>
				<td>
					<table>
						<?php
						$yellow_vip_level = json_decode(M_Config::getVal('yellow_vip_level'), true);
						for ($i = 1; $i < 9; $i++):
							$val = isset($yellow_vip_level[$i]) ? $yellow_vip_level[$i] : '';
							?>
							<tr>
								<td>黄钻等级:<?php echo $i; ?>&nbsp;&nbsp;&nbsp;<input style="width: 100px"
								                                                   name="yellow_vip_level[<?php echo $i; ?>]"
								                                                   type="text"
								                                                   value="<?php echo $val; ?>">
									<?php
									echo B_Utils::awardText($val);
									?>
								</td>
							</tr>
						<?php endfor; ?>
					</table>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>邀请好友奖励ID</strong></td>
				<td><input type="text" class="text" name="qq_invite_friend_award"
				           value="<?php echo M_Config::getVal('qq_invite_friend_award'); ?>"/>
					<?php
					echo B_Utils::awardText(M_Config::getVal('qq_invite_friend_award'));
					?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>邀请好友次数</strong></td>
				<td><input type="text" class="text" name="qq_invite_friend_num"
				           value="<?php echo M_Config::getVal('qq_invite_friend_num'); ?>"/></td>
			</tr>
			<tr>
				<td width="150"><strong>分享成功限制次数</strong></td>
				<td><input type="text" class="text" name="qq_share_success_num"
				           value="<?php echo M_Config::getVal('qq_share_success_num'); ?>"/></td>
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
