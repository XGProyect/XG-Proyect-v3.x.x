<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Sessions extends XGPCore
{
	private $_alive = TRUE;
	private $_dbc	= NULL;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		parent::__construct();

		// WE'RE GOING TO HANDLE A DIFFERENT DB OBJECT FOR THE SESSIONS
		$this->_dbc = clone parent::$db;

		session_set_save_handler
									(
										array ( &$this , 'open' ),
										array ( &$this , 'close' ),
										array ( &$this , 'read' ),
										array ( &$this , 'write' ),
										array ( &$this , 'delete' ),
										array ( &$this , 'clean' )
									);

		if ( session_id() == '' )
		{
			session_start();
		}
	}

	/**
	 * __destruct()
	 */
	public function __destruct()
	{
		if ( $this->_alive )
		{
			session_write_close();
			$this->_alive = FALSE;
		}
	}

	/**
	 * delete()
	 */
	public function delete()
	{
		if ( ini_get ( 'session.use_cookies' ) )
		{
			$params 	= session_get_cookie_params();
			setcookie	(
							session_name() ,
							'' ,
							time() - 42000 ,
							$params['path'] ,
							$params['domain'] ,
							$params['secure'] ,
							$params['httponly']
						);
		}



		if ( ! empty ( $_SESSION ) )
		{
			unset ( $_SESSION );

			@session_destroy();
		}

		$this->_alive = FALSE;
	}

	/**
	 * open()
	 */
	private function open ()
	{

		return $this->_dbc->open_connection();
	}

	/**
	 * close()
	 */
	private function close ()
	{
		return $this->_dbc->close_connection();
	}

	/**
	 * read()
	 */
	private function read ( $sid )
	{
		$row = $this->_dbc->query ( "SELECT `session_data`
										FROM " . SESSIONS . "
										WHERE `session_id` = '" .  $this->_dbc->escape_value ( $sid ) . "'
										LIMIT 1" );

		if ( $this->_dbc->num_rows ( $row ) == 1 )
		{
			$fields = $this->_dbc->fetch_assoc ( $row );

			return $fields['session_data'];
		}
		else
		{
			return '';
		}
	}

	/**
	 * write()
	 */
	private function write ( $sid , $data )
	{
		$this->_dbc->query ( "REPLACE INTO `" . SESSIONS . "` (`session_id`, `session_data`)
								VALUES ('" . $this->_dbc->escape_value ( $sid ) . "', '" . $this->_dbc->escape_value ( $data ) . "')" );

		return $this->_dbc->affected_rows();
	}

	/**
	 * destroy()
	 */
	private function destroy ( $sid )
	{
		$this->_dbc->query ( "DELETE FROM `" . SESSIONS . "`
								WHERE `session_id` = '" . $this->_dbc->escape_value ( $sid ) . "'" );

		$_SESSION = array();

		return $this->_dbc->affected_rows();
	}

	/**
	 * clean()
	 */
	private function clean ( $expire )
	{
		$this->_dbc->query ( "DELETE FROM `" . SESSIONS . "`
								WHERE DATE_ADD(`session_last_accessed`, INTERVAL " . (int) $expire . " SECOND) < NOW()" );

		return $this->_dbc->affected_rows();
	}
}

/* end of Sessions.php */