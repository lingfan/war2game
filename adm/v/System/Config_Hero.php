<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'英雄配置' => 'System/ConfigHero',
);
$baselist = $pageData['baselist'];
?>
<script>
	function addHeroExpRow() {
		var str = "<td><input type=\"text\" name=\"hero_exp_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_exp_val[]\" style=\"width: 80px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_exp_tb').append(newTR);
	}

	function addHeroMoodRow() {
		var str = "<td><input type=\"text\" name=\"hero_attr_mood_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_attr_mood_val[]\" style=\"width: 35px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_mood_tb').append(newTR);
	}

	function addHeroEnergyRow() {
		var str = "<td><input type=\"text\" name=\"hero_attr_energy_key[]\" style=\"width: 25px;\"> => <input type=\"text\" name=\"hero_attr_energy_val[]\" style=\"width: 35px;\"> <a href=\"javascript:void(0);\" onclick=\"del(this);\">删除</a></td>";
		var newTR = document.createElement('tr');
		newTR.innerHTML = str;
		$('#hero_energy_tb').append(newTR);
	}

	function del(a) {
		a.parentNode.parentNode.parentNode.removeChild(a.parentNode.parentNode);
	}
</script>

<div class="top-bar">
	<h1>英雄配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
<iframe name="iframe" style="display: none;"></iframe>
<form action="?r=System/ConfigHero&act=edit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<th class="full" colspan="2">英雄配置信息 重要内容！请勿随意更改！！！</th>
</tr>
<tr>
	<td width="180"><strong>英雄最大等级</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_maxlv"
	           value="<?php echo $baselist['hero_maxlv']; ?>"/></td>
</tr>

<tr>
	<td><strong>召募价格系数</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_base_value"
	           value="<?php echo $baselist['hero_base_value']; ?>"/></td>
</tr>

<tr>
	<td><strong>寻将重置间隔</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_find_interval"
	           value="<?php echo $baselist['hero_find_interval']; ?>"/>小时[12/24]
	</td>
</tr>

<tr>
	<td><strong>传奇英雄出现保留时间</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_succ_keep_time"
	           value="<?php echo $baselist['hero_succ_keep_time']; ?>"/>小时
	</td>
</tr>

<tr>
	<td><strong>复活英雄需要金钱系数</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_relife_gold"
	           value="<?php echo $baselist['hero_relife_gold']; ?>"/></td>
</tr>

<tr>
	<td><strong>出征英雄最大数量</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_num_troop"
	           value="<?php echo $baselist['hero_num_troop']; ?>"/></td>
</tr>

<tr>
	<td><strong>城市允许英雄最大数量</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_num_city_max"
	           value="<?php echo $baselist['hero_num_city_max']; ?>"/></td>
</tr>

<tr>
	<td><strong>概率范围（多少分之一）</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_max_rate_num"
	           value="<?php echo $baselist['hero_max_rate_num']; ?>"/></td>
</tr>

<tr>
	<td><strong>寻将花费</strong></td>
	<td>

		初级寻将(免费次数,初始花费,累加值,最大花费,最大次数)<br>
		<input style="width: 200px;" type="text" class="text" name="hero_seek_cost[1]"
		       value="<?php echo implode(',', $baselist['hero_seek_cost'][1]); ?>"/>
		<br>中级寻将(免费次数,初始花费,累加值,最大花费,最大次数)<br>
		<input style="width: 200px;" type="text" class="text" name="hero_seek_cost[2]"
		       value="<?php echo implode(',', $baselist['hero_seek_cost'][2]); ?>"/>
		<br>高级寻将(免费次数,初始花费,累加值,最大花费,最大次数)<br>
		<input style="width: 200px;" type="text" class="text" name="hero_seek_cost[3]"
		       value="<?php echo implode(',', $baselist['hero_seek_cost'][3]); ?>"/>
	</td>
</tr>

