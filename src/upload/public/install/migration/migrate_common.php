<?php
/**
 * MOVE DATA
 */

// "aks" table -> "acs_fleets" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . ACS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . ACS . "`(
                `acs_id`,
                `acs_name`,
                `acs_owner`,
                `acs_galaxy`,
                `acs_system`,
                `acs_planet`,
                `acs_planet_type`)
                SELECT
                    `id`,
                    `name`,
                    `teilnehmer`,
                    `galaxy`,
                    `system`,
                    `planet`,
                    `planet_type`
                FROM `{prefix}aks`;";

// "alliance" table -> "alliance" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . ALLIANCE . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . ALLIANCE . "`(
                `alliance_id`,
                `alliance_name`,
                `alliance_tag`,
                `alliance_owner`,
                `alliance_register_time`,
                `alliance_description`,
                `alliance_web`,
                `alliance_text`,
                `alliance_image`,
                `alliance_request`,
                `alliance_request_notallow`)
                SELECT
                    `id`,
                    `ally_name`,
                    `ally_tag`,
                    `ally_owner`,
                    `ally_register_time`,
                    `ally_description`,
                    `ally_web`,
                    `ally_text`,
                    `ally_image`,
                    `ally_request`,
                    `ally_request_notallow`
                FROM `{prefix}alliance`;";

// "banned" table -> "banned" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . BANNED . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . BANNED . "`(
                `banned_id`,
                `banned_who`,
                `banned_theme`,
                `banned_time`,
                `banned_longer`,
                `banned_author`,
                `banned_email`)
                SELECT
                    `id`,
                    `who`,
                    `theme`,
                    `time`,
                    `longer`,
                    `author`,
                    `email`
                FROM `{prefix}banned`;";

// "buddy" table -> "buddys" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . BUDDY . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . BUDDY . "`(
                `buddy_id`,
                `buddy_sender`,
                `buddy_receiver`,
                `buddy_status`,
                `buddy_request_text`)
                SELECT
                    `id`,
                    `sender`,
                    `owner`,
                    `active`,
                    `text`
                FROM `{prefix}buddy`;";

// "fleets" table -> "fleets" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . FLEETS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . FLEETS . "`(
                `fleet_id`,
                `fleet_owner`,
                `fleet_mission`,
                `fleet_amount`,
                `fleet_array`,
                `fleet_start_time`,
                `fleet_start_galaxy`,
                `fleet_start_system`,
                `fleet_start_planet`,
                `fleet_start_type`,
                `fleet_end_time`,
                `fleet_end_stay`,
                `fleet_end_galaxy`,
                `fleet_end_system`,
                `fleet_end_planet`,
                `fleet_end_type`,
                `fleet_target_obj`,
                `fleet_resource_metal`,
                `fleet_resource_crystal`,
                `fleet_resource_deuterium`,
                `fleet_fuel`,
                `fleet_target_owner`,
                `fleet_group`,
                `fleet_mess`,
                `fleet_creation`)
                SELECT
                    `fleet_id`,
                    `fleet_owner`,
                    `fleet_mission`,
                    `fleet_amount`,
                    `fleet_array`,
                    `fleet_start_time`,
                    `fleet_start_galaxy`,
                    `fleet_start_system`,
                    `fleet_start_planet`,
                    `fleet_start_type`,
                    `fleet_end_time`,
                    `fleet_end_stay`,
                    `fleet_end_galaxy`,
                    `fleet_end_system`,
                    `fleet_end_planet`,
                    `fleet_end_type`,
                    `fleet_target_obj`,
                    `fleet_resource_metal`,
                    `fleet_resource_crystal`,
                    `fleet_resource_deuterium`,
                    '0',
                    `fleet_target_owner`,
                    `fleet_group`,
                    `fleet_mess`,
                    `start_time`
                FROM `{prefix}fleets`;";

// "messages" table -> "messages" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . MESSAGES . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . MESSAGES . "`(
                `message_id`,
                `message_sender`,
                `message_receiver`,
                `message_time`,
                `message_type`,
                `message_from`,
                `message_subject`,
                `message_text`,
                `message_read`)
                SELECT
                    `message_id`,
                    `message_owner`,
                    `message_sender`,
                    `message_time`,
                    `message_type`,
                    `message_from`,
                    `message_subject`,
                    `message_text`,
                    '0'
                FROM `{prefix}messages`;";

// "notes" table -> "notes" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . NOTES . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . NOTES . "`(
                `note_id`,
                `note_owner`,
                `note_time`,
                `note_priority`,
                `note_title`,
                `note_text`)
                SELECT
                    `id`,
                    `owner`,
                    `time`,
                    `priority`,
                    `title`,
                    `text`
                FROM `{prefix}notes`;";

