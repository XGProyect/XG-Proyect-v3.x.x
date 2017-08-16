<?php

// SOME MESSAGES
$lang['404_error']					= 'La p&aacute;gina solicitada no existe';
$lang['ins_no_server_requirements']	= 'Tu servidor/hosting no cumple con los requisitos m&iacute;nimos que requiere XG Proyect para funcionar.<br /><br />Requisitos: <br />- PHP 5.5.x<br />- MySQL 5.5.x';
$lang['ins_not_writable']               = 'Debes dar permisos de escritura (chmod 777) al directorio application/config para poder continuar con la instalación.';
$lang['ins_already_installed']		= 'XG Proyect ya se encuentra instalado. Selecciona una opci&oacute;n: <br /><br /> - <a href="../admin.php?page=update">Actualizar</a> <br /> - <a href="../admin.php?page=migrate">Migrar</a> <br /> - <a href="../">Volver al juego</a> <br /><br />En el caso de que no desees realizar ninguna acci&oacute;n, por seguridad, te recomendamos <span style="color:red;text-decoration:underline;">BORRAR</span> el directorio install.';

// SOME ERROR HEADERS
$lang['ins_error_title']			= '¡Alerta!';
$lang['ins_warning_title']			= '¡Advertencia!';
$lang['ins_ok_title']				= '¡Ok!';

// TOP MENU
$lang['ins_overview']				= 'Vista general';
$lang['ins_license']				= 'Licencia';
$lang['ins_install']				= 'Instalar';
$lang['ins_language_select']		= 'Seleccionar idioma';


// OVERVIEW PAGE
$lang['ins_title']					= 'Introducci&oacute;n';
$lang['ins_welcome']				= '&iexcl;Bienvenido a XG Proyect!';
$lang['ins_welcome_first_line']		= 'XG Proyect es el mejor clon de OGame existente hasta el momento. XG Proyect 3 es el &uacute;ltimo y m&aacute;s estable paquete nunca antes desarrollado. Tal cual como las otras versiones, XG Proyect recibe soporte del equipo antes conocido como Xtreme-gameZ, asegur&aacute;ndonos siempre de lograr la mejor calidad en atenci&oacute;n y la estabilidad de la versi&oacute;n. XG Proyect 3 d&iacute;a a d&iacute;a busca: crecimiento, estabilidad, flexibilidad, dinamismo, calidad y la confianza del usuario en que es su mejor opci&oacute;n y elecci&oacute;n. Siempre esperamos que XG Proyect sea mejor que tus expectativas.';
$lang['ins_welcome_second_line']	= 'El sistema de instalaci&oacute;n te guiar&aacute; a trav&eacute;s de la instalaci&oacute;n del mismo, o la actualizaci&oacute;n de una versi&oacute;n anterior a la m&aacute;s reciente. Cualquier duda, problema o consulta no dudes en consulta nuestra <a href="http://www.xgproyect.org/"><em>comunidad de desarrollo y soporte</em></a>.';
$lang['ins_welcome_third_line']		= 'XG Proyect es un proyecto OpenSource (c&oacute;digo abierto), para ver las especificaciones de la licencia haz click en licencia en la barra superior. Para comenzar la instalaci&oacute;n haz click en el bot&oacute;n instalar, para actualizar a la versi&oacute;n m&aacute;s reciente o migrar deber&aacute;s iniciar sesi&oacute;n en el ADMIN CP.';
$lang['ins_install_license']		= 'Licencia';

// INSTALL PAGE
// GENERAL
$lang['ins_steps']					= 'Pasos';
$lang['ins_step1']					= 'Datos de conexión';
$lang['ins_step2']					= 'Verificar conexión';
$lang['ins_step3']					= 'Archivo de configuración';
$lang['ins_step4']					= 'Inserción de datos';
$lang['ins_step5']					= 'Crear Administrador';
$lang['ins_continue']				= 'Continuar';

// STEP1
$lang['ins_install_title']			= 'Instalación';
$lang['ins_connection_data_title']	= 'Datos para la conexi&oacute;n con la Base de Datos';
$lang['ins_server_title']			= 'Servidor SQL:';
$lang['ins_db_title']				= 'Base de datos:';
$lang['ins_user_title']				= 'Usuario:';
$lang['ins_password_title']			= 'Contraseña:';
$lang['ins_prefix_title']			= 'Prefix de las tablas:';
$lang['ins_ex_tag']					= 'Ej:';
$lang['ins_install_go']				= 'Instalar';

// ERRORS
$lang['ins_not_connected_error']	= 'No fue posible conectarse a la base de datos con los datos ingresados.';
$lang['ins_db_not_exists']              = 'No se pudo acceder a la base de datos con el nombre establecido.';
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
$lang['ins_adm_empty_fields_error']	    = 'Todos los campos son obligatorios';
$lang['ins_adm_invalid_email_address']	= 'Por favor, especifica una dirección de correo válida';

// STEP 4
$lang['ins_completed']				= '&iexcl;INSTALACI&Oacute;N FINALIZADA!';
$lang['ins_admin_account_created']	= 'El Administrador ha sido creado correctamente.';
$lang['ins_delete_install']			= '&iexcl;Ahora debes borrar la carpeta <i>install</i> asi evitaras problemas graves de seguridad!';
$lang['ins_end']					= 'Finalizar';
/* end of INSTALL.php */
