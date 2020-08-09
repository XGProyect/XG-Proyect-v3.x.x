<?php
/**
 * Renameplanet Controller
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
namespace application\controllers\game;

use application\core\Controller;
use application\libraries\FunctionsLib;

/**
 * Renameplanet Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Renameplanet extends Controller
{

    const MODULE_ID = 1;

    private $_current_user;
    private $_current_planet;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/renameplanet');

        // load Language
        parent::loadLang('renameplanet');

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();

        $this->build_page();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $parse = $this->langs->language;
        $parse['planet_name'] = $this->_current_planet['planet_name'];
        $parse['planet_id'] = $this->_current_planet['planet_id'];
        $parse['galaxy_galaxy'] = $this->_current_planet['planet_galaxy'];
        $parse['galaxy_system'] = $this->_current_planet['planet_system'];
        $parse['galaxy_planet'] = $this->_current_planet['planet_planet'];

        // DEFAULT VIEW
        $current_view = 'renameplanet/renameplanet_view';

        // CHANGE THE ACTION
        switch ((isset($_POST['action']) ? $_POST['action'] : null)) {
            case $this->langs->line('rp_planet_rename_action'):

                $this->rename_planet($_POST['newname']);

                break;

            case $this->langs->line('rp_abandon_planet'):

                // DELETE VIEW
                $current_view = 'renameplanet/renameplanet_delete_view';

                break;
        } // switch

        if (isset($_POST['kolonieloeschen']) && (int) $_POST['kolonieloeschen'] == 1 && (int) $_POST['deleteid'] == $this->_current_user['user_current_planet']) {
            $this->delete_planet();
        }

        // SET THE VIEW
        parent::$page->display(
            $this->getTemplate()->set(
                $current_view,
                $parse
            )
        );
    }

    /**
     * method rename_planet
     * param $new_name
     * return main method, loads everything
     */
    private function rename_planet($new_name)
    {
        $new_name = strip_tags(trim($new_name));

        if (preg_match("/[^A-z0-9_\- ]/", $new_name) == 1) {
            FunctionsLib::message($this->langs->line('rp_newname_error'), "game.php?page=renameplanet", 2);
        }

        if ($new_name != '') {
            $this->Renameplanet_Model->updatePlanetName($new_name, $this->_current_user['user_current_planet']);
            FunctionsLib::message($this->langs->line('rp_planet_name_changed'), "game.php?page=renameplanet", 2);
        }
    }

    /**
     * method delete_planet
     * param
     * return deletes the planet
     */
    private function delete_planet()
    {
        $own_fleet = 0;
        $enemy_fleet = 0;
        $fleets_incoming = $this->Renameplanet_Model->getFleets(
            $this->_current_user['user_id'],
            $this->_current_planet['planet_galaxy'],
            $this->_current_planet['planet_system'],
            $this->_current_planet['planet_planet']
        );

        foreach ($fleets_incoming as $fleet) {
            $own_fleet = $fleet['fleet_owner'];
            $enemy_fleet = $fleet['fleet_target_owner'];

            if ($fleet['fleet_target_owner'] == $this->_current_user['user_id']) {
                $end_type = $fleet['fleet_end_type'];
            }

            $mess = $fleet['fleet_mess'];
        }

        if ($own_fleet > 0) {
            FunctionsLib::message($this->langs->line('rp_abandon_planet_not_possible'), 'game.php?page=renameplanet');
        } elseif ((($enemy_fleet > 0) && ($mess < 1)) && $end_type != 2) {
            FunctionsLib::message($this->langs->line('rp_abandon_planet_not_possible'), 'game.php?page=renameplanet');
        } else {
            if (password_verify($_POST['pw'], $this->_current_user['user_password']) && $this->_current_user['user_home_planet_id'] != $this->_current_user['user_current_planet']) {
                if ($this->_current_planet['moon_id'] != 0) {
                    $this->Renameplanet_Model->deleteMoonAndPlanet(
                        $this->_current_user['user_id'],
                        $this->_current_user['user_current_planet'],
                        $this->_current_planet['planet_galaxy'],
                        $this->_current_planet['planet_system'],
                        $this->_current_planet['planet_planet']
                    );
                } else {
                    $this->Renameplanet_Model->deletePlanet($this->_current_user['user_id'], $this->_current_user['user_current_planet']);
                }

                FunctionsLib::message($this->langs->line('rp_planet_abandoned'), 'game.php?page=overview');
            } elseif ($this->_current_user['user_home_planet_id'] == $this->_current_user['user_current_planet']) {
                FunctionsLib::message($this->langs->line('rp_principal_planet_cant_abanone'), 'game.php?page=renameplanet');
            } else {
                FunctionsLib::message($this->langs->line('rp_wrong_pass'), 'game.php?page=renameplanet');
            }
        }
    }
}

/* end of renameplanet.php */
