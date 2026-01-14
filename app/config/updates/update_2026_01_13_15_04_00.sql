
START TRANSACTION;


INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_enable_assign_unassign_orders_button', 1, '1|0::0', NULL, 'bool', 17, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_enable_assign_unassign_orders_button', 'backend', 'Options / Show Assign Orders & Unassign buttons', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Show Assign Orders & Unassign buttons', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'btnSelectMultipleOrders', 'backend', 'Label / Select multiple orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select multiple orders', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'btnSelectMultipleOrdersActivated', 'backend', 'Label / Select multiple orders activated', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Selecting... (Hold Ctrl + Click)', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'btnSelectMultipleOrdersTip', 'backend', 'Label / Select multiple orders tip', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ou are in Selection Mode. Press Ctrl while clicking to pick multiple items.', 'script');
	
  		
  		
COMMIT;