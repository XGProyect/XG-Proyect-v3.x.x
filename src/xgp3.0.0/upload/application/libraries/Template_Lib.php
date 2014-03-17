<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Template_Lib
{
	private $_current_user;
	private $_current_planet;
	private $_lang;
	private $_current_year;

	/**
	 * __construct()
	 */
	public function __construct ( $lang , $users )
	{
		global $user, $planetrow;

		$this->_current_user	= $users->get_user_data();
		$this->_current_planet	= $users->get_planet_data();
		$this->_lang			= $lang;
		$this->_current_year	= date ( 'Y' );
	}

	/**
	 * method display
	 * param $current_page
	 * param $topnav
	 * param $metatags
	 * param $is_admin
	 * param $menu
	 * return the final builded page
	*/
	public function display ( $current_page , $topnav = TRUE , $metatags = '' , $menu = TRUE )
	{
		global $debug;

		$page	= '';

		if ( ! defined ( 'IN_MESSAGE' ) )
		{
			// For the Home page
			if ( defined ( 'IN_LOGIN' ) )
			{
				die ( $current_page );
			}

			// For the Install page
			if ( defined ( 'IN_INSTALL' ) )
			{
				$page  	.= $this->install_header ( $metatags );
				$page	.= $menu ? $this->install_menu() : ''; // MENU
				$page	.= $topnav ? $this->install_navbar() : ''; // TOP NAVIGATION BAR
			}

			// For the Admin page
			if ( defined ( 'IN_ADMIN' ) )
			{
				$page  	.= $this->admin_header ( $metatags );
				$page	.= $menu ? $this->admin_menu() : ''; // MENU
				$page	.= $topnav ? $this->admin_navbar() : ''; // TOP NAVIGATION BAR
			}
		}

		// Anything else
		if ( $page == '' )
		{
			$page  	.= $this->game_header ( $metatags );
			$page	.= $topnav ? $this->game_navbar() : ''; // TOP NAVIGATION BAR
			$page	.= $menu ? $this->game_menu() : ''; // MENU
		}

		// Merge: Header + Topnav + Menu + Page
		if ( ! defined ( 'IN_INSTALL' ) && ! defined ( 'IN_ADMIN' ) )
		{
			$page		.= "\n<center>\n" . $current_page . "\n</center>\n";
		}
		else
		{
			if ( defined ( 'IN_MESSAGE' ) )
			{
				$page	.= "\n<center>\n" . $current_page . "\n</center>\n";
			}
			else
			{
				$page	.= $current_page;
			}
		}

		// Footer
		if ( ! defined ( 'IN_INSTALL' ) && ! defined ( 'IN_ADMIN' ) && ! defined ( 'IN_LOGIN' ) )
		{
			// Is inside the game
			if ( isset ( $_GET['page'] ) && $_GET['page'] != 'galaxy' )
			{
				$page .= $this->parse_template ( $this->get_template ( 'general/footer' ) , '' );
			}
		}

		if ( defined ( 'IN_ADMIN' ) )
		{
			$page .= $this->parse_template ( $this->get_template ( 'adm/simple_footer' ) , array ( 'year' => $this->_current_year ) );
		}

		if ( defined ( 'IN_INSTALL' ) && ! defined ( 'IN_MESSAGE' ) )
		{
			$page .= $this->parse_template ( $this->get_template ( 'install/simple_footer' ) , array ( 'year' => $this->_current_year ) );
		}

		// Show result page
		die ( $page );
	}

	/**
	 * method parse_template
	 * param $template
	 * param $array
	 * return parse information into the template
	*/
	public function parse_template ( $template , $array )
	{
		return preg_replace_callback ( '#\{([a-z0-9\-_]*?)\}#Ssi' , function ( $matches ) use ( $array ) {
			return ( ( isset ( $array[$matches[1]] ) ) ? $array[$matches[1]] : '' );
		} , $template );
	}

	/**
	 * method get_template
	 * param $template_name
	 * return the template data
	*/
	public function get_template ( $template_name )
	{
		$route		=  XGP_ROOT . TEMPLATE_DIR . $template_name . '.php';
		$template 	= @file_get_contents ( $route );

		if ( $template ) // We got something
		{
			return $template; // Return
		}
		else
		{
			// Throw Exception
			die ( 'Template not found or empty: <strong>' . $template_name . '</strong><br />Location: <strong>' . $route . '</strong>' );
		}
	}

	/**
	 * method install_header
	 * param
	 * return install header
	*/
	private function install_header ()
	{
		$parse['title']			= 'Install';
		$parse['xgp_root']		= XGP_ROOT;
		$parse['js_path']		= XGP_ROOT . JS_PATH;
		$parse['css_path']		= XGP_ROOT . CSS_PATH;

		return $this->parse_template ( $this->get_template ( 'install/simple_header' ) , $parse );
	}

	/**
	 * method install_navbar
	 * param
	 * return install navigation bar
	*/
	private function install_navbar ()
	{
		// Update config language to the new setted value
		if ( isset ( $_POST['language'] ) )
		{
			Functions_Lib::update_config ( 'lang' , $_POST['language'] );

			Functions_Lib::redirect ( XGP_ROOT . 'install/' );
		}

		$current_page	= isset ( $_GET['page'] ) ? $_GET['page'] : NULL;
		$items			= '';
		$pages			= array (
									0 	=> array ( 'install' 	, $this->_lang['ins_overview']	, 'overview'),
									1	=> array ( 'install' 	, $this->_lang['ins_license'] 	, 'license'	),
									2 	=> array ( 'install' 	, $this->_lang['ins_install'] 	, 'step1'	),
									3 	=> array ( 'update' 	, $this->_lang['ins_update']	, ''		),
									4 	=> array ( 'migrate' 	, $this->_lang['ins_migrate'] 	, ''		)
								);

		// BUILD THE MENU
		foreach ( $pages as $key => $data )
		{
			if ( $data[2] != '' )
			{
				// URL
				$items	   .= '<li' .  ( $current_page == $data[0] ? ' class="active"' : '' ) . '><a href="index.php?page=' . $data[0] . '&mode=' . $data[2] . '">' . $data[1] . '</a></li>';
			}
			else
			{
				// URL
				$items	   .= '<li' .  ( $current_page == $data[0] ? ' class="active"' : '' ) . '><a href="index.php?page=' . $data[0] . '">' . $data[1] . '</a></li>';
			}
		}

		// PARSE THE MENU AND OTHER DATA
		$parse						= $this->_lang;
		$parse['menu_items']		= $items;
		$parse['language_select']	= Functions_Lib::get_languages ( Functions_Lib::read_config ( 'lang' ) );

		return $this->parse_template ( $this->get_template ( 'install/topnav_view' ) , $parse );
	}

	/**
	 * method install_menu
	 * param
	 * return install progress menu
	*/
	private function install_menu()
	{
		$current_mode	= isset ( $_GET['mode'] ) ? $_GET['mode'] : NULL;
		$items			= '';
		$steps			= array (
									0 	=> array ( 'step1' 	, $this->_lang['ins_step1']	),
									1	=> array ( 'step2' 	, $this->_lang['ins_step2']	),
									2 	=> array ( 'step3' 	, $this->_lang['ins_step3']	),
									3 	=> array ( 'step4' 	, $this->_lang['ins_step4']	),
									4 	=> array ( 'step5' 	, $this->_lang['ins_step5'] )
								);

		// BUILD THE MENU
		foreach ( $steps as $key => $data )
		{
			// URL
			$items	   .= '<li' .  ( $current_mode == $data[0] ? ' class="active"' : '' ) . '><a href="#">' . $data[1] . '</a></li>';
		}

		// PARSE THE MENU AND OTHER DATA
		$parse					= $this->_lang;
		$parse['menu_items']	= $items;

		return $this->parse_template ( $this->get_template ( 'install/menu_view' ) , $parse );
	}

	/**
	 * method game_header
	 * param $metatags
	 * return general ingame header
	*/
	private function game_header ( $metatags = '' )
	{
		$parse['-title-'] 	 = Functions_Lib::read_config ( 'game_name' );
		$parse['-favi-']	 = "<link rel=\"shortcut icon\" href=\"" . XGP_ROOT . "favicon.ico\">\n";
		$parse['-meta-']	 = "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\">\n";
		$parse['-meta-']	.= "<meta name=\"generator\" content=\"XG Proyect " . VERSION . "\" />\n";
		$parse['-style-']  	 = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . XGP_ROOT . CSS_PATH . "default.css\">\n";
		$parse['-style-']  	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . XGP_ROOT . CSS_PATH . "formate.css\">\n";
		$parse['-style-'] 	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . XGP_ROOT . DPATH ."formate.css\" />\n";
		$parse['-meta-']	.= "<script type=\"text/javascript\" src=\"" . XGP_ROOT . JS_PATH . "overlib-min.js\"></script>\n";
		$parse['-meta-']	.= ( $metatags ) ? $metatags : "";

		return $this->parse_template ( $this->get_template ( 'general/simple_header' ) , $parse );
	}

	/**
	 * method topnav_bar
	 * param
	 * return get the top navigation bar data and parses it
	*/
	private function game_navbar()
	{
		$parse					= $this->_lang;
		$parse['dpath']			= DPATH;
		$parse['image']			= $this->_current_planet['planet_image'];
		$parse['planetlist']	= Functions_Lib::build_planet_list ( $this->_current_user );

		// VACATION MODE & DELETE MODE MESSAGES
		if ( $this->_current_user['setting_vacations_status'] && $this->_current_user['setting_delete_account'] )
        {
            $parse['show_umod_notice']		.= $this->_current_user['setting_delete_account'] ? '<table width="100%" style="border: 2px solid red; text-align:center;background:transparent;"><tr style="background:transparent;"><td style="background:transparent;">' . $this->_lang['tn_delete_mode'] . date(Functions_Lib::read_config ( 'date_format_extended' ),$this->_current_user['setting_delete_account'] + (60 * 60 * 24 * 7)).'</td></tr></table>' : '';
        }
        else
        {
            if ( $this->_current_user['setting_vacations_status'] < time() )
            {
                $parse['show_umod_notice']   = $this->_current_user['setting_vacations_status'] ? '<table width="100%" style="border: 2px solid #1DF0F0; text-align:center;background:transparent;"><tr style="background:transparent;"><td style="background:transparent;">' . $this->_lang['tn_vacation_mode'] . date(Functions_Lib::read_config ( 'date_format_extended' ),$this->_current_user['setting_vacations_until']).'</td></tr></table><br>' : '';
            }

            $parse['show_umod_notice']      .= $this->_current_user['setting_delete_account'] ? '<table width="100%" style="border: 2px solid red; text-align:center;background:transparent;"><tr style="background:transparent;"><td style="background:transparent;">' . $this->_lang['tn_delete_mode'] . date(Functions_Lib::read_config ( 'date_format_extended' ),$this->_current_user['setting_delete_account'] + (60 * 60 * 24 * 7)).'</td></tr></table>' : '';
        }

		// RESOURCES FORMAT
		$metal 		= Format_Lib::pretty_number ( $this->_current_planet['planet_metal'] );
		$crystal 	= Format_Lib::pretty_number ( $this->_current_planet['planet_crystal'] );
		$deuterium	= Format_Lib::pretty_number ( $this->_current_planet['planet_deuterium'] );
		$darkmatter	= Format_Lib::pretty_number ( $this->_current_user['premium_dark_matter'] );
		$energy		= Format_Lib::pretty_number ( $this->_current_planet['planet_energy_max'] + $this->_current_planet['planet_energy_used'] ) . "/" . Format_Lib::pretty_number ( $this->_current_planet['planet_energy_max'] );

		// OFFICERS AVAILABILITY
		$commander	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_commander'] ) ? '' : '_un';
		$admiral	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_admiral'] ) ? '' : '_un';
		$engineer	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_engineer'] ) ? '' : '_un';
		$geologist	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_geologist'] ) ? '' : '_un';
		$technocrat	= Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_technocrat'] ) ? '' : '_un';

		// METAL
		if ( ( $this->_current_planet['planet_metal'] >= $this->_current_planet['planet_metal_max'] ) )
		{
			$metal		= Format_Lib::color_red ( $metal );
		}

		// CRYSTAL
		if ( ( $this->_current_planet['planet_crystal'] >= $this->_current_planet['planet_crystal_max'] ) )
		{
			$crystal	= Format_Lib::color_red ( $crystal );
		}

		// DEUTERIUM
		if ( ( $this->_current_planet['planet_deuterium'] >= $this->_current_planet['planet_deuterium_max'] ) )
		{
			$deuterium	= Format_Lib::color_red ( $deuterium );
		}

		// ENERGY
		if ( ( $this->_current_planet['planet_energy_max'] + $this->_current_planet['planet_energy_used'] ) < 0 )
		{
			$energy		= Format_Lib::color_red ( $energy );
		}

		$parse['metal']			= $metal;
		$parse['crystal']		= $crystal;
		$parse['deuterium']		= $deuterium;
		$parse['darkmatter']	= $darkmatter;
		$parse['energy']		= $energy;
		$parse['img_commander']	= $commander;
		$parse['img_admiral']	= $admiral;
		$parse['img_engineer']	= $engineer;
		$parse['img_geologist']	= $geologist;
		$parse['img_technocrat']= $technocrat;

		return $this->parse_template ( $this->get_template ( 'general/topnav' ) , $parse );
	}

	/**
	 * method game_menu
	 * param
	 * return get the left menu data and parses it
	*/
	private function game_menu()
	{
		$menu_block1	= '';
		$menu_block2	= '';
		$menu_block3	= '';
		$modules_array	= Functions_Lib::read_config ( 'modules' );
		$modules_array	= explode ( ';' , $modules_array );
		$sub_template	= $this->get_template ( 'general/left_menu_row_view' );
		$tota_rank		= $this->_current_user['user_statistic_total_rank'] == '' ? $this->_current_planet['stats_users'] : $this->_current_user['user_statistic_total_rank'];
		$pages			= array (
									array ( 'changelog' 		, VERSION 									, '' 					, 'FFF'		, '' 		, '0' ,  '0' ),
									array ( 'overview' 			, $this->_lang['lm_overview'] 				, '' 					, 'FFF' 	, '' 		, '1' ,  '1' ),
									array ( 'imperium' 			, $this->_lang['lm_empire'] 				, '' 					, 'FFF' 	, '' 		, '1' ,  '2' ),
									array ( 'resources' 		, $this->_lang['lm_resources'] 				, '' 					, 'FFF' 	, '' 		, '1' ,  '3' ),
									array ( 'resourceSettings' 	, $this->_lang['lm_resources_settings'] 	, '' 					, 'FFF' 	, '' 		, '1' ,  '4' ),
									array ( 'station' 			, $this->_lang['lm_station'] 				, '' 					, 'FFF' 	, '' 		, '1' ,  '3' ),
									array ( 'trader' 			, $this->_lang['lm_trader'] 				, '' 					, 'FF8900'	, '' 		, '1' ,  '5' ),
									array ( 'research' 			, $this->_lang['lm_research'] 				, ''					, 'FFF'  	, '' 		, '1' ,  '6' ),
									array ( 'shipyard' 			, $this->_lang['lm_shipyard'] 				, '' 					, 'FFF'  	, '' 		, '1' ,  '7' ),
									array ( 'fleet1' 			, $this->_lang['lm_fleet'] 					, '' 					, 'FFF'  	, '' 		, '1' ,  '8' ),
									array ( 'movement' 			, $this->_lang['lm_movement'] 				, '' 					, 'FFF'  	, '' 		, '1' ,  '9' ),
									array ( 'techtree' 			, $this->_lang['lm_technology']				, '' 					, 'FFF'  	, '' 		, '1' , '10' ),
									array ( 'galaxy' 			, $this->_lang['lm_galaxy'] 				, 'mode=0' 				, 'FFF'  	, '' 		, '1' , '11' ),
									array ( 'defense' 			, $this->_lang['lm_defenses'] 				, '' 					, 'FFF'  	, '' 		, '1' , '12' ),
									array ( 'alliance' 			, $this->_lang['lm_alliance'] 				, '' 					, 'FFF'  	, '' 		, '2' , '13' ),
									array ( 'forums' 			, $this->_lang['lm_forums'] 				, '' 					, 'FFF'  	, '' 		, '2' , '14' ),
									array ( 'officier' 			, $this->_lang['lm_officiers'] 				, '' 					, 'FF8900'  , '' 		, '2' , '15' ),
									array ( 'statistics' 		, $this->_lang['lm_statistics'] 			, 'range=' . $tota_rank	, 'FFF'		, '' 		, '2' , '16' ),
									array ( 'search' 			, $this->_lang['lm_search'] 				, '' 					, 'FFF'  	, '' 		, '2' , '17' ),
									array ( 'messages' 			, $this->_lang['lm_messages'] 				, '' 					, 'FFF'  	, '' 		, '3' , '18' ),
									array ( 'notes' 			, $this->_lang['lm_notes'] 					, '' 					, 'FFF'  	, 'true'	, '3' , '19' ),
									array ( 'buddy' 			, $this->_lang['lm_buddylist'] 				, '' 					, 'FFF'  	, 'true'	, '3' , '20' ),
									array ( 'options' 			, $this->_lang['lm_options'] 				, '' 					, 'FFF'  	, ''		, '3' , '21' ),
									array ( 'banned' 			, $this->_lang['lm_banned'] 				, '' 					, 'FFF'  	, ''		, '3' , '22' ),
									array ( 'logout' 			, $this->_lang['lm_logout'] 				, '' 					, 'FFF'  	, ''		, '3' , '' ),
								);

		// BUILD THE MENU
		foreach ( $pages as $key => $data )
		{
			// IF THE MODULE IT'S NOT ENABLED, CONTINUE!
			if ( isset ( $modules_array[$data[6]] ) && $modules_array[$data[6]] == 0 && $modules_array[$data[6]] != '' )
			{
				continue;
			}

			if ( !Officiers_Lib::is_officier_active ( $this->_current_user['premium_officier_commander'] ) && $data[0] == 'imperium' )
			{
				continue;
			}

			// BUILD URL
			if ( $data[2] != '' )
			{
				$link	= 'game.php?page=' . $data[0] . '&' . $data[2];
			}
			else
			{
				$link 	= 'game.php?page=' . $data[0];
			}

			// POP UP OR NOT
			if ( $data[4] == 'true' )
			{
				$link_type	= '<a href="#" onClick="f(\'' . $link . '\', \'' . $data[1] . '\')"><font color="' . ( ( $data[3] != 'FFF' ) ? $data[3] : '' ) . '">' . $data[1] . '</font></a>';
			}
			else
			{
				$link_type = '<a href="' . $link . '"><font color="' . (($data[3]!='FFF')?$data[3]:'') . '">' . $data[1] . '</font></a>';
			}

			// COLOR AND URL
			$parse['color']			= $data[3];
			$parse['menu_link']		= $link_type;

			// ONLY FOR THE CHANGELOG
			if ( $data[5] == 0 )
			{
				$parse['changelog']	= '(' . $link_type . ')';
			}

			// MENU BLOCK [1 - 2 - 3]
			switch ( $data[5] )
			{
				case '1':

					$menu_block1	.= $this->parse_template ( $sub_template , $parse );

				break;

				case '2':

					$menu_block2	.= $this->parse_template ( $sub_template , $parse );

				break;

				case '3':

					$menu_block3	.= $this->parse_template ( $sub_template , $parse );

				break;
			}
		}

		// PARSE THE MENU AND OTHER DATA
		$parse['dpath']			= DPATH;
		$parse['version']		= VERSION;
		$parse['servername']	= Functions_Lib::read_config ( 'game_name' );
		$parse['year']			= $this->_current_year;
		$parse['menu_block1']	= $menu_block1;
		$parse['menu_block2']	= $menu_block2;
		$parse['menu_block3']	= $menu_block3;
		$parse['admin_link']	= ( ( $this->_current_user['user_authlevel'] > 0 ) ? "<tr><td><div align=\"center\"><a href=\"admin.php\" target=\"_blank\"> <font color=\"lime\">" . $this->_lang['lm_administration'] . "</font></a></div></td></tr>" : "" );

		return $this->parse_template ( $this->get_template ( 'general/left_menu_view' ) , $parse );
	}

	/**
	 * method admin_header
	 * param $metatags
	 * return general admin header
	*/
	private function admin_header ( $metatags = '' )
	{
		$parse['title']			= 'Admin CP';
		$parse['xgp_root']		= XGP_ROOT;
		$parse['js_path']		= XGP_ROOT . JS_PATH;
		$parse['css_path']		= XGP_ROOT . CSS_PATH;
		$parse['secure_url']	= Functions_Lib::read_config ( 'ssl_enabled' ) == 1 ? 'https://' : 'http://';
		$parse['-meta-'] 	    = $metatags ? $metatags : '';

		return $this->parse_template ( $this->get_template ( 'adm/simple_header' ) , $parse );
	}

	/**
	 * method admin_navbar
	 * param
	 * return the builded admin top navigation bar
	 */
	private function admin_navbar()
	{
		$current_page	= isset ( $_GET['page'] ) ? $_GET['page'] : NULL;
		$items			= '';
		$pages			= array (
									0 	=> array ( '' 			, $this->_lang['tn_index'] 			),
									1 	=> array ( 'moderation' , $this->_lang['tn_permissions'] 	),
									2	=> array ( 'reset' 		, $this->_lang['tn_reset_universe'] ),
									3 	=> array ( 'queries' 	, $this->_lang['tn_sql_queries'] 	),
									4 	=> array ( 'logout' 	, $this->_lang['tn_logout'] 		),
								);

		// BUILD THE MENU
		foreach ( $pages as $key => $data )
		{
			// URL
			$items	   .= '<li' .  ( $current_page == $data[0] ? ' class="active"' : '' ) . '><a href="admin.php?page=' . $data[0] . '">' . $data[1] . '</a></li>';
		}

		// PARSE THE MENU AND OTHER DATA
		$parse					= $this->_lang;
		$parse['username']		= $this->_current_user['user_name'];
		$parse['menu_items']	= $items;

		return $this->parse_template ( $this->get_template ( 'adm/topnav_view' ) , $parse );
	}

	/**
	 * method admin_menu
	 * param
	 * return the builded admin menu
	 */
	private function admin_menu()
	{
		$current_page	= isset ( $_GET['page'] ) ? $_GET['page'] : NULL;
		$items			= '';
		$flag			= '';
		$exclude		= array ( 1 , 2 , 3 , 4 );
		$pages			= array (
									array ( 'server' 			, $this->_lang['mn_config_server'] 			, '1'	),
									array ( 'modules' 			, $this->_lang['mn_config_modules'] 		, '1'	),
									array ( 'planets' 			, $this->_lang['mn_config_planets'] 		, '1'	),
									array ( 'registration' 		, $this->_lang['mn_config_registrations'] 	, '1'	),
									array ( 'statistics' 		, $this->_lang['mn_config_stats'] 			, '1'	),
									array ( 'premium' 			, $this->_lang['mn_premium'] 				, '1'	),
									array ( 'editor' 			, $this->_lang['mn_config_changelog'] 		, '1'	),
									array ( 'information' 		, $this->_lang['mn_info_general'] 			, '2'	),
									array ( 'errors' 			, $this->_lang['mn_info_db'] 				, '2'	),
									array ( 'fleetmovements' 	, $this->_lang['mn_info_fleets'] 			, '2'	),
									array ( 'messages' 			, $this->_lang['mn_info_messages'] 			, '2'	),
									array ( 'maker' 			, $this->_lang['mn_edition_maker'] 			, '3'	),
									array ( 'users' 			, $this->_lang['mn_edition_users'] 			, '3'	),
									array ( 'alliances' 		, $this->_lang['mn_edition_alliances'] 		, '3'	),
									array ( 'backup' 			, $this->_lang['mn_tools_backup'] 			, '4'	),
									array ( 'encrypter' 		, $this->_lang['mn_tools_encrypter'] 		, '4'	),
									array ( 'globalmessage' 	, $this->_lang['mn_tools_global_message'] 	, '4'	),
									array ( 'ban' 				, $this->_lang['mn_tools_ban'] 				, '4'	),
									array ( 'buildstats' 		, $this->_lang['mn_tools_manual_update'] 	, '4'	),
									array ( 'database' 			, $this->_lang['mn_maintenance_db'] 		, '5'	),
								);
		// BUILD THE MENU
		foreach ( $pages as $key => $data )
		{
			if ( $data[2] != $flag )
			{
				$flag	= $data[2];
				$items	= '';
			}

			if ( $data[0] == 'buildstats' )
			{
				$extra	= 'onClick="return confirm(\'' . $this->_lang['mn_tools_manual_update_confirm'] . '\');"';
			}
			else
			{
				$extra	= '';
			}

			// URL
			if ( Functions_Lib::read_config ( 'ssl_enabled' ) == 1 )
			{
				$url	= 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			}
			else
			{
				$url	= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			}

			$items	   .= '<li' .  ( $current_page == $data[0] ? ' class="active"' : '' ) . '><a href="' . $url . '?page=' . $data[0] . '" ' . $extra . '>' . $data[1] . '</a></li>';

			$parse_block[$data[2]]	= $items;
		}

		// PARSE THE MENU AND OTHER DATA
		$parse					= $this->_lang;
		$parse['username']		= $this->_current_user['user_name'];
		$parse['menu_block_1']	= $parse_block[1];
		$parse['menu_block_2']	= $parse_block[2];
		$parse['menu_block_3']	= $parse_block[3];
		$parse['menu_block_4']	= $parse_block[4];
		$parse['menu_block_5']	= $parse_block[5];

		return $this->parse_template ( $this->get_template ( 'adm/menu_view' ) , $parse );
	}
}
/* end of Template_Lib.php */