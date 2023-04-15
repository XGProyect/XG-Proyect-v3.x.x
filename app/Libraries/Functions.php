<?php

namespace App\Libraries;

use App\Core\Database;
use App\Core\Enumerators\MessagesEnumerator;
use App\Core\Language;
use App\Core\Options;
use App\Core\Template;
use App\Helpers\StringsHelper;
use App\Libraries\Messenger\MessagesFormat;
use App\Libraries\Messenger\MessagesOptions;
use App\Libraries\Messenger\Messenger;
use App\Libraries\Page;
use App\Libraries\Users;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

abstract class Functions
{
    /**
     * Return a new instance of Template
     *
     * @return Template
     */
    public static function getTemplate(): Template
    {
        return new Template();
    }

    /**
     * loadLibrary
     *
     * @param string $library Library
     *
     * @return boolean
     */
    public static function loadLibrary($library = '')
    {
        if (!empty($library)) {
            // Require file
            require_once XGP_ROOT . LIB_PATH . $library . '.php';

            $class_name = 'App\Libraries\\' . $library;

            // Create new $library object
            return new $class_name();
        } else {
            // ups!
            return false;
        }
    }

    /**
     * chronoApplet
     *
     * @param string  $type  Type
     * @param string  $ref   Ref
     * @param string  $value Value
     * @param boolean $init  Init
     *
     * @return string
     */
    public static function chronoApplet($type, $ref, $value, $init)
    {
        if ($init == true) {
            $template = 'general/chrono_applet_init';
        } else {
            $template = 'general/chrono_applet';
        }

        $parse['type'] = $type;
        $parse['ref'] = $ref;
        $parse['value'] = $value;

        return self::getTemplate()->set(
            $template,
            $parse
        );
    }

    /**
     * readConfig
     *
     * @param string  $config_name Config name
     * @param boolean $all         All
     *
     * @return string
     */
    public static function readConfig($config_name = '', $all = false)
    {
        $configs = Options::getInstance();

        if ($all) {
            foreach ($configs->getOptions() as $row) {
                $return[$row['option_name']] = $row['option_value'];
            }

            return $return;
        } else {
            return $configs->getOptions($config_name);
        }
    }

    /**
     * updateConfig
     *
     * @param string $config_name  Config name
     * @param string $config_value Config value
     *
     * @return string
     */
    public static function updateConfig($config_name, $config_value)
    {
        return Options::getInstance()->writeOptions($config_name, $config_value);
    }

    /**
     * validEmail
     *
     * @param string $address Email address
     *
     * @return string
     */
    public static function validEmail($address)
    {
        return (!preg_match(
            "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",
            $address
        )) ? false : true;
    }

    /**
     * fleetSpeedFactor
     *
     * @return string
     */
    public static function fleetSpeedFactor()
    {
        return self::readConfig('fleet_speed') / 2500;
    }

    /**
     * message
     *
     * @param string  $mes    Message
     * @param string  $dest   Redirect destination
     * @param string  $time   Time to redirect
     * @param boolean $topnav Show top navigation
     * @param boolean $menu   Show menu
     * @param boolean $center Center message
     *
     * @return void
     */
    public static function message($mes, $dest = '', $time = '3', $topnav = false, $menu = true, $center = true)
    {
        define('IN_MESSAGE', true);

        $parse['mes'] = $mes;
        $parse['middle1'] = '';
        $parse['middle2'] = '';

        if ($center) {
            $parse['middle1'] = '<div id="content">';
            $parse['middle2'] = '</div>';
        }

        (new Page(new Users()))->display(
            self::getTemplate()->set('general/message_body', $parse),
            $topnav,
            (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"$time;URL=$dest\">" : ""),
            $menu
        );
    }

    /**
     * isModuleAccesible
     *
     * @param int $module_id Module ID
     *
     * @return array
     */
    public static function isModuleAccesible($module_id = 0)
    {
        $modules_array = self::readConfig('modules');
        $modules_array = explode(';', $modules_array);

        if ($module_id == 0) {
            return $modules_array;
        } else {
            return $modules_array[$module_id];
        }
    }

