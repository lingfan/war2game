<style>
	td {
		word-break: break-all;
		width: 200px;
	}
</style>


<div class="table">
	状态:
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">名称</th>
			<th width="50px">值</th>
		</tr>
		<?php
		$pageData = B_View::getVal('pageData');
		foreach ($pageData['statusinfo'] as $key => $val) {
			?>

			<tr>
				<td><?php echo $key ?></td>
				<td><?php echo $val; ?></td>
			</tr>
		<?php } ?>
	</table>
</div>

<div class="table">
	状态:
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">名称</th>
			<th width="50px">值</th>
		</tr>
		<?php
		$i = 0;
		$pageData = B_View::getVal('pageData');
		foreach ($pageData['status'] as $key => $val) {
			$i++;
			if (!empty($val['Value'])) {
				?>

				<tr>
					<td><?php echo $val['Variable_name']; ?></td>
					<td><?php
						if ($val['Variable_name'] == 'Uptime') {
							echo B_Utils::formatTime($val['Value']);
						} else {
							echo $val['Value'];
						}

						?></td>
				</tr>
			<?php
			}
		} ?>
	</table>
	共有 <?php echo $i; ?> 个进程

</div>

<div class="table">
	变量:
	<table id="list" class="listing form" cellpadding="0" cellspacing="0">
		<tr>
			<th width="50px">名称</th>
			<th width="50px">值</th>
		</tr>
		<?php
		$i = 0;
		foreach ($pageData['list'] as $key => $val) {
			$i++;
			?>
			<tr>
				<td><?php echo $val['Variable_name']; ?></td>
				<td><?php
					if ($val['Variable_name'] == 'optimizer_switch') {
						echo implode(' ', explode(',', $val['Value']));
					} else {
						echo $val['Value'];
					}
					?></td>
			</tr>
		<?php } ?>
	</table>
	共有 <?php echo $i; ?> 个进程

</div>

计算表扫描率：Handler_read_rnd_next / Com_select <br>
如果表扫描率超过4000，说明进行了太多表扫描，很有可能索引没有建好，增加read_buffer_size值会有一些好处，但最好不要超过8MB。<br>

Max_used_connections / max_connections * 100% ≈ 85%
最大连接数占上限连接数的85％左右，如果发现比例在10%以下，MySQL服务器连接数上限设置的过高了。

Key_blocks_unused表示未使用的缓存簇(blocks)数，Key_blocks_used表示曾经用到的最大的blocks数，比如这台服务器，所有的缓存都用到了，要么增加key_buffer_size，要么就是过渡索引了，把缓存占满了。比较理想的设置：
Key_blocks_used / (Key_blocks_unused + Key_blocks_used) * 100% ≈ 80%

MySQL查询缓存变量解释：
Qcache_free_blocks：缓存中相邻内存块的个数。数目大说明可能有碎片。FLUSH QUERY CACHE会对缓存中的碎片进行整理，从而得到一个空闲块。<br>
Qcache_free_memory：缓存中的空闲内存。<br>
Qcache_hits：每次查询在缓存中命中时就增大<br>
Qcache_inserts：每次插入一个查询时就增大。命中次数除以插入次数就是不中比率。<br>
Qcache_lowmem_prunes：缓存出现内存不足并且必须要进行清理以便为更多查询提供空间的次数。这个数字最好长时间来看；如果这个数字在不断增长，就表示可能碎片非常严重，或者内存很少。（上面的 free_blocks和free_memory可以告诉您属于哪种情况）
<br>
Qcache_not_cached：不适合进行缓存的查询的数量，通常是由于这些查询不是 SELECT 语句或者用了now()之类的函数。<br>
Qcache_queries_in_cache：当前缓存的查询（和响应）的数量。<br>
Qcache_total_blocks：缓存中块的数量。 <br>

query_cache_limit：超过此大小的查询将不缓存<br>
query_cache_min_res_unit：缓存块的最小大小<br>
query_cache_size：查询缓存大小<br>
query_cache_type：缓存类型，决定缓存什么样的查询，示例中表示不缓存 select sql_no_cache 查询<br>
query_cache_wlock_invalidate：当有其他客户端正在对MyISAM表进行写操作时，如果查询在query cache中，是否返回cache结果还是等写操作完成再读表获取结果。<br>
query_cache_min_res_unit的配置是一柄”双刃剑”，默认是4KB，设置值大对大数据查询有好处，但如果你的查询都是小数据查询，就容易造成内存碎片和浪费。<br>

查询缓存碎片率 = Qcache_free_blocks / Qcache_total_blocks * 100%<br>

如果查询缓存碎片率超过20%，可以用FLUSH QUERY CACHE整理缓存碎片，或者试试减小query_cache_min_res_unit，如果你的查询都是小数据量的话。<br>

查询缓存利用率 = (query_cache_size - Qcache_free_memory) / query_cache_size * 100%<br>

查询缓存利用率在25%以下的话说明query_cache_size设置的过大，可适当减小；查询缓存利用率在80％以上而且Qcache_lowmem_prunes > 50的话说明query_cache_size可能有点小，要不就是碎片太多。
<br>

查询缓存命中率 = (Qcache_hits - Qcache_inserts) / Qcache_hits * 100%<br>

示例服务器 查询缓存碎片率 ＝ 20.46％，查询缓存利用率 ＝ 62.26％，查询缓存命中率 ＝ 1.94％，命中率很差，可能写操作比较频繁吧，而且可能有些碎片。<br>

文件打开数 比较合适的设置：Open_files / open_files_limit * 100% <= 75％<br>

Table_locks_immediate表示立即释放表锁数，Table_locks_waited表示需要等待的表锁数，如果Table_locks_immediate / Table_locks_waited > 5000，最好采用InnoDB引擎，因为InnoDB是行锁而MyISAM是表锁，对于高并发写入的应用InnoDB效果会好些。示例中的服务器Table_locks_immediate / Table_locks_waited ＝ 235，MyISAM就足够了。
<br>

每次创建临时表，Created_tmp_tables增加，如果是在磁盘上创建临时表，Created_tmp_disk_tables也增加,Created_tmp_files表示MySQL服务创建的临时文件文件数，比较理想的配置是：Created_tmp_disk_tables / Created_tmp_tables * 100% <= 25%
<br>

Open_tables表示打开表的数量，Opened_tables表示打开过的表数量，如果Opened_tables数量过大，说明配置中table_cache(5.1.3之后这个值叫做table_open_cache)值可能太小，我们查询一下服务器table_cache值：
比较合适的值为：Open_tables / Opened_tables * 100% >= 85%, Open_tables / table_cache * 100% <= 95% <br>

key_cache_miss_rate ＝ Key_reads / Key_read_requests * 100%
比如上面的数据，key_cache_miss_rate为0.0244%，4000个索引读取请求才有一个直接读硬盘，已经很BT了，key_cache_miss_rate在0.1%以下都很好（每1000个请求有一个直接读硬盘），如果key_cache_miss_rate在0.01%以下的话，key_buffer_size分配的过多，可以适当减少。