<?php
$queries[] = "DELETE FROM `" . OPTIONS . "` WHERE `option_name` = 'ssl_enabled'";
$queries[] = "ALTER TABLE `" . BUDDY . "` ADD PRIMARY KEY (`buddy_id`), ADD KEY `buddy_id` (`buddy_id`);";
$queries[] = "ALTER TABLE `" . FLEETS . "` CHANGE `fleet_creation` `fleet_creation` DATETIME NULL DEFAULT NULL;";