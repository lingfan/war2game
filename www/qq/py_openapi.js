var Sys = {};
var ua = navigator.userAgent.toLowerCase();
var s;
var _appid = 100646281;

(s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
        (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
            (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
//分享随机资源
var shareObjs = new Array();
//shareObjs[7]={title:"spaceshare004_title",msg:"spaceshare004_desc",img:"share/share_04.gif",summary:"spaceshare004_summary",button:"1"};
shareObjs[0] = {title: "秦时明月", msg: "住手！放开那妹子，让我来！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_02.gif", summary: "竟然背后偷袭！流氓土匪无耻败类！拖出去，KO三百次！", button: "1"};
shareObjs[1] = {title: "秦时明月", msg: "我刚刚对这个小正太做了很过分的事=ε=", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_03.gif", summary: "人见人爱的的男孩为何会成为人见人怕的护国法师？年少成名的他又有怎样的经历？请看《杀手小正太养成记》", button: "1"};
shareObjs[2] = {title: "秦时明月", msg: "你们都弱爆了！有谁能接得住我三招？", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_04.gif", summary: "风靡全国的《秦时明月》同名游戏终于登场啦！", button: "1"};
shareObjs[3] = {title: "秦时明月", msg: "秦时明月送暗金英雄了！登录7天直接领！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_05.gif", summary: "2013最萌的动漫游戏，玄机唯一正版授权，沈乐平导演力荐！众多英雄等你来战！", button: "1"};
shareObjs[4] = {title: "秦时明月", msg: "暗金项羽！！！终于出来了！！！！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_16.gif", summary: "生当作人杰，死亦为鬼雄。至今思项羽，不肯过江东。", button: "1"};
shareObjs[5] = {title: "秦时明月", msg: "其实试玩的动画就做得很好", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_15.gif", summary: "“风萧萧兮易水寒，壮士一去兮不复还。”—荆轲", button: "1"};
shareObjs[6] = {title: "秦时明月", msg: "今天出航探索碰到一个暗金宝箱，可是我没有钥匙！！！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_18.gif", summary: "神秘的苍龙七宿与东海玄秘诸岛到底有何联系，快与月儿一起前去出航探秘吧！", button: "1"};
shareObjs[7] = {title: "秦时明月", msg: "命丢了下辈子可以再来，这辈子得不到姬如千泷，死了我都不甘心！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_06.gif", summary: "光明或是黑暗，大家拼出性命守护的究竟是什么？是正义，还是自己？", button: "1"};
shareObjs[8] = {title: "秦时明月", msg: "我di妈呀，这是要发啊！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_07.gif", summary: "做有节操的好游戏，做任务送金币，金币拿到你手软！——《秦时明月》", button: "1"};
shareObjs[9] = {title: "秦时明月", msg: "秦时明月送暗金英雄了！登录7天直接领！", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_05.gif", summary: "风靡全国的《秦时明月》同名游戏终于登场啦！", button: "1"};

//PK分享资源
var sharePKObjs = new Array();
sharePKObjs[0] = {title: "秦时明月", msg: "艾玛，我实在是太强了", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_04.gif"};
//炫耀资源
var shareBragObjs = new Array();
shareBragObjs[0] = {title: "秦时明月", msg: "光明或是黑暗，大家拼出性命守护的究竟是什么？是正义，还是自己？", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_06.gif"};
shareBragObjs[1] = {title: "秦时明月", msg: "做有节操的好游戏，做任务送金币，金币拿到你手软！——《秦时明月》", img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/share_07.gif"};
/**
 * 重新登录
 */
function relogin() {
    fusion2.dialog.relogin();
}

/**
 * 邀请好友
 *
 * @param appId
 * @return
 */
function inviteFriend(appId) {
    hideSwf();
    fusion2.dialog.invite({
        msg: "2013全民的期待！国内最受欢迎的动漫游戏《秦时明月》，和我一起来玩吧！",
        img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/spaceshare0004.jpg",
        source: "app_custom=invisit_" + getRoleId(),
        onSuccess: function (opt) {
            callSwf().CallFl("InviteFriend");
        },
        onClose: function () {
            showSwf();
        }
    });
}

function inviteFriendByOpenID(appId, openID) {
    hideSwf();
    fusion2.dialog.invite({
        receiver: openID,
        msg: "2013全民的期待！国内最受欢迎的动漫游戏《秦时明月》，和我一起来玩吧！",
        img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/spaceshare0004.jpg",
        source: "app_custom=invisit_" + getRoleId(),
        onSuccess: function (opt) {
            callSwf().CallFl("InviteFriend");
        },
        onClose: function () {
            showSwf();
        }
    });
}

/**
 * 炫耀
 *
 * @param _title 标题
 * @param _msg 消息
 * @param _receiver 接收者
 * @param _img 图片
 */
function brag(_title, _msg, _receiver, _img) {
    alert("before hideSwf()");
    hideSwf();
    alert("after hideSwf()");
    alert("_receiver = " + _receiver);
    fusion2.dialog.brag({
        type: "brag",
        title: _title,
        msg: _msg,
        receiver: _receiver,
        img: _img,
        onSuccess: function (opt) {

        },
        onCancel: function (opt) {

        },
        onClose: function (opt) {

        }
    });
    alert("after brag invocation!!!");
}

/**
 * 游戏试玩
 * @return
 */
function shareDemo() {
    hideSwf();
    fusion2.dialog.shareDemo({
        img: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/spaceshare1002.jpg",
        flashlink: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/share/demo.swf",
        title: "经典动漫《秦时明月》",
        summary: "Flash动画真实再现荆轲刺秦全过程",
        msg: "看了动画才知道，原来荆轲刺秦是有内应的！",
        source: "app_custom=sharedemo_" + getRoleId(),
        context: "share-demo",
        onShown: function (opt) {

        },
        onSuccess: function (opt) {
            callSwf().CallFl("SharePresent", 3);
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
/**
 * 跳转到个人中心
 *
 * @return
 */
function toHome() {
    fusion.nav.toHome();
}
/**
 * 跳转到好友主页
 *
 * @param openId
 *            好友的OpenID
 * @return
 */
function toFriendHome(openId) {
    fusion.nav.toFriendHome(openId);
}
/**
 * 跳转到应用
 *
 * @param appId
 * @return
 */
function toApp(appId) {
    fusion.nav.toApp({
        appid: appId
    });
}
/**
 * 游戏币充值页面
 *
 * @return
 */
function pay() {
    hideSwf();
    fusion2.dialog.pay({
        // 可选。表示是否使用沙箱测试环境。true：使用； false或不指定：不使用。
        sandbox: false,
        // 可选。对话框关闭时的回调方法。
        onClose: function () {
            showSwf();
        }
    });
}
/**
 * Q点充值页面
 *
 * @return
 */
function recharge() {
    hideSwf();
    fusion2.dialog.recharge({
        // 可选。对话框关闭时的回调方法。
        onClose: function () {
            showSwf();
        }
    });
}
/**
 * Q点余额查询页面
 *
 * @return
 */
function checkBalance() {
    hideSwf();
    fusion2.dialog.checkBalance({
        // 可选。对话框关闭时的回调方法。
        onClose: function () {
            showSwf();
        }
    });
}
/**
 * Q点购买道具页面
 *
 * @param url_param
 *            必须。 表示Q点购买物品的url参数，url_params是调用qz_buy_goods接口的返回
 * @param context
 *            可选，上下文变量，用于回调时识别来源
 * @return
 */
function buy(url_param, context) {
    hideSwf();
    fusion2.dialog.buy({
        // 必须。 表示Q点购买物品的url参数，url_params是调用qz_buy_goods接口的返回。
        param: url_param,
        // 可选。免打扰，true：开启；false或不指定，关闭。
        disturb: true,
        // 可选。表示是否使用沙箱测试环境。true：使用； false或不指定：不使用。
        sandbox: false,
        // 可选。前台使用的上下文变量，用于回调时识别来源。
        context: context,
        // 可选。用户购买成功时的回调方法，其中opt.context为上述context参数。如果用户购买成功，则立即回调onSuccess，当用户关闭对话框时再回调onClose。
        onSuccess: function (opt) {
            callSwf().onSuccess(opt.context);
        },
        // 可选。用户取消购买时的回调方法，其中opt.context为上述context参数。如果用户购买失败或没有购买，关闭对话框时将先回调onCancel再回调onClose。
        onCancel: function (opt) {
            callSwf().onCancel(opt.context);
        },
        // 可选。对话框关闭时的回调方法，主要用于对话框关闭后进行UI方面的调整，onSuccess和onCancel则用于应用逻辑的处理，避免过度耦合。
        onClose: function () {
            showSwf();
        }
    });
}

/**
 * 通用的赠送索要接口，客户端直接调用，传入所有参数

 * type: 只能传入request 或 freegift
 * title , msg , img : 分享内容，由客户端调用时传入，必须传入非空值
 * receiver : (1)发给指定的好友，传入['000000000000000000000000009FED', '0000000000000000000000001CEDF9'] 如果指定了该参数，则不会弹出好友选择页面
 *            (2)不指定好友，传入[""]，则会弹出好友选择页面
 * itemId : 要发送的道具id
 */
function sendRequest(type, title, msg, img, itemId, receiver) {
    //hideSwf();
    if (!type || typeof(type) == 'undefined' || "" == type || (type != "request" && type != "freegift")) {
        alert("调用sendRequest错误，请传入type值:" + type);
        return;
    }
    if (!title || typeof(title) == 'undefined' || "" == title) {
        alert("调用sendRequest错误，请传入title值:" + title);
        return;
    }
    if (!msg || typeof(msg) == 'undefined' || "" == msg) {
        alert("调用sendRequest错误，请传入msg值:" + msg);
        return;
    }
    if (!img || typeof(img) == 'undefined' || "" == img) {
        alert("调用sendRequest错误，请传入img值:" + img);
        return;
    }
    // 如果没有传入接受者，默认为api需要的参数
    if (!receiver || typeof(receiver) == 'undefined') {
        receiver = "";
    }
    fusion2.dialog.sendRequest({
        // 必须.可传入request或freegift
        type: type,
        // 必须，接收者的OpenId。OpenId数量必须<=20
        receiver: receiver.split(","),
        // 必须，request的标题或freegift的名称
        title: title,
        // 必须，request的内容或freegift的物品描述
        msg: msg,
        // 必须，图片的url
        img: img,
        //来源
        source: "app_custom=" + type + "_" + getRoleId(),
        // 可选
        onSuccess: function (opt) {
            var sendSucc = new Array();
            if (receiver.length > 0) { //如果指定了好友，opt.receiver不会返回值
                sendSucc = receiver.split(",");
            } else {
                sendSucc = opt.receiver;
            }
            sendSucess(itemId, type, sendSucc);
        },
        // 可选
        onCancel: function (opt) {
            //showSwf();
        },
        // 可选
        onClose: function () {
            //showSwf();
        }
    });
}
/**
 * 游戏故事
 * @param title 标题 必填
 * @param summary 故事摘要
 * @param msg 内容
 * @param img 图片 必填
 * @param button 1、进入应用2、领取奖励3、获取能量4、帮助TA 默认不填是1
 * @param shareId 填roleId吧
 */
function sendStory(title, summary, msg, img, button, shareId) {
    hideSwf();
    var index = Math.floor(Math.random() * shareObjs.length);
    fusion2.dialog.sendStory({
        title: title,
        img: img,
        summary: summary,
        msg: msg,
        button: button,
        source: "app_custom=sendstory_" + getRoleId() + ";shareId=" + shareId,
        onSuccess: function (opt) {
            callSwf().CallFl("SharePresent", 1);
            showSwf();
        },
        onClose: function () {
            showSwf();
        }
    });
}

/**
 *发送试玩故事，随机
 */
function sendshare() {
    hideSwf();
    var index = Math.floor(Math.random() * shareObjs.length);
    fusion2.dialog.sendStory({
        title: shareObjs[index].title,
        img: shareObjs[index].img,
        summary: shareObjs[index].summary,
        msg: shareObjs[index].msg,
        button: shareObjs[index].button,
        source: "app_custom=sendstory_" + getRoleId(),
        onSuccess: function (opt) {
            callSwf().CallFl("SharePresent", 1);
            showSwf();
        },
        onClose: function () {
            showSwf();
        }
    });
}

function sendStoryShareId(title, summary, msg, img, button, shareId) {
    hideSwf();
    fusion2.dialog.sendStory({
        title: title,
        img: img,
        summary: summary,
        msg: msg,
        button: button,
        source: "app_custom=sendstory_" + getRoleId() + ";shareId=" + shareId,
        onSuccess: function (opt) {
        },
        onClose: function () {
            showSwf();
        }
    });
}
function addFavorite(url, title) {
    try {
        if (document.all) {
            window.external.addFavorite(url, title);
        } else if (window.sidebar) {
            window.siderbar.addPanel(title, url, "");
        }
    } catch (e) {
    }
    return false;
}

/**
 * 开通包月送礼
 * @param token
 * @param actid
 * @param openid
 */
function openVipGift(token, actid, openid) {
    hideSwf();
    fusion2.dialog.openVipGift({
        //必须，从v3/pay/get_pay取得
        token: token,
        //必选，活动号
        actid: actid,
        zoneid: "0",
        openid: openid,
        version: 'v3',
        paytime: 'month',
        defaultMonth: '1',
        onSuccess: function (ret) {
            callSwf().CallFl("openVipGift");
        },
        onClose: function () {
            showSwf();
        }
    });
}

//黄钻爆竹营销活动
function openYDiamondVipGift(token, actid, openid, pf, ch) {
    hideSwf();
    fusion2.dialog.openVipGift({
        //必须，从v3/pay/get_pay取得
        token: token,
        //必选，活动号
        actid: actid,
        //"发货配置"中配置好的分区ID
        zoneid: "0",
        openid: openid,
        //XXJZGHH代表豪华黄钻，传空或不传代码普通黄钻
        pf: pf,
        //self:给自己开通,send:给好友开通
        ch: ch,
        onSuccess: function (ret) {
            callSwf().BlueDiamondPayCall(actid);
        },
        onClose: function () {
            showSwf();
        }
    });
}

function hideSwf() {
    if (Sys.chrome) {
        iframeLocation(2);
    }
}

function showSwf() {
    if (Sys.chrome) {
        iframeLocation(1);
    }
}

/**
 * 召唤老玩家
 */
function reactive(title, msg, roleId, openids) {
    hideSwf();
    fusion2.dialog.reactive({
        title: title,
        receiveImg: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/images/zhaohuihaoyou.jpg",
        sendImg: "http://app100646281.imgcache.qzoneapp.com/app100646281/s1/images/ItemIcon21000113.jpg",
        msg: msg,
        specified: openids,
        only: 1,
        source: 'reactive_' + roleId,
        context: "",
        onSuccess: function (opt) {
            callSwf().CallFl("reactiveCallback");
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}

/**
 *设置QQ提醒
 **/
function authReminder() {
    hideSwf();
    fusion2.dialog.authReminder({
        onSuccess: function () {
            callSwf().QQtixing();
        },
        onShown: function () {

        },
        onError: function () {
            alert("主公，您曾经开启过提醒，但现在关闭了，萌萌请求您再次开启提醒，这样才能领取奖励哦~\n友情提示：您可以在右上角“复制应用地址”中找到“QQ提醒”");
        },
        onCancel: function () {
            showSwf();
        },
        onClose: function () {
            showSwf();
        }
    });
}

/**
 * 抵扣券接口
 */
function getCoupon() {
    hideSwf();
    fusion2.dialog.getCoupon({
        context: "getCoupon",
        onClose: function () {
            showSwf();
        }
    });
}

Array.prototype.in_array = function (e) {
    for (i = 0; i < this.length; i++) {
        if (this[i] == e)
            return true;
    }
    return false;
}

/**
 * 随机3个好友
 */
function getRcndFriends() {
    var f = rcmdf;
    if (f.length <= 3) {
        return f;
    }
    var ret = [];
    var n = 4;
    while (n > 1) {
        var i = Math.floor(Math.random() * f.length);
        var x = f[i];
        if (!ret.in_array(x)) {
            ret.push(x);
            n = n - 1;
        }
    }
    return ret;
}

//从推荐好友中随机指定个数好友
function getFriends(n) {
    var f = rcmdf;
    if (f.length <= n) {
        return f;
    }
    var ret = [];
    while (n > 0) {
        var x = f[Math.floor(Math.random() * f.length)];
        if (ret.indexOf(x) < 0) {
            ret.push(x);
            n = n - 1;
        }
    }
    return ret;
}
//炫耀(brag)||挑战(pk)分享
function shareBragPK(type, title, _msg, _receiver, _img) {
    hideSwf();
    var obj;
    if (type == "brag") {
        var index = Math.floor(Math.random() * shareBragObjs.length);
        obj = shareBragObjs[index];
    } else {
        var index = Math.floor(Math.random() * sharePKObjs.length);
        obj = sharePKObjs[index];
    }
    fusion2.dialog.brag({
        type: type,
        title: obj.title,
        msg: obj.msg,
        receiver: _receiver,
        img: obj.img,
        context: "",
        source: "",
        onSuccess: function (opt) {
            if (type == "brag") {
                callSwf().CallFl("SharePresent", 2);
            } else {
                callSwf().CallFl("SharePresent", 4);
            }
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//推荐好友
function recommendPal() {
    hideSwf();
    fusion2.dialog.recommendPal({
        context: "recommendPal",
        onSuccess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//应用内添加平台好友
function addPal(openId) {
    hideSwf();
    fusion2.dialog.addPal({
        openid: openId,
        context: "addPal",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//添加到QQ主面板
function addPanel() {
    hideSwf();
    fusion2.dialog.addClientPanel({
        context: "addClientPanel",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//添加领取金卷
function getCoupon() {
    hideSwf();
    fusion2.dialog.getCoupon({
        context: "getCoupon",
        onClose: function (opt) {
            showSwf();
        }
    });
}

//创建公会群
//guildId游戏公会ID
//name公会名称
function guildCreate(guildId, name) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "create",
        unionid: guildId,
        groupname: name,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            callSwf().CallFl("guildQQCreate");
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//公会邀请
//info邀请的内容
//openIds 要邀请的人员openId 列表，多个openId之间用","分隔，使用数组的格式s
function guildInvite(guildId, info, openIds) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "invite",
        msg: info,
        receiver: openIds.split(","),
        unionid: guildId,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}//推荐好友
function recommendPal() {
    hideSwf();
    fusion2.dialog.recommendPal({
        context: "recommendPal",
        onSuccess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//应用内添加平台好友
function addPal(openId) {
    hideSwf();
    fusion2.dialog.addPal({
        openid: openId,
        context: "addPal",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//添加到QQ主面板
function addPanel() {
    hideSwf();
    fusion2.dialog.addClientPanel({
        context: "addClientPanel",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        }
    });
}
//添加领取金卷
function getCoupon() {
    hideSwf();
    fusion2.dialog.getCoupon({
        context: "getCoupon",
        onClose: function (opt) {
            showSwf();
        }
    });
}

//创建公会群
//guildId游戏公会ID
//name公会名称
function guildCreate(guildId, name) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "create",
        unionid: guildId,
        groupname: name,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            callSwf().CallFl("guildQQCreate");
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//公会邀请
//info邀请的内容
//openIds 要邀请的人员openId 列表，多个openId之间用","分隔，使用数组的格式s
function guildInvite(guildId, info, openIds, groupopenid) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "invite",
        msg: info,
        receiver: openIds.split(","),
        unionid: guildId,
        groupopenid: groupopenid,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//加入公会QQ群
//guildId游戏公会ID
//name 加入公会QQ群时设置的群名片
//openId 公会群openId
function guildJoin(guildId, name, openId) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "join",
        name: name,
        unionid: guildId,
        groupopenid: openId,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//退出公会群
function quildQuit() {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "quit",
        unionid: guildId,
        groupopenid: openId,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//加入公会QQ群
//guildId游戏公会ID
//name 加入公会QQ群时设置的群名片
//openId 公会群openId
function guildJoin(guildId, name, openId) {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "join",
        name: name,
        unionid: guildId,
        groupopenid: openId,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
//退出公会群
function quildQuit() {
    hideSwf();
    fusion2.dialog.manageQQGroup({
        type: "quit",
        unionid: guildId,
        groupopenid: openId,
        zoneid: 0,
        context: "manage-QQGroup-guildId",
        onSucess: function (opt) {
            showSwf();
        },
        onCancel: function (opt) {
            showSwf();
        },
        onClose: function (opt) {
            showSwf();
        },
        onError: function (opt) {
            showSwf();
        }
    });
}
