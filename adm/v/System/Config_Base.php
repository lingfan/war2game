<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'系统管理' => 'System/index',
	'基础配置' => 'System/ConfigBase',
);
$baselist = $pageData['baselist'];
$arr_auc_time_cost = $baselist['auc_time_cost'];
?>
<script type="text/javascript" src="styles/adm/js/My97DatePicker/WdatePicker.js"></script>
<script>
	document.getElementById('start_time').innerText =<?php echo isset($pageData['list'][1]) ? $pageData['list'][1] : '';?>;
</script>
<div class="top-bar">
	<h1>基础配置</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="table">
<iframe name="iframe" style="display: none;"></iframe>
<form action="?r=System/ConfigBase&act=edit" method="post" target="iframe">
<table class="listing form" cellpadding="0" cellspacing="0">
<tr>
	<th class="full" colspan="2">基础配置信息 重要内容！请勿随意更改！！！</th>
</tr>


<tr>
	<td width="150"><strong>金币初始值</strong></td>
	<td><input type="text" class="text" name="city_base_gold" value="<?php echo $baselist['city_base_gold']; ?>"/></td>
</tr>

<tr>
	<td><strong>粮食初始值</strong></td>
	<td><input type="text" class="text" name="city_base_food" value="<?php echo $baselist['city_base_food']; ?>"/></td>
</tr>

<tr>
	<td><strong>石油初始值</strong></td>
	<td><input type="text" class="text" name="city_base_oil" value="<?php echo $baselist['city_base_oil']; ?>"/></td>
</tr>

<tr>
	<td><strong>金币初始增长值/小时</strong></td>
	<td><input type="text" class="text" name="city_gold_grow" value="<?php echo $baselist['city_gold_grow']; ?>"/></td>
</tr>

<tr>
	<td><strong>食物初始增长值/小时</strong></td>
	<td><input type="text" class="text" name="city_food_grow" value="<?php echo $baselist['city_food_grow']; ?>"/></td>
</tr>

<tr>
	<td><strong>石油初始增长值/小时</strong></td>
	<td><input type="text" class="text" name="city_oil_grow" value="<?php echo $baselist['city_oil_grow']; ?>"/></td>
</tr>

<tr>
	<td><strong>初始仓库容量</strong></td>
	<td><input type="text" class="text" name="city_max_store" value="<?php echo $baselist['city_max_store']; ?>"/></td>
</tr>

<tr>
	<td><strong>初始人口数量</strong></td>
	<td><input type="text" class="text" name="city_max_people" value="<?php echo $baselist['city_max_people']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>初始允许最大建筑CD列</strong></td>
	<td><input type="text" class="text" name="base_cd_build_num" value="<?php echo $baselist['base_cd_build_num']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>最终允许最大建筑CD列</strong></td>
	<td><input type="text" class="text" name="final_cd_build_num"
	           value="<?php echo $baselist['final_cd_build_num']; ?>"/></td>
</tr>

<tr>
	<td><strong>各建筑队列开启的花费[多个用逗号隔开]</strong></td>
	<td><input type="text" class="text" name="build_list_cost"
	           value="<?php echo implode(',', $baselist['build_list_cost']); ?>"/></td>
</tr>

<tr>
	<td><strong>各城市等级可最多建筑仓库数量[多个用逗号隔开]</strong></td>
	<td><input type="text" class="text" name="city_max_store_num"
	           value="<?php echo implode(',', $baselist['city_max_store_num']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td><strong>各城市等级可最多建筑住宅数量[多个用逗号隔开]</strong></td>
	<td><input type="text" class="text" name="city_max_house_num"
	           value="<?php echo implode(',', $baselist['city_max_house_num']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td><strong>最终允许最大科技CD列</strong></td>
	<td><input type="text" class="text" name="final_cd_tech_num" value="<?php echo $baselist['final_cd_tech_num']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>各科技队列开启的花费</strong></td>
	<td><input type="text" class="text" name="tech_list_cost"
	           value="<?php echo implode(',', $baselist['tech_list_cost']); ?>"/></td>
