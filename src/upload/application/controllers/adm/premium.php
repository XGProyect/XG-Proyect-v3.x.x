<?php

declare (strict_types = 1);

/**
 * Premium Controller
 *
 * PHP Version 7.1+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\controllers\adm;

use application\core\Controller;
use application\libraries\adm\AdministrationLib as Administration;
use application\libraries\FunctionsLib;

/**
 * Premium Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Premium extends Controller
{
    const PREMIUM_SETTINGS = [
        'premium_url' => FILTER_VALIDATE_URL,
        'merchant_price' => FILTER_VALIDATE_FLOAT,
        'merchant_base_min_exchange_rate' => FILTER_VALIDATE_FLOAT,
        'merchant_base_max_exchange_rate' => FILTER_VALIDATE_FLOAT,
        'merchant_metal_multiplier' => FILTER_VALIDATE_FLOAT,
        'merchant_crystal_multiplier' => FILTER_VALIDATE_FLOAT,
        'merchant_deuterium_multiplier' => FILTER_VALIDATE_FLOAT,
        'registration_dark_matter' => FILTER_VALIDATE_INT,
    ];

    /**
     * Current user data
     *
     * @var array
     */
    private $user;

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
        parent::loadLang(['adm/global', 'adm/premium']);

        // set data
        $this->user = $this->getUserData();

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
        $data = filter_input_array(INPUT_POST, self::PREMIUM_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                if ((is_numeric($value) && $value >= 0) or is_string($value)) {
                    FunctionsLib::updateConfig($option, $value);
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
                'adm/premium_view',
                array_merge(
                    $this->langs->language,
                    $this->getPremiumSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Get premium settings
     *
     * @return void
     */
    private function getPremiumSettings(): array
    {
        return array_filter(
            FunctionsLib::readConfig('', true),
            function ($key) {
                return array_key_exists($key, self::PREMIUM_SETTINGS);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

/* end of premium.php */
