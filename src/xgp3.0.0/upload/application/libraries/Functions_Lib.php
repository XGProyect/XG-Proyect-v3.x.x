<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

abstract class Functions_Lib extends XGPCore
{
	/**
	 * __construct()
	 */
	public function __construct ()
	{
		parent::__construct();
	}

	/**
	 * method load_library
	 * param $library_name
	 * return loads an specific library
	 */
	public static function load_library ( $library = '' )
	{
		if ( ! empty ( $library ) )
		{
			// Require file
			require_once ( XGP_ROOT . 'application/libraries/' . $library . '.php' );

			// Create new $library object
			return new $library();
		}
		else
		{
			// ups!
			return FALSE;
		}
	}

	/**
	 * method format_text
	 * param $text
	 * return formats the text before insert into the data base
	*/
	public static function format_text ( $text )
	{
		$text	= parent::$db->escape_value ( $text );
		$text	= trim ( nl2br ( strip_tags ( $text , '<br>' ) ) );
		$text	= preg_replace ( '|[\r][\n]|' , '\\r\\n' , $text );

		return $text;
	}

	/**
	 * method chrono_applet
	 * param $type
	 * param $ref
	 * param $value
	 * param $init
	 * return timers for fleet (overview, phalanx, jumpgate)
	*/
	public static function chrono_applet ( $type , $ref , $value , $init )
	{
		if ( $init == TRUE )
		{
			$template	= parent::$page->get_template ( 'general/chrono_applet_init' );
		}
		else
		{
			$template	= parent::$page->get_template ( 'general/chrono_applet' );
		}

		$parse['type']	= $type;
		$parse['ref']	= $ref;
		$parse['value']	= $value;

		return parent::$page->parse_template ( $template , $parse );
	}

	/**
	 * method read_config
	 * param $config_name
	 * param $all
	 * return reads the configuration file and returns a single config or all
	*/
	public static function read_config ( $config_name = '' , $all = FALSE )
	{
		$configs	= Xml::getInstance ( XML_CONFIG_FILE );

		if ( $all )
		{
			return $configs->get_configs ();
		}
		else
		{
			return $configs->get_config ( $config_name );
		}

	}

	/**
	 * method update_config
	 * param $config_name
	 * param $config_value
	 * return write the configuration file
	*/
	public static function update_config ( $config_name, $config_value )
	{
		$configs	= Xml::getInstance ( XML_CONFIG_FILE );

		$configs->write_config ( $config_name , $config_value );
	}

