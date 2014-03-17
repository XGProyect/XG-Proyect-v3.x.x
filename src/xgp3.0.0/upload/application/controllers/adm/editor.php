<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Editor extends XGPCore
{
	private $_lang;
	private $_current_file;
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
		if ( Administration_Lib::have_access ( $this->_current_user['user_authlevel'] ) && Administration_Lib::authorization ( $this->_current_user['user_authlevel'] , 'config_game' ) == 1 )
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
		$parse						= $this->_lang;
		$parse['alert']				= '';
		$parse['language_files']	= $this->get_files();

		if ( $_POST )
		{
			if ( isset ( $_POST['file_edit'] ) )
			{
				$this->_current_file	= $_POST['file_edit'];
			}

			if ( isset ( $_POST['save_file'] ) )
			{
				$this->save_contents ( $_POST['file_content'] );

				$parse['alert']	= Administration_Lib::save_message ( 'ok' , $this->_lang['ce_all_ok_message'] );
			}
		}

		$parse['language_files']	= $this->get_files();
		$parse['contents']			= empty ( $this->_current_file ) ? '' : $this->get_contents();

		parent::$page->display ( parent::$page->parse_template ( parent::$page->get_template ( 'adm/editor_view' ) , $parse ) );
	}

	/**
	 * method get_contents
	 * param
	 * return get file contents
	 */
	private function get_contents()
	{
		// GET THE FILE
		$changelog_file	= XGP_ROOT . LANG_PATH . DEFAULT_LANG . '/' . $this->_current_file;

		// OPEN THE FILE
		$fs 			= fopen ( $changelog_file, 'a+' ) or die ( 'File not found!' );
		$contents		= '';

		// LOOP THRU THE FILE TO GET ITS CONTENT
		while ( !feof ( $fs ) )
		{
			$contents .= fgets ( $fs , 1024 );
		}

		fclose ( $fs );

		// RETURN CONTENT
		return $contents;
	}

	/**
	 * method get_contents
	 * param
	 * return get file contents
	 */
	private function save_contents ( $file_data )
	{
		// GET THE FILE
		$changelog_file	= XGP_ROOT . LANG_PATH . DEFAULT_LANG . '/' . $this->_current_file;

		// OPEN THE FILE
		$fs 			= fopen ( $changelog_file , 'w' ) or die ( 'File not found!' );

		fwrite ( $fs , $file_data );

		fclose ( $fs );
	}

	/**
	 * method get_files
	 * param
	 * return the list of language files
	 */
	private function get_files()
	{
		$langs_files 	= opendir ( XGP_ROOT . LANG_PATH . DEFAULT_LANG );
		$exceptions		= array ( '.' , '..' , '.htaccess' , 'index.html' );
		$lang_options	= '';

		while ( ( $lang_file = readdir ( $langs_files ) ) !== FALSE )
		{
			if ( ! in_array ( $lang_file , $exceptions ) && strpos ( $lang_file , '.' , 0 ) != 0 )
			{
				$lang_options .= '<option ';

				if ( $this->_current_file == $lang_file )
				{
					$lang_options .= 'selected = selected';
				}

				$lang_options .= ' value="' . $lang_file . '">' . $lang_file . '</option>';
			}
		}

		return $lang_options;
	}
}
/* end of editor.php */