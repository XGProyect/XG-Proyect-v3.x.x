<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Search extends XGPCore
{
	const MODULE_ID	= 17;

	private $_current_user;
	private $_lang;

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
	 * return main method, loads everything
	 */
	private function build_page()
	{
		$parse 			= $this->_lang;
		$type 			= isset ( $_POST['type'] ) ? $_POST['type'] : '';
		$searchtext 	= parent::$db->escape_value ( isset ( $_POST['searchtext'] ) ? $_POST['searchtext'] : '' );
		$search_results	= '';

		if ( $_POST )
		{
			switch ( $type )
			{
				case 'playername':
				default:
					$table 	= parent::$page->get_template ('search/search_user_table');
					$row 	= parent::$page->get_template ('search/search_user_row');
					$search = parent::$db->query ( "SELECT u.user_id AS user_id, u.user_name, p.planet_name, p.planet_galaxy, p.planet_system, p.planet_planet, s.user_statistic_total_rank AS rank, a.alliance_id, a.alliance_name
														FROM " . USERS . " AS u
														INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = u.user_id
														INNER JOIN " . PLANETS . " AS p ON p.`planet_id` = u.user_home_planet_id
														LEFT JOIN " . ALLIANCE . " AS a ON a.alliance_id = u.user_ally_id
														WHERE u.user_name LIKE '%".$searchtext."%' LIMIT 25;" );
				break;
				case 'planetname':
					$table 	= parent::$page->get_template ('search/search_user_table');
					$row 	= parent::$page->get_template ('search/search_user_row');
					$search = parent::$db->query ( "SELECT u.user_id AS user_id, u.user_name, p.planet_name, p.planet_galaxy, p.planet_system, p.planet_planet, s.user_statistic_total_rank AS rank, a.alliance_id, a.alliance_name
														FROM " . USERS . " AS u
														INNER JOIN " . USERS_STATISTICS . " AS s ON s.user_statistic_user_id = u.user_id
														INNER JOIN " . PLANETS . " AS p ON p.`planet_id` = u.user_home_planet_id
														LEFT JOIN " . ALLIANCE . " AS a ON a.alliance_id = u.user_ally_id
														WHERE p.planet_name LIKE '%".$searchtext."%' LIMIT 25;" );

				break;
				case 'allytag':
					$table 	= parent::$page->get_template ('search/search_ally_table');
					$row 	= parent::$page->get_template ('search/search_ally_row');
                    $search = parent::$db->query ("SELECT a.alliance_id,
                    									a.alliance_name,
                    									a.alliance_tag,
                    									s.alliance_statistic_total_points as points,
                    									(SELECT COUNT(user_id) AS `ally_members` FROM `" . USERS . "` WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
                    								FROM " . ALLIANCE . " AS a
													LEFT JOIN " . ALLIANCE_STATISTICS . " AS s ON a.alliance_id = s.alliance_statistic_alliance_id
                    								WHERE a.alliance_tag LIKE '%".$searchtext."%' LIMIT 25;");

				break;
				case 'allyname':
					$table 	= parent::$page->get_template ('search/search_ally_table');
					$row 	= parent::$page->get_template ('search/search_ally_row');
                    $search = parent::$db->query ("SELECT a.alliance_id,
                    										a.alliance_name,
                    										a.alliance_tag,
                    										s.alliance_statistic_total_points as points,
                    										(SELECT COUNT(user_id) AS `ally_members` FROM `" . USERS . "` WHERE `user_ally_id` = a.`alliance_id`) AS `ally_members`
                    								FROM " . ALLIANCE . " AS a
													LEFT JOIN " . ALLIANCE_STATISTICS . " AS s ON a.alliance_id = s.alliance_statistic_alliance_id
                    								WHERE a.alliance_name LIKE '%".$searchtext."%' LIMIT 25;" );
				break;
			}
		}

		if ( isset ( $searchtext ) && isset ( $type ) && isset ( $search ) )
		{
			$result_list	= '';

			while ( $s = parent::$db->fetch_array ( $search ) )
			{
				if ( $type == 'playername' or $type == 'planetname' )
				{
					if ( $this->_current_user['user_id'] != $s['user_id'] )
					{
						$s['actions'] 	= '<a href="game.php?page=messages&mode=write&id='.$s['user_id'].'" title="'.$this->_lang['write_message'].'"><img src="'.DPATH.'img/m.gif"/></a>&nbsp;';
						$s['actions'] 	.= '<a href="#" title="'.$this->_lang['sh_buddy_request'].'" onClick="f(\'game.php?page=buddy&mode=2&u=' . $s['user_id'] . '\', \''.$this->_lang['sh_buddy_request'].'\')"><img src="'.DPATH.'img/b.gif" border="0"></a>';
					}

					$s['planet_name'] 	= $s['planet_name'];
					$s['username'] 		= $s['user_name'];
					$s['alliance_name'] = ($s['alliance_name']!='')?"<a href=\"game.php?page=alliance&mode=ainfo&allyid={$s['alliance_id']}\">{$s['alliance_name']}</a>":'';
					$s['position'] 		= "<a href=\"game.php?page=statistics&start=".$s['rank']."\">".$s['rank']."</a>";
					$s['coordinated'] 	= "{$s['planet_galaxy']}:{$s['planet_system']}:{$s['planet_planet']}";
					$result_list 	   .= parent::$page->parse_template ( $row , $s );
				}
				elseif ( $type == 'allytag' or $type == 'allyname' )
				{
					$s['ally_points'] 	= Format_Lib::pretty_number ( $s['points'] );
					$s['ally_tag'] 		= "<a href=\"game.php?page=alliance&mode=ainfo&allyid={$s['alliance_id']}\">{$s['alliance_tag']}</a>";
					$result_list 	   .= parent::$page->parse_template ( $row , $s );
				}
			}

			if ( $result_list != '' )
			{
				$parse['result_list'] 	= $result_list;
				$search_results 		= parent::$page->parse_template ( $table , $parse );
			}
		}

		$parse['type_playername'] 	= ( $type == "playername" ) ? " selected" : "";
		$parse['type_planetname'] 	= ( $type == "planetname" ) ? " selected" : "";
		$parse['type_allytag'] 		= ( $type == "allytag" ) ? " selected" : "";
		$parse['type_allyname'] 	= ( $type == "allyname" ) ? " selected" : "";
		$parse['searchtext'] 		= $searchtext;
		$parse['search_results'] 	= $search_results;

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'search/search_body' ) , $parse ) );
	}
}
/* end of search.php */