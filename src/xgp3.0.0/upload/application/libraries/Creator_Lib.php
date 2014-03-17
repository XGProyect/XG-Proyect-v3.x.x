<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Creator_Lib extends XGPCore
{
	private $_lang;

	/**
	 * __construct()
	 */
	public function __construct ()
	{
		parent::__construct();

		$this->_lang	= parent::$lang;
	}

	/**
	 * method return_size
	 * param $position
	 * param $home_world
	 * return a radomized size for the planet
	 */
	private function return_size ( $position , $home_world = FALSE )
	{
		if ( ! $home_world )
		{
			// THIS DIAMETERS ARE CALCULATED TO RETURN THE CORRECT AMOUNT OF FIELDS, IT SHOULD WORK AS OGAME.
			$min 			= array (  9747 ,  9849 ,  9900 , 11091 , 12166 , 12166 , 11875 , 12962 , 12689 , 12410 , 12084 , 11662 , 10441 , 9000 , 8063 );
			$max 			= array ( 10393 , 10489 , 11705 , 14248 , 14595 , 15067 , 15395 , 15685 , 15428 , 14934 , 14283 , 13077 , 11000 , 9644 , 8603 );

			$diameter		= mt_rand ( $min[$position - 1] , $max[$position - 1] );
			$diameter	   *= PLANETSIZE_MULTIPLER;

			$fields 		= (int)pow ( ( $diameter / 1000 ) , 2 );
		}
		else
		{
			$diameter	= '12800';
			$fields 	= Functions_Lib::read_config ( 'initial_fields' );
		}

		$return['planet_diameter'] 	= $diameter;
		$return['planet_field_max'] = $fields;

		return $return;
	}

	/**
	 * method create_planet
	 * param $Galaxy
	 * param $System
	 * param $Position
	 * param $PlanetOwnerID
	 * param $PlanetName
	 * param $HomeWorld
	 * return creates a planet into the data base.
	 */
	public function create_planet ( $galaxy , $system , $position , $planet_owner_id , $planet_name = '' , $home_world = FALSE )
	{
		$planet_exist	= parent::$db->query_fetch ( "SELECT `planet_id`
						   								FROM " . PLANETS . "
						   								WHERE `planet_galaxy` = '" . $galaxy . "' AND
						   										`planet_system` = '" . $system . "' AND
						   										`planet_planet` = '" . $position . "';" );


		if ( ! $planet_exist )
		{
			$planet 							= $this->return_size ( $position , $home_world );
			$planet['planet_diameter'] 			= ($planet['planet_field_max'] ^ (14 / 1.5)) * 75;
			$planet['metal']	 				= BUILD_METAL;
			$planet['crystal'] 					= BUILD_CRISTAL;
			$planet['deuterium'] 				= BUILD_DEUTERIUM;
			$planet['planet_metal_perhour'] 	= Functions_Lib::read_config ( 'metal_basic_income' );
			$planet['planet_crystal_perhour'] 	= Functions_Lib::read_config ( 'crystal_basic_income' );
			$planet['planet_deuterium_perhour'] = Functions_Lib::read_config ( 'deuterium_basic_income' );
			$planet['galaxy'] 					= $galaxy;
			$planet['system'] 					= $system;
			$planet['planet'] 					= $position;

			if($position == 1 || $position == 2 || $position == 3)
			{
				$PlanetType 					= array('trocken');
				$PlanetClass 					= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07','08','09','10');
				$planet['planet_temp_min'] 		= mt_rand(0,100);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}
			elseif($position == 4 || $position == 5 || $position == 6)
			{
				$PlanetType 					= array('dschjungel');
				$PlanetClass 					= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07','08','09','10');
				$planet['planet_temp_min'] 		= mt_rand(-25,75);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}
			elseif($position == 7 || $position == 8 || $position == 9)
			{
				$PlanetType 					= array('normaltemp');
				$PlanetClass			 		= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07');
				$planet['planet_temp_min'] 		= mt_rand(-50,50);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}
			elseif($position == 10 || $position == 11 || $position == 12)
			{
				$PlanetType 					= array('wasser');
				$PlanetClass 					= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07','08','09');
				$planet['planet_temp_min'] 		= mt_rand(-75,25);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}
			elseif($position == 13 || $position == 14 || $position == 15)
			{
				$PlanetType 					= array('eis');
				$PlanetClass 					= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07','08','09','10');
				$planet['planet_temp_min'] 		= mt_rand(-100,10);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}
			else
			{
				$PlanetType 					= array('dschjungel','gas','normaltemp','trocken','wasser','wuesten','eis');
				$PlanetClass 					= array('planet');
				$PlanetDesign 					= array('01','02','03','04','05','06','07','08','09','10','00');
				$planet['planet_temp_min'] 		= mt_rand(-120,10);
				$planet['planet_temp_max'] 		= $planet['planet_temp_min'] + 40;
			}

			$planet['image'] 				= $PlanetType[mt_rand(0,count($PlanetType) - 1)];
			$planet['image'] 		   	   .= $PlanetClass[mt_rand(0,count($PlanetClass) - 1)];
			$planet['image'] 		   	   .= $PlanetDesign[mt_rand(0,count($PlanetDesign) - 1)];
			$planet['planet_type'] 			= 1;
			$planet['planet_user_id'] 		= $planet_owner_id;
			$planet['planet_last_update'] 	= time();
			$planet['planet_name'] 			= ($planet_name == '') ? $this->_lang['ge_colony'] : $planet_name;

			parent::$db->query ( "INSERT INTO " . PLANETS . " SET
									" . ( ( $home_world == FALSE ) ? "`planet_name` = '{$planet['planet_name']}'," : $this->_lang['ge_home_planet'] ) ."
									`planet_user_id` = '" . $planet['planet_user_id'] . "',
									`planet_galaxy` = '" . $planet['galaxy'] . "',
									`planet_system` = '" . $planet['system'] . "',
									`planet_planet` = '" . $planet['planet'] . "',
									`planet_last_update` = '" . $planet['planet_last_update'] . "',
									`planet_type` = '" . $planet['planet_type'] . "',
									`planet_image` = '" . $planet['image'] . "',
									`planet_diameter` = '" . $planet['planet_diameter'] . "',
									`planet_field_max` = '" . $planet['planet_field_max'] . "',
									`planet_temp_min` = '" . $planet['planet_temp_min'] . "',
									`planet_temp_max` = '" . $planet['planet_temp_max'] . "',
									`planet_metal` = '" . $planet['metal'] . "',
									`planet_metal_perhour` = '" . $planet['planet_metal_perhour'] . "',
									`planet_crystal` = '" . $planet['crystal'] . "',
									`planet_crystal_perhour` = '" . $planet['planet_crystal_perhour'] . "',
									`planet_deuterium` = '" . $planet['deuterium'] . "',
									`planet_deuterium_perhour` = '" . $planet['planet_deuterium_perhour'] . "';" );

			$last_id	= parent::$db->insert_id();

			parent::$db->query ( "INSERT INTO " . BUILDINGS . " SET
									`building_planet_id` = '" . $last_id . "';" );

			parent::$db->query ( "INSERT INTO " . DEFENSES . " SET
									`defense_planet_id` = '" . $last_id . "';" );

			parent::$db->query ( "INSERT INTO " . SHIPS . " SET
									`ship_planet_id` = '" . $last_id . "';" );

			$RetValue = TRUE;
		}
		else
		{
			$RetValue = FALSE;
		}

		return $RetValue;
	}

	/**
	 * method create_moon
	 * param $galaxy
	 * param $system
	 * param $planet
	 * param $owner
	 * param $moon_name
	 * param $chance
	 * param $size
	 * return creates a moon into the data base.
	 */
	public function create_moon ( $galaxy , $system , $planet , $owner , $moon_name = '' , $chance = '' , $size )
	{
		$planet_name            = '';

		$MoonPlanet = parent::$db->query_fetch ( "SELECT pm2.`planet_id`,
														pm2.`planet_name`,
														pm2.`planet_temp_max`,
														pm2.`planet_temp_min`,
														(SELECT pm.`planet_id` AS `id_moon`
															FROM " . PLANETS . " AS pm
															WHERE pm.`planet_galaxy` = '". $galaxy ."' AND
																	pm.`planet_system` = '". $system ."' AND
																	pm.`planet_planet` = '". $planet ."' AND
																	pm.`planet_type` = 3) AS `id_moon`
													FROM " . PLANETS . " AS pm2
													WHERE pm2.`planet_galaxy` = '". $galaxy ."' AND
															pm2.`planet_system` = '". $system ."' AND
															pm2.`planet_planet` = '". $planet ."';" );

		if ( $MoonPlanet['id_moon'] == '' && $MoonPlanet['planet_id'] != 0 )
		{
			$SizeMin        = 2000 + ( $chance * 100 );
			$SizeMax        = 6000 + ( $chance * 200 );
			$planet_name	= $MoonPlanet['planet_name'];
			$maxtemp        = $MoonPlanet['planet_temp_max'] - mt_rand(10, 45);
			$mintemp        = $MoonPlanet['planet_temp_min'] - mt_rand(10, 45);
			$size           = $chance == '' ? $size : mt_rand ( $SizeMin , $SizeMax );

			parent::$db->query ( "INSERT INTO " . PLANETS . " SET
									`planet_name` = '". ( ($moon_name == '') ? $this->_lang['fcm_moon'] : $moon_name ) ."',
									`planet_user_id` = '". $owner ."',
									`planet_galaxy` = '". $galaxy ."',
									`planet_system` = '". $system ."',
									`planet_planet` = '". $planet ."',
									`planet_last_update` = '". time() ."',
									`planet_type` = '3',
									`planet_image` = 'mond',
									`planet_diameter` = '". $size ."',
									`planet_field_max` = '1',
									`planet_temp_min` = '". $mintemp ."',
									`planet_temp_max` = '". $maxtemp ."';" );

			$last_id	= parent::$db->insert_id();

			parent::$db->query ( "INSERT INTO " . BUILDINGS . " SET
									`building_planet_id` = '" . $last_id . "';" );

			parent::$db->query ( "INSERT INTO " . DEFENSES . " SET
									`defense_planet_id` = '" . $last_id . "';" );

			parent::$db->query ( "INSERT INTO " . SHIPS . " SET
									`ship_planet_id` = '" . $last_id . "';" );
		}

		return $planet_name;
	}
}
/* end of Creator_Lib.php */