/**
 * PLANETS TABLE QUERYS
 */

// "planets" table -> "planets" table && "galaxy" table -> "planets" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . PLANETS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . PLANETS . "`(
                `planet_id`,
                `planet_name`,
                `planet_user_id`,
                `planet_galaxy`,
                `planet_system`,
                `planet_planet`,
                `planet_last_update`,
                `planet_type`,
                `planet_destroyed`,
                `planet_b_building`,
                `planet_b_building_id`,
                `planet_b_tech`,
                `planet_b_tech_id`,
                `planet_b_hangar`,
                `planet_b_hangar_id`,
                `planet_image`,
                `planet_diameter`,
                `planet_field_current`,
                `planet_field_max`,
                `planet_temp_min`,
                `planet_temp_max`,
                `planet_metal`,
                `planet_metal_perhour`,
                `planet_crystal`,
                `planet_crystal_perhour`,
                `planet_deuterium`,
                `planet_deuterium_perhour`,
                `planet_energy_used`,
                `planet_energy_max`,
                `planet_building_metal_mine_percent`,
                `planet_building_crystal_mine_percent`,
                `planet_building_deuterium_sintetizer_percent`,
                `planet_building_solar_plant_percent`,
                `planet_building_fusion_reactor_percent`,
                `planet_ship_solar_satellite_percent`,
                `planet_last_jump_time`,
                `planet_debris_metal`,
                `planet_debris_crystal`,
                `planet_invisible_start_time`)
                SELECT
                    p.`id`,
                    p.`name`,
                    p.`id_owner`,
                    p.`galaxy`,
                    p.`system`,
                    p.`planet`,
                    p.`last_update`,
                    p.`planet_type`,
                    p.`destruyed`,
                    p.`b_building`,
                    p.`b_building_id`,
                    p.`b_tech`,
                    p.`b_tech_id`,
                    p.`b_hangar`,
                    p.`b_hangar_id`,
                    p.`image`,
                    p.`diameter`,
                    p.`field_current`,
                    p.`field_max`,
                    p.`temp_min`,
                    p.`temp_max`,
                    p.`metal`,
                    p.`metal_perhour`,
                    p.`crystal`,
                    p.`crystal_perhour`,
                    p.`deuterium`,
                    p.`deuterium_perhour`,
                    p.`energy_used`,
                    p.`energy_max`,
                    p.`metal_mine_porcent`,
                    p.`crystal_mine_porcent`,
                    p.`deuterium_sintetizer_porcent`,
                    p.`solar_plant_porcent`,
                    p.`fusion_plant_porcent`,
                    p.`solar_satelit_porcent`,
                    p.`last_jump_time`,
                    g.`metal`,
                    g.`crystal`,
                    g.`invisible_start_time`
                FROM `{prefix}planets` AS p
                LEFT JOIN `{prefix}galaxy` AS g
                    ON (p.galaxy = g.galaxy
                        AND p.system = g.system
                        AND p.planet = g.planet);";

// "planets" table -> "buildings" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . BUILDINGS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . BUILDINGS . "`(
                    `building_planet_id`,
                    `building_metal_mine`,
                    `building_crystal_mine`,
                    `building_deuterium_sintetizer`,
                    `building_solar_plant`,
                    `building_fusion_reactor`,
                    `building_robot_factory`,
                    `building_nano_factory`,
                    `building_hangar`,
                    `building_metal_store`,
                    `building_crystal_store`,
                    `building_deuterium_tank`,
                    `building_laboratory`,
                    `building_terraformer`,
                    `building_ally_deposit`,
                    `building_missile_silo`,
                    `building_mondbasis`,
                    `building_phalanx`,
                    `building_jump_gate`)
                SELECT
                    `id`,
                    `metal_mine`,
                    `crystal_mine`,
                    `deuterium_sintetizer`,
                    `solar_plant`,
                    `fusion_plant`,
                    `robot_factory`,
                    `nano_factory`,
                    `hangar`,
                    `metal_store`,
                    `crystal_store`,
                    `deuterium_store`,
                    `laboratory`,
                    `terraformer`,
                    `ally_deposit`,
                    `silo`,
                    `mondbasis`,
                    `phalanx`,
                    `sprungtor`
                FROM `{prefix}planets`;";

