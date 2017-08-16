<?php
$queries[] = "DELETE FROM `{prefix}config` WHERE `config_name` = 'VERSION'";
$queries[] = "INSERT INTO `{prefix}config` (`config_name`, `config_value`) VALUES ('VERSION', '".SYSTEM_VERSION."');";
$queries[] = "ALTER TABLE {prefix}galaxy ADD `invisible_start_time` int(11) NOT NULL default '0'; ";
$queries[] = "ALTER TABLE `{prefix}users` DROP `rpg_espion`,DROP `rpg_constructeur`,DROP `rpg_scientifique`,DROP `rpg_commandant`,DROP `rpg_stockeur`,DROP `rpg_defenseur`,DROP `rpg_destructeur`,DROP `rpg_general`,DROP `rpg_empereur`;";
$queries[] = "DROP TABLE `{prefix}config`";
