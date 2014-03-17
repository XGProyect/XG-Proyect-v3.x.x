<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Globalmessage extends XGPCore
{
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

		$this->_lang			= parent::$lang;
		$this->_current_user	= parent::$users->get_user_data();

		// Check if the user is allowed to access
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'use_tools' ) == 1 )
		{
			$this->build_page();
		}
		else
		{
			die ( Functions_Lib::message ( $this->_lang['ge_no_permissions'] ) );
		}
	}

	/**
	 * method __destruct
	 * param
	 * return close db connection
	 */
	public function __destruct ()
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
		$parse 				= $this->_lang;
		$parse['js_path']	= XGP_ROOT . JS_PATH;

		if ( isset ( $_POST ) && $_POST && $_GET['mode'] == "change" )
		{
			$info	= array (
								1 => array	( 'color' => 'yellow' ),
								2 => array	( 'color' => 'skyblue' ),
								3 => array	( 'color' => 'red' ),
							);

			$color	= $info[$this->_current_user['user_authlevel']]['color'];
			$level 	= $this->_lang['user_level'][$this->_current_user['user_authlevel']];

			if ( ( isset ( $_POST['tresc'] ) && $_POST['tresc'] != '' ) && ( isset ( $_POST['temat'] ) && $_POST['temat'] != '' ) && ( isset ( $_POST['message'] ) or isset ( $_POST['mail'] ) ) )
			{
				$sq      	= parent::$db->query ( "SELECT `user_id` , `user_name`, `user_email`
														FROM " . USERS . "" );

				if ( isset ( $_POST['message'] ) )
				{
					$time    	= time();
					$from    	= '<font color="' . $color . '">' . $level . ' '. $this->_current_user['user_name'] . '</font>';
					$subject 	= '<font color="' . $color . '">' . $_POST['temat'] . '</font>';
					$message 	= '<font color="' . $color . '"><b>' . Functions_Lib::format_text ( $_POST['tresc'] ) . '</b></font>';

					while ( $u = parent::$db->fetch_array ( $sq ) )
					{
						Functions_Lib::send_message ( $u['user_id'] , $this->_current_user['user_id'] , $time , 5 , $from , $subject , $message );
						$_POST['tresc'] = str_replace ( ":name:" , $u['user_name'] , $_POST['tresc'] );
					}
				}

				if ( isset ( $_POST['mail'] ) )
				{
					$i	= 0;

					while ( $u = parent::$db->fetch_array ( $sq ) )
					{
						mail ( $u['user_email'] , $_POST['temat'] , $_POST['tresc'] );

						// 20 per row
						if ( $i % 20 == 0 )
						{
							sleep ( 1 ); // wait, prevent flooding
						}

						$i++;
					}
				}

				$parse['alert']		= Administration_Lib::save_message ( 'ok' , $this->_lang['ma_message_sended'] );
			}
			else
			{
				$parse['alert']		= Administration_Lib::save_message ( 'warning' , $this->_lang['ma_subject_needed'] );
			}

			;
		}

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/global_message_view' ) , $parse ) );
	}
}
/* end of globalmessage.php */