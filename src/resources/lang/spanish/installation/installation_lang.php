<?php

$lang = [
    // messages
    'lang_code' => 'es',
    'ins_no_server_requirements' => 'Tu servidor/hosting no cumple con los requisitos m&iacute;nimos que requiere XG Proyect para funcionar.<br /><br />Requisitos: <br />- PHP 7.3+<br />- MySQL 5.5+',
    'ins_not_writable' => 'Debes dar permisos de escritura (chmod 777) al directorio config para poder continuar con la instalación.',
    'ins_already_installed' => 'XG Proyect ya se encuentra instalado. Selecciona una opci&oacute;n: <br /><br /> - <a href="../admin.php?page=update">Actualizar</a> <br /> - <a href="../admin.php?page=migrate">Migrar</a> <br /> - <a href="../">Volver al juego</a> <br /><br />En el caso de que no desees realizar ninguna acci&oacute;n, por seguridad, te recomendamos <span style="color:red;text-decoration:underline;">BORRAR</span> el directorio install.',

    // error headers
    'ins_error_title' => '¡Alerta!',
    'ins_warning_title' => '¡Advertencia!',
    'ins_ok_title' => '¡Ok!',

    // navigation bar
    'ins_overview' => 'Vista general',
    'ins_license' => 'Licencia',
    'ins_install' => 'Instalar',
    'ins_language_select' => 'Seleccionar idioma',

    // overview page
    'ins_title' => 'Introducci&oacute;n',
    'ins_welcome' => '&iexcl;Bienvenido a XG Proyect!',
    'ins_welcome_first_line' => 'XG Proyect es el mejor clon de OGame existente hasta el momento. XG Proyect 3 es el &uacute;ltimo y m&aacute;s estable paquete nunca antes desarrollado. Tal cual como las otras versiones, XG Proyect recibe soporte del equipo antes conocido como Xtreme-gameZ, asegur&aacute;ndonos siempre de lograr la mejor calidad en atenci&oacute;n y la estabilidad de la versi&oacute;n. XG Proyect 3 d&iacute;a a d&iacute;a busca: crecimiento, estabilidad, flexibilidad, dinamismo, calidad y la confianza del usuario en que es su mejor opci&oacute;n y elecci&oacute;n. Siempre esperamos que XG Proyect sea mejor que tus expectativas.',
    'ins_welcome_second_line' => 'El sistema de instalaci&oacute;n te guiar&aacute; a trav&eacute;s de la instalaci&oacute;n del mismo, o la actualizaci&oacute;n de una versi&oacute;n anterior a la m&aacute;s reciente. Cualquier duda, problema o consulta no dudes en consulta nuestra <a href="https://www.xgproyect.org/"><em>comunidad de desarrollo y soporte</em></a>.',
    'ins_welcome_third_line' => 'XG Proyect es un proyecto OpenSource (c&oacute;digo abierto), para ver las especificaciones de la licencia haz click en licencia en la barra superior. Para comenzar la instalaci&oacute;n haz click en el bot&oacute;n instalar, para actualizar a la versi&oacute;n m&aacute;s reciente o migrar deber&aacute;s iniciar sesi&oacute;n en el ADMIN CP.',
    'ins_install_license' => 'Licencia',

    // installation - general
    'ins_steps' => 'Pasos',
    'ins_step1' => 'Datos de conexión',
    'ins_step2' => 'Verificar conexión',
    'ins_step3' => 'Archivo de configuración',
    'ins_step4' => 'Inserción de datos',
    'ins_step5' => 'Crear Administrador',
    'ins_continue' => 'Continuar',

    // installation - step 1
    'ins_install_title' => 'Instalación',
    'ins_connection_data_title' => 'Datos para la conexi&oacute;n con la Base de Datos',
    'ins_server_title' => 'Servidor SQL:',
    'ins_db_title' => 'Base de datos:',
    'ins_user_title' => 'Usuario:',
    'ins_password_title' => 'Contraseña:',
    'ins_prefix_title' => 'Prefix de las tablas:',
    'ins_ex_tag' => 'Ej:',
    'ins_install_go' => 'Instalar',

    // installation - errors
    'ins_not_connected_error' => 'No fue posible conectarse a la base de datos con los datos ingresados.',
    'ins_db_not_exists' => 'No se pudo acceder a la base de datos con el nombre establecido.',
    'ins_empty_fields_error' => 'Todos los campos son obligatorios',
    'ins_write_config_error' => 'Error al escribir el archivo config.php, aseg&uacute;rese de que sea CHMOD 777 (permisos de escritura) o que exista el archivo config.php',
    'ins_insert_tables_error' => 'Error al insertar datos en la base de datos, verifique los datos o que el servidor este activo.',

    // installation -  step 2
    'ins_done_config' => 'Archivo config.php configurado con &eacute;xito.',
    'ins_done_connected' => 'Conexi&oacute;n establecida con &eacute;xito.',
    'ins_done_insert' => 'Datos base insertados en la base de datos con &eacute;xito.',

    // installation - step 3
    'ins_admin_create_title' => 'Establecer cuenta de administraci&oacute;n',
    'ins_admin_create_user' => 'Usuario:',
    'ins_admin_create_pass' => 'Contrase&ntilde;a:',
    'ins_admin_create_email' => 'Correo electr&oacute;nico:',
    'ins_admin_create_create' => 'Crear',

    // installation - errors
    'ins_adm_empty_fields_error' => 'Todos los campos son obligatorios',
    'ins_adm_invalid_email_address' => 'Por favor, especifica una dirección de correo válida',

    // installation - step 4
    'ins_completed' => '&iexcl;INSTALACI&Oacute;N FINALIZADA!',
    'ins_admin_account_created' => 'El Administrador ha sido creado correctamente.',
    'ins_delete_install' => '&iexcl;Ahora debes borrar la carpeta <i>install</i> asi evitaras problemas graves de seguridad!',
    'ins_end' => 'Finalizar',
];
