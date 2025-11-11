<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
?>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<?php if ($tpl['arr']['status'] == 'ERROR') { ?>
					<h3 class="text-danger">Error: <?php echo $tpl['arr']['error_msg'];?></h3>
				<?php } else { ?>
					<h3 class="text-success">Total records: <?php echo $tpl['arr']['total_records'];?></h3>
					<h3 class="text-success">Total pages: <?php echo $tpl['arr']['total_pages'];?></h3>
					<?php if (isset($tpl['arr']['total_records_updated'])) { ?>
						<h3 class="text-success">Total records updated: <?php echo $tpl['arr']['total_records_updated'];?></h3>
					<?php } ?>
					<h3 class="text-success">Page updated: <?php echo $tpl['page'].' / '.$tpl['arr']['total_pages'];?></h3>
					<?php if ($get['page'] >= $tpl['arr']['total_pages']) { ?>
						<h3 class="text-success">All data has been updated!!!</h3>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div><!-- /.col-lg-8 -->
</div>