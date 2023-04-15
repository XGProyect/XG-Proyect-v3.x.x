<?php
$lang = [
    // messages
    'ins_no_server_requirements' => 'Dein Server / Hosting erfüllt nicht die Mindestanforderungen, die für die Ausführung von kmpr.at-Game erforderlich sind.<br /><br />Anforderungen: <br />- PHP 7.3+<br />- MySQL 5.5+',
    'ins_not_writable' => 'Du musst dem app/config-Verzeichnis eine Schreibberechtigung (chmod 777) geben, um die Installation fortzusetzen.',
    'ins_already_installed' => 'kmpr.at ist bereits installiert. Wähle eine Option: <br /><br /> - <a href="../admin.php?page=update">Update</a> <br /> - <a href="../admin.php?page=migrate">Migrieren</a> <br /> - <a href="../">Zurück zum Spiel</a> <br /><br />Falls du keine Maßnahmen ergreifen möchten, empfehlen wir dir <span style="color:red;text-decoration:underline;">LÖSCHE</span> das Installationsverzeichnis.',

    // error headers
    'ins_error_title' => 'Fehler!',
    'ins_warning_title' => 'Warnung!',
    'ins_ok_title' => 'Ok!',

    // navigation bar
    'ins_overview' => 'Übersicht',
    'ins_license' => 'Lizenz',
    'ins_install' => 'Installieren',
    'ins_language_select' => 'Sprache wählen',

    // overview page
    'ins_install_title' => 'Installation',
    'ins_title' => 'Einleitung',
    'ins_welcome' => 'Willkommen bei kmpr.at Game!',
    'ins_welcome_first_line' => 'kmpr.at Game ist der beste OGame-Klon, den es gibt. kmpr.at Game 3 ist das neueste und stabilste Paket, das noch nie zuvor entwickelt wurde. Wie jede andere Version erhält kmpr.at Game Unterstützung von dem als Xtreme-gameZ bekannten Team, das immer darauf achtet, die beste Qualität und die Stabilität der Veröffentlichung zu erhalten. kmpr.at Game 3 blickt Tag für Tag nach vorne und strebt nach Wachstum, Stabilität, Flexibilität, Dynamik, Qualität und Benutzervertrauen. Wir erwarten immer, dass kmpr.at Game besser ist als deine Erwartungen.',
    'ins_welcome_second_line' => 'Das Installationssystem führt dich durch die Installation oder das Upgrade von einer früheren Version auf die neueste. Bei Zweifeln, Problemen oder Fragen wende dich bitte an unsere <a href="https://git.kmpr.at/kmpr.at/kmpr.at-Game"><em>Support- und Entwicklungs-Community</em></a>.',
    'ins_welcome_third_line' => 'kmpr.at Game ist ein OpenSource-Projekt, um die Lizenzdetails anzuzeigen, klicke im Hauptmenü auf Lizenz. Um die Installation zu starten, klicke auf die Schaltfläche Installieren, zum Aktualisieren oder Migrieren melde dich beim ADMIN CP an.',
    'ins_install_license' => 'Lizenz',

    // installation - general
    'ins_steps' => 'Schritte',
    'ins_step1' => 'Verbindungsdaten',
    'ins_step2' => 'Verbindung prüfen',
    'ins_step3' => 'Konfigurationsdatei',
    'ins_step4' => 'Daten einfügen',
    'ins_step5' => 'Administrator erstellen',
    'ins_continue' => 'Weiter',

    // installation - step 1
    'ins_connection_data_title' => 'Daten zur Verbindung mit der Datenbank',
    'ins_server_title' => 'SQL Server:',
    'ins_db_title' => 'Datenbank:',
    'ins_user_title' => 'Benutzer:',
    'ins_password_title' => 'Passwort:',
    'ins_prefix_title' => 'Tabellenpräfixe:',
    'ins_ex_tag' => 'Bsp:',
    'ins_install_go' => 'Installieren',

    // installation - errors
    'ins_not_connected_error' => 'Mit den eingegebenen Daten kann keine Verbindung zur Datenbank hergestellt werden.',
    'ins_db_not_exists' => 'Zugriff auf die Datenbank mit dem angegebenen Namen nicht möglich.',
    'ins_empty_fields_error' => 'Alle Felder sind erforderlich',
    'ins_write_config_error' => 'Fehler beim Schreiben der config.php-Datei, stellen Sie sicher, dass es 777 CHMOD ist (Schreibrechte) und die Datei existiert',
    'ins_insert_tables_error' => 'Fehler beim Einfügen von Daten in die Datenbank, überprüfen Sie die Datenbank oder ob der Server aktiv ist.',

    // installation -  step 2
    'ins_done_config' => 'config.php Datei erfolgreich konfiguriert.',
    'ins_done_connected' => 'Verbindung erfolgreich hergestellt.',
    'ins_done_insert' => 'Basisdaten erfolgreich eingefügt.',

    // installation - step 3
    'ins_admin_create_title' => 'Neues Administratorkonto',
    'ins_admin_create_user' => 'Benutzer:',
    'ins_admin_create_pass' => 'Passwort:',
    'ins_admin_create_email' => 'E-Mmail Adresse:',
    'ins_admin_create_create' => 'Erstellen',

    // installation - errors
    'ins_adm_empty_fields_error' => 'Alle Felder sind erforderlich',
    'ins_adm_invalid_email_address' => 'Bitte geben Sie eine gültige E-Mail-Adresse an',

    // installation - step 4
    'ins_completed' => 'INSTALLATION VOLLSTÄNDIG!',
    'ins_admin_account_created' => 'Administrator erfolgreich erstellt!',
    'ins_delete_install' => 'Sie müssen das Verzeichnis <i>install</i> löschen, um Sicherheitsrisiken zu vermeiden!',
    'ins_end' => 'Abschließen',
];
