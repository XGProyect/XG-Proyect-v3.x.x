<?php

declare (strict_types = 1);

/**
 * Planets Controller
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
use application\libraries\FunctionsLib as Functions;

/**
 * Planets Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Planets extends Controller
{
    const PLANET_SETTINGS = [
        'initial_fields' => FILTER_VALIDATE_INT,
        'metal_basic_income' => FILTER_VALIDATE_INT,
        'crystal_basic_income' => FILTER_VALIDATE_INT,
        'deuterium_basic_income' => FILTER_VALIDATE_INT,
        'energy_basic_income' => FILTER_VALIDATE_INT,
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
        parent::loadLang(['adm/global', 'adm/planets']);

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
        $data = filter_input_array(INPUT_POST, self::PLANET_SETTINGS);

        if ($data) {
            $data = array_diff($data, [null, false]);

            foreach ($data as $option => $value) {
                Functions::updateConfig($option, $value);
            }

            $this->alert = Administration::saveMessage('ok', $this->langs->line('np_all_ok_message'));
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
                'adm/planets_view',
                array_merge(
                    $this->langs->language,
                    $this->getNewPlanetSettings(),
                    [
                        'alert' => $this->alert ?? '',
                    ]
                )
            )
        );
    }

    /**
     * Get new planet settings
     *
     * @return void
     */
    private function getNewPlanetSettings(): array
    {
        return array_filter(
            Functions::readConfig('', true),
            function ($key) {
                return array_key_exists($key, self::PLANET_SETTINGS);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

/* end of planets.php */
