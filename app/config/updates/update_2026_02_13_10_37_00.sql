
START TRANSACTION;

DROP TABLE IF EXISTS `partners`;
CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL, 
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `company_number` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `bic` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `commission_pct` decimal(5,2) DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `partner_vehicles`;
CREATE TABLE IF NOT EXISTS `partner_vehicles` (
    `partner_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  UNIQUE KEY (partner_id, vehicle_id),
  INDEX (partner_id),
    INDEX (vehicle_id)
);

DROP TABLE IF EXISTS `partner_reports`;
CREATE TABLE IF NOT EXISTS `partner_reports` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `total_bookings_count` int(11) DEFAULT '0',
  `total_bookings_amount` decimal(15,2) DEFAULT '0.00',
  `paid_by_partner_amount` decimal(15,2) DEFAULT '0.00',
  `commission_pct` decimal(5,2) DEFAULT '0.00', 
  `commission_amount` decimal(15,2) DEFAULT '0.00',
  `paid_bookings_we_made` decimal(15,2) DEFAULT '0.00', 
  `billing_amount` decimal(15,2) DEFAULT '0.00',
  `status` enum('open', 'pending', 'completed') DEFAULT 'open',
  `pdf_path` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `partner_contract_documents`;
CREATE TABLE IF NOT EXISTS `partner_contract_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tmp_hash` varchar(32) DEFAULT NULL,
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `small_path` varchar(255) DEFAULT NULL,
  `source_path` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tmp_hash` (`tmp_hash`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuPartners', 'backend', 'Menu / Partners', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partners', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuPartnerReport', 'backend', 'Menu / Partner Report', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner Report', 'script');
	
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddPartnerTitle', 'backend', 'Info / Add Partner', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddPartnerBody', 'backend', 'Info / Add Partner', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to add partner.', 'script');
 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerVehicles', 'backend', 'Label / Vehicles', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerCommissionIn', 'backend', 'Label / Commission in', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Commission in', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerPartnerName', 'backend', 'Label / Partner name', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerPhone', 'backend', 'Label / Phone', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerMail', 'backend', 'Label / Mail', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Mail', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerCompanyName', 'backend', 'Label / Company name', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerAddress', 'backend', 'Label / Address', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerTaxNumber', 'backend', 'Label / ATU / Tax Number', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ATU / Tax Number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerCompanyNumber', 'backend', 'Label / Company number', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerIban', 'backend', 'Label / Iban', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Iban', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerBic', 'backend', 'Label / Bic', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Bic', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerNotes', 'backend', 'Label / Notes', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnGeneratePartnerContract', 'backend', 'Label / Generate Partner Contract', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Generate Partner Contract', 'script');
  	  			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_APAN01', 'arrays', 'error_titles_ARRAY_APAN01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Record Added Successfully', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_APAN01', 'arrays', 'error_bodies_ARRAY_APAN01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'he partner contract has been successfully generated..', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_APAN04', 'arrays', 'error_titles_ARRAY_APAN04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to Add Record', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_APAN04', 'arrays', 'error_bodies_ARRAY_APAN04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred while saving the data. Please check and try again.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_APAN03', 'arrays', 'error_titles_ARRAY_APAN03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Changes Saved', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_APAN03', 'arrays', 'error_bodies_ARRAY_APAN03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The partner contract has been updated successfully.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_APAN08', 'arrays', 'error_titles_ARRAY_APAN08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner Contract Not Found', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_APAN08', 'arrays', 'error_bodies_ARRAY_APAN08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We couldn''t find the record you were looking for. It might have been deleted or the ID is incorrect.', 'script');
  		  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdatePartnerTitle', 'backend', 'Info / Update Partner Record Title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Partner Record', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdatePartnerBody', 'backend', 'Info / Update Partner Record Body', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Manage partner profiles, assigned vehicles, contracts, and monthly reports.', 'script');
  		  		 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddContractDocuments', 'backend', 'Button / Add Contract Documents', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Contract Documents', 'script');
  		  		 
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddReport', 'backend', 'Button / Add report', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add report', 'script');
  		  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportPeriod', 'backend', 'Label / Period', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Period', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportCreated', 'backend', 'Label / Created', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportReportFile', 'backend', 'Label / Report file', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Report file', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportStatus', 'backend', 'Label / Status', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportAction', 'backend', 'Label / Action', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Action', 'script');
  		  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddReportBillingTitle', 'backend', 'Label / Add Report / Billing', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Report / Billing', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingTo', 'backend', 'Label / to', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'to', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingTotal', 'backend', 'Label / Total', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingTotalBookings', 'backend', 'Label / Total bookings', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingTotalAmount', 'backend', 'Label / Total amount', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total amount', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingPaid', 'backend', 'Label / Paid', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingCreditCard', 'backend', 'Label / Credit card', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingCash', 'backend', 'Label / Cash', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingCommission', 'backend', 'Label / Commission', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Commission', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingDate', 'backend', 'Label / Date', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingFromTo', 'backend', 'Label / From - To', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From - To', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingTotalBookingsMadeByPartner', 'backend', 'Label / Total bookings made by our partner', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings made by our partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingPaidBookingsMadeByPartner', 'backend', 'Label / Paid bookings made by our partner', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid bookings made by our partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingBillingAmount', 'backend', 'Label / Billing amount', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Billing amount', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnGenerateReportBilling', 'backend', 'Label / Generate Report / Billing', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Generate Report / Billing', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnDownloadReportBillingPdf', 'backend', 'Label / Download Report / Billing pdf', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Download Report / Billing pdf', 'script');
  		  							
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingPaidBookingsFromPartnerWemade', 'backend', 'Label / Paid Bookings from partner we made or commissions', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid Bookings from partner we made or commissions', 'script');
  		  				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingPaidBookingsWeMade', 'backend', 'Label / Paid bookings we made', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid bookings we made', 'script');
  		  			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_billing_statuses_ARRAY_open', 'arrays', 'report_billing_statuses_ARRAY_open', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Open', 'script');
		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_billing_statuses_ARRAY_pending', 'arrays', 'report_billing_statuses_ARRAY_pending', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pending', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_billing_statuses_ARRAY_completed', 'arrays', 'report_billing_statuses_ARRAY_completed', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Completed', 'script');
  		  				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportBillingStatus', 'backend', 'Label / Status', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');
			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoPartnersTitle', 'backend', 'Info / Partners Management title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partners Management', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoPartnersBody', 'backend', 'Info / Partners Management body', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Overview of all partners, their vehicles, and latest billing cycles.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddPartner', 'backend', 'Button / Add partner', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add partner', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerLastBilling', 'backend', 'Label / Last billing', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last billing', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerStatusLastBilling', 'backend', 'Label / Status last billing', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status last billing', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoPartnerReportsTitle', 'backend', 'infoReportsTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Partner Reports', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoPartnerReportsDesc', 'backend', 'infoReportsDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Select date range and location that you want to see the report.', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSelectPartber', 'backend', 'Label / Partner', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Partner', 'script');
			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportTotalCommissions', 'backend', 'Label / Total Commissions', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total Commissions', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportTotalBookingsByPartners', 'backend', 'Label / Total Bookings by Partners', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total Bookings by Partners', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportTotalRevenue', 'backend', 'Label / Total Revenue', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total Revenue', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportBestPartner', 'backend', 'Label / Best Partner made most bookings list', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Best Partner made most bookings list', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportPartnerName', 'backend', 'Label / Partner Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Partner Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportBookings', 'backend', 'Label / Bookings', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Bookings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportRevenue', 'backend', 'Label / Revenue', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Revenue', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPartnerReportCommissions', 'backend', 'Label / Commissions', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Commissions', 'script');
		
 

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminPartners');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminPartners_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminPartners_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminPartners_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminPartners_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminPartners_pjActionDeleteBulk');
					
					
INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminPartnerReport');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminPartnerReport_pjActionIndex');
					
					
  		
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners', 'backend', 'pjAdminPartners', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partners Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners_pjActionIndex', 'backend', 'pjAdminPartners_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partners List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners_pjActionCreate', 'backend', 'pjAdminPartners_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners_pjActionUpdate', 'backend', 'pjAdminPartners_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners_pjActionDelete', 'backend', 'pjAdminPartners_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartners_pjActionDeleteBulk', 'backend', 'pjAdminPartners_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple partners', 'script');
  		
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartnerReport', 'backend', 'pjAdminPartnerReport', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner Report Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminPartnerReport_pjActionIndex', 'backend', 'pjAdminPartnerReport_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner Report', 'script');
  		
  		
COMMIT;