// "planets" table -> "defenses" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . DEFENSES . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . DEFENSES . "`(
                    `defense_planet_id`,
                    `defense_rocket_launcher`,
                    `defense_light_laser`,
                    `defense_heavy_laser`,
                    `defense_ion_cannon`,
                    `defense_gauss_cannon`,
                    `defense_plasma_turret`,
                    `defense_small_shield_dome`,
                    `defense_large_shield_dome`,
                    `defense_anti-ballistic_missile`,
                    `defense_interplanetary_missile`)
                SELECT
                    `id`,
                    `misil_launcher`,
                    `small_laser`,
                    `big_laser`,
                    `ionic_canyon`,
                    `gauss_canyon`,
                    `buster_canyon`,
                    `small_protection_shield`,
                    `big_protection_shield`,
                    `interceptor_misil`,
                    `interplanetary_misil`
                FROM `{prefix}planets`;";

// "planets" table -> "ships" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . SHIPS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . SHIPS . "`(
                    `ship_planet_id`,
                    `ship_small_cargo_ship`,
                    `ship_big_cargo_ship`,
                    `ship_light_fighter`,
                    `ship_heavy_fighter`,
                    `ship_cruiser`,
                    `ship_battleship`,
                    `ship_colony_ship`,
                    `ship_recycler`,
                    `ship_espionage_probe`,
                    `ship_bomber`,
                    `ship_solar_satellite`,
                    `ship_destroyer`,
                    `ship_deathstar`,
                    `ship_battlecruiser`)
                SELECT
                    `id`,
                    `small_ship_cargo`,
                    `big_ship_cargo`,
                    `light_hunter`,
                    `heavy_hunter`,
                    `crusher`,
                    `battle_ship`,
                    `colonizer`,
                    `recycler`,
                    `spy_sonde`,
                    `bomber_ship`,
                    `solar_satelit`,
                    `destructor`,
                    `dearth_star`,
                    `battleship`
                FROM `{prefix}planets`;";

// "rw" table -> "reports" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . REPORTS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . REPORTS . "`(
                    `report_owners`,
                    `report_rid`,
                    `report_content`,
                    `report_destroyed`,
                    `report_time`)
                SELECT
                    `owners`,
                    `rid`,
                    `raport`,
                    `a_zestrzelona`,
                    `time`
                FROM `{prefix}rw`;";

/**
 * STATISTICS
 */

// "statpoints" table -> "users_statistics" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . USERS_STATISTICS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . USERS_STATISTICS . "`(
                    `user_statistic_user_id`,
                    `user_statistic_buildings_points`,
                    `user_statistic_buildings_old_rank`,
                    `user_statistic_buildings_rank`,
                    `user_statistic_defenses_points`,
                    `user_statistic_defenses_old_rank`,
                    `user_statistic_defenses_rank`,
                    `user_statistic_ships_points`,
                    `user_statistic_ships_old_rank`,
                    `user_statistic_ships_rank`,
                    `user_statistic_technology_points`,
                    `user_statistic_technology_old_rank`,
                    `user_statistic_technology_rank`,
                    `user_statistic_total_points`,
                    `user_statistic_total_old_rank`,
                    `user_statistic_total_rank`,
                    `user_statistic_update_time`)
                SELECT
                    `id_owner`,
                    `build_points`,
                    `build_old_rank`,
                    `build_rank`,
                    `defs_points`,
                    `defs_old_rank`,
                    `defs_rank`,
                    `fleet_points`,
                    `fleet_old_rank`,
                    `fleet_rank`,
                    `tech_points`,
                    `tech_old_rank`,
                    `tech_rank`,
                    `total_points`,
                    `total_old_rank`,
                    `total_rank`,
                    `stat_date`
                FROM `{prefix}statpoints`
                WHERE `id_ally` = '0';";

// "statpoints" table -> "alliance_statistics" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . ALLIANCE_STATISTICS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . ALLIANCE_STATISTICS . "`(
                    `alliance_statistic_alliance_id`,
                    `alliance_statistic_buildings_points`,
                    `alliance_statistic_buildings_old_rank`,
                    `alliance_statistic_buildings_rank`,
                    `alliance_statistic_defenses_points`,
                    `alliance_statistic_defenses_old_rank`,
                    `alliance_statistic_defenses_rank`,
                    `alliance_statistic_ships_points`,
                    `alliance_statistic_ships_old_rank`,
                    `alliance_statistic_ships_rank`,
                    `alliance_statistic_technology_points`,
                    `alliance_statistic_technology_old_rank`,
                    `alliance_statistic_technology_rank`,
                    `alliance_statistic_total_points`,
                    `alliance_statistic_total_old_rank`,
                    `alliance_statistic_total_rank`,
                    `alliance_statistic_update_time`)
                SELECT
                    `id_ally`,
                    `build_points`,
                    `build_old_rank`,
                    `build_rank`,
                    `defs_points`,
                    `defs_old_rank`,
                    `defs_rank`,
                    `fleet_points`,
                    `fleet_old_rank`,
                    `fleet_rank`,
                    `tech_points`,
                    `tech_old_rank`,
                    `tech_rank`,
                    `total_points`,
                    `total_old_rank`,
                    `total_rank`,
                    `stat_date`
                FROM `{prefix}statpoints`
                WHERE `id_ally` <> '0';";

