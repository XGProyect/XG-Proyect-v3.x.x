<?php

$lang = [
    // messages
    'lang_code' => 'en',
    'ins_no_server_requirements' => 'Your server / hosting does not meet the minimum requirements needed to run XG Proyect.<br /><br />Requirements: <br />- PHP 7.3+<br />- MySQL 5.5+',
    'ins_not_writable' => 'You must provide write permission (chmod 777) to the config directory to continue with the installation.',
    'ins_already_installed' => 'XG Proyect is already installed. Select an option: <br /><br /> - <a href="../admin.php?page=update">Update</a> <br /> - <a href="../admin.php?page=migrate">Migrate</a> <br /> - <a href="../">Back to the game</a> <br /><br />In case you do not want to take any action, for safety, we recommend <span style="color:red;text-decoration:underline;">DELETE</span> the install directory.',

    // error headers
    'ins_error_title' => 'Alert!',
    'ins_warning_title' => 'Warning!',
    'ins_ok_title' => 'Ok!',

    // navigation bar
    'ins_overview' => 'Overview',
    'ins_license' => 'License',
    'ins_install' => 'Install',
    'ins_language_select' => 'Select language',

    // overview page
    'ins_install_title' => 'Installation',
    'ins_title' => 'Introduction',
    'ins_welcome' => 'Welcome to XG Proyect!',
    'ins_welcome_first_line' => 'XG Proyect is the best OGame clones around. XG Proyect 3 is the latest and most stable package never before developed. As any other version, XG Proyect receives support from the team known as Xtreme-gameZ, always making sure to get the best quality care and the stability of the release. XG Proyect 3 day by day looks forward and seeks growth, stability, flexibility, dynamism, quality and user confidence. We\'re always expecting that XG Proyect is better than your expectations.',
    'ins_welcome_second_line' => 'The installation system will guide you through the installation or upgrading from a previous version to the latest. For doubts, problems o queries, do not hesitate to see our <a href="https://www.xgproyect.org/"><em>support and development community</em></a>.',
    'ins_welcome_third_line' => 'XG Proyect is an OpenSource project, to see the license specifications click over license in the main menu. To start the installation click on the install button, to update or migrate log into the ADMIN CP.',
    'ins_install_license' => 'License',

    // installation - general
    'ins_steps' => 'Steps',
    'ins_step1' => 'Connection data',
    'ins_step2' => 'Check connection',
    'ins_step3' => 'Configuration file',
    'ins_step4' => 'Insert data',
    'ins_step5' => 'Create administrator',
    'ins_continue' => 'Continue',

    // installation - step 1
    'ins_connection_data_title' => 'Data to connect to the database',
    'ins_server_title' => 'SQL server:',
    'ins_db_title' => 'Database:',
    'ins_user_title' => 'User:',
    'ins_password_title' => 'Password:',
    'ins_prefix_title' => 'Tables prefix:',
    'ins_ex_tag' => 'Ex:',
    'ins_install_go' => 'Install',

    // installation - errors
    'ins_not_connected_error' => 'Unable to connect to the database with the data entered.',
    'ins_db_not_exists' => 'Unable to access the database with the provided name.',
    'ins_empty_fields_error' => 'All fields are required',
    'ins_write_config_error' => 'Error writing the config.php file, make sure it is 777 CHMOD (write permissions) or the file exists',
    'ins_insert_tables_error' => 'Failed to insert data into the database, check the database or that the server is active.',

    // installation -  step 2
    'ins_done_config' => 'config.php file successfully configurated.',
    'ins_done_connected' => 'Connection succesfully stablished.',
    'ins_done_insert' => 'Base data succesfully inserted.',

    // installation - step 3
    'ins_admin_create_title' => 'New administrator account',
    'ins_admin_create_user' => 'User:',
    'ins_admin_create_pass' => 'Password:',
    'ins_admin_create_email' => 'Email address:',
    'ins_admin_create_create' => 'Create',

    // installation - errors
    'ins_adm_empty_fields_error' => 'All fields are required',
    'ins_adm_invalid_email_address' => 'Please specify a valid email address',

    // installation - step 4
    'ins_completed' => 'INSTALLATION COMPLETE!',
    'ins_admin_account_created' => 'Administrator successfully created!',
    'ins_delete_install' => 'You must delete the <i>install</i> directory to prevent security risks!',
    'ins_end' => 'Finalize',
];
