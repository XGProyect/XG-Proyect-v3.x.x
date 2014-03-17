<?php

/**
 * @project XG Proyect
 * @version 3.x.x build 0000
 * @copyright Copyright (C) 2008 - 2014
 */

define ( 'INSIDE'  	, TRUE );
define ( 'IN_LOGIN'	, TRUE );
define ( 'XGP_ROOT'	, './' );

$InLogin	= TRUE;

include ( XGP_ROOT . 'application/core/common.php' );

switch ( ( isset ( $_GET['page'] ) ? $_GET['page'] : '' ) )
{
	// REGISTER PAGE
	case 'reg':

		include ( XGP_ROOT . HOME_PATH . 'register.php' );
		new Register();

	break;

	// RECOVER PASSWORD PAGE
	case 'recoverpassword':

		include ( XGP_ROOT . HOME_PATH . 'recoverpassword.php' );
		new Recoverpassword();

	break;

	// HOME - INDEX - DEFAULT - START PAGE
	case '':
	default:

		include ( XGP_ROOT . HOME_PATH . 'home.php' );
		new Home();

	break;
}
/* end of index.php */