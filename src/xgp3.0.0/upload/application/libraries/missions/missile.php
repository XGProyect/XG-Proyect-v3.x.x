<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) die ( header ( 'location:../../' ) );

class Missile extends Missions
{
	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * method missile_mission
	 * param $fleet_row
	 * return the missile result
	*/
	public function missile_mission ( $fleet_row )
	{
		if ( $fleet_row['fleet_start_time'] <= time() )
		{
			if ( $fleet_row['fleet_mess'] == 0 )
			{
				$attacker_data	= parent::$db->query_fetch ( "SELECT p.`planet_name`, r.`research_weapons_technology`
																FROM " . PLANETS . " AS p
																INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
																WHERE `planet_galaxy` = " . $fleet_row['fleet_start_galaxy'] . " AND
																		`planet_system` = " . $fleet_row['fleet_start_system'] . " AND
																		`planet_planet` = " . $fleet_row['fleet_start_planet'] . " AND
																		`planet_type` = " . $fleet_row['fleet_start_type'] . ";" );

				$target_data	= parent::$db->query_fetch ( "SELECT p.`planet_id`, p.`planet_name`, p.`planet_user_id`, d.*, r.`research_shielding_technology`
																FROM " . PLANETS . " AS p
																INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
																INNER JOIN " . RESEARCH . " AS r ON r.research_user_id = p.planet_user_id
																WHERE `planet_galaxy` = " . $fleet_row['fleet_end_galaxy'] . " AND
																		`planet_system` = " . $fleet_row['fleet_end_system'] . " AND
																		`planet_planet` = " . $fleet_row['fleet_end_planet'] . " AND
																		`planet_type` = " . $fleet_row['fleet_end_type'] . ";" );



				if ( $target_data['defense_anti-ballistic_missile'] >= $fleet_row['fleet_amount'] )
				{
					$message			= $this->_lang['ma_all_destroyed'] . '<br>';
					$amount				= $fleet_row['fleet_amount'];
				}
				else
				{
					$amount				= 0;

					if ( $target_data['defense_anti-ballistic_missile'] > 0 )
					{
						$message		= $target_data['defense_anti-ballistic_missile'] . $this->_lang['ma_some_destroyed'] . " <br>";
					}

					$attack 			= floor ( ( $fleet_row['fleet_amount'] - $target_data['defense_anti-ballistic_missile'] ) * ( $this->_combat_caps[503]['attack'] * ( 1 + ( $attacker_data['research_weapons_technology'] / 10 ) ) ) );
					$attack_order		= $this->set_attack_order ( $fleet_row['fleet_target_obj'] );
					$destroyed_query	= '';

					// PROCESS THE MISSILE ATTACK
					for ( $t = 0 ; $t < count ( $attack_order ) ; $t++ )
					{
						$n	= $attack_order[$t];

						if ( $target_data[$this->_resource[$n]] )
						{
							$defense		= ( ( $this->_pricelist[$n]['metal'] + parent::$_pricelist[$n]['crystal'] ) / 10 ) * ( 1 + ( $target_data['research_shielding_technology'] / 10 ) );

							if ( $attack >= ( $defense * $target_data[$this->_resource[$n]] ) )
							{
								$destroyed	= $target_data[$this->_resource[$n]];
							}
							else
							{
								$destroyed	= floor ( $attack / $defense );
							}

							$attack	-= $destroyed * $defense;

							if ( $destroyed != 0 )
							{
								$message 			.= $this->_lang['tech'][$n] . " (-" . $destroyed . ")<br>";
								$destroyed_query	.= "`" . $this->_resource[$n] . "` = `" . $this->_resource[$n] . "` - ".$destroyed . ",";
							}
						}
					}

					if ( $destroyed_query != '' )
					{
						parent::$db->query ( "UPDATE " . DEFENSES . " SET
												{$destroyed_query}
												`defense_anti-ballistic_missile` = '" . $amount . "'
												WHERE defense_planet_id = ".$target_data['id'] );
					}
				}

				$search				= array ( '%1%' , '%2%' , '%3%' );
				$replace			= array ( $fleet_row['fleet_amount'] , $attacker_data['planet_name'] . ' [' . $fleet_row['fleet_start_galaxy'] . ':' . $fleet_row['fleet_start_system'] . ':' . $fleet_row['fleet_start_planet'] .'] ' , $target_data['name'] . ' [' . $fleet_row['fleet_end_galaxy'] . ':' . $fleet_row['fleet_end_system'] . ':' .  $fleet_row['fleet_end_planet'] .'] ' );
				$message_vorlage	= str_replace ( $search , $replace , $this->_lang['ma_missile_string'] );

				if ( empty ( $message ) or $message == '' )
				{
					$message	= $this->_lang['ma_planet_without_defens'];
				}

				Functions_Lib::send_message ( $target_data['planet_user_id'] , '' , $fleet_row['fleet_end_time'] , 5 , $this->_lang['sys_mess_tower'] , $this->_lang['gl_missile_attack'] , $message_vorlage . $message );

				parent::remove_fleet ( $fleet_row['fleet_id'] );
			}
		}
	}

	/**
	 * method set_attack_order
	 * param $primary_objective
	 * return the list of objectives
	*/
	private function set_attack_order ( $primary_objective )
	{
		switch ( $primary_objective )
		{
			case 0:

				return array ( 401 , 402 , 403 , 404 , 405 , 406 , 407 , 408 , 503 );

			break;

			case 1:

				return array ( 402 , 401 , 403 , 404 , 405 , 406 , 407 , 408 , 503 );

			break;

			case 2:

				return array ( 403 , 401 , 402 , 404 , 405 , 406 , 407 , 408 , 503 );

			break;

			case 3:

				return array ( 404 , 401 , 402 , 403 , 405 , 406 , 407 , 408 , 503 );

			break;

			case 4:

				return array ( 405 , 401 , 402 , 403 , 404 , 406 , 407 , 408 , 503 );

			break;

			case 5:

				return array ( 406 , 401 , 402 , 403 , 404 , 405 , 407 , 408 , 503 );

			break;

			case 6:

				return array ( 407 , 401 , 402 , 403 , 404 , 405 , 406 , 408 , 503 );

			break;

			case 7:

				return array ( 408 , 401 , 402 , 403 , 404 , 405 , 406 , 407 , 503 );

			break;

			case 8:

				return array ( 401 , 402 , 403 , 404 , 405 , 406 , 407 , 408 , 503 );

			break;
		}
	}
}
/* end of missile.php */