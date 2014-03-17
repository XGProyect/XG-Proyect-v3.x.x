<?php

// SOME MESSAGES
$lang['404_error']					= 'The requested page doesn\'t exists';
$lang['ins_no_server_requirements']	= 'Your server / hosting does not meet the minimum requirements needed to run XG Proyect.<br /><br />Requisitos: <br />- PHP 5.3.0<br />- MySQL 5.0.19';
$lang['ins_already_installed']		= 'XG Proyect is already installed. Select an option: <br /><br /> - <a href="index.php?page=update">Update</a> <br /> - <a href="index.php?page=migrate">Migrate</a> <br /> - <a href="../">Back to the game</a> <br /><br />In case you do not want to take any action, for safety, we recommend <span style="color:red;text-decoration:underline;">DELETE</span> the install directory.';
$lang['ins_missing_xml_file']		= 'Could not find the config.xml file or the file config.xml.cfg, you must have one of these in order to continue with the installation. Check your application/config directory and the permissions must be set on chmod 777';

// SOME ERROR HEADERS
$lang['ins_error_title']			= 'Alert!';
$lang['ins_warning_title']			= 'Warning!';
$lang['ins_ok_title']				= 'Ok!';

// TOP MENU
$lang['ins_overview']				= 'Overview';
$lang['ins_license']				= 'License';
$lang['ins_install']				= 'Install';
$lang['ins_update']					= 'Update';
$lang['ins_migrate']				= 'Migrate';
$lang['ins_language_select']		= 'Select language';


// OVERVIEW PAGE
$lang['ins_title']					= 'Introduction';
$lang['ins_welcome']				= 'Welcome to XG Proyect!';
$lang['ins_welcome_first_line']		= 'XG Proyect is one of the best OGame clones. XG Proyect 3 is the last and most stable package never before developed. As any other version, XG Proyect receives support from the team known as Xtreme-gameZ, always making sure to get the best quality care and the stability of the release.. XG Proyect 3 day by day looks forward; growth, stability, flexibility, dynamism, quality and user confidence. We\'re always expecting that XG Proyect is better than your expectations.';
$lang['ins_welcome_second_line']	= 'The installation system will guide you through the installation or upgrading from a previous version to the latest. For doubts, problems o queries, do not hesitate to see our <a href="http://www.xgproyect.net/"><em>support and development community</em></a>.';
$lang['ins_welcome_third_line']		= 'XG Proyect is an OpenSource project, to see the license specifications click over license in the main menu. To start the installation click on the install button or click on the update button to start the upgrade to a newest version.';
$lang['ins_install_license']		= 'License';

// INSTALL PAGE
// GENERAL
$lang['ins_steps']					= 'Steps';
$lang['ins_step1']					= 'Connection data';
$lang['ins_step2']					= 'Configuration file';
$lang['ins_step3']					= 'Check connectiom';
$lang['ins_step4']					= 'Insert data';
$lang['ins_step5']					= 'Create administrator';
$lang['ins_continue']				= 'Continue';

// STEP1
$lang['ins_connection_data_title']	= 'Data to connect to the database';
$lang['ins_chmod_notice']			= 'Before installing change the permissions of the config.php file to "CHMOD 777"';
$lang['ins_server_title']			= 'SQL server:';
$lang['ins_db_title']				= 'Database:';
$lang['ins_user_title']				= 'User:';
$lang['ins_password_title']			= 'Password:';
$lang['ins_prefix_title']			= 'Tables prefix:';
$lang['ins_ex_tag']					= 'Ex:';
$lang['ins_install_go']				= 'Intall';

// ERRORS
$lang['ins_not_connected_error']	= 'Unable to connect to the database with the data entered.';
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
$lang['ins_adm_empty_fields_eror']	= 'All fields are required';

// STEP 4
$lang['ins_completed']				= 'INSTALLATION COMPLETE!';
$lang['ins_admin_account_created']	= 'Administrator successfully created!';
$lang['ins_delete_install']			= 'You must delete the <i>install</i> directory to prevent security risks!';
$lang['ins_end']					= 'Finalize';

// UPDATE PAGE
// Administator login
$lang['ins_update_title']			= 'Update';
$lang['ins_update_admin_data']		= 'Administrator data';
$lang['ins_update_admin_email']		= 'Email address';
$lang['ins_update_admin_password']	= 'Password';
$lang['ins_update_start']			= 'Login and Update to version';

// MIGRATION PAGE
// Administator login
$lang['ins_migrate_title']			= 'Migrate';
$lang['ins_migrate_admin_data']		= 'Administrator data';
$lang['ins_migrate_admin_email']	= 'Email address';
$lang['ins_migrate_admin_password']	= 'Password';
$lang['ins_migrate_start']			= 'Login';
/* end of INSTALL.php */