<tr>
	<td><strong>寻将军官</strong></td>
	<td>
		<?php

		$tmp = array();
		foreach ($baselist['hero_rate_time'][5] as $q => $v) {
			$tmp[] = implode("_", $v);
		}
		$str = implode("\n", $tmp);
		?>
		蓝色(军官ID_出现概率_招募概率_招募时间)<br>
		<textarea rows="5" cols="50" name="hero_rate_time[5]"><?php echo $str; ?></textarea>
		<?php
		$tmp = array();
		foreach ($baselist['hero_rate_time'][6] as $q => $v) {
			$tmp[] = implode("_", $v);
		}
		$str = implode("\n", $tmp);
		?>
		<br>紫色(军官ID_出现概率_招募概率_招募时间<br>
		<textarea rows="5" cols="50" name="hero_rate_time[6]"><?php echo $str; ?></textarea>
		<?php
		$tmp = array();
		foreach ($baselist['hero_rate_time'][7] as $q => $v) {
			$tmp[] = implode("_", $v);
		}
		$str = implode("\n", $tmp);
		?>
		<br>红色(军官ID_出现概率_招募概率_招募时间<br>
		<textarea rows="5" cols="50" name="hero_rate_time[7]"><?php echo $str; ?></textarea>
		<?php
		$tmp = array();
		foreach ($baselist['hero_rate_time'][8] as $q => $v) {
			$tmp[] = implode("_", $v);
		}
		$str = implode("\n", $tmp);
		?>
		<br>金色(军官ID_出现概率_招募概率_招募时间<br>
		<textarea rows="5" cols="50" name="hero_rate_time[8]"><?php echo $str; ?></textarea>
	</td>
</tr>

<tr>
	<td><strong>寻将概率</strong></td>
	<td>

		初级寻将(蓝|紫|红|金):<br>
		<input type="text" class="text" name="hero_seek_rate[1]"
		       value="<?php echo implode(",", $baselist['hero_seek_rate'][1]); ?>"/>

		<br>中级寻将(蓝|紫|红|金):<br>
		<input type="text" class="text" name="hero_seek_rate[2]"
		       value="<?php echo implode(",", $baselist['hero_seek_rate'][2]); ?>"/>

		<br>高级寻将(蓝|紫|红|金):<br>
		<input type="text" class="text" name="hero_seek_rate[3]"
		       value="<?php echo implode(",", $baselist['hero_seek_rate'][3]); ?>"/>

	</td>
</tr>

<tr>
	<td><strong>军官培养上限</strong></td>
	<td><?php $hero_train_limit = $baselist['hero_train_limit']; ?>
		<?php foreach (T_Hero::$heroQual as $key => $val) { ?>
			<?php echo $val; ?>:<input type="text" name="hero_train_limit[<?php echo $key; ?>]"
			                           value="<?php echo $hero_train_limit[$key]; ?>" style="width: 35px;">
		<?php } ?>
	</td>
</tr>

<tr>
	<td><strong>英雄等级对应的经验值</strong></td>
	<td>
		<?php
		$hero_exp = $baselist['hero_exp'];
		?>
		<textarea name="hero_exp" cols="50" rows="5"><?php echo implode(',', $hero_exp); ?></textarea>
		<?php
		echo "<br>";
		$curLv = count($hero_exp) - 1;
		$maxLv = $baselist['hero_maxlv'];
		if ($curLv == $maxLv) {
			echo "<span style=\"color:blue\">正确数据:{$curLv}/{$maxLv}</span><br>";
		} else {
			echo "<span style=\"color:red\">错误数据:{$curLv}/{$maxLv}</span><br>";
		}
		?>
	</td>
</tr>

<tr>
	<td><strong>英雄等级对应的情绪值</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td>等级 =》 情绪值 <a href="javascript:void(0);" onclick="addHeroMoodRow();">添加</a></td>
			</tr>
			<tbody id="hero_mood_tb">
			<?php
			$hero_attr_mood = $baselist['hero_attr_mood'];
			foreach ($hero_attr_mood as $key => $val) {
				?>
				<tr>
					<td>
						<input type="text" name="hero_attr_mood_key[]" value="<?php echo $key; ?>" style="width: 25px;">
						<input type="text" name="hero_attr_mood_val[]" value="<?php echo $val; ?>" style="width: 35px;">
						<a href="javascript:void(0);" onclick="del(this);">删除</a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</td>
</tr>

<tr>
	<td><strong>英雄等级对应的精力值</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td>等级 =》 精力值 <a href="javascript:void(0);" onclick="addHeroEnergyRow();">添加</a></td>
			</tr>
			<tbody id="hero_energy_tb">
			<?php
			$hero_attr_energy = $baselist['hero_attr_energy'];
			foreach ($hero_attr_energy as $key => $val) {
				?>
				<tr>
					<td>
						<input type="text" name="hero_attr_energy_key[]" value="<?php echo $key; ?>"
						       style="width: 25px;">
						<input type="text" name="hero_attr_energy_val[]" value="<?php echo $val; ?>"
						       style="width: 35px;"> <a href="javascript:void(0);" onclick="del(this);">删除</a>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td><strong>英雄学习技能成功概率</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_learn_skill_rate"
	           value="<?php echo $baselist['hero_learn_skill_rate']; ?>"/>%
	</td>
</tr>
<?php
$learn_skill = $baselist['hero_learn_skill'];
?>
<tr>
	<td><strong>英雄技能等级对应概率</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td></td>
				<td>技能等级1</td>
				<td>技能等级2</td>
				<td>技能等级3</td>
			</tr>
			<?php
			foreach (M_Skill::$skillGrade as $key => $name):
				$vName = "hero_learn_skill[{$key}]"
				?>
				<tr>
					<td><?php echo $name; ?></td>
					<td><input type="text" name="<?php echo $vName ?>[1]" value="<?php echo $learn_skill[$key][1]; ?>"
					           style="width: 50px;">%
					</td>
					<td><input type="text" name="<?php echo $vName ?>[2]" value="<?php echo $learn_skill[$key][2]; ?>"
					           style="width: 50px;">%
					</td>
					<td><input type="text" name="<?php echo $vName ?>[3]" value="<?php echo $learn_skill[$key][3]; ?>"
					           style="width: 50px;">%
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</td>
</tr>

<tr>
	<td><strong>军官培养</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td></td>
				<td>普通军功</td>
				<td>中级军饷</td>
				<td>高级军饷</td>
			</tr>
			<?php
			$herotrain = $baselist['hero_train'];
			?>
			<tr>
				<td>每次消耗</td>
				<td><input type="text" name="hero_train[cost][1]" value="<?php echo $herotrain['cost'][1]; ?>"
				           style="width: 50px;"></td>
				<td><input type="text" name="hero_train[cost][2]" value="<?php echo $herotrain['cost'][2]; ?>"
				           style="width: 50px;"></td>
				<td><input type="text" name="hero_train[cost][3]" value="<?php echo $herotrain['cost'][3]; ?>"
				           style="width: 50px;"></td>
			</tr>

			<tr>
				<td>最大消耗系数</td>
				<td><input type="text" name="hero_train[max][1]" value="<?php echo $herotrain['max'][1]; ?>"
				           style="width: 50px;"></td>
				<td><input type="text" name="hero_train[max][2]" value="<?php echo $herotrain['max'][2]; ?>"
				           style="width: 50px;"></td>
				<td><input type="text" name="hero_train[max][3]" value="<?php echo $herotrain['max'][3]; ?>"
				           style="width: 50px;"></td>
			</tr>

		</table>
	</td>
</tr>
<tr>
	<td><strong>军官培养免费次数</strong></td>
	<td><input style="width: 50px;" type="text" class="text" name="hero_train_free"
	           value="<?php echo $baselist['hero_train_free']; ?>"/>次
	</td>
</tr>

<tr>
	<td><strong>军官兑换模式</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td>品质</td>
				<td>模式</td>
				<td>军饷</td>
				<td>奖励ID</td>
			</tr>
			<?php
			foreach (T_Hero::$heroQual as $key => $name):
				?>
				<?php
				foreach (T_Hero::$type as $keyType => $type):
					?>
					<tr>
					<td><?php echo $name; ?></td>
					<td><?php echo $type; ?></td>
					<td><input type="text"
					           name="random_cost[<?php echo $key; ?>][<?php echo $keyType; ?>]"
					           value="<?php echo isset($pageData['heroExchange'][$key][$keyType]['cost']) ? $pageData['heroExchange'][$key][$keyType]['cost'] : ''; ?>"
					           style="width: 80px;"/></td>
					<td><input type="text"
					           name="award_id[<?php echo $key; ?>][<?php echo $keyType; ?>]"
					           value="<?php echo isset($pageData['heroExchange'][$key][$keyType]['awardId']) ? $pageData['heroExchange'][$key][$keyType]['awardId'] : ''; ?>"
					           style="width: 80px;"/>
						<?php
						echo B_Utils::awardText($pageData['heroExchange'][$key][$keyType]['awardId']);
						?>
					</td>
				<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>


		</table>
	</td>
</tr>


<tr>
	<td><strong>军官转生配置</strong></td>
	<td>
		等级,成功率,转生丹数量,成长值|等级,成功率,转生丹数量,成长值<br>
		<input style="width: 500px;" type="text" class="text" name="hero_recycle"
		       value="<?php echo $baselist['hero_recycle']; ?>"/>
		<br>
		<?php
		$arr1 = explode("|", $baselist['hero_recycle']);
		$i = 1;
		foreach ($arr1 as $val) {
			$tmp = explode(",", $val);
			echo "{$i}转#等级{$tmp[0]},成功率{$tmp[1]},转生丹数量{$tmp[2]},成长值{$tmp[3]}<br>";
			$i++;
		}
		?>
	</td>
</tr>
<tr>
	<td><strong>军官转生出售属性点</strong></td>
	<td>
		1转(品质1属性点,品质2属性点)|2转(品质1属性点,品质1属性点)<br>
		<input style="width: 500px;" type="text" class="text" name="hero_recycle_attr"
		       value="<?php echo $baselist['hero_recycle_attr']; ?>"/>
		<br>
		<?php
		$arr2 = explode("|", $baselist['hero_recycle_attr']);
		$num1 = count($arr1);
		$num2 = count($arr2);
		if ($num1 != $num2) {
			echo "<span style=\"color:red\">转生数错误{$num1},{$num2}</span><br>";
		}
		$i = 1;
		foreach ($arr2 as $val) {
			echo "====={$i}转======<br>";
			$tmp = explode(",", $val);
			$n = 0;
			foreach ($tmp as $v) {
				$n++;
				echo "品质{$n}#属性点:{$v}<br>";
			}
			$i++;
			if (count(T_Hero::$heroQual) != $n) {
				echo "<span style=\"color:red\">错误品质{$n}</span><br>";
			}
		}
		?>
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" class="button" name="submit" value="保存"/></td>
</tr>
</table>
</form>
</div>
