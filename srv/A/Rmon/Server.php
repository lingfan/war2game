<?php

//服务器信息监控
class A_Rmon_Server {
	static public function Ping() {
		return M_Config::getSvrCfg('server_name');
	}
}

?>