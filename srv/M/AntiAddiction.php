<?php

/** 防沉迷模块 */
class M_AntiAddiction {
	/** 累计上线收益减半时间(秒) */
	const ONLINE_INCOME_HALF = 10800; //3小时
	/** 累计上线收益为0时间(秒) */
	const ONLINE_INCOME_ZERO = 18000; //5小时
	/** 累计下线统计清零时间(秒) */
	const OFFLINE_CLEAN_ZERO = 18000; //5小时
	/** 累计上线初始收益加成系数 */
	const INCOME_BEGIN_RATE = 1;
	/** 累计上线中段收益加成系数 */
	const INCOME_HALF_RATE = 0.5;
	/** 累计上线最终收益加成系数 */
	const INCOME_END_RATE = 0;

	/** 未成年人标识 */
	const ADULT_NO = 0;
	/** 成年人标识 */
	const ADULT_YES = 1;

}

?>