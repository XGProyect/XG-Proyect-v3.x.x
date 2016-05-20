<?php
/**
 * Fleet2 Controller
 *
 * PHP Version 5.5+
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */

namespace application\controllers\game;

use application\core\XGPCore;
use application\libraries\FleetsLib;
use application\libraries\FunctionsLib;
use application\libraries\OfficiersLib;

/**
 * Fleet2 Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Fleet2 extends XGPCore
{
    const MODULE_ID = 8;

    private $_lang;
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

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_lang = parent::$lang;
        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();

        $this->build_page();
    }

    /**
     * method __destruct
     * param
     * return close db connection
     */
    public function __destruct()
    {
        parent::$db->closeConnection();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $reslist = parent::$objects->getObjectsList();

        #####################################################################################################
        // SOME DEFAULT VALUES
        #####################################################################################################
        // QUERYS
        $getCurrentAcs = parent::$db->query("SELECT *
														FROM " . ACS_FLEETS . "
														WHERE acs_fleet_members = '" . $this->_current_user['user_id'] . "'");

        // ARRAYS
        $speed_values = array(10 => 100, 9 => 90, 8 => 80, 7 => 70, 6 => 60, 5 => 50, 4 => 40, 3 => 30, 2 => 20, 1 => 10);
        $planet_type = array('fl_planet', 'fl_debris', 'fl_moon');

        // LOAD TEMPLATES REQUIRED
        $inputs_template = parent::$page->getTemplate('fleet/fleet2_inputs');
        $options_template = parent::$page->getTemplate('fleet/fleet_options');
        $shortcut_template = parent::$page->getTemplate('fleet/fleet2_shortcuts');
        $shortcut_row_template = parent::$page->getTemplate('fleet/fleet2_shortcuts_row');
        $shortcut_noshortcuts = parent::$page->getTemplate('fleet/fleet2_shortcuts_noshortcuts_row');
        $shortcut_acs_row = parent::$page->getTemplate('fleet/fleet2_shortcut_acs_row');

        // LANGUAGE
        $this->_lang['js_path'] = XGP_ROOT . JS_PATH;
        $parse = $this->_lang;

        // COORDS
        $g = ( ( $_POST['galaxy'] == '' ) ? $this->_current_planet['planet_galaxy'] : $_POST['galaxy'] );
        $s = ( ( $_POST['system'] == '' ) ? $this->_current_planet['planet_system'] : $_POST['system'] );
        $p = ( ( $_POST['planet'] == '' ) ? $this->_current_planet['planet_planet'] : $_POST['planet'] );
        $t = ( ( $_POST['planet_type'] == '' ) ? $this->_current_planet['planet_type'] : $_POST['planet_type'] );

        // OTHER VALUES
        $value = 0;
        $FleetHiddenBlock = '';
        #####################################################################################################
        // END DEFAULT VALUES
        #####################################################################################################
        #####################################################################################################
        // LOAD SHIPS INPUTS
        #####################################################################################################
        $fleet['fleetlist'] = '';
        $fleet['amount'] = 0;
        $fleet['consumption'] = 0;

        foreach ($reslist['fleet'] as $n => $i) {
            if (isset($_POST["ship$i"]) && $i >= 201 && $i <= 215 && $_POST["ship$i"] > "0") {
                if (( $_POST["ship$i"] > $this->_current_planet[$resource[$i]]) OR ( !ctype_digit($_POST["ship$i"]))) {
                    FunctionsLib::redirect('game.php?page=fleet1');
                } else {
                    $fleet['fleetarray'][$i] = $_POST["ship$i"];
                    $fleet['fleetlist'] .= $i . "," . $_POST["ship$i"] . ";";
                    $fleet['amount'] += $_POST["ship$i"];
                    $fleet['i'] = $i;
                    $fleet['consumption'] += FleetsLib::shipConsumption($i, $this->_current_user);
                    $fleet['speed'] = FleetsLib::fleetMaxSpeed('', $i, $this->_current_user);
                    $fleet['capacity'] = $pricelist[$i]['capacity'];
                    $fleet['ship'] = $_POST["ship$i"];

                    $speedalls[$i] = FleetsLib::fleetMaxSpeed('', $i, $this->_current_user);
                    $FleetHiddenBlock .= parent::$page->parseTemplate($inputs_template, $fleet);
                }
            }
        }

        if (!$fleet['fleetlist']) {
            FunctionsLib::redirect('game.php?page=fleet1');
        } else {
            $speedallsmin = min($speedalls);
        }

        #####################################################################################################
        // LOAD PLANET TYPES OPTIONS
        #####################################################################################################
        $parse['options_planettype'] = '';

        foreach ($planet_type as $type) {
            $value++;

            $options['value'] = $value;

            if ($value == $t) {
                $options['selected'] = 'SELECTED';
            } else {
                $options['selected'] = '';
            }

            $options['title'] = $this->_lang[$type];


            $parse['options_planettype'] .= parent::$page->parseTemplate($options_template, $options);
        }

        #####################################################################################################
        // LOAD SPEED OPTIONS
        #####################################################################################################
        $parse['options'] = '';

        foreach ($speed_values as $value => $percentage) {
            $speed_percentage['value'] = $value;
            $speed_percentage['selected'] = '';
            $speed_percentage['title'] = $percentage;

            $parse['options'] .= parent::$page->parseTemplate($options_template, $speed_percentage);
        }

        #####################################################################################################
        // PARSE THE REST OF THE OPTIONS
        #####################################################################################################
        $parse['fleetblock'] = $FleetHiddenBlock;
        $parse['speedallsmin'] = $speedallsmin;
        $parse['fleetarray'] = str_rot13(base64_encode(serialize($fleet['fleetarray'])));
        $parse['galaxy'] = $this->_current_planet['planet_galaxy'];
        $parse['system'] = $this->_current_planet['planet_system'];
        $parse['planet'] = $this->_current_planet['planet_planet'];
        $parse['galaxy_post'] = (int) $_POST['galaxy'];
        $parse['system_post'] = (int) $_POST['system'];
        $parse['planet_post'] = (int) $_POST['planet'];
        $parse['speedfactor'] = FunctionsLib::fleetSpeedFactor();
        $parse['planet_type'] = $this->_current_planet['planet_type'];
        $parse['metal'] = floor($this->_current_planet['planet_metal']);
        $parse['crystal'] = floor($this->_current_planet['planet_crystal']);
        $parse['deuterium'] = floor($this->_current_planet['planet_deuterium']);
        $parse['g'] = $g;
        $parse['s'] = $s;
        $parse['p'] = $p;

        #####################################################################################################
        // LOAD FLEET SHORTCUTS
        #####################################################################################################
        if (OfficiersLib::isOfficierActive($this->_current_user['premium_officier_commander'])) {

            if ($this->_current_user['user_fleet_shortcuts']) {

                $shortcut['shortcut_options']   = '';
                $scarray                        = explode(";", $this->_current_user['user_fleet_shortcuts']);

                foreach ($scarray as $a => $b) {
                    if ($b != "") {
                        $c = explode(',', $b);

                        $shortcut['description'] = $c[0] . " " . $c[1] . ":" . $c[2] . ":" . $c[3] . " ";

                        switch ($c[4]) {
                            case 1:
                                $shortcut['description'] .= $this->_lang['fl_planet_shortcut'];
                                break;
                            case 2:
                                $shortcut['description'] .= $this->_lang['fl_debris_shortcut'];
                                break;
                            case 3:
                                $shortcut['description'] .= $this->_lang['fl_moon_shortcut'];
                                break;
                            default:
                                $shortcut['description'] .= '';
                                break;
                        }
                        $shortcut['select'] = 'shortcuts';
                        $shortcut['selected'] = '';
                        $shortcut['value'] = $c['1'] . ';' . $c['2'] . ';' . $c['3'] . ';' . $c['4'];
                        $shortcut['title'] = $shortcut['description'];
                        $shortcut['shortcut_options'] .= parent::$page->parseTemplate($options_template, $shortcut);
                    }
                }

                $parse['shortcuts_rows'] = parent::$page->parseTemplate($shortcut_row_template, $shortcut);
                $parse['shortcut'] = parent::$page->parseTemplate($shortcut_template, $parse);
            } else {
                $parse['fl_shorcut_message'] = $this->_lang['fl_no_shortcuts'];
                $parse['shortcuts_rows'] = parent::$page->parseTemplate($shortcut_noshortcuts, $parse);
                $parse['shortcut'] = parent::$page->parseTemplate($shortcut_template, $parse);
            }
        }
        #####################################################################################################
        // LOAD COLONY SHORTCUTS
        #####################################################################################################
        $colony['select'] = 'colonies';
        $colony['shortcut_options'] = FunctionsLib::buildPlanetList($this->_current_user, $this->_current_planet['planet_id']);
        $parse['colonylist'] = parent::$page->parseTemplate($shortcut_row_template, $colony);

        if ($colony['shortcut_options'] === false) {
            $parse['fl_shorcut_message'] = $this->_lang['fl_no_colony'];
            $parse['colonylist'] = parent::$page->parseTemplate($shortcut_noshortcuts, $parse);
        }

        #####################################################################################################
        // LOAD SAC SHORTCUTS
        #####################################################################################################
        $acs_fleets = '';

        while ($row = parent::$db->fetchArray($getCurrentAcs)) {
            $members = explode(",", $row['acs_fleet_invited']);

            foreach ($members as $a => $b) {
                if ($b == $this->_current_user['user_id']) {
                    $acs['galaxy'] = $row['acs_fleet_galaxy'];
                    $acs['system'] = $row['acs_fleet_system'];
                    $acs['planet'] = $row['acs_fleet_planet'];
                    $acs['planet_type'] = $row['acs_fleet_planet_type'];
                    $acs['id'] = $row['acs_fleet_id'];
                    $acs['name'] = $row['acs_fleet_name'];

                    $acs_fleets .= parent::$page->parseTemplate($shortcut_acs_row, $acs);
                }
            }
        }

        $parse['asc'] = $acs_fleets;
        $parse['maxepedition'] = $_POST['maxepedition'];
        $parse['curepedition'] = $_POST['curepedition'];
        $parse['target_mission'] = $_POST['target_mission'];

        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('fleet/fleet2_table'), $parse));
    }
}

/* end of fleet2.php */
