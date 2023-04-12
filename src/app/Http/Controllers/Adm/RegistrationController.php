<?php

declare(strict_types=1);

namespace App\Http\Controllers\Adm;

use App\Core\BaseController;
use App\Libraries\Adm\AdministrationLib as Administration;
use App\Libraries\Functions;

class RegistrationController extends BaseController
{
    public const REGISTRATION_SETTINGS = [
        'reg_enable' => FILTER_UNSAFE_RAW,
        'reg_welcome_message' => FILTER_UNSAFE_RAW,
        'reg_welcome_email' => FILTER_UNSAFE_RAW,
    ];

    private string $alert = '';

    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/registration']);
    }

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
        $data = filter_input_array(INPUT_POST, self::REGISTRATION_SETTINGS, true);

        if ($data) {
            foreach ($data as $option => $value) {
                Functions::updateConfig($option, ($value == 'on' ? 1 : 0));
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('ur_all_ok_message'));
        }
    }

    private function buildPage(): void
    {
        $this->page->displayAdmin(
            $this->template->set(
                'adm/registration_view',
                array_merge(
                    $this->langs->language,
                    $this->getNewUserRegistrationSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Get new user registration settings
     *
     * @return void
     */
    private function getNewUserRegistrationSettings()
    {
        return $this->setChecked(
            array_filter(
                Functions::readConfig('', true),
                function ($key) {
                    return array_key_exists($key, self::REGISTRATION_SETTINGS);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    /**
     * Coverts the setting value from an int to a "checked"
     *
     * @param array $settings
     * @return array
     */
    private function setChecked(array $settings): array
    {
        foreach ($settings as $key => $value) {
            $settings[$key] = $value == 1 ? 'checked="checked"' : '';
        }

        return $settings;
    }
}
