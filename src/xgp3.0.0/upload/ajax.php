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

switch ( ( isset ( $_GET['content'] ) ? $_GET['content'] : '' ) )
{
	// information ajax request
	case 'info':

		include ( XGP_ROOT . AJAX_PATH. 'info.php' );
		new Info();

	break;

	// media ajax request
	case 'media':

		include ( XGP_ROOT . AJAX_PATH. 'media.php' );
		new Media();

	break;

	// home ajax request
	case '':
	case 'home':
	default:

		include ( XGP_ROOT . AJAX_PATH. 'home.php' );
		new Home();

	break;
}
/* end of ajax.php */