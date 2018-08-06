<?php
$queries[] = "DELETE FROM `" . OPTIONS . "` WHERE `option_name` = 'ssl_enabled'";
$queries[] = "ALTER TABLE `" . BUDDY . "` ADD PRIMARY KEY (`buddy_id`), ADD KEY `buddy_id` (`buddy_id`);";
$queries[] = "ALTER TABLE `" . FLEETS . "` CHANGE `fleet_mess` `fleet_mess` TINYINT(1) NOT NULL DEFAULT '0';";
$queries[] = "ALTER TABLE `" . ACS_FLEETS . "` CHANGE `acs_fleet_members` `acs_fleet_owner` INT(11) NOT NULL DEFAULT '0';";
$queries[] = "ALTER TABLE `" . ACS_FLEETS . "` ADD UNIQUE(`acs_fleet_name`);";