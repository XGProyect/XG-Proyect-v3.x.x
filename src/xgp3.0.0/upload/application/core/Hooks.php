<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

if ( ! defined ( 'INSIDE' ) ) { die ( header ( 'location:../../' ) ) ; }

class Hooks
{
	var $_enabled		= FALSE;
	var $_hooks			= array();
	var $_in_progress	= FALSE;

	/**
	 * __construct()
	 */
	function __construct()
	{
		$this->initialize();
	}

	/**
	 * method initialize
	 * param
	 * return initialize the hooks preferences
	 */
	function initialize()
	{
		// IF HOOKS ARE NOT ENABLED THERE IS NOTHING ELSE TO DO
		if ( HOOKS_ENABLED === 'FALSE' )
		{
			return;
		}

		// GRAB THE HOOKS FILE, IF THERE ARE NO HOOKS, WE'RE DONE
		if ( is_file ( XGP_ROOT . 'application/config/hooks.php' ) )
		{
			include ( XGP_ROOT . 'application/config/hooks.php' );
		}

		if ( !isset ( $hook ) or !is_array ( $hook ) )
		{
			return;
		}

		$this->_hooks	= &$hook;
		$this->_enabled = TRUE;
	}

	/**
	 * method call_hook
	 * param $which
	 * return call a hook
	 */
	function call_hook ( $which = '' )
	{
		if ( !$this->_enabled or !isset ( $this->_hooks[$which] ) )
		{
			return FALSE;
		}

		if ( isset ( $this->_hooks[$which][0] ) && is_array ( $this->_hooks[$which][0] ) )
		{
			foreach ( $this->_hooks[$which] as $val )
			{
				$this->run_hook ( $val );
			}
		}
		else
		{
			$this->run_hook ( $this->_hooks[$which] );
		}

		return TRUE;
	}

	/**
	 * method run_hook
	 * param $data
	 * return run a hook
	 */
	function run_hook ( $data )
	{
		if ( !is_array ( $data ) )
		{
			return FALSE;
		}

		// PREVENTS LOOPS
		if ( $this->_in_progress == TRUE )
		{
			return;
		}

		// SET FILE PATH
		if ( !isset ( $data['filepath'] ) or !isset ( $data['filename'] ) )
		{
			return FALSE;
		}

		$filepath = XGP_ROOT . 'application/' . $data['filepath'] . '/' . $data['filename'];

		if ( !file_exists ( $filepath ) )
		{
			return FALSE;
		}

		// SET CLASS / FUNCTION NAME
		$class		= FALSE;
		$function	= FALSE;
		$params		= '';

		if ( isset ( $data['class'] ) && $data['class'] != '' )
		{
			$class 		= $data['class'];
		}

		if ( isset ( $data['function'] ) )
		{
			$function 	= $data['function'];
		}

		if ( isset ( $data['params'] ) )
		{
			$params		= $data['params'];
		}

		if ( $class === FALSE && $function === FALSE )
		{
			return FALSE;
		}

		// SET in_progress FLAG
		$this->_in_progress = TRUE;

		// CALL THE CLASS AND / OR FUNCTION
		if ( $class !== FALSE )
		{
			if ( !class_exists ( $class ) )
			{
				require ( $filepath );
			}

			$HOOK = new $class;
			$HOOK->$function ( $params );
		}
		else
		{
			if ( !function_exists ( $function ) )
			{
				require ( $filepath );
			}

			$function ( $params );
		}

		$this->_in_progress = FALSE;
		return TRUE;
	}
}

/* end of Hooks.php */