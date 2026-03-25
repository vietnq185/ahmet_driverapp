
START TRANSACTION;

DROP TABLE IF EXISTS `driver_job_status`;
CREATE TABLE IF NOT EXISTS `driver_job_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `driver_id` int(11) DEFAULT NULL,
    `date` date DEFAULT NULL,
    `status` enum('T','F') DEFAULT 'T',
    `created` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX (driver_id),
    INDEX (date)
);

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnConfirmTheJobs', 'backend', 'Label / Confirm the jobs', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirm the jobs', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnJobStatus', 'backend', 'Label / Job Status', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Job Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverLastLogin', 'backend', 'Label / Last Login', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last Login', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverJobViewedAt', 'backend', 'Label / Job Viewed At', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Job Viewed At', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverJobViewedAtNA', 'backend', 'Label / N/A', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'N/A', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverConfirmationStatus', 'backend', 'Label /Confirmation Status', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmation Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_job_statuses_ARRAY_T', 'arrays', '_driver_job_statuses_ARRAY_T', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Confirmed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_job_statuses_ARRAY_F', 'arrays', '_driver_job_statuses_ARRAY_F', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

  		
  		
COMMIT;