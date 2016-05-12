<?php

class B_Page {
	/**
	 * 分页计算
	 * @author huwei
	 * @param int $curPage 当前第几页
	 * @param int $totalNum 总共记录数
	 * @param int $offset 每页多少记录数
	 * @param int $pageSize 显示多少翻页数
	 * @return array
	 */
	static public function make($curPage, $totalNum, $offset = 10, $pageSize = 8) {
		$totalPage = ceil($totalNum / $offset);

		$startPage = 1;
		$tmp = intval($pageSize / 2);
		$startPage = max(1, $curPage - $tmp);
		$endPage = min($startPage + $pageSize - 1, $totalPage);
		$startPage = max(1, $endPage - $pageSize + 1);
		$page['range'] = range($startPage, $endPage);
		$page['totalPage'] = $totalPage;
		$page['curPage'] = $curPage;
		return $page;
	}
}

?>