</tr>

<tr>
	<td><strong>普通玩家活力上限</strong></td>
	<td><input type="text" class="text" name="user_energy_limit" value="<?php echo $baselist['user_energy_limit']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>普通玩家军令上限</strong></td>
	<td><input type="text" class="text" name="user_mil_order_limit"
	           value="<?php echo $baselist['user_mil_order_limit']; ?>"/></td>
</tr>

<tr>
	<td><strong>玩家每小时增加活力值</strong></td>
	<td><input type="text" class="text" name="user_energy_incr" value="<?php echo $baselist['user_energy_incr']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>玩家每小时增加军令值</strong></td>
	<td><input type="text" class="text" name="user_mil_order_incr"
	           value="<?php echo $baselist['user_mil_order_incr']; ?>"/></td>
</tr>

<tr>
	<td><strong>过新手保护期需要功勋</strong></td>
	<td><input type="text" class="text" name="city_newbie_mil_medal"
	           value="<?php echo $baselist['city_newbie_mil_medal']; ?>"/></td>
</tr>

<tr>
	<td><strong>充值军饷汇率(1平台币等于多少军饷)</strong></td>
	<td><input type="text" class="text" name="milpay_exchange" value="<?php echo $baselist['milpay_exchange']; ?>"/>
	</td>
</tr>

<tr>
	<td><strong>城内面积</strong></td>
	<td>
		X<input type="text" class="text" name="city_in_area_x" value="<?php echo $baselist['city_in_area_x']; ?>"
		        style="width: 50px;"/>
		Y<input type="text" class="text" name="city_in_area_y" value="<?php echo $baselist['city_in_area_y']; ?>"
		        style="width: 50px;"/>
	</td>
</tr>

<tr>
	<td><strong>建筑面积</strong></td>
	<td>
		X<input type="text" class="text" name="build_area_x" value="<?php echo $baselist['build_area_x']; ?>"
		        style="width: 50px;"/>
		Y<input type="text" class="text" name="build_area_y" value="<?php echo $baselist['build_area_y']; ?>"
		        style="width: 50px;"/>
	</td>
</tr>

<tr>
	<td><strong>战斗面积</strong></td>
	<td>
		X<input type="text" class="text" name="war_area_x" value="<?php echo $baselist['war_area_x']; ?>"
		        style="width: 50px;"/>
		Y<input type="text" class="text" name="war_area_y" value="<?php echo $baselist['war_area_y']; ?>"
		        style="width: 50px;"/>
	</td>
</tr>

<tr>
	<td><strong>特殊武器槽总数</strong></td>
	<td><input type="text" class="text" name="weapon_max_special"
	           value="<?php echo $baselist['weapon_max_special']; ?>"/></td>
</tr>

<tr>
	<td><strong>各武器槽开启的花费</strong></td>
	<td><input type="text" class="text" name="weapon_slot_cost"
	           value="<?php echo implode(',', $baselist['weapon_slot_cost']); ?>" style="width:400px"/></td>
</tr>

<tr>
	<td><strong>兵种可升至最大等级</strong></td>
	<td><input type="text" class="text" name="army_max_level" value="<?php echo $baselist['army_max_level']; ?>"/></td>
</tr>

<tr>
	<td><strong>拍卖行2种保管方式[小时数,军饷,点券]</strong></td>
	<td>
		第一种 <input type="text" class="text" name="auc_time_cost_1"
		           value="<?php echo implode(',', $arr_auc_time_cost[0]); ?>"/><br/>
		第二种 <input type="text" class="text" name="auc_time_cost_2"
		           value="<?php echo implode(',', $arr_auc_time_cost[1]); ?>"/>
	</td>
</tr>

