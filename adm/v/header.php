<div id="header">
	<ul id="top-navigation">
		<?php
		$DataCom = include('DataCom.php');
		$DataMenu = include('DataMenu.php');
		if (is_array($DataMenu)):
			foreach ($DataMenu as $key => $val):
				$active = $DataCom[0] == $key ? 'class="active"' : '';
				?>
				<li><a href="?r=<?php echo $key; ?>/Index" <?php echo $active; ?>><?php echo $val['name']; ?></a></li>
			<?php
			endforeach;
		endif;
		?>
	</ul>
</div>


