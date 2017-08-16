<?php

// SOME MESSAGES
$lang['404_error']					= 'The requested page doesn\'t exists';
$lang['ins_no_server_requirements']	= 'Your server / hosting does not meet the minimum requirements needed to run XG Proyect.<br /><br />Requirements: <br />- PHP 5.5.x<br />- MySQL 5.5.x';
$lang['ins_not_writable']               = 'You must provide write permission (chmod 777) to the application/config directory to continue with the installation.';
$lang['ins_already_installed']		= 'XG Proyect is already installed. Select an option: <br /><br /> - <a href="../admin.php?page=update">Update</a> <br /> - <a href="../admin.php?page=migrate">Migrate</a> <br /> - <a href="../">Back to the game</a> <br /><br />In case you do not want to take any action, for safety, we recommend <span style="color:red;text-decoration:underline;">DELETE</span> the install directory.';

// SOME ERROR HEADERS
$lang['ins_error_title']			= 'Alert!';
$lang['ins_warning_title']			= 'Warning!';
$lang['ins_ok_title']				= 'Ok!';

// TOP MENU
$lang['ins_overview']				= 'Overview';
$lang['ins_license']				= 'License';
$lang['ins_install']				= 'Install';
$lang['ins_language_select']		= 'Select language';


// OVERVIEW PAGE
$lang['ins_install_title']			= 'Installation';
$lang['ins_title']					= 'Introduction';
$lang['ins_welcome']				= 'Welcome to XG Proyect!';
$lang['ins_welcome_first_line']		= 'XG Proyect is the best OGame clones around. XG Proyect 3 is the latest and most stable package never before developed. As any other version, XG Proyect receives support from the team known as Xtreme-gameZ, always making sure to get the best quality care and the stability of the release. XG Proyect 3 day by day looks forward and seeks growth, stability, flexibility, dynamism, quality and user confidence. We\'re always expecting that XG Proyect is better than your expectations.';
$lang['ins_welcome_second_line']	= 'The installation system will guide you through the installation or upgrading from a previous version to the latest. For doubts, problems o queries, do not hesitate to see our <a href="http://www.xgproyect.org/"><em>support and development community</em></a>.';
$lang['ins_welcome_third_line']		= 'XG Proyect is an OpenSource project, to see the license specifications click over license in the main menu. To start the installation click on the install button, to update or migrate log into the ADMIN CP.';
$lang['ins_install_license']		= 'License';

// INSTALL PAGE
// GENERAL
$lang['ins_steps']					= 'Steps';
$lang['ins_step1']					= 'Connection data';
$lang['ins_step2']					= 'Check connection';
$lang['ins_step3']					= 'Configuration file';
$lang['ins_step4']					= 'Insert data';
$lang['ins_step5']					= 'Create administrator';
$lang['ins_continue']				= 'Continue';

// STEP1
$lang['ins_connection_data_title']	= 'Data to connect to the database';
$lang['ins_server_title']			= 'SQL server:';
$lang['ins_db_title']				= 'Database:';
$lang['ins_user_title']				= 'User:';
$lang['ins_password_title']			= 'Password:';
$lang['ins_prefix_title']			= 'Tables prefix:';
$lang['ins_ex_tag']					= 'Ex:';
$lang['ins_install_go']				= 'Intall';

// ERRORS
$lang['ins_not_connected_error']	= 'Unable to connect to the database with the data entered.';
$lang['ins_db_not_exists']              = 'Unable to access the database with the provided name.';
$lang['ins_empty_fields_error']		= 'All fields are required';
$lang['ins_write_config_error']		= 'Error writing the config.php file, make sure it is 777 CHMOD (write permissions) or the file exists';
$lang['ins_insert_tables_error']	= 'Failed to insert data into the database, check the database or that the server is active.';

// STEP2
$lang['ins_done_config']			= 'config.php file successfully configurated.';
$lang['ins_done_connected']			= 'Connection succesfully stablished.';
$lang['ins_done_insert']			= 'Base data succesfully inserted.';

// STEP3
$lang['ins_admin_create_title']		= 'New administrator account';
$lang['ins_admin_create_user']		= 'User:';
$lang['ins_admin_create_pass']		= 'Password:';
$lang['ins_admin_create_email']		= 'Email address:';
$lang['ins_admin_create_create']	= 'Create';

// ERRORS
$lang['ins_adm_empty_fields_error']	    = 'All fields are required';
$lang['ins_adm_invalid_email_address']	= 'Please specify a valid email address';

// STEP 4
$lang['ins_completed']				= 'INSTALLATION COMPLETE!';
$lang['ins_admin_account_created']	= 'Administrator successfully created!';
$lang['ins_delete_install']			= 'You must delete the <i>install</i> directory to prevent security risks!';
$lang['ins_end']					= 'Finalize';
/* end of INSTALL.php */