<tr>
	<td><strong>战区地形</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<?php
			$map_zone_terrain = $baselist['map_zone_terrain'];

			if (!empty($map_zone_terrain)):
				foreach (T_App::$map as $key => $val) {
					?>
					<tr>
						<td><?php echo $val; ?></td>
						<td>
							<?php foreach (T_App::$terrain as $k => $v) { ?>
								<input type="checkbox" name="map_zone_terrain[<?php echo $key; ?>][]"
								       value="<?php echo $k; ?>" <?php if (in_array($k, $map_zone_terrain[$key])) {
									echo 'checked="checked"';
								} ?>><?php echo $v; ?>
							<?php } ?>
						</td>
					</tr>
				<?php
				}
			endif;
			?>
		</table>
	</td>
</tr>

<tr>
	<td><strong>天气刷新间隔（小时）</strong></td>
	<td><input type="text" class="text" name="weather_refresh_interval"
	           value="<?php echo $baselist['weather_refresh_interval']; ?>"/></td>
</tr>

<tr>
	<td><strong>占领时间间隔（小时）</strong></td>
	<td><input type="text" class="text" name="hold_time_interval"
	           value="<?php echo $baselist['hold_time_interval']; ?>"/></td>
</tr>
<tr>
	<td><strong>占领城市时间间隔（小时）</strong></td>
	<td><input type="text" class="text" name="hold_city_time_interval"
	           value="<?php echo $baselist['hold_city_time_interval']; ?>"/></td>
</tr>
<tr>
	<td><strong>解救自己的CD时间(分钟)</strong></td>
	<td>
		<input type="text" class="text" name="rescue_cd"
		       value="<?php echo $baselist['rescue_cd']; ?>"
			/>
	</td>
</tr>
<tr>
	<td><strong>解救自己城市清除CD时间花费军饷设置</strong></td>
	<td><input type="text" class="text" name="rescue_cd_times" value="<?php echo $baselist['rescue_cd_times']; ?>"/>(免费次数,基础军饷数,最大军饷)
	</td>
</tr>
<tr>
	<td><strong>税收属地的CD时间(分钟)</strong></td>
	<td>
		<input type="text" class="text" name="tax_cd"
		       value="<?php echo $baselist['tax_cd']; ?>"
			/>
	</td>
</tr>

<tr>
	<td><strong>城市被占领后得到的粮食为原产量的百分比</strong></td>
	<td>
		<input type="text" class="text" name="food_reduce"
		       value="<?php echo $baselist['food_reduce']; ?>"
			/>%
	</td>
</tr>
<tr>
	<td><strong>城市被占领后得到的石油为原产量的百分比</strong></td>
	<td>
		<input type="text" class="text" name="oil_reduce"
		       value="<?php echo $baselist['oil_reduce']; ?>"
			/>%
	</td>
</tr>
<tr>
	<td><strong>城市被占领后得到的金钱为原产量的百分比</strong></td>
	<td>
		<input type="text" class="text" name="gold_reduce"
		       value="<?php echo $baselist['gold_reduce']; ?>"
			/>%
	</td>
</tr>
<tr>
	<td><strong>获取战绩值活动起始时间</strong></td>
	<td>
		起始时间：<input type="text" name="record_start" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
		            value="<?php echo isset($pageData['record_list']['start']) ? $pageData['record_list']['start'] : ''; ?>">

		截止时间：<input type="text" name="record_end" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"
		            value="<?php echo isset($pageData['record_list']['end']) ? $pageData['record_list']['end'] : ''; ?>">
