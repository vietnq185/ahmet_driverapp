
START TRANSACTION;

UPDATE `plugin_base_options` SET `value`='index.php?controller=pjAdminSchedule&action=pjActionIndex' WHERE `key`='o_dashboard';

COMMIT;