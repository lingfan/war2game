/**
 *  ShawnYoung 2011/09/05
 **/
(function () {
    if (typeof SWFObject == "undefined") {
        alert("SWFObject.js 未加载!");
    } else {
        var edt = {}
        //编辑器
        this._editor = edt;
        edt.instance = null;
        this._loaded = false;
        edt.parentId = "";
        edt.resURL = "";
        edt.callBackFunc = "";

        edt.id = "_SWFID_" + Math.floor(Math.random() * 1000);

        var id = edt.id;

        this._bg = "#000000";
        this._ver = "9";
        this._wd = "1000px";
        this._ht = "620px";
        this.__url = "styles/adm/images/adm_flash/BattlerCopyScene.swf";

        edt.setURL = function (strURL) {
            this.__url = strURL;
        }
        edt.setSize = function (wd, ht) {
            this._wd = wd;
            this._ht = ht;
        }

        var debug = function (obj) {
            var s = "";
            for (var ss in obj) {
                try {
                    s += ss + ":" + obj[ss] + "\n";
                } catch (e) {
                    s += ss + ":READ ERROR!\n";
                }
            }
            //alert(s);
        }

        var _ua = navigator.userAgent;

        function isFF() {
            return (/.+Firefox\/.*/).test(_ua);
        }

        function isIE() {
            return (/.+MSIE\s+.*/).test(_ua);
        }

        function isChrome() {
            return (/.+Chrome\//).test(_ua);
        }

        var fun = function () {
            if (document && document.body && document.documentElement) {
                clearTimeout(_th);
                _onloaded();
            } else {
                _th = setTimeout(fun, 10);
                //alert('sss');
            }
        }

        if (isIE()) {
            document.attachEvent("onreadystatechange", function () {
                if (document.readyState == "complete") {
                    _onloaded();
                }
            });
        } else {
            var _th = setTimeout(fun, 10);
        }

        function getWindow() {
            return document[id] || window[id];
        }


        //页面加载后执行的js
        function _onloaded() {
            if (CopyScene.parentId) {
                _creatInstanc();
            }
            this._loaded = true;
        }

        function $(id) {
            return document.getElementById(id);
        }

        function setMaxmin(objId, max, min) {
            $(objId).style.maxHeight = max + "px";
            $(objId).style.minHeight = min + "px";
        }

        function _creatInstanc() {
            if (!edt.parentId) {
                return;
            }
            var so = new SWFObject(this.__url, id, this._wd, this._ht, this._ver, this._bg);
            so.addParam("wmode", "Opaque");
            if (edt.resURL) {
                so.addVariable("swf", edt.resURL);
            }
            if (edt.callBackFunc) {
                so.addVariable("clkfunc", edt.callBackFunc);
            }

            if (edt.parentId) {
                so.write(edt.parentId);
            }


            setTimeout(_onloadInit, 200);

        }

        _onloadInit = function () {
            edt.loadRes();
            edt.setCallBackFun(edt.callBackFunc);
        }


        edt.loadRes = function (url) {
            if (url) {
                edt.resURL = url;
            }
            if (this._loaded) {
                var inst = getWindow();
                if (inst) {
                    //alert(inst.load);
                    //alert(!!edt.resURL);
                    if (edt.resURL.replace(/(^\s+)|(\s+$)/gi, "")) {
                        inst.load(edt.resURL);
                    }
                }
            }
        }

        edt.setCallBackFun = function (funcName) {

            edt.callBackFunc = funcName;

            var inst = getWindow();
            if (inst) {
                inst.setCallBackJsFunc(funcName);
            }
        }


        edt.setContainer = function (containerId) {
            edt.parentId = containerId;
            if (this._loaded) {
                _creatInstanc();
            }
        }
        window['CopyScene'] = this._editor;
    }
})();