</tr>
<tr>
	<td><strong>占领城市可获得的战绩值</strong></td>
	<td>
		3级<input type="text" size=5 name="record[record_value_3]"
		         value="<?php echo isset($pageData['record_list']['list']['record_value_3']) ? $pageData['record_list']['list']['record_value_3'] : ''; ?>"
			/>
		4级<input type="text" size=5 name="record[record_value_4]"
		         value="<?php echo isset($pageData['record_list']['list']['record_value_4']) ? $pageData['record_list']['list']['record_value_4'] : ''; ?>"
			/>
		5级<input type="text" size=5 name="record[record_value_5]"
		         value="<?php echo isset($pageData['record_list']['list']['record_value_5']) ? $pageData['record_list']['list']['record_value_5'] : ''; ?>"
			/>
		成功解救1次<input type="text" size=5 name="record[record_value_rescue]"
		             value="<?php echo isset($pageData['record_list']['list']['record_value_rescue']) ? $pageData['record_list']['list']['record_value_rescue'] : ''; ?>"
			/>
		&nbsp;&nbsp;&nbsp;战绩值排行缓存<a href="?r=System/RankCacheUp"
		                            class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		                            target="iframe">清除</a>
	</td>
</tr>
<tr>
	<td><strong>1级城市</strong></td>
	<td>
		每次被攻击失败损失驻军
		<input type="text" class="text" name="city_level_1[loss]" style="width: 50px;"
		       value="<?php echo isset($pageData['atk_list']['city_level_1']['loss']) ? $pageData['atk_list']['city_level_1']['loss'] : ''; ?>"/>%，
		被攻击失败<input type="text" style="width: 50px;" class="text" name="city_level_1[num]"
		            value="<?php echo isset($pageData['atk_list']['city_level_1']['num']) ? $pageData['atk_list']['city_level_1']['num'] : ''; ?>"/>次后，不损失驻军

	</td>
</tr>
<tr>
	<td><strong>2级城市</strong></td>
	<td>
		每次被攻击失败损失驻军
		<input type="text" class="text" name="city_level_2[loss]" style="width: 50px;"
		       value="<?php echo isset($pageData['atk_list']['city_level_2']['loss']) ? $pageData['atk_list']['city_level_2']['loss'] : ''; ?>"/>%，
		被攻击失败<input type="text" class="text" name="city_level_2[num]" style="width: 50px;"
		            value="<?php echo isset($pageData['atk_list']['city_level_2']['num']) ? $pageData['atk_list']['city_level_2']['num'] : ''; ?>"/>次后，不损失驻军

	</td>
</tr>
<tr>
	<td><strong>3级城市</strong></td>
	<td>
		每次被攻击失败损失驻军
		<input type="text" class="text" name="city_level_3[loss]" style="width: 50px;"
		       value="<?php echo isset($pageData['atk_list']['city_level_3']['loss']) ? $pageData['atk_list']['city_level_3']['loss'] : ''; ?>"/>%，
		被攻击失败<input type="text" class="text" name="city_level_3[num]" style="width: 50px;"
		            value="<?php echo isset($pageData['atk_list']['city_level_3']['num']) ? $pageData['atk_list']['city_level_3']['num'] : ''; ?>"/>次后，不损失驻军

	</td>
</tr>
<tr>
	<td><strong>4级城市</strong></td>
	<td>
		每次被攻击失败损失驻军
		<input type="text" class="text" name="city_level_4[loss]" style="width: 50px;"
		       value="<?php echo isset($pageData['atk_list']['city_level_4']['loss']) ? $pageData['atk_list']['city_level_4']['loss'] : ''; ?>"/>%，
		被攻击失败<input type="text" class="text" name="city_level_4[num]" style="width: 50px;"
		            value="<?php echo isset($pageData['atk_list']['city_level_4']['num']) ? $pageData['atk_list']['city_level_4']['num'] : ''; ?>"/>次后，不损失驻军

	</td>
