<?php
$queries[] = "ALTER TABLE `" . ALLIANCE . "` DROP `alliance_owner_range`;";
$queries[] = "INSERT INTO `" . OPTIONS . "` (`option_id`, `option_name`, `option_value`) VALUES ('mailing_protocol', 'mail'), ('mailing_smtp_host', ''), ('mailing_smtp_user', ''), ('mailing_smtp_pass', ''), ('mailing_smtp_port', '25'), ('mailing_smtp_timeout', '5'), ('mailing_smtp_crypto', '');";
