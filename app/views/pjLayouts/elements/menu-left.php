<?php
$controller_name = $controller->_get->toString('controller');
$action_name = $controller->_get->toString('action');

// Dashboard
$isScriptDashboard = in_array($controller_name, array('pjAdmin')) && in_array($action_name, array('pjActionIndex'));

// Schedule
$isScriptSchedule = in_array($controller_name, array('pjAdminSchedule'));

// Vehicles
$isScriptVehicles = in_array($controller_name, array('pjAdminVehicles'));

// Drivers
$isScriptDrivers = in_array($controller_name, array('pjAdminDrivers'));

// Providers
$isScriptProviders = in_array($controller_name, array('pjAdminProviders'));

// Whatsapp Messages
$isScriptWhatsappMessages = in_array($controller_name, array('pjAdminWhatsappMessages'));

// Reports
$isScriptReportsController = in_array($controller_name, array('pjAdminReports'));
$isScriptReportsIndex = $isScriptReportsController && in_array($action_name, array('pjActionIndex'));

// Settings
$isScriptOptionsController = in_array($controller_name, array('pjAdminOptions')) && !in_array($action_name, array('pjActionPreview', 'pjActionInstall'));

$isScriptOptionsIndex         = $isScriptOptionsController && in_array($action_name, array('pjActionIndex'));
$isScriptOptionsNotifications   = $isScriptOptionsController && in_array($action_name, array('pjActionNotifications'));


// Permissions - Dashboard
$hasAccessScriptDashboard = pjAuth::factory('pjAdmin', 'pjActionIndex')->hasAccess();

// Permissions - Schedule
$hasAccessScriptSchedule          = pjAuth::factory('pjAdminSchedule')->hasAccess();

// Permissions - Vehicles
$hasAccessScriptVehicles          = pjAuth::factory('pjAdminVehicles')->hasAccess();

// Permissions - Drivers
$hasAccessScriptDrivers = pjAuth::factory('pjAdminDrivers')->hasAccess();

// Permissions - Providers
$hasAccessScriptProviders = pjAuth::factory('pjAdminProviders')->hasAccess();

// Whatsapp Messages
$hasAccessScriptWhatsappMessages = pjAuth::factory('pjAdminWhatsappMessages')->hasAccess();

// Permissions - Reports
$hasAccessScriptReportsIndex       = pjAuth::factory('pjAdminReports', 'pjActionIndex')->hasAccess();


// Permissions - Settings
$hasAccessScriptOptions                 = pjAuth::factory('pjAdminOptions')->hasAccess();
$hasAccessScriptOptionsIndex          = pjAuth::factory('pjAdminOptions', 'pjActionIndex')->hasAccess();
$hasAccessScriptOptionsNotifications    = pjAuth::factory('pjAdminOptions', 'pjActionNotifications')->hasAccess();
?>

<?php if ($hasAccessScriptDashboard): ?>
    <li<?php echo $isScriptDashboard ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex"><i class="fa fa-th-large"></i> <span class="nav-label"><?php __('plugin_base_menu_dashboard');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptSchedule): ?>
    <li<?php echo $isScriptSchedule ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionIndex"><i class="fa fa-calendar"></i> <span class="nav-label"><?php __('menuSchedule');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptVehicles): ?>
    <li<?php echo $isScriptVehicles ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehicles&amp;action=pjActionIndex"><i class="fa fa-car"></i> <span class="nav-label"><?php __('menuVehicles');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptDrivers): ?>
    <li<?php echo $isScriptDrivers ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionIndex"><i class="fa fa-user"></i> <span class="nav-label"><?php __('menuDrivers');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptProviders): ?>
    <li<?php echo $isScriptProviders ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProviders&amp;action=pjActionIndex"><i class="fa fa-user-circle"></i> <span class="nav-label"><?php __('menuProviders');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptWhatsappMessages): ?>
    <li<?php echo $isScriptWhatsappMessages ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminWhatsappMessages&amp;action=pjActionIndex"><i class="fa fa-whatsapp"></i> <span class="nav-label"><?php __('menuWhatsappMessages');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptReportsIndex): ?>
    <li<?php echo $isScriptReportsIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex"><i class="fa fa-files-o"></i> <span class="nav-label"><?php __('menuReports');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptOptions): ?>
    <li<?php echo $isScriptOptionsController ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label"><?php __('menuOptions');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
        	<?php if ($hasAccessScriptOptionsIndex): ?>
                <li<?php echo $isScriptOptionsIndex ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex"><?php __('menuGeneralOptions');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptOptionsNotifications): ?>
                <li<?php echo $isScriptOptionsNotifications ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionNotifications&amp;recipient=admin&transport=email&amp;variant=change_payment_status"><?php __('menuConfirmation');?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>