    /**
     * moduleMessage
     *
     * @param int $access_level Access level
     *
     * @return void
     */
    public static function moduleMessage($access_level)
    {
        if ($access_level == 0) {
            $lang = new Language();
            die(self::message($lang->loadLang('game/global', true)->line('module_not_accesible'), '', '', true));
        }
    }

    /**
     * sendMessage
     *
     * @param int    $to        To
     * @param int    $sender    Sender
     * @param int    $time      Time
     * @param int    $type      Type
     * @param string $from      From
     * @param string $subject   Subject
     * @param string $message   Message
     * @param bool   $allowHtml Allow HTML
     *
     * @return void
     */
    public static function sendMessage($to, $sender, $time = '', $type = '', $from = '', $subject = '', $message = '', $allowHtml = false)
    {
        $options = new MessagesOptions();
        $options->setTo($to);
        $options->setSender($sender);
        $options->setTime($time);

        switch ($type) {
            case 0:
                $type = MessagesEnumerator::ESPIO;
                break;
            case 1:
                $type = MessagesEnumerator::COMBAT;
                break;
            case 2:
                $type = MessagesEnumerator::EXP;
                break;
            case 3:
                $type = MessagesEnumerator::ALLY;
                break;
            case 4:
                $type = MessagesEnumerator::USER;
                break;
            default:
            case 5:
                $type = MessagesEnumerator::GENERAL;
                break;
        }

        $options->setType($type);
        $options->setFrom($from);
        $options->setSubject($subject);

        if ($allowHtml) {
            $options->setMessageFormat(MessagesFormat::HTML);
        }

        $options->setMessageText($message);

        $messenger = new Messenger();
        $messenger->sendMessage($options);
    }