	/**
	 * method valid_email
	 * param $address
	 * return TRUE success match (is email), FALSE not match (is not email format)
	*/
	public static function valid_email ( $address )
	{
		return ( !preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix" , $address ) ) ? FALSE : TRUE;
	}

	/**
	 * method prep_url
	 * param $url
	 * return set an url format
	*/
	public static function prep_url ( $url = '' )
	{
		if ( $url == 'http://' OR $url == '')
		{
			return '';
		}

		if ( substr ( $url , 0 , 7 ) != 'http://' && substr ( $url , 0 , 8 ) != 'https://' )
		{
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * method fleet_speed_factor
	 * param
	 * return speed factor based on setted configuration value
	*/
	public static function fleet_speed_factor ()
	{
		return Functions_Lib::read_config ( 'fleet_speed' ) / 2500;
	}

	/**
	 * method message
	 * param $mes
	 * param $dest
	 * param $time
	 * param $topnav
	 * param $menu
	 * return a message box to show information or errors
	*/
	public static function message ( $mes , $dest = '' , $time = '3' , $topnav = FALSE , $menu = TRUE , $center = TRUE )
	{
		define ( 'IN_MESSAGE' , TRUE );

		$parse['mes']		= $mes;
		$parse['middle1']	= '';
		$parse['middle2']	= '';

		if ( $center )
		{
			$parse['middle1']	= '<div id="content">';
			$parse['middle2']	= '</div>';
		}

		$page 					= parent::$page->parse_template ( parent::$page->get_template ( 'general/message_body' ) , $parse );

		if ( !defined ( 'IN_ADMIN' ) )
		{
			parent::$page->display ( $page , $topnav , ( ( $dest != "" ) ? "<meta http-equiv=\"refresh\" content=\"$time;URL=$dest\">" : "") , $menu );
		}
		else
		{
			parent::$page->display ( $page , $topnav , ( ( $dest != "" ) ? "<meta http-equiv=\"refresh\" content=\"$time;URL=$dest\">" : "") , $menu );
		}
	}

	/**
	 * method is_module_accesible
	 * param $module_id
	 * return an specific module permission or all the modules array
	*/
	public static function is_module_accesible ( $module_id = 0 )
	{
		$modules_array	= self::read_config ( 'modules' );
		$modules_array	= explode ( ';' , $modules_array );

		if ( $module_id == 0 )
		{
			return $modules_array;
		}
		else
		{
			return $modules_array[$module_id];
		}
	}

	/**
	 * method is_module_accesible
	 * param $module_id
	 * return an specific module permission or all the modules array
	*/
	public static function module_message ( $access_level )
	{
		if ( $access_level == 0 )
		{
			die ( self::message ( parent::$lang['lm_module_not_accesible'] , '' , '' , TRUE ) );
		}
	}

	/**
	 * method sort_planets
	 * param $current_user
	 * return sort user planets
	*/
	public static function sort_planets ( $current_user )
	{
		$order = $current_user['setting_planet_order'] == 1 ? "DESC" : "ASC" ;
		$sort  = $current_user['setting_planet_sort'];

		$planets  = "SELECT `planet_id`, `planet_name`, `planet_galaxy`, `planet_system`, `planet_planet`, `planet_type`
						FROM " . PLANETS . "
						WHERE `planet_user_id` = '" . (int)$current_user['user_id'] ."'
							AND `planet_destroyed` = 0 ORDER BY ";

		switch ( $sort )
		{
			case 0:

				$planets .= "`planet_id` " . $order;

			break;

			case 1:

				$planets .= "`planet_galaxy`, `planet_system`, `planet_planet`, `planet_type` " . $order;

			break;

			case 2:

				$planets .= "`planet_name` " . $order;

			break;
		}

		return parent::$db->query ( $planets );
	}

	/**
	 * method build_planet_list
	 * param $current_user
	 * param $current_planet_id
	 * return build planet list based on the order setted in options
	*/
	public static function build_planet_list ( $current_user , $current_planet_id = 0 )
	{
		$list			= '';
		$user_planets	= self::sort_planets ( $current_user );

		$page	= isset ( $_GET['page'] ) ? $_GET['page'] : '';
		$gid	= isset ( $_GET['gid'] ) ? $_GET['gid'] : '';
		$mode	= isset ( $_GET['mode'] ) ? $_GET['mode'] : '';


		if ( $user_planets )
		{
			while ( $planets = parent::$db->fetch_array ( $user_planets ) )
			{
				if ( $current_planet_id != $planets['planet_id'] )
				{
					$list .= "\n<option ";
					$list .= ( ( $planets['planet_id'] == $current_user['user_current_planet'] ) ? "selected=\"selected\" " : "" );

					if ( $current_planet_id == 0 ) // FOR TOPNAVIGATION BAR PLANET LIST
					{
						$list .= "value=\"game.php?page=" . $page . "&gid=" . $gid . "&cp=" . $planets['planet_id'] . "";
						$list .= "&amp;mode=" . $mode;
						$list .= "&amp;re=0\">";
					}
					else // FOR FLEETS2 PAGE COLONIES SHORTCUTS
					{
						$list .= "value=\"" . $planets['planet_galaxy'].';'.$planets['planet_system'].';'.$planets['planet_planet'].';'.$planets['planet_type'] . "\">";
					}
					$list .= ( ( $planets['planet_type'] != 3 ) ? "" . $planets['planet_name'] : "" . $planets['planet_name'] . " (" . parent::$lang['fcm_moon'] . ")" );
					$list .= "&nbsp;[" . $planets['planet_galaxy'] . ":";
					$list .= $planets['planet_system'] . ":";
					$list .= $planets['planet_planet'];
					$list .= "]&nbsp;&nbsp;</option>";
				}
			}
		}

		// IF THE LIST OF PLANETS IS EMPTY WE SHOULD RETURN FALSE
		if ( $list !== '' )
		{
			return $list;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * method send_message
	 * param $CurrentUser
	 * return send a message
	*/
	public static function send_message ( $to , $sender , $time = '' , $type , $from , $subject , $message )
	{
		if ( $time == '' )
		{
			$time = time();
		}

		$message	= ( strpos ( $message , '/admin.php/' ) === FALSE ) ? $message : '';

		parent::$db->query ( "INSERT INTO " . MESSAGES . " SET
								`message_receiver` = '" . $to . "',
								`message_sender` = '" . $sender . "',
								`message_time` = '" . $time . "',
								`message_type` = '" . $type . "',
								`message_from` = '" . $from . "',
								`message_subject` = '" . $subject . "',
								`message_text` 	= '" . $message . "';" );
	}

	/**
	 * method get_default_vacation_time
	 * param
	 * return the default vacation time
	 */
	public static function get_default_vacation_time ()
	{
		return ( time() + ( 3600 * 24 * VACATION_TIME_FORCED ) );
	}

	/**
	 * method set_url
	 * param
	 * return the url builded
	 */
	public static function set_url ( $url , $title , $content , $attributes = '' )
	{
		if ( empty ( $url ) )
		{
			$url 		= '#';
		}

		if ( ! empty ( $title ) )
		{
			$title 		= 'title="' . $title . '"';
		}

		if ( ! empty ( $attributes ) )
		{
			$attributes	= ' ' . $attributes;
		}

		return '<a href="' . $url . '" ' . $title . ' ' . $attributes . '>' . $content . '</a>';
	}

	/**
	 * method set_image
	 * param
	 * return the image builded
	 */
	public static function set_image ( $path , $title = 'img' , $attributes = '' )
	{
		if ( ! empty ( $attributes ) )
		{
			$attributes	= ' ' . $attributes;
		}

		return '<img src="' . $path . '" title="' . $title . '" border="0"' . $attributes . '>';
	}

	/**
	 * method in_multiarray
	 * param $needle
	 * param $haystack
	 * return (boolean) true something found, false nothing found
	 */
	public static function in_multiarray ( $needle , $haystack )
	{
		foreach ( $haystack as $key => $value )
		{
			if ( $value == $needle )
			{
				return TRUE;
			}
			elseif ( is_array ( $value ) )
			{
				if ( self::in_multiarray ( $needle , $value ) )
				{
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * method recursive_array_search
	 * param $needle
	 * param $haystack
	 * return (boolean) true something found, false nothing found
	 */
	public static function recursive_array_search ( $needle , $haystack )
	{
		foreach ( $haystack as $key => $value )
		{
			$current_key	= $key;

			if ( $needle === $value or ( is_array ( $value ) && self::recursive_array_search ( $needle , $value ) !== FALSE ) )
			{
				return $current_key;
			}
		}
		return FALSE;
	}

	/**
	 * method redirect
	 * param $route
	 * return void
	 */
	public static function redirect ( $route )
	{
		exit ( header ( 'location:' . $route ) );
	}

	/**
	 * method get_languages
	 * param $current_lang
	 * return (string) $lang_options
	 */
	public static function get_languages ( $current_lang )
	{
		$langs_dir 		= opendir ( XGP_ROOT . LANG_PATH );
		$exceptions		= array ( '.' , '..' , '.htaccess' , 'index.html' , '.DS_Store' );
		$lang_options	= '';

		while ( ( $lang_dir = readdir ( $langs_dir ) ) !== FALSE )
		{
			if ( ! in_array ( $lang_dir , $exceptions ) )
			{
				$lang_options .= '<option ';

				if ( $current_lang == $lang_dir )
				{
					$lang_options .= 'selected = selected';
				}

				$lang_options .= ' value="' . $lang_dir . '">' . $lang_dir . '</option>';
			}
		}

		return $lang_options;
	}

	/**
	 * method check_server
	 * param $current_lang
	 * return (void)
	 */
	public static function check_server ( $current_user )
	{
		if ( self::read_config  ( 'game_enable' ) == 0 && $current_user['user_authlevel'] == 3 )
		{
			self::message ( stripslashes ( Functions_Lib::read_config  ( 'close_reason' ) ) , '' , '' , FALSE , FALSE );
			die();
		}
	}
}

/* end of Functions_Lib.php */