<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Imperium extends XGPCore
{
	const MODULE_ID	= 2;

	private $_lang;
	private $_current_user;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		// Check module access
		Functions_Lib::module_message ( Functions_Lib::is_module_accesible ( self::MODULE_ID ) );

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();

		if ( ! Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_commander'] ) )
		{
			Functions_Lib::redirect ( 'game.php?page=officier' );
		}
		else
		{
			$this->build_page();
		}
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct()
	{
		parent::$db->close_connection();
	}

	/**
	 * method build_page
	 * param
	 * return main method, loads everything
	 */
	private function build_page()
	{
		$resource	= parent::$objects->get_objects();
		$reslist	= parent::$objects->get_objects_list();

		$planetsrow = parent::$db->query ( "SELECT `planet_id`,
													`planet_name`,
													`planet_galaxy`,
													`planet_system`,
													`planet_planet`,
													`planet_type`,
													`planet_image`,
													`planet_field_current`,
													`planet_field_max`,
													`planet_metal`,
													`planet_metal_perhour`,
													`planet_crystal`,
													`planet_crystal_perhour`,
													`planet_deuterium`,
													`planet_deuterium_perhour`,
													`planet_energy_used`,
													`planet_energy_max`,
													b.`building_metal_mine`,
													b.`building_crystal_mine`,
													b.`building_deuterium_sintetizer`,
													b.`building_solar_plant`,
													b.`building_fusion_reactor`,
													b.`building_robot_factory`,
													b.`building_nano_factory`,
													b.`building_hangar`,
													b.`building_metal_store`,
													b.`building_crystal_store`,
													b.`building_deuterium_tank`,
													b.`building_laboratory`,
													b.`building_terraformer`,
													b.`building_ally_deposit`,
													b.`building_missile_silo`,
													b.`building_mondbasis`,
													b.`building_phalanx`,
													b.`building_jump_gate`,
													d.`defense_rocket_launcher`,
													d.`defense_light_laser`,
													d.`defense_heavy_laser`,
													d.`defense_gauss_cannon`,
													d.`defense_ion_cannon`,
													d.`defense_plasma_turret`,
													d.`defense_small_shield_dome`,
													d.`defense_large_shield_dome`,
													d.`defense_anti-ballistic_missile`,
													d.`defense_interplanetary_missile`,
													s.`ship_small_cargo_ship`,
													s.`ship_big_cargo_ship`,
													s.`ship_light_fighter`,
													s.`ship_heavy_fighter`,
													s.`ship_cruiser`,
													s.`ship_battleship`,
													s.`ship_colony_ship`,
													s.`ship_recycler`,
													s.`ship_espionage_probe`,
													s.`ship_bomber`,
													s.`ship_solar_satellite`,
													s.`ship_destroyer`,
													s.`ship_deathstar`,
													s.`ship_battlecruiser`
													FROM " . PLANETS . " AS p
													INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
													INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
													INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
													WHERE `planet_user_id` = '" . (int)$this->_current_user['user_id'] . "'
														AND `planet_destroyed` = 0;");

		$parse 			= $this->_lang;
		$planet 		= array();
		$r				= array();
		$EmpireRowTPL	= parent::$page->get_template ( 'empire/empire_row' );
		$f 				= array ( 'file_images' , 'file_names' , 'file_coordinates' , 'file_fields' , 'file_metal', 'file_crystal' , 'file_deuterium' , 'file_energy' );
		$m 				= array ( 'build' , 'tech' , 'fleet' , 'defense' );
		$n	 			= array ( 'building_row' , 'technology_row' , 'fleet_row' , 'defense_row' );

		while ( $p = parent::$db->fetch_array ( $planetsrow ) )
		{
			$planet[] = $p;
		}

		$parse['mount'] = 	count ( $planet ) + 1;

		foreach ( $planet as $p )
		{
			$datat  = array ( '<a href="game.php?page=overview&cp=' . $p['planet_id'] . '&amp;re=0"><img src="' . DPATH . 'planets/small/s_' . $p['planet_image'] . '.jpg" border="0" height="80" width="80"></a>', $p['planet_name'], "[<a href=\"game.php?page=galaxy&mode=3&galaxy={$p['planet_galaxy']}&system={$p['planet_system']}\">{$p['planet_galaxy']}:{$p['planet_system']}:{$p['planet_planet']}</a>]", $p['planet_field_current'] . '/' . $p['planet_field_max'], '<a href="game.php?page=resources&cp=' . $p['planet_id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . Format_Lib::pretty_number($p['planet_metal']) . '</a> / ' . Format_Lib::pretty_number($p['planet_metal_perhour'] + Functions_Lib::read_config ( 'metal_basic_income' )), '<a href="game.php?page=resources&cp=' . $p['planet_id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . Format_Lib::pretty_number($p['planet_crystal']) . '</a> / ' . Format_Lib::pretty_number($p['planet_crystal_perhour'] + Functions_Lib::read_config ( 'crystal_basic_income' )), '<a href="game.php?page=resources&cp=' . $p['planet_id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . Format_Lib::pretty_number($p['planet_deuterium']) . '</a> / ' . Format_Lib::pretty_number($p['planet_deuterium_perhour'] + Functions_Lib::read_config ( 'deuterium_basic_income' )), Format_Lib::pretty_number($p['planet_energy_max'] - $p['planet_energy_used']) . ' / ' . Format_Lib::pretty_number($p['planet_energy_max']));

			for ($k = 0; $k < 8; $k++)
			{
				$parse[$f[$k]]	= isset ( $parse[$f[$k]] ) ? $parse[$f[$k]] : '';
				$data['text'] 	= $datat[$k];
				$parse[$f[$k]] .= parent::$page->parse_template ( $EmpireRowTPL , $data );
			}

			foreach ( $resource as $i => $res )
			{
				$r[$i]			= isset ( $r[$i] ) ? $r[$i] : '';
				$data['text'] 	= ( !isset ( $p[$resource[$i]] ) && !isset ( $this->_current_user[$resource[$i]] ) ) ? '0' : ( ( in_array ( $i , $reslist['build'] ) ) ? "<a href=\"game.php?page=" . Developments_Lib::set_building_page ( $i ) . "&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ( ( in_array ( $i , $reslist['tech'] ) ) ? "<a href=\"game.php?page=research&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$this->_current_user[$resource[$i]]}</a>" : ( ( in_array ( $i , $reslist['fleet'] ) ) ? "<a href=\"game.php?page=shipyard&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ( ( in_array ( $i , $reslist['defense'] ) ) ? "<a href=\"game.php?page=defense&cp={$p['planet_id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : '0' ) ) ) );
				$r[$i] 	   	   .= parent::$page->parse_template ( $EmpireRowTPL , $data );
			}
		}

		for ( $j = 0 ; $j < 4 ; $j++ )
		{
			foreach ( $reslist[$m[$j]] as $a => $i )
			{
				$parse[$n[$j]]		= isset ( $parse[$n[$j]] ) ? $parse[$n[$j]] : '';
				$data['text'] 	 	= $this->_lang['tech'][$i];
				$parse[$n[$j]] 	   .= "<tr>" . parent::$page->parse_template ( $EmpireRowTPL , $data ) . $r[$i] . "</tr>";
			}
		}

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'empire/empire_table' ) , $parse ) , FALSE );
	}
}
/* end of imperium.php */