    /**
     * Send and email
     *
     * @param string $to      Mail To
     * @param string $subject Mail Subject
     * @param string $body    Mail Body
     * @param array  $from    Mail From
     * @param mixed  $headers Mail headers (optional)
     *
     * @return mixed
     */
    public static function sendEmail($to, $subject, $body, $from, $format = 'text', $headers = '')
    {
        try {
            // require email library
            $mailLibPath = XGP_ROOT . VENDOR_PATH . 'phpmailer/phpmailer';

            if (!file_exists($mailLibPath) or !function_exists('mail')) {
                return false;
            }

            require_once $mailLibPath. '/src/Exception.php';
            require_once $mailLibPath. '/src/PHPMailer.php';
            require_once $mailLibPath. '/src/SMTP.php';

            $mail = new PHPMailer();

            // mailing settings
            $mail->protocol = self::readConfig('mailing_protocol');

            if (self::readConfig('mailing_protocol') === 'smtp') {
                $mail->isSMTP();
                $mail->Host = !empty(self::readConfig('mailing_smtp_host')) ? self::readConfig('mailing_smtp_host') : null;
                $mail->Port = !empty(self::readConfig('mailing_smtp_port')) ? self::readConfig('mailing_smtp_port') : null;
                $mail->SMTPSecure = !empty(self::readConfig('mailing_smtp_crypto')) ? self::readConfig('mailing_smtp_crypto') : null;
                $mail->Timeout = !empty(self::readConfig('mailing_smtp_timeout')) ? self::readConfig('mailing_smtp_timeout') : null;

                if (
                    !empty(self::readConfig('mailing_smtp_user')) &&
                    !empty(self::readConfig('mailing_smtp_pass'))
                ) {
                    $mail->SMTPAuth   = true;
                    $mail->Username   = self::readConfig('mailing_smtp_user');
                    $mail->Password   = self::readConfig('mailing_smtp_pass');
                }
            }

            if ($format === 'html') {
                $mail->isHTML(true);
            }

            // from
            if (is_array($from)) {
                $mail->setFrom($from['mail'], $from['name']);
            }

            // to
            if (is_array($to)) {
                foreach ($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }

            // headers
            if (is_array($headers)) {
                foreach ($headers as $header => $value) {
                    $mail->addCustomHeader($header, $value);
                }
            }

            // subject
            $mail->Subject = $subject;

            // message body
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            // send!
            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * getDefaultVacationTime
     *
     * @return int
     */
    public static function getDefaultVacationTime()
    {
        return (time() + (3600 * 24 * VACATION_TIME_FORCED));
    }

    /**
     * setImage
     *
     * @param string $path       Image path
     * @param string $title      Title
     * @param string $attributes Attributes - css & js
     *
     * @return string
     */
    public static function setImage($path, $title = 'img', $attributes = '')
    {
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        return '<img src="' . $path . '" title="' . $title . '" border="0"' . $attributes . '>';
    }

    /**
     * redirect
     *
     * @param string $route Route
     *
     * @return void
     */
    public static function redirect($route)
    {
        exit(header('location:' . $route));
    }

    /**
     * getCurrentLanguage
     *
     * @return string
     */
    public static function getCurrentLanguage($installed = false)
    {
        if ($installed) {
            return self::readConfig('lang');
        }

        // set the user language reading the config file
        if ($installed && !isset($_COOKIE['current_lang'])) {
            $_COOKIE['current_lang'] = self::readConfig('lang');
        }

        // get the language from the session
        if (isset($_COOKIE['current_lang'])) {
            return $_COOKIE['current_lang'];
        }

        return 'english'; // the universal language if nothing was set
    }

    /**
     * setCurrentLanguage
     *
     * @param string $lang Language
     *
     * @return void
     */
    public static function setCurrentLanguage($lang = '')
    {
        // force english
        if (!in_array($lang, self::getLanguagesList())) {
            $lang = 'english';
        }

        $db = new Database();

        // set the user language reading the config file
        if ($db != null && $db->testConnection() && !isset($_COOKIE['current_lang'])) {
            self::updateConfig('lang', $lang);
        }

        setcookie('current_lang', $lang);
    }

    /**
     * Get the list of available languages
     *
     * @return array
     */
    public static function getLanguagesList()
    {
        $langs_dir = opendir(XGP_ROOT . LANG_PATH);
        $exceptions = ['.', '..', '.htaccess', 'index.html', '.DS_Store'];
        $langs = [];

        while (($lang_dir = readdir($langs_dir)) !== false) {
            if (!in_array($lang_dir, $exceptions)) {
                $langs[] = $lang_dir;
            }
        }

        return $langs;
    }

    /**
     * getLanguages
     *
     * @param string $current_lang Current language
     *
     * @return string
     */
    public static function getLanguages($current_lang)
    {
        $langs_dir = opendir(XGP_ROOT . LANG_PATH);
        $exceptions = ['.', '..', '.htaccess', 'index.html', '.DS_Store'];
        $lang_options = '';

        while (($lang_dir = readdir($langs_dir)) !== false) {
            if (!in_array($lang_dir, $exceptions)) {
                $lang_options .= '<option ';

                if ($current_lang == $lang_dir) {
                    $lang_options .= 'selected = selected';
                }

                $lang_options .= ' value="' . $lang_dir . '">' . $lang_dir . '</option>';
            }
        }

        return $lang_options;
    }

    /**
     * Shows a message box
     *
     * @param string $title     Box Tittle
     * @param string $message   Box Message
     * @param string $goto      Go to url
     * @param string $button    Button text
     * @param bool   $two_lines Set the message in two lines
     *
     * @return string
     */
    public static function messageBox($title, $message, $goto = '', $button = ' ok ', $two_lines = false)
    {
        return self::getTemplate()->set(
            'alliance/alliance_message_box',
            [
                'goto' => $goto,
                'title' => $title,
                'message_box_row' => self::getTemplate()->set(
                    'alliance/alliance_message_box_row_' . ($two_lines ? 'two' : 'one'),
                    [
                        'message' => $message,
                        'button' => $button,
                    ]
                ),
            ]
        );
    }

    /**
     * Encrypt a password
     *
     * @param string $password
     * @return string
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    public static function generatePassword(): string
    {
        return StringsHelper::randomString(16);
    }

    /**
     * Check if it is the current planet
     *
     * @param array $current
     * @param array $target
     * @return boolean
     */
    public static function isCurrentPlanet(array $current, array $target): bool
    {
        return ($current['planet_galaxy'] == $target['planet_galaxy']
            && $current['planet_system'] == $target['planet_system']
            && $current['planet_planet'] == $target['planet_planet']
            && $current['planet_type'] == $target['planet_type']);
    }
}
