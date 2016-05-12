<?php
$pageData = B_View::getVal('pageData');
$urlArr = array(
	'首页' => 'index/index',
	'用户列表' => '',
);
?>
<div class="top-bar">
	<h1>用户管理</h1>

	<div class="breadcrumbs">
		<?php echo B_Common::breadcrumb($urlArr); ?>
	</div>
</div>
<div class="select-bar">
	<label>
		<input type="text" name="textfield"/>
	</label>
	<label>
		<input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit"
		       name="Submit" value="Search"/>
	</label>
</div>
<div class="table">

	<table class="listing" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th>用户账号</th>
			<th>元首名</th>
			<!-- <th>城市ID</th> -->
			<th>VIP等级</th>
			<th>威望</th>
			<th>军功</th>
			<th>军饷</th>
			<th>点券</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		<?php foreach ($pageData['list'] as $key => $val): ?>
			<tr>
				<td><?php echo $val['id']; ?></td>
				<td><?php echo $val['username']; ?></td>
				<td><?php echo $val['nickname'] ? $val['nickname'] : '--'; ?></td>
				<!-- <td><?php echo $val['city_id'];?></td> -->
				<td><?php echo $val['vip_level'] ? $val['vip_level'] : '0'; ?></td>
				<td><?php echo $val['renown'] ? $val['renown'] : '0'; ?></td>
				<td><?php echo $val['mil_medal'] ? $val['mil_medal'] : '0'; ?></td>
				<td><?php echo $val['mil_pay'] ? $val['mil_pay'] : '0'; ?></td>
				<td><?php echo $val['coupon'] ? $val['coupon'] : '0'; ?></td>
				<td><?php echo $val['status'] ? '禁止' : '正常'; ?></td>
				<td>
					<a href="?r=user/del&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/del-icon.gif"
					                                                        width="16" height="16" alt="删除"/></a>
					<a href="?r=user/add&id=<?php echo $val['id']; ?>"><img src="styles/adm/images/edit-icon.gif"
					                                                        width="16" height="16" alt="编辑"/></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<div class="select" style="width: 550px;">
		<strong>
			<?php
			foreach ($pageData['page']['range'] as $val) {
				if ($pageData['page']['curPage'] == $val) {
					echo "&nbsp;{$val}&nbsp;";
				} else {
					echo "&nbsp;<a href='?r=user/list&page={$val}'>{$val}</a>&nbsp;";
				}

			}
			echo "&nbsp;&nbsp;" . $pageData['page']['curPage'] . '/' . $pageData['page']['totalPage'];
			?>

		</strong>
	</div>
</div>