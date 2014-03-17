<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Statistics extends XGPCore
{
	const MODULE_ID	= 16;

	private $_lang;
	private $_current_user;
	private $_current_planet;

	/**
	 * __construct()
	 */
	public function __construct ()
	{
		parent::__construct();

		// check if session is active
		parent::$users->check_session();

		// Check module access
		Functions_Lib::module_message ( Functions_Lib::is_module_accesible ( self::MODULE_ID ) );

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();
		$this->_current_planet	= parent::$users->get_planet_data();

		$this->build_page();
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
	 * return the statistics page
	 */
	private function build_page()
	{
		$parse	= $this->_lang;
		$who   	= ( isset ( $_POST['who'] ) ) ? $_POST['who'] : ( ( isset ( $_GET['who'] ) ) ? $_GET['who'] : 1 );
		$type  	= ( isset ( $_POST['type'] ) ) ? $_POST['type'] : ( ( isset ( $_GET['type'] ) ) ? $_GET['type'] : 1 );
		$range 	= ( isset ( $_POST['range'] ) ) ? $_POST['range'] : ( ( isset ( $_GET['range'] ) ) ? $_GET['range'] : 1 );

		$parse['who']    = "<option value=\"1\"". (($who == "1") ? " SELECTED" : "") .">".$this->_lang['st_player']."</option>";
		$parse['who']   .= "<option value=\"2\"". (($who == "2") ? " SELECTED" : "") .">".$this->_lang['st_alliance']."</option>";

		$parse['type']   = "<option value=\"1\"". (($type == "1") ? " SELECTED" : "") .">".$this->_lang['st_points']."</option>";
		$parse['type']  .= "<option value=\"2\"". (($type == "2") ? " SELECTED" : "") .">".$this->_lang['st_fleets']."</option>";
		$parse['type']  .= "<option value=\"3\"". (($type == "3") ? " SELECTED" : "") .">".$this->_lang['st_researh']."</option>";
		$parse['type']  .= "<option value=\"4\"". (($type == "4") ? " SELECTED" : "") .">".$this->_lang['st_buildings']."</option>";
		$parse['type']  .= "<option value=\"5\"". (($type == "5") ? " SELECTED" : "") .">".$this->_lang['st_defenses']."</option>";

		$data		= $this->ranking_type ( $type );
		$Order   	= $data['order'];
		$Points  	= $data['points'];
		$Rank    	= $data['rank'];
		$OldRank 	= $data['oldrank'];

		if ( $who == 2 )
		{
			$MaxAllys = parent::$db->query_fetch ( "SELECT COUNT(`alliance_id`) AS `count`
														FROM " . ALLIANCE . ";" );

			$parse['range']			= $this->build_range_list ( $MaxAllys['count'] , $range );
			$parse['stat_header']	= parent::$page->parse_template ( parent::$page->get_template ( 'stat/stat_alliancetable_header' ) , $parse );
			$start 					= floor($range / 100 % 100) * 100;
			$query					= parent::$db->query ( 'SELECT s.*,
																	a.alliance_id,
																	a.alliance_tag,
																	a.alliance_name,
																	a.alliance_request_notallow,
																	(SELECT COUNT(user_id) AS `ally_members` FROM `' . USERS . '` WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
																FROM ' . ALLIANCE_STATISTICS . ' AS s
																INNER JOIN  ' . ALLIANCE . ' AS a ON a.alliance_id = s.alliance_statistic_alliance_id
																ORDER BY `alliance_statistic_' . $Order . '` DESC, `alliance_statistic_total_rank` ASC
																LIMIT ' . $start . ',100;' );

			$start++;

			$parse['stat_date']   	= date ( Functions_Lib::read_config ( 'date_format_extended' ) , Functions_Lib::read_config ( 'stat_last_update' ) );
			$parse['stat_values'] 	= "";
			$StatAllianceTableTPL	= parent::$page->get_template ( 'stat/stat_alliancetable' );

			while ( $StatRow = parent::$db->fetch_assoc ( $query ) )
			{
				$parse['ally_rank']       		= $start;
				$ranking                  		= $StatRow['alliance_statistic_' . $OldRank] - $StatRow['alliance_statistic_' . $Rank];
				$parse['ally_rankplus']			= $this->rank_difference ( $ranking );
				$parse['ally_id']        	  	= $StatRow['alliance_id'];
				$parse['alliance_name']       	= $StatRow['alliance_name'];
				$parse['ally_members']    	  	= $StatRow['ally_members'];
				$parse['ally_action']		  	= $StatRow['alliance_request_notallow'] == 0 ? '<a href="game.php?page=alliance&mode=apply&allyid=' . $StatRow['alliance_id'] . '"><img src="' . DPATH . 'img/m.gif" border="0" title="' . $this->_lang['st_ally_request'] . '" /></a>' : '';
				$parse['ally_points']     	  	= Format_Lib::pretty_number ( $StatRow['alliance_statistic_' . $Order] );
				$parse['ally_members_points']	= Format_Lib::pretty_number ( floor ( $StatRow['alliance_statistic_' . $Order] / $StatRow['ally_members'] ) );
				$parse['stat_values']    	   .= parent::$page->parse_template ( $StatAllianceTableTPL , $parse );
				$start++;
			}
		}
		else
		{
			$parse['range']			= $this->build_range_list ( $this->_current_planet['stats_users'] , $range );
			$parse['stat_header']	= parent::$page->parse_template ( parent::$page->get_template ( 'stat/stat_playertable_header' ) , $parse );

			$start 	= floor ( $range / 100 % 100 ) * 100;
			$query	= parent::$db->query ( 'SELECT s.*, u.user_id, u.user_name, u.user_ally_id, a.alliance_name
												FROM ' . USERS_STATISTICS . ' as s
												INNER JOIN ' . USERS . ' as u ON u.user_id = s.user_statistic_user_id
												LEFT JOIN ' . ALLIANCE . ' AS a ON a.alliance_id = u.user_ally_id
												ORDER BY `user_statistic_'. $Order .'` DESC, `user_statistic_total_rank` ASC
												LIMIT '. $start .',100;' );
			$start++;
			$parse['stat_date']   	= date ( Functions_Lib::read_config ( 'date_format_extended' ) , Functions_Lib::read_config ( 'stat_last_update' ) );
			$parse['stat_values'] 	= "";
			$previusId 				= 0;
			$StatPlayerTableTPL		= parent::$page->get_template ( 'stat/stat_playertable' );

			while ( $StatRow = parent::$db->fetch_assoc ( $query ) )
			{
				$parse['player_rank']		= $start;
				$ranking                  	= $StatRow['user_statistic_' . $OldRank] - $StatRow['user_statistic_' . $Rank];

				if ( $StatRow['user_id'] == $this->_current_user['user_id'] )
				{
					$parse['player_name']     = "<font color=\"lime\">".$StatRow['user_name']."</font>";
				}
				else
				{
					$parse['player_name']     = $StatRow['user_name'];
				}

				if ( $StatRow['user_id'] != $this->_current_user['user_id'] )
				{
					$parse['player_mes']      = '<a href="game.php?page=messages&mode=write&id=' . $StatRow['user_id'] . '"><img src="' . DPATH . 'img/m.gif" border="0" title="' . $this->_lang['write_message'] . '" /></a>';
				}
				else
				{
					$parse['player_mes']      = "";
				}

				if ( $StatRow['alliance_name'] != '' )
				{
					if ( $StatRow['alliance_name'] == $this->_current_user['alliance_name'] )
					{
						$parse['player_alliance'] = '<a href="game.php?page=alliance&mode=ainfo&allyid='.$StatRow['user_ally_id'].'"><font color="#33CCFF">['.$StatRow['alliance_name'].']</font></a>';
					}
					else
					{
						$parse['player_alliance'] = '<a href="game.php?page=alliance&mode=ainfo&allyid='.$StatRow['user_ally_id'].'">['.$StatRow['alliance_name'].']</a>';
					}
				}
				else
				{
					$parse['player_alliance'] = '';
				}

				$parse['player_rankplus']	= $this->rank_difference ( $ranking );
				$parse['player_points']   	= Format_Lib::pretty_number ( $StatRow['user_statistic_' . $Order] );
				$parse['stat_values']      .= parent::$page->parse_template ( $StatPlayerTableTPL , $parse );
				$start++;
			}
		}

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'stat/stat_body' ) , $parse ) );
	}

	/**
	 * method rank_difference
	 * param $ranking
	 * return return the rank difference between update and update and returns it formated
	 */
	private function rank_difference ( $ranking )
	{
		if ( $ranking == 0 )
		{
			return '<font color="#87CEEB">*</font>';
		}

		if ( $ranking < 0 )
		{
			return '<font color="red">' . $ranking . '</font>';
		}

		if ( $ranking > 0 )
		{
			return '<font color="green">+' . $ranking . '</font>';
		}
	}

	/**
	 * method build_range_list
	 * param $count
	 * param $range
	 * return the list of range values
	 */
	private function build_range_list ( $count , $range )
	{
		$range_list	= '';
		$last_page	= 0;

		// SET LAST PAGE
		if ( $count > 100 )
		{
			$last_page	= floor ( $count / 100 );
		}

		// LOOP TO BUILD THE VALUES LIST
		for ( $page = 0 ; $page <= $last_page ; $page++ )
		{
			$page_value		= $page * 100 + 1;
			$page_range		= $page_value + 99;
			$range_list    .= "<option value=\"" . $page_value . "\"" . ( ( $range >= $page_value && $range <= $page_range ) ? " SELECTED" : "" ) . ">" . $page_value . "-" . $page_range . "</option>";
		}

		return $range_list; // RETURN THE LIST
	}

	/**
	 * method ranking_type
	 * param $type
	 * return the configurations or values for the current statistics type
	 */
	private function ranking_type ( $type )
	{
		// SWITCH TYPE
		switch ( $type )
		{
			case 1: // TOTAL POINTS
			default:
				$return['order']	= "total_points";
				$return['points']  	= "total_points";
				$return['rank']    	= "total_rank";
				$return['oldrank'] 	= "total_old_rank";
			break;

			case 2: // SHIPS
				$return['order']	= "ships_points";
				$return['points']  	= "ships_points";
				$return['rank']    	= "ships_rank";
				$return['oldrank'] 	= "ships_old_rank";
			break;

			case 3: // TECHNOLOGY
				$return['order']	= "technology_points";
				$return['points']  	= "technology_points";
				$return['rank']    	= "technology_rank";
				$return['oldrank'] 	= "technology_old_rank";
			break;

			case 4: // BUILDINGS
				$return['order']	= "buildings_points";
				$return['points']  	= "buildings_points";
				$return['rank']    	= "buildings_rank";
				$return['oldrank'] 	= "buildings_old_rank";
			break;

			case 5: // DEFENSE
				$return['order']	= "defenses_points";
				$return['points']  	= "defenses_points";
				$return['rank']    	= "defenses_rank";
				$return['oldrank'] 	= "defenses_old_rank";
			break;
		}

		return $return;
	}
}
/* end of statistics.php */