</tr>
<tr>
	<td><strong>5级城市</strong></td>
	<td>
		每次被攻击失败损失驻军
		<input type="text" class="text" name="city_level_5[loss]" style="width: 50px;"
		       value="<?php echo isset($pageData['atk_list']['city_level_5']['loss']) ? $pageData['atk_list']['city_level_5']['loss'] : ''; ?>"/>%，
		被攻击失败<input type="text" class="text" name="city_level_5[num]" style="width: 50px;"
		            value="<?php echo isset($pageData['atk_list']['city_level_5']['num']) ? $pageData['atk_list']['city_level_5']['num'] : ''; ?>"/>次后，不损失驻军

	</td>
</tr>
<tr>
	<td><strong>攻击战损开启条件</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<?php foreach (M_War::$warLoss as $value) { ?>
				<tr>
					<td>威望值区间：
						<input name="loss[<?php echo $value . 'before' ?>]" type="text" style="width: 50px;"
						       value="<?php echo isset($pageData['atk_loss_open'][$value]['before']) ? $pageData['atk_loss_open'][$value]['before'] : ''; ?>">~
						<input name="loss[<?php echo $value . 'after' ?>]" type="text" style="width: 50px;"
						       value="<?php echo isset($pageData['atk_loss_open'][$value]['after']) ? $pageData['atk_loss_open'][$value]['after'] : ''; ?>">
					</td>
					<td>威望差多少以内开启：
						<input name="loss[<?php echo $value . 'diff' ?>]" type="text" style="width: 50px;"
						       value="<?php echo isset($pageData['atk_loss_open'][$value]['diff']) ? $pageData['atk_loss_open'][$value]['diff'] : ''; ?>">
					</td>

				</tr>
			<?php } ?>
		</table>
	</td>
</tr>
<tr>
	<td><strong>地形对应天气</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<?php
			$map_zone_weather = $baselist['map_zone_weather'];
			?>
			<?php foreach (T_App::$terrain as $key => $val) { ?>
				<tr>
					<td><?php echo $val; ?></td>
					<td>
						<?php foreach (T_App::$weather as $k => $v) { ?>
							<input type="checkbox" name="map_zone_weather[<?php echo $key; ?>][]"
							       value="<?php echo $k; ?>" <?php if (in_array($k, $map_zone_weather[$key])) {
								echo 'checked="checked"';
							} ?>><?php echo $v; ?>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</td>
</tr>

<tr>
	<td><strong>各等级强化石可强化装备等级范围</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<?php
			$props_strong_level = $baselist['props_strong_level'];
			?>
			<?php foreach ($props_strong_level as $key => $val) { ?>
				<tr>
					<td><?php echo $key; ?>级强化石</td>
					<td>
						可强化装备等级范围：装备最小<input type="text" name="props_strong_level[<?php echo $key; ?>][min]"
						                     value="<?php echo $val['min']; ?>" style="width: 50px;">级&nbsp;&nbsp;&nbsp;&nbsp;
						最大<input type="text" name="props_strong_level[<?php echo $key; ?>][max]"
						         value="<?php echo $val['max']; ?>" style="width: 50px;">级
					</td>
				</tr>
			<?php } ?>
		</table>
	</td>
</tr>


<tr>
	<td><strong>付费操作[军饷/点券]价格</strong></td>
	<td>
		<table cellspacing="0px" border="0" style="font-size: 12px;">
			<tr>
				<td></td>
				<td>军饷</td>
				<td>点券</td>
			</tr>
			<?php
			$pay_action_value = $baselist['pay_action_value'];
			foreach (T_Effect::$payAction as $key => $name):
				$val[$key] = $pay_action_value[$key];
				?>
				<tr>
					<td><?php echo $name; ?></td>
					<td><input type="text" name="<?php echo "pay_action_value[{$key}]" ?>[<?php echo T_App::MILPAY; ?>]"
					           value="<?php echo $val[$key][T_App::MILPAY]; ?>" style="width: 50px;"></td>
					<td><input type="text" name="<?php echo "pay_action_value[{$key}]" ?>[<?php echo T_App::COUPON; ?>]"
					           value="<?php echo $val[$key][T_App::COUPON]; ?>" style="width: 50px;"></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</td>
</tr>

<tr>
	<td><strong>洲对应的战场地图</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<?php
			$wmz = $baselist['war_map_zone'];
			foreach (T_App::$map as $zone => $val):
				foreach (T_App::$terrain as $t => $v):
					$k = $zone . '_' . $t;
					?>
					<tr>
						<td width="150px"><?php echo $val; ?>洲 #<?php echo $v ?>地形：</td>
						<td>
							<input type="text" class="text" name="war_map_zone[<?php echo $k; ?>]"
							       id="war_map_zone<?php echo $k; ?>" value="<?php echo $wmz[$k]; ?>">
						</td>
					</tr>
				<?php
				endforeach;
			endforeach;?>
		</table>
	</td>
</tr>

<tr>
	<td><strong>登入奖励</strong></td>
	<td>
		起始时间：
		<select name="start_time" class="combox">
			<option value="0"<?php if (isset($pageData['list'][1]) && 0 == $pageData['list'][1]) {
				echo 'selected="selected"';
			} ?>><?php echo '0'; ?></option>
			<option
				onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"<?php if (isset($pageData['list'][1]) && 0 != $pageData['list'][1]) {
				echo 'selected="selected"';
			} ?>><?php echo isset($pageData['list'][1]) ? $pageData['list'][1] : ''; ?></option>
		</select>
		截止时间：<input type="text" name="end_time" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})"
		            value="<?php echo isset($pageData['list'][2]) ? $pageData['list'][2] : ''; ?>">
