<?php
$controller_name = $controller->_get->toString('controller');
$action_name = $controller->_get->toString('action');

// Dashboard
$isScriptDashboard = in_array($controller_name, array('pjAdmin')) && in_array($action_name, array('pjActionIndex'));

// Schedule
$isScriptSchedule = in_array($controller_name, array('pjAdminSchedule'));

// Test AI Assignment
$isScriptScheduleNew = in_array($controller_name, array('pjAdminScheduleNew'));

// Live Tracking
$isScriptLiveTrackingController = in_array($controller_name, array('pjAdminTracking'));
$isScriptLiveTrackingIndex = $isScriptLiveTrackingController && in_array($action_name, array('pjActionIndex'));

// Vehicles
$isScriptVehicles = in_array($controller_name, array('pjAdminVehicles'));

// Vehicles Maintrance
$isScriptVehiclesMaintrance = in_array($controller_name, array('pjAdminVehiclesMaintrance'));

// Vehicles Maintrance Service Types
$isScriptVehicleMaintranceServiceTypes = in_array($controller_name, array('pjAdminVehicleMaintranceServiceTypes'));

// Vehicles Maintrance Attribute Types
$isScriptVehicleMaintranceAttributeTypes = in_array($controller_name, array('pjAdminVehicleMaintranceAttributeTypes'));

// Vehicles Maintrance Report
$isScriptVehicleMaintranceReport = in_array($controller_name, array('pjAdminVehicleMaintranceReport'));


// Partbers
$isScriptPartners = in_array($controller_name, array('pjAdminPartners'));

// Partner Report
$isScriptPartnerReport = in_array($controller_name, array('pjAdminPartnerReport'));

// Partner Contract Theme
$isScriptPartnerContractTheme = in_array($controller_name, array('pjAdminContractTheme'));


// Drivers
$isScriptDrivers = in_array($controller_name, array('pjAdminDrivers'));

// Providers
$isScriptProviders = in_array($controller_name, array('pjAdminProviders'));

// Notes
$isScriptNotes = in_array($controller_name, array('pjAdminNotes'));

// WhatsApp Chat
$isScriptWhatsAppChat = in_array($controller_name, array('pjAdminWhatsappChat'));

// Whatsapp Messages
$isScriptWhatsappMessages = in_array($controller_name, array('pjAdminWhatsappMessages'));

// Reports
$isScriptReportsController = in_array($controller_name, array('pjAdminReports'));
$isScriptReportsIndex = $isScriptReportsController && in_array($action_name, array('pjActionIndex'));

// Logs
$isScriptLogs = in_array($controller_name, array('pjAdminLogs'));

// Settings
$isScriptOptionsController = in_array($controller_name, array('pjAdminOptions')) && !in_array($action_name, array('pjActionPreview', 'pjActionInstall'));

$isScriptOptionsIndex         = $isScriptOptionsController && in_array($action_name, array('pjActionIndex'));
$isScriptOptionsNotifications   = $isScriptOptionsController && in_array($action_name, array('pjActionNotifications'));


// Permissions - Dashboard
$hasAccessScriptDashboard = pjAuth::factory('pjAdmin', 'pjActionIndex')->hasAccess();

// Permissions - Schedule
$hasAccessScriptSchedule          = pjAuth::factory('pjAdminSchedule')->hasAccess();

// Permissions - Live Tracking
$hasAccessScriptLiveTrackingIndex       = pjAuth::factory('pjAdminTracking', 'pjActionIndex')->hasAccess();

// Permissions - Vehicles
$hasAccessScriptVehicles          = pjAuth::factory('pjAdminVehicles')->hasAccess();

// Permissions - Vehicles Maintrance
$hasAccessScriptVehiclesMaintrance          = pjAuth::factory('pjAdminVehiclesMaintrance')->hasAccess();

// Permissions - Vehicles Maintrance Service Types
$hasAccessScriptVehicleMaintranceServiceTypes          = pjAuth::factory('pjAdminVehicleMaintranceServiceTypes')->hasAccess();

// Permissions - Vehicles Maintrance Atribute Types
$hasAccessScriptVehicleMaintranceAtributeTypes          = pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes')->hasAccess();

// Permissions - Vehicles Maintrance Report
$hasAccessScriptVehicleMaintranceReport          = pjAuth::factory('pjAdminVehicleMaintranceReport')->hasAccess();


// Permissions - Partners
$hasAccessScriptPartners          = pjAuth::factory('pjAdminPartners')->hasAccess();

// Permissions - Partner Report
$hasAccessScriptPartnerReport          = pjAuth::factory('pjAdminPartnerReport')->hasAccess();

// Permissions - Partner Contract Theme
$hasAccessScriptPartnerContractTheme          = pjAuth::factory('pjAdminContractTheme')->hasAccess();



// Permissions - Drivers
$hasAccessScriptDrivers = pjAuth::factory('pjAdminDrivers')->hasAccess();

// Permissions - Providers
$hasAccessScriptProviders = pjAuth::factory('pjAdminProviders')->hasAccess();

// Permissions - Notes
$hasAccessScriptNotes = pjAuth::factory('pjAdminNotes')->hasAccess();

// Permissions - WhatsApp Chat
$hasAccessScriptWhatsAppChat = pjAuth::factory('pjAdminWhatsappChat')->hasAccess() && $tpl['option_arr']['o_enable_whatsapp_fearure'] == 'Yes';

