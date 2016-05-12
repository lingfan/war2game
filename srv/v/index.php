<html>
<head>
	<title><?php echo $server_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<script type="text/javascript" src="styles/swfobject.js"></script>
	<script type="text/javascript" src="styles/jquery.js"></script>
	<!-- <script src="http://fusion.qq.com/fusion_loader?appid=<?php echo $appid; ?>&platform=<?php echo $pf; ?>" charset="utf-8" type="text/javascript"></script> -->
	<link href="styles/favicon.ico" rel="shortcut icon"/>
	<link href="styles/index.css" type="text/css" rel="stylesheet">
</head>
<body style="background:#000;height:100%;">
<div id="maincont">
	<div id="contentp">
		<div id="cpp" style="margin:5px 0px 0px 5px;">
			<div id="content">游戏加载中，请稍后... ...</div>
		</div>
	</div>
	<div id="footer">
		<p>玩家ID：<strong><span id="uid"><?php echo strtoupper($uid); ?></span></strong>&nbsp;&nbsp;此应用由深圳网腾技术有限公司提供，若您遇到问题，请到
			<a target="blank" class="link" href="http://bbs.open.qq.com/group-371-1.html">【论坛】</a>提交问题或联系客服电话：0755-26909657。
			<a onclick="AddFavorite()" href="javascript:void(0)">【收藏】</a></p>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		function show() {
			$.getJSON("/qq/QqLive.php?token=" + Math.random(), function(d){});
		}

		//show();
		//setInterval(show, 3600000);
	});

	var pf = '<?php echo $pf;?>';
	//查询Q点
	function _getq() {
		fusion2.dialog.checkBalance({
			onClose: function () {}
		});
	}
	//开通黄钻
	function _opendiamond(mode) {
		var url = 'http://pay.qq.com/qzone/index.shtml?aid=game<?php echo $appid;?>.yop&paytime=year';
		if (mode == 1) {
			url = 'http://pay.qq.com/qzone/index.shtml?aid=game<?php echo $appid;?>.op';
		}
		window.open(url);
	}

	//分享
	function _on_share(share_id) {
		$.getJSON("/qq/QqShare.php?ssid=<?php echo $ssid;?>&mode=1&share_id=" + share_id + "&r=" + Math.random(),
			function (d) {
				fusion2.dialog.sendStory({
					title: d.title,
					img: d.server_res_url + d.img,
					receiver: ['00000000000000000000000000009FED', '000000000000000000000000001C2DF9'],
					summary: d.summary,
					msg: d.msg,
					button: "进入应用",
					source: "shareid=" + share_id,
					context: share_id,
					onShown: function (opt) {
						// alert("Shown");
					},
					onSuccess: function (opt) {
						//插入奖励
						$.getJSON("/qq/QqShare.php?ssid=<?php echo $ssid;?>&mode=2&share_id=" + share_id + "&r=" + Math.random(), function (d) {
						});
					},
					onCancel: function (opt) {
						// opt.context：可选。opt.context为调用该接口时的context透传参数，以识别请求
						//alert("Cancelled: " + opt.context);
					},
					onClose: function (opt) {
						// alert("Closed");
					}
				});
			});
	}
	//购买道具
	function buy(num, mode) {
		num = parseInt(num);
		mode = parseInt(mode);
		if (mode == 0) {
			if (num > 9 || num < 1) {
				num = 1;
			}
		}
//window.alert(" mode=" + mode + " num=="+ num);exit;
		$.getJSON("/qq/pay.php?ssid=<?php echo $ssid;?>&num=" + num + "&mode=" + mode + "&r=" + Math.random(),
			function(d) {
				if (d.ret != 0) {
					alert(d.msg);
					return false;
				}
				if (d.url_params) {
					fusion2.dialog.buy({
						appid: <?php echo !empty($appid)?$appid:0;?>,
						sandbox: <?php echo $sandbox;?>,
						param: d.url_params,
						success: function () {}
					});
				}
			}
		);
	}

	//邀请功能
	function _invite() {
		fusion2.dialog.invite({
			msg: "邀请你来玩&lt;&lt;二战&gt;&gt;~",
			img: "http://qzonestyle.gtimg.cn/qzonestyle/act/qzone_app_img/app353_353_75.png",
			source: "uid=<?php echo $uid?>",
			context: "invite",
			onSuccess: function (ret) {},
			onCancel: function () {},
			onClose: function () {}
		});
	}

	<?php
		$key = '987654321';
		$arr = array($domain, $sid, date('dHi'), md5($domain.$sid.date('dHi').$key));
		$val = "cfg.php?data=".B_Utils::base64url_encode($arr);
		?>

	var url = "<?php echo $server_res_url;?>GameLoader.swf?ssid=<?php echo $ssid;?>&cfg=<?php echo $val;?>";
	//var url = "<?php echo $server_res_url;?>GameLoader.swf?ssid=<?php echo $ssid?>&cfg=config.xml";

	var flashvars = {
		amf: "http://<?php echo $domain;?>/call.php",
		playback: "http://<?php echo $domain;?>/report.html?vk={0}",
		loginurl: "http://<?php echo $domain;?>/testlogin.php",
		rpturl: "http://<?php echo $domain;?>/report/",
		homepage: "http://<?php echo $domain;?>"
	};

	var params = {
		menu: false,
		align: "center",
		wmode: "window",
		allowScriptAccess: "always",
		bgColor: "#000000"
	};

	var attr = {
		id: "game",
		name: "game",
		allowScriptAccess: "always"
	};

	swfobject.embedSWF(url, "content", "100%", "100%", "9.0.0", "expressInstall.swf", flashvars, params, attr);

	var onrsz = function () {
		var o = $("cpp");
		var h = (document.body ? document.body.clientHeight : 0) - 35;
		var w = (document.body ? document.body.clientWidth : 0);

		var nh = Math.min(Math.max(h, 600), 900);
		var nw = Math.min(Math.max(w, 980), 1400);

		if (o) {
			var op = o.offsetParent;
			o.style.width = nw + "px";
			o.style.height = nh + "px";
			if (document.all) {
				o.style.lineHeight = nh + "px";
			} else {
				op.style.height = (nh + 35) + "px";
			}
		} else {
			alert("emptyc!");
		}
	};

	if (window.onresize) {
		var onr = window.onresize;
		window.onresize = function () {
			onrsz();
		}
	} else {
		window.onresize = onrsz;
	}
	onrsz();


	function AddFavorite() {
		var sTitle = document.title;
		var sURL = document.location.href;
		sURL = encodeURI(sURL);
		try {
			window.external.addFavorite(sURL, sTitle);//IE
		} catch (e) {
			try {
				window.sidebar.addPanel(sTitle, sURL, "");//firefox
			} catch (e) {
				alert("加入收藏失败，请使用Ctrl+D进行添加");
			}
		}
	}
</script>

<div style="display:none">
</div>

</body>
</html>