</tr>
<tr>
	<td><strong>每日登陆奖励</strong></td>
	<td><input type="text" class="text" name="daily_login_award"
	           value="<?php echo isset($pageData['list'][3]) ? $pageData['list'][3] : ''; ?>"/>
		设置创建城市天数奖励ID
	</td>
</tr>

<tr>
	<td><strong>学院奖励活动起始时间</strong></td>
	<td>
		起始时间：<input type="text" name="start" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})"
		            value="<?php echo isset($pageData['active_list']['start']) ? $pageData['active_list']['start'] : ''; ?>">

		截止时间：<input type="text" name="end" class="Wdate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})"
		            value="<?php echo isset($pageData['active_list']['end']) ? $pageData['active_list']['end'] : ''; ?>">
</tr>

<tr>
	<td><strong>学院奖励(第一阶段)</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>占领步兵学院的奖励</td>
				<td>
					<input name="pros[hold_npc_type_1]" type="text"
					       value="<?php echo isset($pageData['active_list']['list']['hold_npc_type_1']) ? $pageData['active_list']['list']['hold_npc_type_1'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list']['hold_npc_type_1']) ? $pageData['active_list']['list']['hold_npc_type_1'] : ''); ?>
				</td>

			</tr>

			<tr>
				<td>占领炮兵学院的奖励</td>
				<td>
					<input name="pros[hold_npc_type_2]" type="text"
					       value="<?php echo isset($pageData['active_list']['list']['hold_npc_type_2']) ? $pageData['active_list']['list']['hold_npc_type_2'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list']['hold_npc_type_2']) ? $pageData['active_list']['list']['hold_npc_type_2'] : ''); ?>
				</td>
			</tr>

			<tr>
				<td>占领装甲兵学院的奖励</td>
				<td>
					<input name="pros[hold_npc_type_3]" type="text"
					       value="<?php echo isset($pageData['active_list']['list']['hold_npc_type_3']) ? $pageData['active_list']['list']['hold_npc_type_3'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list']['hold_npc_type_3']) ? $pageData['active_list']['list']['hold_npc_type_3'] : ''); ?>
				</td>
			</tr>

			<tr>
				<td>占领空军学院的奖励</td>
				<td>
					<input name="pros[hold_npc_type_4]" type="text"
					       value="<?php echo isset($pageData['active_list']['list']['hold_npc_type_4']) ? $pageData['active_list']['list']['hold_npc_type_4'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list']['hold_npc_type_4']) ? $pageData['active_list']['list']['hold_npc_type_4'] : ''); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><strong>学院奖励(第二阶段)</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>占领任意玩家已占领的一个步兵学院的奖励</td>
				<td>
					<input name="pros2[hold_npc_type_1]" type="text"
					       value="<?php echo isset($pageData['active_list']['list2']['hold_npc_type_1']) ? $pageData['active_list']['list2']['hold_npc_type_1'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list2']['hold_npc_type_1']) ? $pageData['active_list']['list2']['hold_npc_type_1'] : ''); ?>
				</td>

			</tr>

			<tr>
				<td>占领任意玩家已占领的一个炮兵学院的奖励</td>
				<td>
					<input name="pros2[hold_npc_type_2]" type="text"
					       value="<?php echo isset($pageData['active_list']['list2']['hold_npc_type_2']) ? $pageData['active_list']['list2']['hold_npc_type_2'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list2']['hold_npc_type_2']) ? $pageData['active_list']['list2']['hold_npc_type_2'] : ''); ?>
				</td>
			</tr>

			<tr>
				<td>占领任意玩家已占领的一个装甲兵学院的奖励</td>
				<td>
					<input name="pros2[hold_npc_type_3]" type="text"
					       value="<?php echo isset($pageData['active_list']['list2']['hold_npc_type_3']) ? $pageData['active_list']['list2']['hold_npc_type_3'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list2']['hold_npc_type_3']) ? $pageData['active_list']['list2']['hold_npc_type_3'] : ''); ?>
				</td>
			</tr>

			<tr>
				<td>占领任意玩家已占领的一个空军学院的奖励</td>
				<td>
					<input name="pros2[hold_npc_type_4]" type="text"
					       value="<?php echo isset($pageData['active_list']['list2']['hold_npc_type_4']) ? $pageData['active_list']['list2']['hold_npc_type_4'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list2']['hold_npc_type_4']) ? $pageData['active_list']['list2']['hold_npc_type_4'] : ''); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><strong>学院奖励(第三阶段)</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>占领任意一个玩家城市
					<input name="pros3[num]" type="text" style="width: 50px;"
					       value="<?php echo isset($pageData['active_list']['list3']['num']) ? $pageData['active_list']['list3']['num'] : ''; ?>">次
				</td>
				<td>

					奖励：<input name="pros3[award]" type="text"
					          value="<?php echo isset($pageData['active_list']['list3']['award']) ? $pageData['active_list']['list3']['award'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list3']['award']) ? $pageData['active_list']['list3']['award'] : ''); ?>
				</td>

			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><strong>学院奖励(第四阶段)</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>占领任意一个和自己城市等级一样玩家城市
					<input name="pros4[num]" type="text" style="width: 50px;"
					       value="<?php echo isset($pageData['active_list']['list4']['num']) ? $pageData['active_list']['list4']['num'] : ''; ?>">次
				</td>
				<td>

					奖励：<input name="pros4[award]" type="text"
					          value="<?php echo isset($pageData['active_list']['list4']['award']) ? $pageData['active_list']['list4']['award'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list4']['award']) ? $pageData['active_list']['list4']['award'] : ''); ?>
				</td>

			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><strong>学院奖励(第五阶段)</strong></td>
	<td>

		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<td>占领任意一个大于等于自己城市等级的玩家城市
					<input name="pros5[num]" type="text" style="width: 50px;"
					       value="<?php echo isset($pageData['active_list']['list5']['num']) ? $pageData['active_list']['list5']['num'] : ''; ?>">次
				</td>
				<td>

					奖励：<input name="pros5[award]" type="text"
					          value="<?php echo isset($pageData['active_list']['list5']['award']) ? $pageData['active_list']['list5']['award'] : ''; ?>">
					<?php echo B_Utils::awardText(isset($pageData['active_list']['list5']['award']) ? $pageData['active_list']['list5']['award'] : ''); ?>
				</td>

			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><strong>军衔升级</strong></td>
	<td>
		<?php
		$tmpArr = $baselist['mil_rank_renown'];
		$t = array();
		foreach ($tmpArr as $v) {
			$t[] = implode("|", $v);
		}

		$str = implode("\n", $t);
		?>
		(威望|升级奖励|每日奖励|攻击_防御_生命_暴击)<br>
		<textarea rows="10" cols="50" name="mil_rank_renown"><?php echo $str; ?></textarea>
		<br>等级总数:    <?php echo count($t); ?>
	</td>
