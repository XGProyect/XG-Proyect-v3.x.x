<?php
$queries[] = "DELETE FROM `" . OPTIONS . "` WHERE `option_name` = 'ssl_enabled'";
$queries[] = "ALTER TABLE `" . BUDDY . "` ADD PRIMARY KEY (`buddy_id`), ADD KEY `buddy_id` (`buddy_id`);";