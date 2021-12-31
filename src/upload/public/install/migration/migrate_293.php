<?php

$queries[] = "DELETE FROM `{prefix}config` WHERE `config_name` = 'VERSION'";
$queries[] = "INSERT INTO `{prefix}config` (`config_name`, `config_value`) VALUES ('VERSION', '" . SYSTEM_VERSION . "');";
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