</tr>

<tr>
	<td><strong>抽奖刷新花费</strong></td>
	<td><input type="text" class="text" name="lotter_refresh" value="<?php echo $baselist['lotter_refresh']; ?>"/>(免费次数,基础军饷数,最大军饷)
	</td>
</tr>
<tr>
	<td><strong>突围开启花费</strong></td>
	<td><input type="text" class="text" name="bout_times_cost"
	           value="<?php echo implode(',', $baselist['bout_times_cost']); ?>"/>(免费次数,初始花费,累加值,最大值)
	</td>
</tr>
<tr>
	<td><strong>多人副本购买次数花费</strong></td>
	<td><input type="text" class="text" name="multi_fb_buy_cost"
	           value="<?php echo implode(',', $baselist['multi_fb_buy_cost']); ?>"/>(免费次数,初始花费,累加值,最大值)
	</td>
</tr>

<tr>
	<td><strong>据点出征部队数量</strong></td>
	<td><input type="text" class="text" name="march_camp_max_num"
	           value="<?php echo $baselist['march_camp_max_num']; ?>"/></td>
</tr>


<tr>
	<td><strong>多人副本加成属性</strong></td>
	<td>
		加成_消耗军饷|加成_消耗军饷|加成_消耗军饷<br>
		<?php
		$tmp = $baselist['multi_fb_addition_cost'];
		$ttt = array('攻击', '防御', '生命', '复活兵');
		foreach (M_MultiFB::$addition as $key => $val):
			$str = array();
			if (!empty($tmp[$val])) {
				foreach ($tmp[$val] as $v) {
					$str[] = implode('_', $v);
				}
			}
			echo $ttt[$key];
			?>
			<input type="text" class="text" name="multi_fb_addition_cost[<?php echo $val; ?>]"
			       value="<?php echo implode('|', $str); ?>"/><br>
		<?php endforeach; ?>
	</td>
