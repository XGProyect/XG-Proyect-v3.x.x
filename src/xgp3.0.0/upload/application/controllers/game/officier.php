<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Officier extends XGPCore
{
	const MODULE_ID = 15;

	private $_lang;
	private $_resource;
	private $_pricelist;
	private $_reslist;
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
		$this->_resource		= parent::$objects->get_objects();
		$this->_pricelist		= parent::$objects->get_price();
		$this->_reslist			= parent::$objects->get_objects_list();

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
		$parse 			= $this->_lang;
		$parse['dpath']	= DPATH;
		$bloc			= $this->_lang;
		$mode			= isset ( $_GET['mode'] ) ? $_GET['mode'] : '';
		$time			= isset ( $_GET['time'] ) ? $_GET['time'] : '';

		if ( $mode == 2 && ( $time == 'month' or $time == 'week' ) )
		{
			$Selected	= $_GET['offi'];
			$time		= 'darkmatter_' . $time;
			$set_time	= $time == 'darkmatter_month' ? ( 3600 * 24 * 30 * 3 ) : ( 3600 * 24 * 7 );

			if ( in_array ( $Selected , $this->_reslist['officier'] ) )
			{
				$Result	= $this->is_officier_accesible ( $Selected , $time );
				$Price	= $this->get_officier_price ( $Selected , $time );

				if ( $Result !== FALSE )
				{
					$this->_current_user['premium_dark_matter']	-= $Price;

					// IF THE OFFICIER IS ACTIVE
					if ( Officiers_Lib::is_officier_active ( $this->_current_user[$this->_resource[$Selected]] ) )
					{
						$this->_current_user[$this->_resource[$Selected]] += $set_time; // ADD TIME
					}
					else // ELSE
					{
						$this->_current_user[$this->_resource[$Selected]]	= time() + $set_time; // SET TIME
					}

					parent::$db->query ( "UPDATE " . PREMIUM . " SET
											`premium_dark_matter` = '". $this->_current_user['premium_dark_matter'] ."',
											`".$this->_resource[$Selected]."` = '". $this->_current_user[$this->_resource[$Selected]] ."'
											WHERE `premium_user_id` = '". $this->_current_user['user_id'] ."';");
				}
			}
			Functions_Lib::redirect ( 'game.php?page=officier' );
		}
		else
		{
			$OfficierRowTPL				= parent::$page->get_template ( 'officier/officier_row' );
			$parse['disp_off_tbl']		= '';
			$parse['premium_pay_url']	= Functions_Lib::read_config ( 'premium_url' ) != '' ? Functions_Lib::read_config ( 'premium_url' ) : 'game.php?page=officier';

			foreach ( $this->_lang['tech'] as $Element => $ElementName )
			{
				if ( $Element >= 601 && $Element <= 605 )
				{
					$bloc['dpath']			= DPATH;
					$bloc['off_id']   		= $Element;
					$bloc['off_status']		= ( ( Officiers_Lib::is_officier_active ( $this->_current_user[$this->_resource[$Element]] ) ) ? '<font color=lime>' . $this->_lang['of_active'] . ' ' . date ( Functions_Lib::read_config ( 'date_format' ) , $this->_current_user[$this->_resource[$Element]] ) . '</font>' : '<font color=red>' . $this->_lang['of_inactive'] . '</font>' );
					$bloc['off_name']		= $ElementName;
					$bloc['off_desc'] 		= $this->_lang['res']['descriptions'][$Element];
					$bloc['off_desc_short'] = $this->_lang['info'][$Element]['description'];
					$bloc['month_price']	= Format_Lib::pretty_number ( $this->get_officier_price ( $Element , 'darkmatter_month' ) );
					$bloc['week_price']		= Format_Lib::pretty_number ( $this->get_officier_price ( $Element , 'darkmatter_week' ) );
					$bloc['img_big']		= $this->get_officier_image ( $Element , 'img_big' );
					$bloc['img_small']		= $this->get_officier_image ( $Element , 'img_small' );
					$bloc['off_link_month']	= "game.php?page=officier&mode=2&offi=" . $Element . "&time=month";
					$bloc['off_link_week'] 	= "game.php?page=officier&mode=2&offi=" . $Element . "&time=week";

					$parse['disp_off_tbl'] .= parent::$page->parse_template ( $OfficierRowTPL , $bloc );
				}
			}
		}

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'officier/officier_table' ) , $parse ) );
	}

	/**
	 * method is_officier_accesible
	 * param $Officier
	 * param $time
	 * return if the officier is accesible or not
	 */
	private function is_officier_accesible ( $officier , $time )
	{
		if ( $this->_pricelist[$officier][$time] <= $this->_current_user['premium_dark_matter'] )
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * method get_officier_price
	 * param $officier
	 * param $time
	 * return the officier darkmatter price
	 */
	private function get_officier_price ( $officier , $time )
	{
		return floor ( $this->_pricelist[$officier][$time] );
	}

	/**
	 * method get_officier_image
	 * param $officier
	 * param $type
	 * return the officier darkmatter price
	 */
	private function get_officier_image ( $officier , $type )
	{
		return $this->_pricelist[$officier][$type];
	}
}
/* end of officier.php */