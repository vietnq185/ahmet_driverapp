START TRANSACTION;


INSERT INTO `plugin_base_cron_jobs` (`name`, `controller`, `action`, `interval`, `period`, `is_active`) VALUES
('Synchronize general data (clients, drivers, fleets, locations...etc) from all providers.', 'pjCron', 'pjActionSyncGeneralData', 5, 'minute', 1);

COMMIT;