</tr>

<tr>
	<td><strong>兑换军饷对应成功率加成</strong></td>
	<td>
		军饷,成功率|军饷,成功率|军饷,成功率<br>
		<input type="text" class="text" name="exchange_milpay_succ_rate"
		       value="<?php echo $baselist['exchange_milpay_succ_rate']; ?>"/></td>
</tr>


<tr>
	<td><strong>图鉴</strong></td>
	<?php
	$str = '';
	foreach ($baselist['help_detail'] as $cate => $val) {
		foreach ($val as $type => $idArr) {
			$idStr = implode(",", $idArr);
			$str .= "{$cate},{$type},{$idStr}\n";
		}
	}

	?>
	<td>
		[分类[1图纸|2军官|3技能],子类[1|2|3],ID1,ID2,...]<br>
		<textarea rows="10" cols="100" name="help_detail"><?php echo $str; ?></textarea></td>
</tr>



<tr>
	<td><strong>快速战斗优化 地图（空为不开启,多个为随机）</strong></td>
	<td><input type="text" class="text" name="quick_map_no"
	           value="<?php echo implode(',', $baselist['quick_map_no']); ?>"/>9262,9261 (地图减少障碍和寻路,无法查看战报)
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="submit" class="button" name="submit" value="保存"/></td>
</tr>

</table>
</form>
</div>
