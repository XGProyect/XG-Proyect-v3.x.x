<?php

// SOME MESSAGES
$lang['404_error']					= 'La p&aacute;gina solicitada no existe';
$lang['ins_no_server_requirements']	= 'Tu servidor/hosting no cumple con los requisitos m&iacute;nimos que requiere XG Proyect para funcionar.<br /><br />Requisitos: <br />- PHP 5.2.0<br />- MySQL 5.0.19';
$lang['ins_already_installed']		= 'XG Proyect ya se encuentra instalado. Selecciona una opci&oacute;n: <br /><br /> - <a href="index.php?page=update">Actualizar</a> <br /> - <a href="index.php?page=migrate">Migrar</a> <br /> - <a href="../">Volver al juego</a> <br /><br />En el caso de que no desees realizar ninguna acci&oacute;n, por seguridad, te recomendamos <span style="color:red;text-decoration:underline;">BORRAR</span> el directorio install.';
$lang['ins_missing_xml_file']		= 'No se puede encontrar el archivo config.xml ni el archivo config.xml.cfg, debes tener alguno de estos para poder continuar con la instalación. Revisa tu directorio application/config y los permisos del mismo deben estar en chmod 777';

// SOME ERROR HEADERS
$lang['ins_error_title']			= '¡Alerta!';
$lang['ins_warning_title']			= '¡Advertencia!';
$lang['ins_ok_title']				= '¡Ok!';

// TOP MENU
$lang['ins_overview']				= 'Vista general';
$lang['ins_license']				= 'Licencia';
$lang['ins_install']				= 'Instalar';
$lang['ins_update']					= 'Actualizar';
$lang['ins_migrate']				= 'Migrar';
$lang['ins_language_select']		= 'Seleccionar idioma';


// OVERVIEW PAGE
$lang['ins_title']					= 'Introducci&oacute;n';
$lang['ins_welcome']				= '&iexcl;Bienvenido a XG Proyect!';
$lang['ins_welcome_first_line']		= 'XG Proyect es uno de los mejores clones de OGame existentes hasta el momento. XG Proyect 3 es el &uacute;ltimo y m&aacute;s estable paquete nunca antes desarrollado. Tal cual como las otras versiones, XG Proyect recibe soporte del equipo antes conocido como Xtreme-gameZ, asegur&aacute;ndonos siempre de lograr la mejor calidad en atenci&oacute;n y la estabilidad de la versi&oacute;n. XG Proyect 3 d&iacute;a a d&iacute;a busca; crecimiento, estabilidad, flexibilidad, dinamismo, calidad y la confianza del usuario en que es su mejor opci&oacute;n y elecci&oacute;n. Siempre esperamos que XG Proyect sea mejor que sus expectativas.';
$lang['ins_welcome_second_line']	= 'El sistema de instalaci&oacute;n te guiar&aacute; a trav&eacute;s de la instalaci&oacute;n del mismo, o la actualizaci&oacute;n de una versi&oacute;n anterior a la m&aacute;s reciente. Cualquier duda, problema o consulta no dudes en consulta nuestra <a href="http://www.xgproyect.net/"><em>comunidad de desarrollo y soporte</em></a>.';
$lang['ins_welcome_third_line']		= 'XG Proyect es un proyecto OpenSource (c&oacute;digo abierto), para ver las especificaciones de la licencia haz click en licencia en la barra superior. Para comenzar la instalaci&oacute;n haz click en el bot&oacute;n instalar o para actualizar a la versi&oacute;n m&aacute;s nueva haz click en el bot&oacute;n actualizar.';
$lang['ins_install_license']		= 'Licencia';

// INSTALL PAGE
// GENERAL
$lang['ins_steps']					= 'Pasos';
$lang['ins_step1']					= 'Datos de conexión';
$lang['ins_step2']					= 'Archivo de configuración';
$lang['ins_step3']					= 'Verificar conexión';
$lang['ins_step4']					= 'Inserción de datos';
$lang['ins_step5']					= 'Crear Administrador';
$lang['ins_continue']				= 'Continuar';

// STEP1
$lang['ins_install_title']			= 'Instalación';
$lang['ins_connection_data_title']	= 'Datos para la conexi&oacute;n con la Base de Datos';
$lang['ins_chmod_notice']			= 'Antes de instalar cambie los permisos del archivo config.php a "CHMOD 777"';
$lang['ins_server_title']			= 'Servidor SQL:';
$lang['ins_db_title']				= 'Base de datos:';
$lang['ins_user_title']				= 'Usuario:';
$lang['ins_password_title']			= 'Contraseña:';
$lang['ins_prefix_title']			= 'Prefix de las tablas:';
$lang['ins_ex_tag']					= 'Ej:';
$lang['ins_install_go']				= 'Instalar';

// ERRORS
$lang['ins_not_connected_error']	= 'No fue posible conectarse a la base de datos con los datos ingresados.';
$lang['ins_empty_fields_error']		= 'Todos los campos son obligatorios';
$lang['ins_write_config_error']		= 'Error al escribir el archivo config.php, aseg&uacute;rese de que sea CHMOD 777 (permisos de escritura) o que exista el archivo config.php';
$lang['ins_insert_tables_error']	= 'Error al insertar datos en la base de datos, verifique los datos o que el servidor este activo.';

// STEP2
$lang['ins_done_config']			= 'Archivo config.php configurado con &eacute;xito.';
$lang['ins_done_connected']			= 'Conexi&oacute;n establecida con &eacute;xito.';
$lang['ins_done_insert']			= 'Datos base insertados en la base de datos con &eacute;xito.';

// STEP3
$lang['ins_admin_create_title']		= 'Establecer cuenta de administraci&oacute;n';
$lang['ins_admin_create_user']		= 'Usuario:';
$lang['ins_admin_create_pass']		= 'Contrase&ntilde;a:';
$lang['ins_admin_create_email']		= 'Correo electr&oacute;nico:';
$lang['ins_admin_create_create']	= 'Crear';

// ERRORS
$lang['ins_adm_empty_fields_eror']	= 'Todos los campos son obligatorios';

// STEP 4
$lang['ins_completed']				= '&iexcl;INSTALACI&Oacute;N FINALIZADA!';
$lang['ins_admin_account_created']	= 'El Administrador ha sido creado correctamente.';
$lang['ins_delete_install']			= '&iexcl;Ahora debes borrar la carpeta <i>install</i> asi evitaras problemas graves de seguridad!';
$lang['ins_end']					= 'Finalizar';

// UPDATE PAGE
// Administator login
$lang['ins_update_title']			= 'Actualizar';
$lang['ins_update_admin_data']		= 'Datos del administrador';
$lang['ins_update_admin_email']		= 'Correo electrónico';
$lang['ins_update_admin_password']	= 'Contraseña';
$lang['ins_update_start']			= 'Ingresar y Actualizar a la versión';

// MIGRATION PAGE
// Administator login
$lang['ins_migrate_title']			= 'Migrar';
$lang['ins_migrate_admin_data']		= 'Datos del administrador';
$lang['ins_migrate_admin_email']	= 'Correo electrónico';
$lang['ins_migrate_admin_password']	= 'Contraseña';
$lang['ins_migrate_start']			= 'Ingresar';
/* end of INSTALL.php */