
START TRANSACTION;



DROP TABLE IF EXISTS `contract_themes`;
CREATE TABLE IF NOT EXISTS `contract_themes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('T','F') DEFAULT 'T',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `partners` ADD COLUMN `contract_theme` int(10) DEFAULT NULL AFTER `commission_pct`;		

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddContractThemeTitle', 'backend', 'Info / Add contract theme', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add contract theme', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddContractThemeBody', 'backend', 'Info / Add contract theme', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to add contract theme.', 'script');
 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateContractThemeTitle', 'backend', 'Info / Update contract theme Record Title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update contract theme', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateContractThemeBody', 'backend', 'Info / Update contract theme Body', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to update contract theme.', 'script');
  		  		 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_ACT01', 'arrays', 'error_titles_ARRAY_ACT01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Record Added Successfully', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_ACT01', 'arrays', 'error_bodies_ARRAY_ACT01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract theme has been successfully generated..', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_ACT04', 'arrays', 'error_titles_ARRAY_ACT04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to Add Record', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_ACT04', 'arrays', 'error_bodies_ARRAY_ACT04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred while saving the data. Please check and try again.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_ACT03', 'arrays', 'error_titles_ARRAY_ACT03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Changes Saved', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_ACT03', 'arrays', 'error_bodies_ARRAY_ACT03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract theme has been updated successfully.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_ACT08', 'arrays', 'error_titles_ARRAY_ACT08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract theme not found', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_ACT08', 'arrays', 'error_bodies_ARRAY_ACT08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We couldn''t find the record you were looking for. It might have been deleted or the ID is incorrect.', 'script');
  		  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoContractThemeTitle', 'backend', 'Infobox / Contract Theme Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract Themes List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoContractThemeBody', 'backend', 'Infobox / Contract Theme Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all contract theme that you operate.', 'script');
  		  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblContractThemeName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblContractThemeContent', 'backend', 'Label / Content', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Content', 'script');
		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblCustomGridAction', 'backend', 'Label / Action', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Action', 'script');
		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblContractTheme', 'backend', 'Label / Contract theme', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract theme', 'script');
	
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerContract', 'backend', 'Label / Contract', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract', 'script');
		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSaveGeneratePartnerContract', 'backend', 'Label / Save & Generate Partner Contract', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Save & Generate Partner Contract', 'script');
		
		

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblContractThemeNameTokens', 'backend', 'Label / Available tokens', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available tokens:
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">{PartnerName}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Phone}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Email}</div>
</div>
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">{CompanyName}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Address}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{TaxNumber}</div>
</div>
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">{CompanyNumber}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Iban}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Bic}</div>
</div>
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">{CommissionPercentage}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Notes}</div>
	<div class="col-md-4 col-sm-6 col-xs-12">{Vehicles}</div>
</div>
', 'script');
				
	


INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme', 'backend', 'pjAdminContractTheme', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract Theme Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme_pjActionIndex', 'backend', 'pjAdminContractTheme_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract Themes List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme_pjActionCreate', 'backend', 'pjAdminContractTheme_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add contract theme', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme_pjActionUpdate', 'backend', 'pjAdminContractTheme_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update contract theme', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme_pjActionDelete', 'backend', 'pjAdminContractTheme_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single contract theme', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminContractTheme_pjActionDeleteBulk', 'backend', 'pjAdminContractTheme_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple contract themes', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminContractTheme');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminContractTheme_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminContractTheme_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminContractTheme_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminContractTheme_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminContractTheme_pjActionDeleteBulk');
	
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuContractTheme', 'backend', 'Menu / Contract Theme', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Contract Theme', 'script');

  		
  		
COMMIT;