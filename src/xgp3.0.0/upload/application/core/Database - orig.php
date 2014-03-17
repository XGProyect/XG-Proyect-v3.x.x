<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

require ( XGP_ROOT . 'application/config/config.php' );

class Database
{

	public  $_last_query;
	private $_connection;
	private $_magic_quotes_active;
	private $_real_escape_string_exists;
	private $_debug;

	/**
	 * __construct()
	 */
	public function __construct()
	{
		global $debug;

		$this->_debug	= $debug;
		$this->open_connection();
		$this->_magic_quotes_active			= get_magic_quotes_gpc();
		$this->_real_escape_string_exists 	= function_exists ( "mysql_real_escape_string" );
	}

	/**
	* open_connection();
	*/
	public function open_connection()
	{
		if ( defined ( 'DB_HOST' ) && defined ( 'DB_USER' ) && defined ( 'DB_PASS' ) && defined ( 'DB_NAME' ) )
		{
			if ( !$this->try_connection() )
			{
				if ( !defined ( 'IN_INSTALL' ) )
				{
					die ( $this->_debug->error ( 'Database connection failed: ' . mysql_error()  , "SQL Error" ) );
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				if ( !$this->try_database() )
				{
					if ( !defined ( 'IN_INSTALL' ) )
					{
						die ( $this->_debug->error ( 'Database selection failed: ' . mysql_error()  , "SQL Error" ) );
					}
					else
					{
						return FALSE;
					}
				}
			}
		}
	}

	/**
	* open_connection();
	*/
	public function try_connection()
	{
		$this->_connection	= @mysql_connect ( DB_HOST, DB_USER , DB_PASS );

		return $this->_connection;
	}

	/**
	* try_database();
	*/
	public function try_database()
	{
		$db_select	= @mysql_select_db ( DB_NAME , $this->_connection );

		if ( $db_select )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* close_connection();
	*/
	public function close_connection()
	{
		if ( is_resource ( $this->_connection ) OR is_object ( $this->_connection ) )
		{
			mysql_close ( $this->_connection );
			unset ( $this->_connection );
		}
	}

	/**
	* query();
	*/
	public function query ( $sql = FALSE )
	{
		if ( $sql != FALSE )
		{
			$this->_last_query	= $sql;
			$result 			= @mysql_query ( $sql , $this->_connection );
			$this->_confirm_query ( $result );

			return $result;
		}

		return FALSE;
	}

	/**
	* query_fetch();
	*/
	public function query_fetch ( $sql )
	{
		if ( $sql != FALSE )
		{

			$this->_last_query	= $sql;
			$result 			= @mysql_query ( $sql , $this->_connection );
			$this->_confirm_query ( $result );

			return $this->fetch_array ( $result );
		}

		return FALSE;
	}

	/**
	* escape_value();
	*/
	public function escape_value ( $value )
	{
		if( $this->_real_escape_string_exists )
		{
			// PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->_magic_quotes_active )
			{
				$value = stripslashes( $value );
			}
			$value = mysql_real_escape_string( $value );
		}
		else
		{
			// before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->_magic_quotes_active )
			{
				$value = addslashes ( $value );
			}
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}

	/**
	* fetch_array();
	*/
	public function fetch_array ( $result_set )
	{
		return mysql_fetch_array ( $result_set );
	}

	/**
	* fetch_assoc();
	*/
	public function fetch_assoc ( $result_set )
	{
		return mysql_fetch_assoc ( $result_set );
	}

	/**
	* fetch_row();
	*/
	public function fetch_row ( $result_set )
	{
		return mysql_fetch_row ( $result_set );
	}

	/**
	* num_rows();
	*/
	public function num_rows ( $result_set )
	{
		return mysql_num_rows ( $result_set );
	}

	/**
	* num_fields();
	*/
	public function num_fields ( $result_set )
	{
		return mysql_num_fields ( $result_set );
	}

	/**
	* insert_id();
	*/
	public function insert_id()
	{
		// get the last id inserted over the current db connection
		return mysql_insert_id ( $this->_connection );
	}

	/**
	* affected_rows();
	*/
	public function affected_rows()
	{
		return mysql_affected_rows ( $this->_connection );
	}

	/**
	* server_info();
	*/
	public function server_info()
	{
		return mysql_get_server_info ( $this->_connection );
	}

	/**
	* free_resul();
	*/
	public function free_result ( $result )
	{
		return mysql_free_result ( $result );
	}

	/**
	* _confirm_query();
	*/
	private function _confirm_query ( $result )
	{
		if ( !$result )
		{
			$output	= "Database query failed: " . mysql_error();

			// uncomment below line when you want to debug your last query
			$output .= " Last SQL Query: " . $this->_last_query;
			die ( $this->_debug->error ( $output , "SQL Error" ) );
		}

		// DEBUG LOG
		$this->_debug->add ( $this->_last_query );
	}

	/**
	* backup_db();
	*/
	public function backup_db ( $tables = '*' )
	{
		// GET ALL THE TABLES
		if ( $tables == '*' )
		{
			$tables = array();
			$result = $this->query ( 'SHOW TABLES' );

			while ( $row = $this->fetch_row ( $result ) )
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array ( $tables ) ? $tables : explode ( ',' , $tables );
		}

		$return	= '';

		//CYCLE TROUGHT
		foreach ( $tables as $table )
		{
			$result 	= $this->query ( 'SELECT * FROM ' . $table );
			$num_fields = $this->num_fields ( $result );

			$return	.= 'DROP TABLE ' . $table . ';';
			$row2	 = $this->fetch_row ( $this->query ( 'SHOW CREATE TABLE ' . $table ) );
			$return	.= "\n\n".$row2[1].";\n\n";

			for ( $i = 0 ; $i < $num_fields ; $i++ )
			{
				while ( $row = $this->fetch_row ( $result ) )
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';

					for ( $j = 0 ; $j < $num_fields ; $j++ )
					{
						$row[$j]	= addslashes ( $row[$j] );
						$row[$j]	= str_replace ( "\n" , "\\n" , $row[$j] );

						if ( isset ( $row[$j] ) )
						{
							$return	.= '"'.$row[$j].'"' ;
						}
						else
						{
							$return	.= '""';
						}

						if ( $j < ( $num_fields - 1 ) )
						{
							$return	.= ',';
						}
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}

		// SAVE FILE
		$handle	= fopen ( XGP_ROOT . BACKUP_PATH . 'db-backup-' . date ( 'Ymd' ) . '-' . time() . '-' . ( sha1 ( implode ( ',' , $tables ) ) ) . '.sql' , 'w+' );

		$writed	= fwrite ( $handle , $return );
		fclose ( $handle );

		return $writed;
	}
}

/* end of Database.php */