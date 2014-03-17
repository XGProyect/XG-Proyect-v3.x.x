<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Galaxy_Lib extends XGPCore
{
	const PLANET_TYPE = 1;
	const DEBRIS_TYPE = 2;
	const MOON_TYPE	= 3;

	private $_lang;
	private $_current_user;
	private $_current_planet;
	private $_row_data;
	private $_galaxy;
	private $_system;
	private $_planet;
	private $_resource;
	private $_pricelist;
	private $_formula;
	private $_noob;

	/**
	 * __construct()
	 */
	public function __construct ( $user , $planet , $galaxy , $system )
	{
		parent::__construct();

		$this->_lang			= parent::$lang;
		$this->_current_user	= $user;
		$this->_current_planet	= $planet;
		$this->_galaxy			= $galaxy;
		$this->_system			= $system;
		$this->_resource		= parent::$objects->get_objects();
		$this->_pricelist		= parent::$objects->get_price();
		$this->_formula			= Functions_Lib::load_library ( 'Formula_Lib' );
		$this->_noob			= Functions_Lib::load_library ( 'NoobsProtection_Lib' );
	}

	######################################
	#
	# main methods
	#
	######################################

	/**
	 * method build_row
	 * param $row_data
	 * param $planet
	 * return true if the user was found, false if not
	 */
	public function build_row ( $row_data , $planet )
	{
		// SOME DATA THAT WE ARE GOING TO REQUIRE FOR EACH COLUMN
		$this->_row_data		= $row_data;
		$this->_planet 			= $planet;

		// BLOCK TEMPLATES
		$block['planet']		= parent::$page->get_template ( 'galaxy/galaxy_planet_block' );
		$block['moon']			= parent::$page->get_template ( 'galaxy/galaxy_moon_block' );
		$block['debris']		= parent::$page->get_template ( 'galaxy/galaxy_debris_block' );
		$block['username']		= parent::$page->get_template ( 'galaxy/galaxy_username_block' );
		$block['alliance']		= parent::$page->get_template ( 'galaxy/galaxy_alliance_block' );

		// PRE CREATED BLOCK TO PREVENT REDUNDANCY
		$debris_block			= $this->debris_block();

		// POSITION COLUMN, VALUES BY DEFAULT
		$row['pos']  	   		= $planet;
		$row['planet'] 			= '';
		$row['planetname']		= $this->planet_name_block();
		$row['moon'] 			= '';
		$row['debris']			= $debris_block != '' ? parent::$page->parse_template ( $block['debris'] , $debris_block ) : '';
		$row['username'] 		= '';
		$row['alliance'] 		= '';
		$row['actions'] 		= '';

		// ALL OTHER COLUMNS
		if ( $row_data['planet_destroyed'] == 0 ) // IF THE PLANET ON THIS POSITION IS ACTIVE
		{
			// PRE CREATED BLOCK TO PREVENT REDUNDANCY
			$moon_block			= $this->moon_block();

			// PARSE DATA
			$row['planet'] 		= parent::$page->parse_template ( $block['planet'] , $this->planet_block() );
			$row['moon'] 		= $moon_block != '' ? parent::$page->parse_template ( $block['moon'] , $moon_block ) : '';
			$row['username'] 	= parent::$page->parse_template ( $block['username'] , $this->username_block() );
			$row['alliance'] 	= parent::$page->parse_template ( $block['alliance'] , $this->ally_block() );
			$row['actions'] 	= $this->actions_block();
		}

		// RETURN DATA
		return $row;
	}

	######################################
	#
	# blocks methods
	#
	######################################

	/**
	 * method planet_block
	 * param
	 * return planet link and information for the current planet
	 */
	private function planet_block()
	{
		$action['spy']				= '';
		$action['phalanx'] 			= '';
		$action['attack']			= '';
		$action['hold_position']	= '';
		$action['deploy']			= '';
		$action['transport']		= '';
		$action['missile']			= '';

		// GLOBAL
		$action['transport'] 		= $this->transport_link ( self::PLANET_TYPE );

		// ONLY IF IS NOT THE CURRENT USER
		if ( $this->_row_data['user_id'] != $this->_current_user['user_id'] )
		{
			$action['attack'] 		= $this->attack_link ( self::PLANET_TYPE );
			$action['spy']			= $this->spy_link ( self::PLANET_TYPE );

			// HOLD POSITION ONLY IF IS A FRIEND
			if ( $this->is_friend ( $this->_row_data['buddys'] , $this->_row_data['user_id'] ) )
			{
				$action['hold_position'] = $this->hold_position_link ( self::PLANET_TYPE );
			}
		}

		// ONLY IF IS THE CURRENT USER
		if ( $this->_row_data['user_id'] == $this->_current_user['user_id'] )
		{
			$action['deploy'] 		=	$this->deploy_link ( self::PLANET_TYPE );
		}

		// MISSILE
		if ( $this->_current_user['setting_galaxy_missile'] == '1' && $this->is_missile_active() )
		{
			$action['missile'] 		= $this->missile_link ( self::PLANET_TYPE );
		}

		// PHALANX
		if ( $this->is_phalanx_active() )
		{
			$action['phalanx']		= $this->phalanx_link ( self::PLANET_TYPE );
		}

		// PARSE THE DATA
		$parse				= $this->_lang;
		$parse['dpath']		= DPATH;
		$parse['name']		= $this->_row_data['planet_name'];
		$parse['galaxy']	= $this->_galaxy;
		$parse['system']	= $this->_system;
		$parse['planet']	= $this->_planet;
		$parse['image']		= $this->_row_data['planet_image'];
		$parse['links']		= '';

		// LOOP THRU ACTIONS
		foreach ( $action as $to_parse )
		{
			if ( $to_parse != '' ) // SKIP EMPTY ACTIONS
			{
				$parse['links'] .= $to_parse . '<br>';
			}
		}

		return $parse;
	}

	/**
	 * method planet_name_block
	 * param
	 * return ally link and information for the current planet
	 */
	private function planet_name_block()
	{
		$phalanx_link	= stripslashes ( $this->_row_data['planet_name'] );

		if ( $this->_row_data['planet_destroyed'] == 0 )
		{
			if ( $this->is_phalanx_active() )
			{
				$attributes		= "onclick=fenster('game.php?page=phalanx&galaxy=" . $this->_galaxy . "&amp;system=" . $this->_system . "&amp;planet=" . $this->_planet . "&amp;planettype=" . self::PLANET_TYPE . "')";
				$phalanx_link	= Functions_Lib::set_url ( '' , 'Phalanx' , $this->_row_data['planet_name'] , $attributes );
			}

			$planetname	=  $phalanx_link;

			if ( $this->_row_data['planet_last_update']  > ( time() - 59 * 60 ) && $this->_row_data['user_id'] != $this->_current_user['user_id'] )
			{
				if ( $this->_row_data['planet_last_update']  > ( time() - 10 * 60 ) && $this->_row_data['user_id'] != $this->_current_user['user_id'] )
				{
					$planetname	.= "(*)";
				}
				else
				{
					$planetname	.= " (" . Format_Lib::pretty_time_hour ( time() - $this->_row_data['planet_last_update'] ) . ")";
				}
			}
		}
		else
		{
			$planetname	= $this->_lang['gl_planet_destroyed'];
		}

		return $planetname;
	}

	/**
	 * method moon_block
	 * param
	 * return moon link and information for the current planet
	 */
	private function moon_block()
	{
		$action['spy']					= '';
		$action['transport']			= '';
		$action['deploy']				= '';
		$action['attack']				= '';
		$action['hold_position']		= '';
		$action['destroy']				= '';

		// GLOBAL
		$action['transport'] 			= $this->transport_link ( self::MOON_TYPE );

		// ONLY IF IS NOT THE CURRENT USER
		if ( $this->_row_data['user_id'] != $this->_current_user['user_id'] )
		{
			$action['attack'] 			= $this->attack_link ( self::MOON_TYPE );
			$action['spy']				= $this->spy_link ( self::MOON_TYPE );
			$action['hold_position'] 	= $this->hold_position_link ( self::MOON_TYPE );

			// DESTROY
			if ( $this->_current_planet[$this->_resource[214]] > 0 )
			{
				$action['destroy'] 		= $this->destroy_link ( self::MOON_TYPE );
			}
		}

		// ONLY IF IS THE CURRENT USER
		if ( $this->_row_data['user_id'] == $this->_current_user['user_id'] )
		{
			$action['deploy'] =	$this->deploy_link ( self::MOON_TYPE );
		}

		// CHECK MOON STATUS AND COMPLETE DATA IF REQUIRED
		if ( $this->_row_data['destruyed_moon'] == 0 && $this->_row_data['id_luna'] != 0 )
		{
			$parse						= $this->_lang;
			$parse['dpath']				= DPATH;
			$parse['name_moon']			= $this->_row_data['name_moon'];
			$parse['galaxy']			= $this->_galaxy;
			$parse['system']			= $this->_system;
			$parse['planet']			= $this->_planet;
			$parse['planet_diameter']	= Format_Lib::pretty_number ( $this->_row_data['planet_diameter'] );
			$parse['temperature']		= Format_Lib::pretty_number ( $this->_row_data['planet_temp_min'] );
			$parse['links']				= '';

			// LOOP THRU ACTIONS
			foreach ( $action as $to_parse )
			{
				if ( $to_parse != '' ) // SKIP EMPTY ACTIONS
				{
					$parse['links'] .= $to_parse . '<br>';
				}
			}

			return $parse;

		}

		return '';
	}

	/**
	 * method debris_block
	 * param
	 * return debris link and information for the current planet
	 */
	private function debris_block()
	{
		if ( $this->_row_data['metal'] + $this->_row_data['crystal'] >= DEBRIS_MIN_VISIBLE_SIZE )
		{
			$recyclers_needed = ceil ( ( $this->_row_data['metal'] + $this->_row_data['crystal'] ) / $this->_pricelist[209]['capacity'] );

			if ( $recyclers_needed < $this->_current_planet['ship_recycler'] )
			{
				$recyclers_sended = $recyclers_needed;
			}
			elseif ( $recyclers_needed >= $this->_current_planet['ship_recycler'] )
			{
				$recyclers_sended = $this->_current_planet['ship_recycler'];
			}

			$parse							=	$this->_lang;
			$parse['dpath']					=	DPATH;
			$parse['galaxy']				=	$this->_galaxy;
			$parse['system']				=	$this->_system;
			$parse['planet']				=	$this->_planet;
			$parse['planettype']			=	self::DEBRIS_TYPE;
			$parse['recsended']				=	$recyclers_sended;
			$parse['planet_debris_metal']	=	Format_Lib::pretty_number ( $this->_row_data['metal'] );
			$parse['planet_debris_crystal']	=	Format_Lib::pretty_number( $this->_row_data['crystal'] );

			return $parse;
		}

		return '';
	}

	/**
	 * method username_block
	 * param
	 * return user link and information for the current planet
	 */
	private function username_block()
	{
		$MyGameLevel				= $this->_current_user['user_statistic_total_points'];
		$HeGameLevel				= $this->_row_data['user_statistic_total_points'];
		$status['vacation']			= '';
		$status['banned']			= '';
		$status['inactive']			= '';
		$status['noob_protection']	= '';

		if ( $this->_row_data['setting_vacations_status'] )
		{
			$status['vacation']	= '<span class="vacation">' . $this->_lang['gl_v'] . '</span>';
		}

		if ( $this->_row_data['user_banned'] )
		{
			$status['banned']	= '<span class="banned">' . Functions_Lib::set_url ( 'game.php?page=banned' , '' , $this->_lang['gl_b'] ) . '</span>';
		}

		if ( $this->_row_data['user_onlinetime'] < (time()-60 * 60 * 24 * 7 ) && $this->_row_data['user_onlinetime'] > ( time()-60 * 60 * 24 * 28 ) )
		{
			$status['inactive']	 = '<span class="inactive">' . $this->_lang['gl_i'] . '</span>';
		}

		if ( $this->_row_data['user_onlinetime'] < ( time() - 60 * 60 * 24 * 28 ) )
		{
			$status['inactive']	.= '<span class="longinactive">' . $this->_lang['gl_I'] . '</span>';
		}

		if ( $this->_noob->is_weak ( $MyGameLevel , $HeGameLevel ) )
		{
			$status['noob_protection'] = '<span class="noob">' . $this->_lang['gl_w'] . '</span>';
		}

		if ( $this->_noob->is_strong ( $MyGameLevel , $HeGameLevel ) )
		{
			$status['noob_protection'] = '<span class="strong">' . $this->_lang['gl_s'] . '</span>';
		}

		// POP UP BLOCK DATA
		$parse					=	$this->_lang;
		$parse['username']		=	$this->_row_data['user_name'];
		$parse['current_rank']	=	$this->_row_data['user_statistic_total_rank'];
		$parse['start']			=	( floor ( $this->_row_data['user_statistic_total_rank'] / 100 ) * 100) + 1;

		if ($this->_row_data['user_id'] != $this->_current_user['user_id'])
		{
			$parse['actions'] 	= "<td>";
			$parse['actions']  .= str_replace ( '"' , '' , Functions_Lib::set_url ( 'game.php?page=messages&mode=write&id=' . $this->_row_data['user_id'] , '' , $this->_lang['write_message'] ) );
			$parse['actions']  .= "</td></tr><tr><td>";
			$parse['actions']  .= str_replace ( '"' , '' , Functions_Lib::set_url ( "&quot;#&quot; onClick=&quot;f&#40;\'game.php?page=buddy&mode=2&u=" . $this->_row_data['user_id'] . "\', \'" . $this->_lang['gl_buddy_request'] . "\'&#41;&quot;" , '' , $this->_lang['gl_buddy_request'] ) );
			$parse['actions']  .= "</td></tr><tr>";
		}

		// USER STATUS AND NAME
		$parse['status'] 	    = $this->_row_data['user_name'];

		foreach ( $status as $to_parse )
		{
			if ( $to_parse != '' )
			{
				$parse['status']   .= '<font color="white">(</font>' . $to_parse . '<font color="white">)</font>';
			}
		}

		return $parse;
	}

	/**
	 * method ally_block
	 * param
	 * return ally link and information for the current planet
	 */
	private function ally_block()
	{
		$parse	= '';
		$add 	= '';

		if ( $this->_row_data['user_ally_id'] != 0 )
		{
			if ( $this->_row_data['ally_members'] > 1 )
			{
				$add = $this->_lang['gl_member_add'];
			}

			$parse						=	$this->_lang;
			$parse['alliance_name']		=	str_replace ( "'" , "\'" , htmlspecialchars ( $this->_row_data['alliance_name'] , ENT_COMPAT ) );
			$parse['ally_members']		=	$this->_row_data['ally_members'];
			$parse['add']				=	$add;
			$parse['ally_id']			=	$this->_row_data['user_ally_id'];

			if ( $this->_row_data['alliance_web'] != '' )
			{
				$web_url			= Functions_Lib::set_url ( Functions_Lib::prep_url ( $this->_row_data['alliance_web'] ) , '' , $this->_lang['gl_alliance_web_page'] , 'target="_new"' );
				$parse['web'] 	   	= '</tr><tr>';
				$parse['web']      .= '<td>' . str_replace ( '"' , '' , $web_url ) . '</td>';
			}

			if ( $this->_current_user['user_ally_id'] == $this->_row_data['user_ally_id'] )
			{
				$parse['tag']		= '<span class="allymember">' . $this->_row_data['alliance_tag'] . '</span>';
			}
			else
			{
				$parse['tag']  		= $this->_row_data['alliance_tag'];
			}
		}

		return $parse;
	}

	/**
	 * method actions_block
	 * param
	 * return all galaxy possible actions for the current planet
	 */
	private function actions_block()
	{
		$links				= '';

		if ( $this->_row_data['user_id'] != $this->_current_user['user_id'] )
		{
			if ( $this->_current_user['setting_galaxy_espionage'] == '1' )
			{
				$image 			= Functions_Lib::set_image ( DPATH . 'img/e.gif' , $this->_lang['gl_spy'] );
				$attributes		= "onclick=\"javascript:doit(6, " . $this->_galaxy . ", " . $this->_system . ", " . $this->_planet . ", 1, " . $this->_current_user['setting_probes_amount'] . ");\"";
				$links 		   .= Functions_Lib::set_url ( '' , '' , $image , $attributes ) . '&nbsp;';
			}

			if ( $this->_current_user['setting_galaxy_write'] == '1' )
			{
				$image 			= Functions_Lib::set_image ( DPATH . 'img/m.gif' , $this->_lang['write_message'] );
				$url			= 'game.php?page=messages&mode=write&id=' . $this->_row_data['user_id'] . '>';
				$links 		   .= Functions_Lib::set_url ( $url , '' , $image ) . '&nbsp;';
			}

			if ( $this->_current_user['setting_galaxy_buddy'] == '1' )
			{
				$image 			= Functions_Lib::set_image ( DPATH . 'img/b.gif' , $this->_lang['gl_buddy_request'] );
				$attributes		= "onClick=\"f('game.php?page=buddy&mode=2&u=".$this->_row_data['user_id']."', '".$this->_lang['gl_buddy_request']."')\"";
				$links 		   .= Functions_Lib::set_url ( '' , '' , $image , $attributes ) . '&nbsp;';
			}

			if ( $this->_current_user['setting_galaxy_missile'] == '1' && $this->is_missile_active() )
			{
				$image 			= Functions_Lib::set_image ( DPATH . 'img/r.gif' , $this->_lang['gl_missile_attack'] );
				$url			= 'game.php?page=galaxy&mode=2&galaxy=' . $this->_galaxy . '&system=' . $this->_system . '&planet=' . $this->_planet . '&current=' . $this->_current_user['user_current_planet'];
				$links 		   .= Functions_Lib::set_url ( $url , '' , $image ) . '&nbsp;';
			}
		}

		return $links;
	}

	######################################
	#
	# missions methods
	#
	######################################

	/**
	 * method attack_link
	 * param $planet_type
	 * return attack link
	 */
	private function attack_link ( $planet_type )
	{
		$url = "game.php?page=fleet1&galaxy=" . $this->_galaxy . "&amp;system=" . $this->_system . "&amp;planet=" . $this->_planet . "&amp;planettype=" . $planet_type . "&amp;target_mission=1";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['type_mission'][1] ) );
	}

	/**
	 * method transport_link
	 * param $planet_type
	 * return transport link
	 */
	private function transport_link ( $planet_type )
	{
		$url = "game.php?page=fleet1&galaxy=" . $this->_galaxy . "&system=" . $this->_system . "&planet=" . $this->_planet . "&planettype=" . $planet_type . "&target_mission=3";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['type_mission'][3] ) );
	}

	/**
	 * method deploy_link
	 * param $planet_type
	 * return deploy link
	 */
	private function deploy_link ( $planet_type )
	{
		$url = "game.php?page=fleet1&galaxy=" . $this->_galaxy . "&system=" . $this->_system . "&planet=" . $this->_planet . "&planettype=" . $planet_type . "&target_mission=4";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['type_mission'][4] ) );
	}

	/**
	 * method hold_position_link
	 * param $planet_type
	 * return hold position link
	 */
	private function hold_position_link ( $planet_type )
	{
		$url = "game.php?page=fleet1&galaxy=" . $this->_galaxy . "&system=" . $this->_system . "&planet=" . $this->_planet . "&planettype=" . $planet_type . "&target_mission=5";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['type_mission'][5] ) );
	}

	/**
	 * method spy_link
	 * param $planet_type
	 * return spy link
	 */
	private function spy_link ( $planet_type )
	{
		$attributes	= "onclick=&#039javascript:doit(6, " . $this->_galaxy . ", " . $this->_system . ", " . $this->_planet . ", " . $planet_type . ", " . $this->_current_user['setting_probes_amount'] . ");&#039";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( '' , '' , $this->_lang['type_mission'][6] , $attributes ) );
	}

	/**
	 * method destroy_link
	 * param $planet_type
	 * return destroy link
	 */
	private function destroy_link ( $planet_type )
	{
		$url = "game.php?page=fleet1&galaxy=" . $this->_galaxy . "&system=" . $this->_system . "&planet=" . $this->_planet . "&planettype=" . $planet_type . "&target_mission=9";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['type_mission'][9] ) );
	}

	/**
	 * method missile_link
	 * param $planet_type
	 * return missile link
	 */
	private function missile_link ( $planet_type )
	{
		$url = "game.php?page=galaxy&mode=2&galaxy=" . $this->_galaxy . "&system=" . $this->_system . "&planet=" . $this->_planet . "&current=" . $this->_current_user['user_current_planet'];
		return str_replace ( '"' , '' , Functions_Lib::set_url ( $url , '' , $this->_lang['gl_missile_attack'] ) );
	}

	/**
	 * method phalanx_link
	 * param $planet_type
	 * return phalanx link
	 */
	private function phalanx_link ( $planet_type )
	{
		$attributes	= "onclick=fenster(&#039;game.php?page=phalanx&galaxy=" . $this->_galaxy . "&amp;system=" . $this->_system . "&amp;planet=" . $this->_planet . "&amp;planettype=" . $planet_type . "&#039;)";
		return str_replace ( '"' , '' , Functions_Lib::set_url ( '' , '' , $this->_lang['gl_phalanx'] , $attributes ) );
	}

	######################################
	#
	# other methods
	#
	######################################

	/**
	 * method is_friend
	 * param $friends_array
	 * param $current_user_id
	 * return true if the user was found, false if not
	 */
	private function is_friend ( $friends_array , $current_user_id )
	{
		if ( $current_user_id ==  $this->_current_user['user_id'] )
		{
			return FALSE;
		}

		$friends	= explode ( ',' , $friends_array );

		return ( in_array ( $current_user_id , $friends ) );
	}

	/**
	 * method is_missile_active
	 * param
	 * return true if the missiles can be launched, false if not
	 */
	private function is_missile_active ()
	{
		if ( ( $this->_current_planet['defense_interplanetary_missile'] != 0 ) && ( $this->_row_data['user_id'] != $this->_current_user['user_id'] ) && ( $this->_row_data['planet_galaxy'] == $this->_current_planet['planet_galaxy'] ) )
		{
			return $this->is_in_range ( $this->_formula->missile_range ( $this->_current_user['research_impulse_drive'] ) );
		}
	}

	/**
	 * method is_phalanx_active
	 * param
	 * return true if the phalanx can be used, false if not
	 */
	private function is_phalanx_active ()
	{
		if ( ( $this->_current_planet['building_phalanx'] != 0 ) && ( $this->_row_data['user_id'] != $this->_current_user['user_id'] ) && ( $this->_row_data['planet_galaxy'] == $this->_current_planet['planet_galaxy'] ) )
		{
			return $this->is_in_range ( $this->_formula->phalanx_range ( $this->_current_planet['building_phalanx'] ) );
		}
	}

	/**
	 * method is_in_range
	 * param $range
	 * return true if in range, false if not
	 */
	private function is_in_range ( $range )
	{
		$min_system	= $this->_current_planet['planet_system'] - $range;
		$max_system = $this->_current_planet['planet_system'] + $range;

		$min_system = ( $min_system < 1 ) ? 1 : $min_system;
		$max_system = ( $max_system > MAX_SYSTEM_IN_GALAXY ) ? MAX_SYSTEM_IN_GALAXY : $max_system;

		return ( ( $this->_system <= $max_system ) && ( $this->_system >= $min_system ) );
	}
}
/* end of Galaxy_Lib.php */