<?php

declare (strict_types = 1);

/**
 * Encrypter Controller
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
use application\libraries\adm\AdministrationLib;
use application\libraries\FunctionsLib as Functions;

/**
 * Encrypter Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Encrypter extends Controller
{
    /**
     * Current user data
     *
     * @var array
     */
    private $user;

    /**
     * Contains the unencrypted password
     *
     * @var string
     */
    private $unencrypted = '';

    /**
     * Contains the encrypted password
     *
     * @var string
     */
    private $encrypted = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        AdministrationLib::checkSession();

        // load Language
        parent::loadLang(['adm/global', 'adm/encrypter']);

        // set data
        $this->user = $this->getUserData();

        // Check if the user is allowed to access
        if (AdministrationLib::authorization($this->user['user_authlevel'], 'use_tools') != 1) {
            AdministrationLib::noAccessMessage($this->langs->line('no_permissions'));
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
        $unencrypted = filter_input(INPUT_POST, 'unencrypted');

        if ($unencrypted) {
            $this->unencrypted = $unencrypted;
            $this->encrypted = Functions::hash($unencrypted);
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
                'adm/encrypter_view',
                array_merge(
                    $this->langs->language,
                    [
                        'unencrypted' => $this->unencrypted ?? '',
                        'encrypted' => $this->encrypted ?? '',
                    ]
                )
            )
        );
    }
}

/* end of encrypter.php */
