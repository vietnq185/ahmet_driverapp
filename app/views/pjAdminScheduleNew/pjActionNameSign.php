<style>
.pjSbFullscreen {
    width: 100%;
    height: 100%;
	position: relative;
}
.pjSbCenter {
    position: absolute;
    width: 100%;
    top: 45%;
	font-family: Arial, Verdana, sans-serif;
	word-spacing: normal;
	font-weight: bold;
	font-size: 10rem;
	text-align: center;
	text-transform: uppercase;
}​
</style>
<div class="pjSbFullscreen">
	<div class="pjSbCenter">
	<?php if (isset($tpl['status'])) {
		$_driver_name_sign = __('_driver_name_sign', true);
		echo @$_driver_name_sign[$tpl['status']];
	} else { 
		if (!empty($tpl['arr']['customized_name_plate'])) {
			echo pjSanitize::html($tpl['arr']['customized_name_plate']);
		} else {
			$personal_titles = __('personal_titles', true);
			$name_arr = array();
			if (!empty($tpl['arr']['c_title'])) {
				$name_arr[] = @$personal_titles[$tpl['arr']['c_title']];
			}
			if (!empty($tpl['arr']['c_fname'])) {
				$name_arr[] = pjSanitize::html($tpl['arr']['c_fname']);
			}
			if (!empty($tpl['arr']['c_lname'])) {
				$name_arr[] = pjSanitize::html($tpl['arr']['c_lname']);
			}
			echo implode(" ", $name_arr);
		}
	} 
	if (!empty($tpl['option_arr']['o_name_sign_logo']) && is_file($tpl['option_arr']['o_name_sign_logo']))
	{
		?>
		<div><img src="<?php echo PJ_INSTALL_URL.$tpl['option_arr']['o_name_sign_logo']?>" class="img-responsive" /></div>
		<?php 
	}
	?>
	</div>
</div>