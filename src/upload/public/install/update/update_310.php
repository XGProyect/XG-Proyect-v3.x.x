<?php
$queries[] = "DELETE FROM `" . OPTIONS . "` WHERE `option_name` = 'ssl_enabled'";
$queries[] = "ALTER TABLE `" . BUDDY . "` ADD PRIMARY KEY (`buddy_id`), ADD KEY `buddy_id` (`buddy_id`);";
$queries[] = "ALTER TABLE `" . FLEETS . "` CHANGE `fleet_mess` `fleet_mess` TINYINT(1) NOT NULL DEFAULT '0';";
$queries[] = "RENAME TABLE `" . DB_PREFIX . "acs_fleets` TO `" . ACS . "`;";
$queries[] = "ALTER TABLE `" . ACS . "` CHANGE `acs_fleet_members` `acs_fleet_owner` INT(11) NOT NULL DEFAULT '0';";
$queries[] = "ALTER TABLE `" . ACS . "` ADD UNIQUE(`acs_fleet_name`);";
$queries[] = "CREATE TABLE `" . ACS_MEMBERS . "` (
`acs_member_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`acs_group_id` int(11) UNSIGNED NOT NULL,
`acs_user_id` int(11) UNSIGNED NOT NULL,
PRIMARY KEY (`acs_member_id`),
UNIQUE( `acs_group_id`, `acs_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
$queries[] = "ALTER TABLE `" . ACS . "` DROP `acs_fleet_fleets`, DROP `acs_fleet_invited`;";
$queries[] = "ALTER TABLE `" . ACS . "` CHANGE `acs_fleet_id` `acs_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `acs_fleet_name` `acs_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `acs_fleet_owner` `acs_owner` INT(11) NOT NULL DEFAULT '0', CHANGE `acs_fleet_galaxy` `acs_galaxy` INT(2) NULL DEFAULT NULL, CHANGE `acs_fleet_system` `acs_system` INT(4) NULL DEFAULT NULL, CHANGE `acs_fleet_planet` `acs_planet` INT(2) NULL DEFAULT NULL, CHANGE `acs_fleet_planet_type` `acs_planet_type` TINYINT(1) NULL DEFAULT NULL;";