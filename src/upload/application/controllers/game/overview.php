<?php
/**
 * Overview Controller
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
use application\core\enumerators\PlanetTypesEnumerator;
use application\helpers\UrlHelper;
use application\libraries\DevelopmentsLib;
use application\libraries\FleetsLib;
use application\libraries\FormatLib;
use application\libraries\FunctionsLib;
use application\libraries\TimingLibrary as Timing;
use application\libraries\UpdatesLibrary;

/**
 * Overview Class
 */
class Overview extends Controller
{
    const MODULE_ID = 1;

    /**
     * @var mixed
     */
    private $_current_user;
    /**
     * @var mixed
     */
    private $_current_planet;
    /**
     * @var mixed
     */
    private $_noob;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        parent::$users->checkSession();

        // load Model
        parent::loadModel('game/overview');

        // load Language
        parent::loadLang(['game/global', 'game/overview', 'game/buildings', 'game/constructions']);

        // Check module access
        FunctionsLib::moduleMessage(FunctionsLib::isModuleAccesible(self::MODULE_ID));

        $this->_current_user = parent::$users->getUserData();
        $this->_current_planet = parent::$users->getPlanetData();
        $this->_noob = FunctionsLib::loadLibrary('NoobsProtectionLib');

        $this->build_page();
    }

    /**
     * method build_page
     * param
     * return main method, loads everything
     */
    private function build_page()
    {
        // SOME DEFAULT VALUES
        $parse = $this->langs->language;
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
        $parse['date_time'] = Timing::formatExtendedDate(time());
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
        $parse['user_rank'] = $this->getUserRank();

        // DISPLAY THE RESULT PAGE
        parent::$page->display(
            $this->getTemplate()->set(
                'overview/overview_body',
                $parse
            )
        );
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
        $building_block = $this->langs->line('ov_free');

        if (!$is_current_planet) {
            // UPDATE THE PLANET INFORMATION FIRST, MAY BE SOMETHING HAS JUST FINISHED
            UpdatesLibrary::updateBuildingsQueue($user_planet, $this->_current_user);
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
                    $building_block = DevelopmentsLib::currentBuilding("overview", $this->langs->language, $building);
                    $building_block .= $this->langs->language[$this->getObjects()->getObjects($building)] . ' (' . $level . ')';
                    $building_block .= "<br /><div id=\"blc\" class=\"z\">" . FormatLib::prettyTime($time_to_end) . "</div>";
                    $building_block .= "\n<script language=\"JavaScript\">";
                    $building_block .= "\n	pp = \"" . $time_to_end . "\";\n";
                    $building_block .= "\n	pk = \"" . 1 . "\";\n";
                    $building_block .= "\n	pm = \"cancel\";\n";
                    $building_block .= "\n	pl = \"" . $this->_current_planet['planet_id'] . "\";\n";
                    $building_block .= "\n	t();\n";
                    $building_block .= "\n</script>\n";
                } else {
                    $building_block = '' . $this->langs->language[$this->getObjects()->getObjects($building)] . ' (' . $level . ')';
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
                $new_message .= '<th colspan="4">' . UrlHelper::setUrl('game.php?page=messages', $this->langs->line('ov_have_new_message'), $this->langs->line('ov_have_new_message')) . '</th>';
            }

            if ($this->_current_user['new_message'] > 1) {
                $link_text = str_replace('%m', FormatLib::prettyNumber($this->_current_user['new_message']), $this->langs->line('ov_have_new_messages'));
                $new_message .= '<th colspan="4">' . UrlHelper::setUrl('game.php?page=messages', $link_text, $link_text) . '</th>';
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
        $fleet_row = [];
        $record = 0;

        $own_fleets = $this->Overview_Model->getOwnFleets($this->_current_user['user_id']);

        foreach ($own_fleets as $fleets) {
            ######################################
            #
            # own fleets
            #
            ######################################

            $start_time = $fleets['fleet_start_time'];
            $stay_time = $fleets['fleet_end_stay'];
            $end_time = $fleets['fleet_end_time'];

            $fleet_status = $fleets['fleet_mess'];
            $fleet_group = $fleets['fleet_group'];
            $id = $fleets['fleet_id'];

            if ($fleets['fleet_owner'] == $this->_current_user['user_id']) {
                $record++;

                $label = 'fs';
                $start_block_id = (string) $start_time . $id;
                $stay_block_id = (string) $stay_time . $id;
                $end_block_id = (string) $end_time . $id;

                $fleet_row[$start_block_id] = !isset($fleet_row[$start_block_id]) ? '' : $fleet_row[$start_block_id];
                $fleet_row[$stay_block_id] = !isset($fleet_row[$stay_block_id]) ? '' : $fleet_row[$stay_block_id];
                $fleet_row[$end_block_id] = !isset($fleet_row[$end_block_id]) ? '' : $fleet_row[$end_block_id];

                if ($start_time > time()) {
                    $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, true, $label, $record, $this->_current_user);
                }

                if (($fleets['fleet_mission'] != 4) && ($fleets['fleet_mission'] != 10)) {
                    $label = 'ft';

                    if ($stay_time > time()) {
                        $fleet_row[$stay_block_id] = FleetsLib::flyingFleetsTable($fleets, 1, true, $label, $record, $this->_current_user);
                    }

                    $label = 'fe';

                    if ($end_time > time()) {
                        $fleet_row[$end_block_id] = FleetsLib::flyingFleetsTable($fleets, 2, true, $label, $record, $this->_current_user);
                    }
                }

                if ($fleets['fleet_mission'] == 4 && $start_time < time() && $end_time > time()) {
                    $fleet_row[$end_block_id] = FleetsLib::flyingFleetsTable($fleets, 2, true, 'none', $record, $this->_current_user);
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

                    $start_block_id = (string) $start_time . $id;
                    $fleet_row[$start_block_id] = !isset($fleet_row[$start_block_id]) ? '' : $fleet_row[$start_block_id];

                    if ($start_time > time()) {
                        $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable(
                            $fleets,
                            0,
                            false,
                            'ofs',
                            $record,
                            $this->_current_user
                        );
                    }
                }

                if (($fleets['fleet_mission'] == 1) && ($fleet_group > 0)) {
                    $record++;

                    if ($fleet_status > 0) {
                        $start_time = '';
                    } else {
                        $start_time = $fleets['fleet_start_time'];
                    }

                    $start_block_id = (string) $start_time . $id;
                    $fleet_row[$start_block_id] = !isset($fleet_row[$start_block_id]) ? '' : $fleet_row[$start_block_id];

                    if ($start_time > time()) {
                        $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->_current_user);
                    }
                }
            }

            ######################################
            #
            # other fleets
            #
            ######################################

            if ($fleets['fleet_owner'] != $this->_current_user['user_id']) {
                $acs_member = false;

                if (in_array($this->_current_user['user_id'], explode(',', $fleets['acs_members']))) {
                    $acs_member = true;
                }

                if ($fleets['fleet_mission'] != 8) {
                    $record++;

                    $start_time = $fleets['fleet_start_time'];
                    $stay_time = $fleets['fleet_end_stay'];
                    $id = $fleets['fleet_id'];

                    $start_block_id = (string) $start_time . $id;
                    $stay_block_id = (string) $stay_time . $id;

                    $fleet_row[$start_block_id] = !isset($fleet_row[$start_block_id]) ? '' : $fleet_row[$start_block_id];
                    $fleet_row[$stay_block_id] = !isset($fleet_row[$stay_block_id]) ? '' : $fleet_row[$stay_block_id];

                    if ($start_time > time()) {
                        $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->_current_user, $acs_member);
                    }
                    if ($fleets['fleet_mission'] == 5) {
                        if ($stay_time > time()) {
                            $fleet_row[$stay_block_id] = FleetsLib::flyingFleetsTable($fleets, 1, false, 'oft', $record, $this->_current_user, $acs_member);
                        }
                    }
                }
            }
        }

        unset($own_fleets);

        if (count($fleet_row) > 0 && $fleet_row != '') {
            ksort($fleet_row);

            foreach ($fleet_row as $time => $content) {
                $fleet .= $content . "\n";
            }

            unset($fleet_row);
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

        if ($this->_current_planet['moon_id'] != 0 && $this->_current_planet['moon_destroyed'] == 0 && $this->_current_planet['planet_type'] == PlanetTypesEnumerator::PLANET) {
            $moon_name = $this->_current_planet['moon_name'] . " (" . $this->langs->line('moon') . ")";
            $url = 'game.php?page=overview&cp=' . $this->_current_planet['moon_id'] . '&re=0';
            $image = DPATH . 'planets/' . $this->_current_planet['moon_image'] . '.jpg';
            $attributes = 'height="50" width="50"';

            $return['moon_img'] = UrlHelper::setUrl($url, FunctionsLib::setImage($image, $moon_name, $attributes), $moon_name);
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

        $planets_query = $this->Overview_Model->getPlanets($this->_current_user['user_id']);
        $planet_block = '<tr>';

        foreach ($planets_query as $user_planet) {
            if ($user_planet['planet_id'] != $this->_current_user['user_current_planet'] && $user_planet['planet_type'] != PlanetTypesEnumerator::MOON) {
                $url = 'game.php?page=overview&cp=' . $user_planet['planet_id'] . '&re=0';
                $image = DPATH . 'planets/small/s_' . $user_planet['planet_image'] . '.jpg';
                $attributes = 'height="50" width="50"';

                $planet_block .= '<th>' . $user_planet['planet_name'] . '<br>';
                $planet_block .= UrlHelper::setUrl($url, FunctionsLib::setImage($image, $user_planet['planet_name'], $user_planet['planet_name'], $attributes));
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
        unset($planets_query);

        return $planet_block;
    }

    /**
     * method getUserRank
     * param
     * return the current user rank
     */
    private function getUserRank()
    {
        $user_rank = '-';
        $total_rank = $this->_current_user['user_statistic_total_rank'] == '' ? $this->_current_planet['stats_users'] : $this->_current_user['user_statistic_total_rank'];

        if ($this->_noob->isRankVisible($this->_current_user['user_authlevel'])) {
            $user_rank = FormatLib::prettyNumber($this->_current_user['user_statistic_total_points']) . " (" . $this->langs->line('ov_place') . ' ' . UrlHelper::setUrl('game.php?page=statistics&range=' . $total_rank, $total_rank, $total_rank) . ' ' . $this->langs->line('ov_of') . ' ' . $this->_current_planet['stats_users'] . ")";
        }

        return $user_rank;
    }
}
