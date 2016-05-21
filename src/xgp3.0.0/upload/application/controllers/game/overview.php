<?php
/**
 * Overview Controller
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
use application\libraries\DevelopmentsLib;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\UpdateLib;

/**
 * Overview Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class Overview extends XGPCore
{
    const MODULE_ID = 1;

    private $_lang;
    private $_current_user;
    private $_current_planet;
    private $_noob;

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
        $this->_noob = FunctionsLib::loadLibrary('NoobsProtectionLib');

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
        // SOME DEFAULT VALUES
        $parse = $this->_lang;
        $parse['dpath'] = DPATH;

        ######################################
        #
            # blocks
        #
            ######################################
        // MESSAGES BLOCK
        $block['messages'] = $this->get_messages();

        // FLEET MOVEMENTS BLOCK
        $block['fleet_movements'] = $this->get_fleet_movements();

        // MOON BLOCK
        $block['moon'] = $this->get_planet_moon();

        // PLANETS BLOCK
        $block['planets'] = $this->get_planets();

        ######################################
        #
            # parse information
        #
            ######################################
        // SHOW ALL THE INFORMATION, IN ORDER, ACCORDING TO THE TEMPLATE
        $parse['planet_name'] = $this->_current_planet['planet_name'];
        $parse['user_name'] = $this->_current_user['user_name'];
        $parse['date_time'] = date(FunctionsLib::readConfig('date_format_extended'), time());
        $parse['Have_new_message'] = $block['messages'];
        $parse['fleet_list'] = $block['fleet_movements'];
        $parse['planet_image'] = $this->_current_planet['planet_image'];
        $parse['building'] = $this->get_current_work($this->_current_planet);
        $parse['moon_img'] = $block['moon']['moon_img'];
        $parse['moon'] = $block['moon']['moon'];
        $parse['anothers_planets'] = $block['planets'];
        $parse['planet_diameter'] = FormatLib::prettyNumber($this->_current_planet['planet_diameter']);
        $parse['planet_field_current'] = $this->_current_planet['planet_field_current'];
        $parse['planet_field_max'] = DevelopmentsLib::maxFields($this->_current_planet);
        $parse['planet_temp_min'] = $this->_current_planet['planet_temp_min'];
        $parse['planet_temp_max'] = $this->_current_planet['planet_temp_max'];
        $parse['galaxy_galaxy'] = $this->_current_planet['planet_galaxy'];
        $parse['galaxy_system'] = $this->_current_planet['planet_system'];
        $parse['galaxy_planet'] = $this->_current_planet['planet_planet'];
        $parse['user_rank'] = $this->get_user_rank();

        // DISPLAY THE RESULT PAGE
        parent::$page->display(parent::$page->parseTemplate(parent::$page->getTemplate('overview/overview_body'), $parse));
    }

    /**
     * method get_planets
     * param $user_planet
     * param $is_current_planet
     * return building in progress or free text
     */
    private function get_current_work($user_planet, $is_current_planet = true)
    {
        // THE PLANET IS "FREE" BY DEFAULT
        $building_block = $this->_lang['ov_free'];

        if (!$is_current_planet) {
            // UPDATE THE PLANET INFORMATION FIRST, MAY BE SOMETHING HAS JUST FINISHED
            UpdateLib::updateBuildingsQueue($user_planet, $this->_current_user);
        }

        if ($user_planet['planet_b_building'] != 0) {
            if ($user_planet['planet_b_building'] != 0) {
                $queue = explode(';', $user_planet['planet_b_building_id']); // GET ALL
                $current_building = explode(',', $queue[0]); // GET ONLY THE FIRST ELEMENT
                $building = $current_building[0]; // THE BUILDING
                $level = $current_building[1]; // THE LEVEL
                $time_to_end = $current_building[3] - time(); // THE TIME
                // THE BUILDING BLOCK
                if ($is_current_planet) {
                    $building_block = DevelopmentsLib::currentBuilding("overview", $building);
                    $building_block .= $this->_lang['tech'][$building] . ' (' . $level . ')';
                    $building_block .= "<br /><div id=\"blc\" class=\"z\">" . FormatLib::prettyTime($time_to_end) . "</div>";
                    $building_block .= "\n<script language=\"JavaScript\">";
                    $building_block .= "\n	pp = \"" . $time_to_end . "\";\n";
                    $building_block .= "\n	pk = \"" . 1 . "\";\n";
                    $building_block .= "\n	pm = \"cancel\";\n";
                    $building_block .= "\n	pl = \"" . $this->_current_planet['planet_id'] . "\";\n";
                    $building_block .= "\n	t();\n";
                    $building_block .= "\n</script>\n";
                } else {
                    $building_block = '' . $this->_lang['tech'][$building] . ' (' . $level . ')';
                    $building_block .= '<br><font color="#7f7f7f">(' . FormatLib::prettyTime($time_to_end) . ')</font>';
                }
            }
        }

        // BACK TO THE PLANET!
        return $building_block;
    }

    /**
     * method get_messages
     * param
     * return messages row
     */
    private function get_messages()
    {
        $new_message = '';

        if ($this->_current_user['new_message'] != 0) {
            $new_message = '<tr>';

            if ($this->_current_user['new_message'] == 1) {
                $new_message .= '<th colspan="4">' . FunctionsLib::setUrl('game.php?page=messages', $this->_lang['ov_have_new_message'], $this->_lang['ov_have_new_message']) . '</th>';
            }

            if ($this->_current_user['new_message'] > 1) {
                $link_text = str_replace('%m', FormatLib::prettyNumber($this->_current_user['new_message']), $this->_lang['ov_have_new_messages']);
                $new_message .= '<th colspan="4">' . FunctionsLib::setUrl('game.php?page=messages', $link_text, $link_text) . '</th>';
            }

            $new_message .= '</tr>';
        }

        return $new_message;
    }

    /**
     * method get_fleet_movements
     * param
     * return fleets movements rows
     */
    private function get_fleet_movements()
    {
        $fleet = '';
        $fleet_row = '';
        $record = 0;

        $own_fleets = parent::$db->query(
                "SELECT *
            FROM " . FLEETS . "
            WHERE `fleet_owner` = '" . (int) $this->_current_user['user_id'] . "' OR
            `fleet_target_owner` = '" . (int) $this->_current_user['user_id'] . "';"
        );

        while ($fleets = parent::$db->fetchArray($own_fleets)) {

            ######################################
            #
            # own fleets
            #
            ######################################

            $start_time = $fleets['fleet_start_time'];
            $stay_time = $fleets['fleet_end_stay'];
            $end_time = $fleets['fleet_end_time'];

            $target_galaxy = $fleets['fleet_end_galaxy'];
            $target_system = $fleets['fleet_end_system'];
            $target_planet = $fleets['fleet_end_planet'];
            $fleet_status = $fleets['fleet_mess'];
            $fleet_group = $fleets['fleet_group'];
            $id = $fleets['fleet_id'];

            if ($fleets['fleet_owner'] == $this->_current_user['user_id']) {

                $record++;

                $label = 'fs';

                if ($start_time > time()) {

                    $fleet_row[$start_time . $id] = FleetsLib::flyingFleetsTable($fleets, 0, true, $label, $record, $this->_current_user);
                }

                if (( $fleets['fleet_mission'] != 4 ) && ( $fleets['fleet_mission'] != 10 )) {
                    $label = 'ft';

                    if ($stay_time > time()) {
                        $fleet_row[$stay_time . $id] = FleetsLib::flyingFleetsTable($fleets, 1, true, $label, $record, $this->_current_user);
                    }

                    $label = 'fe';

                    if ($end_time > time()) {
                        $fleet_row[$end_time . $id] = FleetsLib::flyingFleetsTable($fleets, 2, true, $label, $record, $this->_current_user);
                    }
                }

                if ($fleets['fleet_mission'] == 4 && $start_time < time() && $end_time > time()) {
                    $fleet_row[$end_time . $id] = FleetsLib::flyingFleetsTable($fleets, 2, true, 'none', $record, $this->_current_user);
                }
            }

            ######################################
            #
            # incoming fleets
            #
            ######################################
            if ($fleets['fleet_owner'] != $this->_current_user['user_id']) {

                if ($fleets['fleet_mission'] == 2) {

                    $record++;
                    $start_time = ($fleet_status > 0) ? '' : $fleets['fleet_start_time'];

                    if ($start_time > time()) {

                        $fleet_row[$start_time . $id] = FleetsLib::flyingFleetsTable(
                                        $fleets, 0, false, 'ofs', $record, $this->_current_user
                        );
                    }
                }

                if (( $fleets['fleet_mission'] == 1 ) && ( $fleet_group > 0 )) {
                    $record++;

                    if ($fleet_status > 0) {
                        $start_time = '';
                    } else {
                        $start_time = $fleets['fleet_start_time'];
                    }

                    if ($start_time > time()) {
                        $fleet_row[$start_time . $id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->_current_user);
                    }
                }
            }

            ######################################
            #
                    # other fleets
            #
                    ######################################

            if ($fleets['fleet_owner'] != $this->_current_user['user_id']) {
                if ($fleets['fleet_mission'] != 8) {
                    $record++;

                    $start_time = $fleets['fleet_start_time'];
                    $stay_time = $fleets['fleet_end_stay'];
                    $id = $fleets['fleet_id'];

                    if ($start_time > time()) {
                        $fleet_row[$start_time . $id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->_current_user);
                    }
                    if ($fleets['fleet_mission'] == 5) {
                        if ($stay_time > time()) {
                            $fleet_row[$stay_time . $id] = FleetsLib::flyingFleetsTable($fleets, 1, false, 'oft', $record, $this->_current_user);
                        }
                    }
                }
            }
        }

        parent::$db->freeResult($own_fleets);

        if (count($fleet_row) > 0 && $fleet_row != '') {
            ksort($fleet_row);

            foreach ($fleet_row as $time => $content) {
                $fleet .= $content . "\n";
            }
        }

        return $fleet;
    }

    /**
     * method get_planet_moon
     * param
     * return the moon image and data for the current planet
     */
    private function get_planet_moon()
    {
        $return['moon_img'] = '';
        $return['moon'] = '';

        if ($this->_current_planet['moon_id'] != 0 && $this->_current_planet['moon_destroyed'] == 0 && $this->_current_planet['planet_type'] == 1) {
            $moon_name = $this->_current_planet['moon_name'] . " (" . $this->_lang['fcm_moon'] . ")";
            $url = 'game.php?page=overview&cp=' . $this->_current_planet['moon_id'] . '&re=0';
            $image = DPATH . 'planets/' . $this->_current_planet['moon_image'] . '.jpg';
            $attributes = 'height="50" width="50"';

            $return['moon_img'] = FunctionsLib::setUrl($url, $moon_name, FunctionsLib::setImage($image, $moon_name, $attributes));
            $return['moon'] = $moon_name;
        }

        return $return;
    }

    /**
     * method get_planets
     * param
     * return all the user planets
     */
    private function get_planets()
    {
        $colony = 1;

        $planets_query = parent::$db->query("SELECT *
                                                                                            FROM " . PLANETS . " AS p
                                                                                            INNER JOIN " . BUILDINGS . " AS b ON b.building_planet_id = p.`planet_id`
                                                                                            INNER JOIN " . DEFENSES . " AS d ON d.defense_planet_id = p.`planet_id`
                                                                                            INNER JOIN " . SHIPS . " AS s ON s.ship_planet_id = p.`planet_id`
                                                                                            WHERE `planet_user_id` = '" . (int) $this->_current_user['user_id'] . "'
                                                                                                    AND `planet_destroyed` = 0;");
        $planet_block = '<tr>';

        while ($user_planet = parent::$db->fetchArray($planets_query)) {
            if ($user_planet['planet_id'] != $this->_current_user['user_current_planet'] && $user_planet['planet_type'] != 3) {
                $url = 'game.php?page=overview&cp=' . $user_planet['planet_id'] . '&re=0';
                $image = DPATH . 'planets/small/s_' . $user_planet['planet_image'] . '.jpg';
                $attributes = 'height="50" width="50"';

                $planet_block .= '<th>' . $user_planet['planet_name'] . '<br>';
                $planet_block .= FunctionsLib::setUrl($url, $user_planet['planet_name'], FunctionsLib::setImage($image, $user_planet['planet_name'], $attributes));
                $planet_block .= '<center>';
                $planet_block .= $this->get_current_work($user_planet, false);
                $planet_block .= '</center></th>';

                if ($colony <= 1) {
                    $colony++;
                } else {
                    $planet_block .= '</tr><tr>';
                    $colony = 1;
                }
            }
        }

        $planet_block .= '</tr>';

        // CLEAN SOME MEMORY
        parent::$db->freeResult($planets_query);

        return $planet_block;
    }

    /**
     * method get_user_rank
     * param
     * return the current user rank
     */
    private function get_user_rank()
    {
        $user_rank = '-';
        $total_rank = $this->_current_user['user_statistic_total_rank'] == '' ? $this->_current_planet['stats_users'] : $this->_current_user['user_statistic_total_rank'];

        if ($this->_noob->isRankVisible($this->_current_user['user_authlevel'])) {
            $user_rank = FormatLib::prettyNumber($this->_current_user['user_statistic_total_points']) . " (" . $this->_lang['ov_place'] . ' ' . FunctionsLib::setUrl('game.php?page=statistics&range=' . $total_rank, $total_rank, $total_rank) . ' ' . $this->_lang['ov_of'] . ' ' . $this->_current_planet['stats_users'] . ")";
        }

        return $user_rank;
    }
}

/* end of overview.php */
