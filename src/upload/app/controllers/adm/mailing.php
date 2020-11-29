<?php declare (strict_types = 1);

namespace App\controllers\adm;

use App\core\BaseController;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\Functions;

/**
 * Mailing Class
 */
class Mailing extends BaseController
{
    const MAILING_SETTINGS = [
        'mailing_protocol' => FILTER_SANITIZE_STRING,
        'mailing_smtp_host' => FILTER_SANITIZE_STRING,
        'mailing_smtp_user' => FILTER_SANITIZE_STRING,
        'mailing_smtp_pass' => FILTER_SANITIZE_STRING,
        'mailing_smtp_port' => FILTER_VALIDATE_INT,
        'mailing_smtp_timeout' => FILTER_VALIDATE_INT,
        'mailing_smtp_crypto' => FILTER_SANITIZE_STRING,
    ];

    /**
     * Contains the alert string
     *
     * @var string
     */
    private $alert = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/mailing']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

        // time to do something
        $this->runAction();

        // build the page
        $this->buildPage();
    }

    /**
     * Run an action
     *
     * @return void
     */
    private function runAction(): void
    {
        $data = filter_input_array(INPUT_POST, self::MAILING_SETTINGS);

        if ($data) {
            foreach ($data as $option => $value) {
                if ((is_numeric($value) && $value >= 0) or is_string($value) && ($value !== false && $value !== null)) {
                    Functions::updateConfig($option, $value);
                }
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('pr_all_ok_message'));
        }
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        parent::$page->displayAdmin(
            $this->getTemplate()->set(
                'adm/mailing_view',
                array_merge(
                    $this->langs->language,
                    $this->getMailingSettings(),
                    $this->buildProtocolsDropdown(),
                    $this->buildCryptoDropdown(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Get mailing settings
     *
     * @return void
     */
    private function getMailingSettings(): array
    {
        return array_filter(
            Functions::readConfig('', true),
            function ($key) {
                return array_key_exists($key, self::MAILING_SETTINGS);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Build the list of available protocol options
     *
     * @return array
     */
    private function buildProtocolsDropdown(): array
    {
        $options = [];

        foreach (['mail', 'sendmail', 'smtp'] as $option) {
            $options[] = [
                'value' => $option,
                'selected' => ($option == Functions::readConfig('mailing_protocol') ? ' selected' : ''),
                'option' => $option,
            ];
        }

        return ['protocol_options' => $options];
    }

    /**
     * Build the list of available encryption options
     *
     * @return array
     */
    private function buildCryptoDropdown(): array
    {
        $options = [];

        foreach (['', 'tls', 'ssl'] as $option) {
            $options[] = [
                'value' => $option,
                'selected' => ($option == Functions::readConfig('mailing_smtp_crypto') ? ' selected' : ''),
                'option' => strtoupper($option),
            ];
        }

        return ['smtp_crypto_options' => $options];
    }
}
