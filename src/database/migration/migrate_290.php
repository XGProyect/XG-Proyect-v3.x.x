<?php

$queries[] = "DELETE FROM `{prefix}config` WHERE `config_name` = 'VERSION'";
$queries[] = "INSERT INTO `{prefix}config` (`config_name`, `config_value`) VALUES ('VERSION', '" . SYSTEM_VERSION . "');";
$queries[] = "INSERT INTO `{prefix}config` (`config_name`, `config_value`) VALUES ('moderation', '1,0,0,1;1,1,0,1;');";
$queries[] = " ALTER TABLE `{prefix}banned` CHANGE `who` `who` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                        CHANGE `who2` `who2` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                        CHANGE `author` `author` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
                        CHANGE `email` `email` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ";
$queries[] = "UPDATE `{prefix}config` SET `config_value` = '1,0,0,1,1;1,1,0,1,1;1;' WHERE `{prefix}config`.`config_name` = 'moderation';";
$queries[] = "ALTER TABLE `{prefix}planets` CHANGE `small_protection_shield` `small_protection_shield` TINYINT( 1 ) NOT NULL DEFAULT '0', CHANGE `big_protection_shield` `big_protection_shield` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$queries[] = "UPDATE `{prefix}rw` SET `{prefix}rw`.`owners` = CONCAT(id_owner1,\",\",id_owner2)";
$queries[] = "ALTER TABLE `{prefix}rw`
                        DROP `id_owner1`,
                        DROP `id_owner2`;";
$queries[] = "ALTER TABLE {prefix}galaxy ADD `invisible_start_time` int(11) NOT NULL default '0'; ";
$queries[] = "ALTER TABLE `{prefix}users` DROP `rpg_espion`,DROP `rpg_constructeur`,DROP `rpg_scientifique`,DROP `rpg_commandant`,DROP `rpg_stockeur`,DROP `rpg_defenseur`,DROP `rpg_destructeur`,DROP `rpg_general`,DROP `rpg_empereur`;";
$queries[] = "DROP TABLE `{prefix}config`";
$queries[] = "INSERT INTO `{prefix}statpoints` (`id_owner`, `id_ally`) SELECT `id`, '0' FROM `{prefix}users` LEFT JOIN `{prefix}statpoints` ON `id_owner` = `id` WHERE `id_owner` IS NULL";
$queries[] = "INSERT INTO `{prefix}statpoints` (`id_owner`, `id_ally`) SELECT '0', `id` FROM `{prefix}alliance` LEFT JOIN `{prefix}statpoints` ON `id_ally` = `id` WHERE `id_ally` IS NULL";
