
START TRANSACTION;


SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key`='opt_plugin_sms_message_bird_access_key');
UPDATE `plugin_base_multi_lang` SET `content`='Message bird access key' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';

SET @id := (SELECT `id` FROM `plugin_base_fields` WHERE `key`='opt_plugin_sms_message_bird_originator');
UPDATE `plugin_base_multi_lang` SET `content`='Message bird originator' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';



COMMIT;