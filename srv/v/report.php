<html>
<head>
	<title><?php echo $server_title; ?>
	</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<script type="text/javascript" src="/styles/swfobject.js"></script>
	<link href="<?php echo $server_res_url; ?>global/images/favicon.ico" rel="shortcut icon"/>
	<link type="text/css" href="styles/main.css" rel="stylesheet"/>
</head>
<body>

<div id="page">
	<div id="game_flash">
		<div id="gameswf" style="color: #ffffe0;">&gt;&gt;loading...</div>
	</div>
</div>

<script type="text/javascript">
	var k = "<?php echo urlencode($val);?>";
	if (k) {
		var resUrlPath = "<?php echo $server_res_url;?>";
		//var resXmlFile = "
		<?php echo $resXmlFile."?v=".date('dHi');?>";
		<?php
		$key = '987654321';
		$arr = array($domain, $sid, date('dHi'), md5($domain.$sid.date('dHi').$key));
		$val = "cfg.php?data=".B_Utils::base64url_encode($arr);
		?>
		var resXmlFile = "<?php echo $val;?>";
		if (resUrlPath) {
			var url = resUrlPath + 'GameLoader.swf?cfg=' + encodeURIComponent(resXmlFile) + "&mcn=RptViewer&extres=" + encodeURIComponent("swf/RptViewer.swf") + "&rptviewer=1&ver=0.1";
			swfobject.embedSWF(url, "gameswf", "100%", "100%", "9.0.0", "expressInstall.swf", {key: k}, {menu: false, align: "center", allowScriptAccess: "always", bgColor: "#000000"}, {id: "my_viewer", name: "my_viewer", allowScriptAccess: "always"});
		}
	}
	else {
		alert("err report url");
	}
</script>
</body>
</html>