/**
 * USERS
 */

// "users" table -> "users" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . USERS . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . USERS . "`(
                    `user_id`,
                    `user_name`,
                    `user_password`,
                    `user_email`,
                    `user_authlevel`,
                    `user_home_planet_id`,
                    `user_galaxy`,
                    `user_system`,
                    `user_planet`,
                    `user_current_planet`,
                    `user_lastip`,
                    `user_ip_at_reg`,
                    `user_agent`,
                    `user_current_page`,
                    `user_register_time`,
                    `user_onlinetime`,
                    `user_fleet_shortcuts`,
                    `user_ally_id`,
                    `user_ally_request`,
                    `user_ally_request_text`,
                    `user_ally_register_time`,
                    `user_ally_rank_id`,
                    `user_banned`)
                SELECT
                    `id`,
                    `username`,
                    IF(`id` = 1, '" . $password . "', `password`) AS `password`,
                    `email`,
                    `authlevel`,
                    `id_planet`,
                    `galaxy`,
                    `system`,
                    `planet`,
                    `current_planet`,
                    `user_lastip`,
                    `ip_at_reg`,
                    `user_agent`,
                    `current_page`,
                    `register_time`,
                    `onlinetime`,
                    `fleet_shortcut`,
                    `ally_id`,
                    `ally_request`,
                    `ally_request_text`,
                    `ally_register_time`,
                    `ally_rank_id`,
                    `banaday`
                FROM `{prefix}users`;";

// "users" table -> "premium" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . PREMIUM . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . PREMIUM . "`(
                    `premium_user_id`,
                    `premium_dark_matter`,
                    `premium_officier_commander`,
                    `premium_officier_admiral`,
                    `premium_officier_engineer`,
                    `premium_officier_geologist`,
                    `premium_officier_technocrat`)
                SELECT
                    `id`,
                    `darkmatter`,
                    '0',
                    `rpg_amiral`,
                    `rpg_ingenieur`,
                    `rpg_geologue`,
                    `rpg_technocrate`
                FROM `{prefix}users`;";

// "users" table -> "research" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . RESEARCH . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . RESEARCH . "`(
                    `research_user_id`,
                    `research_current_research`,
                    `research_espionage_technology`,
                    `research_computer_technology`,
                    `research_weapons_technology`,
                    `research_shielding_technology`,
                    `research_armour_technology`,
                    `research_energy_technology`,
                    `research_hyperspace_technology`,
                    `research_combustion_drive`,
                    `research_impulse_drive`,
                    `research_hyperspace_drive`,
                    `research_laser_technology`,
                    `research_ionic_technology`,
                    `research_plasma_technology`,
                    `research_intergalactic_research_network`,
                    `research_astrophysics`,
                    `research_graviton_technology`)
                SELECT
                    `id`,
                    `b_tech_planet`,
                    `spy_tech`,
                    `computer_tech`,
                    `military_tech`,
                    `defence_tech`,
                    `shield_tech`,
                    `energy_tech`,
                    `hyperspace_tech`,
                    `combustion_tech`,
                    `impulse_motor_tech`,
                    `hyperspace_motor_tech`,
                    `laser_tech`,
                    `ionic_tech`,
                    `buster_tech`,
                    `intergalactic_tech`,
                    `expedition_tech`,
                    `graviton_tech`
                FROM `{prefix}users`;";

// "users" table -> "settings" table
$queries[] = "TRUNCATE " . DB_NAME . ".`" . PREFERENCES . "`;";
$queries[] = "INSERT INTO " . DB_NAME . ".`" . PREFERENCES . "`(
                    `preference_user_id`,
                    `preference_spy_probes`,
                    `preference_planet_sort`,
                    `preference_planet_sort_sequence`,
                    `preference_vacation_mode`,
                    `preference_delete_mode`)
                SELECT
                    `id`,
                    `spio_anz`,
                    `planet_sort`,
                    `planet_sort_order`,
                    `urlaubs_until`,
                    `db_deaktjava`
                FROM `{prefix}users`;";
$queries[] = "TRUNCATE " . DB_NAME . ".`" . SESSIONS . "`;";
