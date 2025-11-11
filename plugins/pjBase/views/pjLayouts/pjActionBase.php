<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="https://driver.alpstria.com/app/web/upload/logos/icon.png">
    	<link rel="apple-touch-icon" sizes="128x128" href="https://driver.alpstria.com/app/web/upload/logos/icon.png">
    	<link rel="icon" sizes="192x192" href="https://driver.alpstria.com/app/web/upload/logos/icon.png">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php __('script_name') ?></title>
        <?php
        $cnt = count($controller->getCss());
        foreach ($controller->getCss() as $i => $css)
        {
        	echo '<link rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'">';
        	echo "\n";
        	if ($i < $cnt - 1)
        	{
        		echo "\t";
        	}
        }
        ?>
	</head>
	<body>
		<div id="wrapper">
			<?php require dirname(__FILE__) . '/elements/menu-left.php'; ?>
			<div id="page-wrapper" class="gray-bg dashbard-1">
    			<?php
    			require dirname(__FILE__) . '/elements/menu-top.php'; 
    			
    			require $content_tpl;
    			
    			include dirname(__FILE__) . '/elements/footer-default.php'; 
    			?>
    		</div>
		</div><!-- #wrapper -->
		<?php 
    	$cnt = count($controller->getJs());
    	foreach ($controller->getJs() as $i => $js)
    	{
    		echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
    		echo "\n";
    		if ($i < $cnt - 1)
    		{
    			echo "\t";
    		}
    	}
    	?>
	</body>
</html>