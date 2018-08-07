<?php
/**
 * Database Schema File
 *
 * PHP Version 5.5+
 *
 * @category Data
 * @package  N/A
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
$tables['acs'] = "CREATE TABLE `" . ACS . "` (
`acs_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`acs_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
`acs_owner` INT(11) NOT NULL DEFAULT '0',
`acs_galaxy` int(2) DEFAULT NULL,
`acs_system` int(4) DEFAULT NULL,
`acs_planet` int(2) DEFAULT NULL,
`acs_planet_type` tinyint(1) DEFAULT NULL,
PRIMARY KEY (`acs_id`),
UNIQUE KEY `acs_name` (`acs_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
";

$tables['acs_members'] = "CREATE TABLE `" . ACS_MEMBERS . "` (
`acs_member_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`acs_group_id` int(11) UNSIGNED NOT NULL,
`acs_user_id` int(11) UNSIGNED NOT NULL,
PRIMARY KEY (`acs_member_id`),
UNIQUE( `acs_group_id`, `acs_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

$tables['alliance'] = "CREATE TABLE `" . ALLIANCE . "` (
`alliance_id` bigint(11) NOT NULL AUTO_INCREMENT,
`alliance_name` varchar(32) DEFAULT NULL,
`alliance_tag` varchar(8) DEFAULT NULL,
`alliance_owner` int(11) NOT NULL DEFAULT '0',
`alliance_register_time` int(11) NOT NULL DEFAULT '0',
`alliance_description` text,
`alliance_web` varchar(255) DEFAULT NULL,
`alliance_text` text,
`alliance_image` varchar(255) DEFAULT NULL,
`alliance_request` text,
`alliance_request_notallow` tinyint(4) NOT NULL DEFAULT '0',
`alliance_owner_range` varchar(32) DEFAULT NULL,
`alliance_ranks` text,
PRIMARY KEY (`alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$tables['alliance_statistics'] = "CREATE TABLE `" . ALLIANCE_STATISTICS . "` (
`alliance_statistic_alliance_id` int(11) NOT NULL,
`alliance_statistic_buildings_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`alliance_statistic_buildings_old_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_buildings_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_defenses_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`alliance_statistic_defenses_old_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_defenses_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_ships_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`alliance_statistic_ships_old_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_ships_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_technology_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`alliance_statistic_technology_old_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_technology_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_total_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`alliance_statistic_total_old_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_total_rank` int(11) NOT NULL DEFAULT '0',
`alliance_statistic_update_time` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`alliance_statistic_alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['banned'] = "CREATE TABLE `" . BANNED . "` (
`banned_id` bigint(11) NOT NULL AUTO_INCREMENT,
`banned_who` varchar(64) NOT NULL DEFAULT '',
`banned_theme` text NOT NULL,
`banned_who2` varchar(64) NOT NULL DEFAULT '',
`banned_time` int(11) NOT NULL DEFAULT '0',
`banned_longer` int(11) NOT NULL DEFAULT '0',
`banned_author` varchar(64) NOT NULL DEFAULT '',
`banned_email` varchar(64) NOT NULL DEFAULT '',
PRIMARY KEY (`banned_id`),
KEY `ID` (`banned_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$tables['buddys'] = "CREATE TABLE `" . BUDDY . "` (
  `buddy_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `buddy_sender` int(10) unsigned NOT NULL,
  `buddy_receiver` int(10) unsigned NOT NULL,
  `buddy_status` tinyint(1) NOT NULL DEFAULT '0',
  `buddy_request_text` text,
PRIMARY KEY (`buddy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['buildings'] = "CREATE TABLE `" . BUILDINGS . "` (
`building_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`building_planet_id` int(11) unsigned NOT NULL,
`building_metal_mine` int(11) NOT NULL DEFAULT '0',
`building_crystal_mine` int(11) NOT NULL DEFAULT '0',
`building_deuterium_sintetizer` int(11) NOT NULL DEFAULT '0',
`building_solar_plant` int(11) NOT NULL DEFAULT '0',
`building_fusion_reactor` int(11) NOT NULL DEFAULT '0',
`building_robot_factory` int(11) NOT NULL DEFAULT '0',
`building_nano_factory` int(11) NOT NULL DEFAULT '0',
`building_hangar` int(11) NOT NULL DEFAULT '0',
`building_metal_store` int(11) NOT NULL DEFAULT '0',
`building_crystal_store` int(11) NOT NULL DEFAULT '0',
`building_deuterium_tank` int(11) NOT NULL DEFAULT '0',
`building_laboratory` int(11) NOT NULL DEFAULT '0',
`building_terraformer` int(11) NOT NULL DEFAULT '0',
`building_ally_deposit` int(11) NOT NULL DEFAULT '0',
`building_missile_silo` int(11) NOT NULL DEFAULT '0',
`building_mondbasis` int(11) NOT NULL DEFAULT '0',
`building_phalanx` int(11) NOT NULL DEFAULT '0',
`building_jump_gate` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`building_id`),
UNIQUE KEY `building_planet_id` (`building_planet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$tables['defenses'] = "CREATE TABLE `" . DEFENSES . "` (
`defense_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`defense_planet_id` int(11) unsigned NOT NULL,
`defense_rocket_launcher` int(11) NOT NULL DEFAULT '0',
`defense_light_laser` int(11) NOT NULL DEFAULT '0',
`defense_heavy_laser` int(11) NOT NULL DEFAULT '0',
`defense_ion_cannon` int(11) NOT NULL DEFAULT '0',
`defense_gauss_cannon` int(11) NOT NULL DEFAULT '0',
`defense_plasma_turret` int(11) NOT NULL DEFAULT '0',
`defense_small_shield_dome` int(11) NOT NULL DEFAULT '0',
`defense_large_shield_dome` int(11) NOT NULL DEFAULT '0',
`defense_anti-ballistic_missile` int(11) NOT NULL DEFAULT '0',
`defense_interplanetary_missile` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`defense_id`),
UNIQUE KEY `defense_planet_id` (`defense_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['fleets'] = "CREATE TABLE `" . FLEETS . "` (
`fleet_id` bigint(11) NOT NULL AUTO_INCREMENT,
`fleet_owner` int(11) NOT NULL DEFAULT '0',
`fleet_mission` int(11) NOT NULL DEFAULT '0',
`fleet_amount` bigint(11) NOT NULL DEFAULT '0',
`fleet_array` text,
`fleet_start_time` int(11) NOT NULL DEFAULT '0',
`fleet_start_galaxy` int(11) NOT NULL DEFAULT '0',
`fleet_start_system` int(11) NOT NULL DEFAULT '0',
`fleet_start_planet` int(11) NOT NULL DEFAULT '0',
`fleet_start_type` int(11) NOT NULL DEFAULT '0',
`fleet_end_time` int(11) NOT NULL DEFAULT '0',
`fleet_end_stay` int(11) NOT NULL DEFAULT '0',
`fleet_end_galaxy` int(11) NOT NULL DEFAULT '0',
`fleet_end_system` int(11) NOT NULL DEFAULT '0',
`fleet_end_planet` int(11) NOT NULL DEFAULT '0',
`fleet_end_type` int(11) NOT NULL DEFAULT '0',
`fleet_target_obj` int(2) NOT NULL DEFAULT '0',
`fleet_resource_metal` bigint(11) NOT NULL DEFAULT '0',
`fleet_resource_crystal` bigint(11) NOT NULL DEFAULT '0',
`fleet_resource_deuterium` bigint(11) NOT NULL DEFAULT '0',
`fleet_fuel` bigint(11) NOT NULL DEFAULT '0',
`fleet_target_owner` int(11) NOT NULL DEFAULT '0',
`fleet_group` varchar(15) NOT NULL DEFAULT '0',
`fleet_mess` TINYINT(1) NOT NULL DEFAULT '0',
`fleet_creation` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`fleet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$tables['messages'] = "CREATE TABLE `" . MESSAGES . "` (
`message_id` BIGINT(11) NOT NULL AUTO_INCREMENT,
`message_sender` INT(11) NOT NULL DEFAULT '0',
`message_receiver` INT(11) NOT NULL DEFAULT '0',
`message_time` INT(11) NOT NULL DEFAULT '0',
`message_type` INT(11) NOT NULL DEFAULT '0',
`message_from` VARCHAR(48) CHARACTER SET utf8 DEFAULT NULL,
`message_subject` text CHARACTER SET utf8,
`message_text` text CHARACTER SET utf8,
`message_read` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0',
PRIMARY KEY  (`message_id`)
) ENGINE=InnoDB;";

$tables['notes'] = "CREATE TABLE `" . NOTES . "` (
`note_id` bigint(11) NOT NULL AUTO_INCREMENT,
`note_owner` int(11) DEFAULT NULL,
`note_time` int(11) DEFAULT NULL,
`note_priority` tinyint(1) DEFAULT NULL,
`note_title` varchar(32) DEFAULT NULL,
`note_text` text,
PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$tables['options'] = "CREATE TABLE IF NOT EXISTS `" . OPTIONS . "` (
`option_id` bigint(20) unsigned NOT NULL,
`option_name` varchar(191) DEFAULT NULL,
`option_value` longtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;";

$tables['options_insert'] = "INSERT INTO `" . OPTIONS . "` (`option_id`, `option_name`, `option_value`) VALUES
(1, 'game_name', 'XG Proyect'),
(2, 'game_logo', 'http://www.xgproyect.org/images/misc/xg-logo.png'),
(3, 'lang', 'spanish'),
(4, 'game_speed', '2500'),
(5, 'fleet_speed', '2500'),
(6, 'resource_multiplier', '1'),
(7, 'admin_email', ''),
(8, 'forum_url', 'http://www.xgproyect.org/'),
(9, 'game_enable', '1'),
(10, 'close_reason', 'Sorry, the server is currently offline.'),
(11, 'date_time_zone', 'America/Argentina/Buenos_Aires'),
(12, 'date_format', 'd.m.Y'),
(13, 'date_format_extended', 'd.m.Y H:i:s'),
(14, 'adm_attack', '1'),
(15, 'fleet_cdr', '30'),
(16, 'defs_cdr', '30'),
(17, 'noobprotection', '1'),
(18, 'noobprotectiontime', '50000'),
(19, 'noobprotectionmulti', '5'),
(20, 'modules', '1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;0;1;1;'),
(21, 'moderation', '1,1,0,0,1,0;1,1,0,1,1,1;1;'),
(22, 'initial_fields', '163'),
(23, 'metal_basic_income', '90'),
(24, 'crystal_basic_income', '45'),
(25, 'deuterium_basic_income', '0'),
(26, 'energy_basic_income', '0'),
(27, 'reg_enable', '1'),
(28, 'reg_welcome_message', '1'),
(29, 'reg_welcome_email', '1'),
(30, 'stat_points', '1000'),
(31, 'stat_update_time', '1'),
(32, 'stat_admin_level', '0'),
(33, 'stat_last_update', '0'),
(34, 'premium_url', 'http://www.xgproyect.org/game.php?page=officier'),
(35, 'trader_darkmatter', '3500'),
(36, 'auto_backup', '0'),
(37, 'last_backup', '0'),
(38, 'last_cleanup', '0'),
(39, 'version', '" . SYSTEM_VERSION . "'),
(40, 'lastsettedgalaxypos', '1'),
(41, 'lastsettedsystempos', '1'),
(42, 'lastsettedplanetpos', '1');";

$tables['planets'] = "CREATE TABLE `" . PLANETS . "` (
`planet_id` bigint(11) NOT NULL AUTO_INCREMENT,
`planet_name` varchar(255) DEFAULT NULL,
`planet_user_id` int(11) DEFAULT NULL,
`planet_galaxy` int(11) NOT NULL DEFAULT '0',
`planet_system` int(11) NOT NULL DEFAULT '0',
`planet_planet` int(11) NOT NULL DEFAULT '0',
`planet_last_update` int(11) DEFAULT NULL,
`planet_type` int(11) NOT NULL DEFAULT '1',
`planet_destroyed` int(11) NOT NULL DEFAULT '0',
`planet_b_building` int(11) NOT NULL DEFAULT '0',
`planet_b_building_id` text NOT NULL,
`planet_b_tech` int(11) NOT NULL DEFAULT '0',
`planet_b_tech_id` int(11) NOT NULL DEFAULT '0',
`planet_b_hangar` int(11) NOT NULL DEFAULT '0',
`planet_b_hangar_id` text  DEFAULT NULL,
`planet_image` varchar(32) NOT NULL DEFAULT 'normaltempplanet01',
`planet_diameter` int(11) NOT NULL DEFAULT '12800',
`planet_field_current` int(11) NOT NULL DEFAULT '0',
`planet_field_max` int(11) NOT NULL DEFAULT '163',
`planet_temp_min` int(3) NOT NULL DEFAULT '-17',
`planet_temp_max` int(3) NOT NULL DEFAULT '23',
`planet_metal` double(132,8) NOT NULL DEFAULT '0.00000000',
`planet_metal_perhour` int(11) NOT NULL DEFAULT '0',
`planet_metal_max` bigint(20) DEFAULT '10000',
`planet_crystal` double(132,8) NOT NULL DEFAULT '0.00000000',
`planet_crystal_perhour` int(11) NOT NULL DEFAULT '0',
`planet_crystal_max` bigint(20) DEFAULT '10000',
`planet_deuterium` double(132,8) NOT NULL DEFAULT '0.00000000',
`planet_deuterium_perhour` int(11) NOT NULL DEFAULT '0',
`planet_deuterium_max` bigint(20) DEFAULT '10000',
`planet_energy_used` int(11) NOT NULL DEFAULT '0',
`planet_energy_max` bigint(20) NOT NULL DEFAULT '0',
`planet_building_metal_mine_percent` int(11) NOT NULL DEFAULT '10',
`planet_building_crystal_mine_percent` int(11) NOT NULL DEFAULT '10',
`planet_building_deuterium_sintetizer_percent` int(11) NOT NULL DEFAULT '10',
`planet_building_solar_plant_percent` int(11) NOT NULL DEFAULT '10',
`planet_building_fusion_reactor_percent` int(11) NOT NULL DEFAULT '10',
`planet_ship_solar_satellite_percent` int(11) NOT NULL DEFAULT '10',
`planet_last_jump_time` int(11) NOT NULL DEFAULT '0',
`planet_debris_metal` bigint(11) NOT NULL DEFAULT '0',
`planet_debris_crystal` bigint(11) NOT NULL DEFAULT '0',
`planet_invisible_start_time` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`planet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$tables['premium'] = "CREATE TABLE `" . PREMIUM . "` (
`premium_user_id` INT(10) UNSIGNED NOT NULL,
`premium_dark_matter` INT(10) NOT NULL DEFAULT '0',
`premium_officier_commander` INT(10) NOT NULL DEFAULT '0',
`premium_officier_admiral` INT(10) NOT NULL DEFAULT '0',
`premium_officier_engineer` INT(10) NOT NULL DEFAULT '0',
`premium_officier_geologist` INT(10) NOT NULL DEFAULT '0',
`premium_officier_technocrat` INT(10) NOT NULL DEFAULT '0',
UNIQUE KEY `premium_user_id` (`premium_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['reports'] = "CREATE TABLE `" . REPORTS . "` (
`report_owners` varchar(255) NOT NULL,
`report_rid` varchar(42) NOT NULL,
`report_content` text NOT NULL,
`report_destroyed` tinyint(1) unsigned NOT NULL DEFAULT '0',
`report_time` int(10) unsigned NOT NULL DEFAULT '0',
UNIQUE KEY `report_rid` (`report_rid`),
KEY `time` (`report_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$tables['research'] = "CREATE TABLE `" . RESEARCH . "` (
`research_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`research_user_id` int(11) unsigned NOT NULL,
`research_current_research` int(11) NOT NULL DEFAULT '0',
`research_espionage_technology` int(11) NOT NULL DEFAULT '0',
`research_computer_technology` int(11) NOT NULL DEFAULT '0',
`research_weapons_technology` int(11) NOT NULL DEFAULT '0',
`research_shielding_technology` int(11) NOT NULL DEFAULT '0',
`research_armour_technology` int(11) NOT NULL DEFAULT '0',
`research_energy_technology` int(11) NOT NULL DEFAULT '0',
`research_hyperspace_technology` int(11) NOT NULL DEFAULT '0',
`research_combustion_drive` int(11) NOT NULL DEFAULT '0',
`research_impulse_drive` int(11) NOT NULL DEFAULT '0',
`research_hyperspace_drive` int(11) NOT NULL DEFAULT '0',
`research_laser_technology` int(11) NOT NULL DEFAULT '0',
`research_ionic_technology` int(11) NOT NULL DEFAULT '0',
`research_plasma_technology` int(11) NOT NULL DEFAULT '0',
`research_intergalactic_research_network` int(11) NOT NULL DEFAULT '0',
`research_astrophysics` int(11) NOT NULL DEFAULT '0',
`research_graviton_technology` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`research_id`),
UNIQUE KEY `research_user_id` (`research_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$tables['sessions'] = "CREATE TABLE `" . SESSIONS . "` (
`session_id` CHAR(32) NOT NULL,
`session_data` longtext NOT NULL,
`session_last_accessed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['settings'] = "CREATE TABLE `" . SETTINGS . "` (
`setting_user_id` INT(10) UNSIGNED NOT NULL,
`setting_no_ip_check` TINYINT(1) NOT NULL DEFAULT '1',
`setting_planet_sort` TINYINT(1) NOT NULL DEFAULT '0',
`setting_planet_order` TINYINT(1) NOT NULL DEFAULT '0',
`setting_probes_amount` TINYINT(1) NOT NULL DEFAULT '1',
`setting_fleet_actions` TINYINT(1) NOT NULL DEFAULT '0',
`setting_galaxy_espionage` TINYINT(1) NOT NULL DEFAULT '1',
`setting_galaxy_write` TINYINT(1) NOT NULL DEFAULT '1',
`setting_galaxy_buddy` TINYINT(1) NOT NULL DEFAULT '1',
`setting_galaxy_missile` TINYINT(1) NOT NULL DEFAULT '1',
`setting_vacations_status` TINYINT(1) NOT NULL DEFAULT '0',
`setting_vacations_until` INT(10) NOT NULL DEFAULT '0',
`setting_delete_account` INT(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `setting_user_id` (`setting_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['ships'] = "CREATE TABLE `" . SHIPS . "` (
`ship_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`ship_planet_id` int(11) unsigned NOT NULL,
`ship_small_cargo_ship` int(11) NOT NULL DEFAULT '0',
`ship_big_cargo_ship` int(11) NOT NULL DEFAULT '0',
`ship_light_fighter` int(11) NOT NULL DEFAULT '0',
`ship_heavy_fighter` int(11) NOT NULL DEFAULT '0',
`ship_cruiser` int(11) NOT NULL DEFAULT '0',
`ship_battleship` int(11) NOT NULL DEFAULT '0',
`ship_colony_ship` int(11) NOT NULL DEFAULT '0',
`ship_recycler` int(11) NOT NULL DEFAULT '0',
`ship_espionage_probe` int(11) NOT NULL DEFAULT '0',
`ship_bomber` int(11) NOT NULL DEFAULT '0',
`ship_solar_satellite` int(11) NOT NULL DEFAULT '0',
`ship_destroyer` int(11) NOT NULL DEFAULT '0',
`ship_deathstar` int(11) NOT NULL DEFAULT '0',
`ship_battlecruiser` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`ship_id`),
UNIQUE KEY `ship_planet_id` (`ship_planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$tables['users'] = "CREATE TABLE `" . USERS . "` (
`user_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
`user_name` varchar(64) NOT NULL DEFAULT '',
`user_password` varchar(64) NOT NULL DEFAULT '',
`user_email` varchar(64) NOT NULL DEFAULT '',
`user_email_permanent` varchar(64) NOT NULL DEFAULT '',
`user_authlevel` tinyint(4) NOT NULL DEFAULT '0',
`user_home_planet_id` int(11) NOT NULL DEFAULT '0',
`user_galaxy` int(11) NOT NULL DEFAULT '0',
`user_system` int(11) NOT NULL DEFAULT '0',
`user_planet` int(11) NOT NULL DEFAULT '0',
`user_current_planet` int(11) NOT NULL DEFAULT '0',
`user_lastip` varchar(39) NOT NULL DEFAULT '',
`user_ip_at_reg` varchar(39) NOT NULL DEFAULT '',
`user_agent` text NOT NULL,
`user_current_page` text NOT NULL,
`user_register_time` int(11) NOT NULL DEFAULT '0',
`user_onlinetime` int(11) NOT NULL DEFAULT '0',
`user_fleet_shortcuts` text,
`user_ally_id` int(11) NOT NULL DEFAULT '0',
`user_ally_request` int(11) NOT NULL DEFAULT '0',
`user_ally_request_text` text,
`user_ally_register_time` int(11) NOT NULL DEFAULT '0',
`user_ally_rank_id` int(11) NOT NULL DEFAULT '0',
`user_banned` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$tables['user_statistics'] = "CREATE TABLE `" . USERS_STATISTICS . "` (
`user_statistic_user_id` int(11) NOT NULL,
`user_statistic_buildings_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`user_statistic_buildings_old_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_buildings_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_defenses_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`user_statistic_defenses_old_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_defenses_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_ships_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`user_statistic_ships_old_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_ships_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_technology_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`user_statistic_technology_old_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_technology_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_total_points` double(132,8) NOT NULL DEFAULT '0.00000000',
`user_statistic_total_old_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_total_rank` int(11) NOT NULL DEFAULT '0',
`user_statistic_update_time` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`user_statistic_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

/* end of database.php */
