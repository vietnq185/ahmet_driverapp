START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'btnAssignOrders', 'backend', 'Label / Assign Orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Assign Orders', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblOrderCar', 'backend', 'Label / Car', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Car', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblOrderShift', 'backend', 'Label / Shift', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shift', 'script');

INSERT INTO `fields` VALUES (NULL, '_order_shift_ARRAY_1', 'arrays', '_order_shift_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shift 1', 'script');

INSERT INTO `fields` VALUES (NULL, '_order_shift_ARRAY_2', 'arrays', '_order_shift_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shift 2', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnAssign', 'backend', 'Label / Assign', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Assign', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblAssignOrderError', 'backend', 'Label / Please select at least one order.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select at least one order.', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblAssignOrdersSuccess', 'backend', 'Label / Orders have been assigned.', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The orders you selected have been successfully assigned.', 'script');

COMMIT;