
START TRANSACTION;

DROP TABLE IF EXISTS `api_cache_distances`;
CREATE TABLE IF NOT EXISTS `api_cache_distances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hash_key` varchar(255) DEFAULT NULL,
  `duration_sec` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


ALTER TABLE `bookings` ADD `is_manual` TINYINT(1) NOT NULL DEFAULT 0;

INSERT INTO `fields` VALUES (NULL, 'btnAssignWithAI', 'backend', 'Label / Assign with AI', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Assign with AI', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_buffer', 'backend', 'Label / Safety Buffer Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Safety Buffer Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAssignOrdersWithAITitle', 'backend', 'Label / Assign Orders with AI Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Assign Orders with AI', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoAssignOrdersWithAIDesc', 'backend', 'Label / Assign Orders with AI Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to use AI to assign the orders?', 'script');



INSERT INTO `fields` VALUES (NULL, 'btnYes', 'backend', 'Label / Yes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnNo', 'backend', 'Label / No', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');



COMMIT;