// Whatsapp Messages
$hasAccessScriptWhatsappMessages = pjAuth::factory('pjAdminWhatsappMessages')->hasAccess();

// Permissions - Reports
$hasAccessScriptReportsIndex       = pjAuth::factory('pjAdminReports', 'pjActionIndex')->hasAccess();

// Logs
$hasAccessScriptLogs = pjAuth::factory('pjAdminLogs')->hasAccess();


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
    
    <?php /*
    <li<?php echo $isScriptScheduleNew ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminScheduleNew&amp;action=pjActionIndex"><i class="fa fa-microchip"></i> <span class="nav-label">New AI Assignment</span></a>
    </li>
    */?>
<?php endif; ?>

<?php if ($hasAccessScriptLiveTrackingIndex): ?>
    <li<?php echo $isScriptLiveTrackingIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTracking&amp;action=pjActionIndex"><i class="fa fa-map-marker"></i> <span class="nav-label"><?php __('menuLiveTracking');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptVehicles): ?>
    <li<?php echo $isScriptVehicles ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehicles&amp;action=pjActionIndex"><i class="fa fa-car"></i> <span class="nav-label"><?php __('menuVehicles');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptVehiclesMaintrance || $hasAccessScriptVehicleMaintranceServiceTypes || $hasAccessScriptVehicleMaintranceAtributeTypes || $hasAccessScriptVehicleMaintranceReport): ?>
    <li<?php echo $isScriptVehiclesMaintrance || $isScriptVehicleMaintranceServiceTypes || $isScriptVehicleMaintranceAttributeTypes || $isScriptVehicleMaintranceReport ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-wrench"></i> <span class="nav-label"><?php __('MenuVehiclesMaintraince');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
        	<?php if ($hasAccessScriptVehiclesMaintrance): ?>
                <li<?php echo $isScriptVehiclesMaintrance ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehiclesMaintrance&amp;action=pjActionIndex"><?php __('MenuMaintrainceVehicles');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptVehicleMaintranceReport): ?>
                <li<?php echo $isScriptVehicleMaintranceReport ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehicleMaintranceReport&amp;action=pjActionIndex"><?php __('MenuMaintrainceReport');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptVehicleMaintranceServiceTypes): ?>
                <li<?php echo $isScriptVehicleMaintranceServiceTypes ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehicleMaintranceServiceTypes&amp;action=pjActionIndex"><?php __('menuVehicleMaintranceServiceTypes');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptVehicleMaintranceAtributeTypes): ?>
                <li<?php echo $isScriptVehicleMaintranceAttributeTypes ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehicleMaintranceAttributeTypes&amp;action=pjActionIndex"><?php __('menuVehicleMaintranceAttributeTypes');?></a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>


<?php if ($hasAccessScriptPartners || $hasAccessScriptPartnerReport || $hasAccessScriptPartnerContractTheme): ?>
    <li<?php echo $isScriptPartners || $isScriptPartnerReport || $isScriptPartnerContractTheme ? ' class="active"' : NULL; ?>>
        <a href="#"><i class="fa fa-wrench"></i> <span class="nav-label"><?php __('MenuPartners');?></span><span class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
        	<?php if ($hasAccessScriptPartners): ?>
                <li<?php echo $isScriptPartners ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartners&amp;action=pjActionIndex"><?php __('MenuPartners');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptPartnerReport): ?>
                <li<?php echo $isScriptPartnerReport ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartnerReport&amp;action=pjActionIndex"><?php __('MenuPartnerReport');?></a></li>
            <?php endif; ?>
            <?php if ($hasAccessScriptPartnerContractTheme): ?>
                <li<?php echo $isScriptPartnerContractTheme ? ' class="active"' : NULL; ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminContractTheme&amp;action=pjActionIndex"><?php __('MenuContractTheme');?></a></li>
            <?php endif; ?>
        </ul>
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

<?php if ($hasAccessScriptNotes): ?>
    <li<?php echo $isScriptNotes ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminNotes&amp;action=pjActionIndex"><i class="fa fa-exclamation-triangle"></i> <span class="nav-label"><?php __('menuNotes');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptWhatsAppChat): ?>
    <li<?php echo $isScriptWhatsAppChat ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminWhatsappChat&amp;action=pjActionIndex"><i class="fa fa-whatsapp"></i> <span class="nav-label"><?php __('menuWhatsappChat');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptWhatsappMessages): ?>
    <li<?php echo $isScriptWhatsappMessages ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminWhatsappMessages&amp;action=pjActionIndex"><i class="fa fa-copy"></i> <span class="nav-label"><?php __('menuWhatsappMessages');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptReportsIndex): ?>
    <li<?php echo $isScriptReportsIndex ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex"><i class="fa fa-clone"></i> <span class="nav-label"><?php __('menuReports');?></span></a>
    </li>
<?php endif; ?>

<?php if ($hasAccessScriptLogs): ?>
    <li<?php echo $isScriptLogs ? ' class="active"' : NULL; ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLogs&amp;action=pjActionIndex"><i class="fa fa-sticky-note"></i> <span class="nav-label"><?php __('menuLogs');?></span></a>
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