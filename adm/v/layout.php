<html>
<head>
	<title>
		<?php
		$db = B_Cache_File::get('basedb');
		echo M_Config::getSvrCfg('server_title');
		?> - 二战后台管理系统</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link type="text/css" href="styles/adm/css/start/jquery-ui-1.8.13.custom.css" rel="stylesheet"/>
	<link type="text/css" href="styles/adm/css/base.css" rel="stylesheet"/>
	<link type="text/css" href="styles/adm/css/jquery.cleditor.css" rel="stylesheet"/>

	<script type="text/javascript" src="styles/adm/js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="styles/adm/js/jquery-ui-1.8.13.custom.min.js"></script>
	<script type="text/javascript" src="styles/adm/js/jquery.swfobject.1-1-1.min.js"></script>
	<script type="text/javascript" src="styles/adm/js/jquery.cleditor.min.js"></script>


	<script type="text/javascript">
		$(function () {
			$("#accordion").accordion({ header: "h3" });
		});
	</script>
</head>
<?php
$DataCom = include('DataCom.php');
$DataMenu = include('DataMenu.php');

?>

<div id="main">
	<?php echo B_View::load('header') ?>

	<div id="middle">
		<div id="left-column">

			<h4 style="background: none repeat scroll 0 0 #0078AE; border-radius: 7px; color: #FFFFFF; font-size: 12px; height: 20px; line-height: 23px;  margin: 0 0 10px 0; padding: 0 0 0 10px;">
				<?php
				$info = M_Adm::getLoginInfo();
				if (!empty($info['username'])) {
					echo $info['username'] . '<a href="?r=index/logout">登出</a>';
				}
				?>
			</h4>
			<?php
			if (isset($DataMenu[$DataCom[0]])):
				$subMenu = $DataMenu[$DataCom[0]];
				?>
				<h3><?php echo $subMenu['name']; ?></h3>
				<?php
				echo "版本: " . ETC_NO . "<br />";
				echo "区编号: " . SERVER_NO . "<br />";
				echo "服务器名: " . $_SERVER["SERVER_NAME"] . "<br />";
				echo "主机名: " . shell_exec("hostname") . "<br />";
				echo "外网IP: " . $_SERVER["SERVER_ADDR"] . "<br />";
				echo "基础库IP: {$db['hostname']} <br />";
				echo "基础库端口: {$db['port']} <br />";
				echo "库名字: {$db['database']}";
				?>

				<ul class="nav">
					<?php
					if (is_array($subMenu['sub'])):
						foreach ($subMenu['sub'] as $key => $val):
							?>
							<li><a href="?r=<?php echo $DataCom[0] . '/' . $key; ?>"><?php echo $val; ?></a></li>
						<?php
						endforeach;
					endif;
					?>
				</ul>
			<?php endif; ?>
		</div>
		<div id="center-column">
			<?php echo $content; ?>

		</div>
	</div>

	<?php echo B_View::load('footer') ?>
</div>


</body>
</html>
