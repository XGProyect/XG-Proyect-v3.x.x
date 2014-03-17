<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

define ( 'INSIDE'		, TRUE );
define ( 'IN_INSTALL'	, TRUE );
define ( 'XGP_ROOT'		, './../' );

require ( XGP_ROOT . 'application/core/common.php' );

switch ( ( isset ( $_GET['page'] ) ? $_GET['page'] : '' ) )
{
	case 'update':

		include_once ( XGP_ROOT . INSTALL_PATH . 'update.php' );
		new Update();

	break;

	case 'migrate':

		include_once ( XGP_ROOT . INSTALL_PATH . 'migration.php' );
		new Migration();

	break;

	case '':
	case 'install':
	default:

		include_once ( XGP_ROOT . INSTALL_PATH . 'installation.php' );
		new Installation();

	break;
}
/* end of index.php */