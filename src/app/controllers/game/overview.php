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

namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\PlanetTypesEnumerator;
use App\helpers\UrlHelper;
use App\libraries\DevelopmentsLib;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;
use App\libraries\UpdatesLibrary;
use App\libraries\Users;

/**
 * Overview Class
 */
class Overview extends BaseController
{
    public const MODULE_ID = 1;

    /**
     * @var mixed
     */
    private $_noob;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Model
        parent::loadModel('game/overview');

        // load Language
        parent::loadLang(['game/global', 'game/overview', 'game/buildings', 'game/constructions']);

        $this->_noob = Functions::loadLibrary('NoobsProtectionLib');
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // build the page
        $this->buildPage();
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage(): void
    {
        $moon = $this->getPlanetMoon();

        $data = [
            'dpath' => DPATH,
            'planet_name' => $this->planet['planet_name'],
            'user_name' => $this->user['user_name'],
            'date_time' => Timing::formatExtendedDate(time()),
            'Have_new_message' => $this->getMessages(),
            'fleet_list' => $this->getFleetMovements(),
            'planet_image' => $this->planet['planet_image'],
            'building' => $this->getCurrentWork($this->planet),
            'moon_img' => $moon['moon_img'],
            'moon' => $moon['moon'],
            'anothers_planets' => $this->getPlanets(),
            'planet_diameter' => FormatLib::prettyNumber($this->planet['planet_diameter']),
            'planet_field_current' => $this->planet['planet_field_current'],
            'planet_field_max' => DevelopmentsLib::maxFields($this->planet),
            'planet_temp_min' => $this->planet['planet_temp_min'],
            'planet_temp_max' => $this->planet['planet_temp_max'],
            'galaxy_galaxy' => $this->planet['planet_galaxy'],
            'galaxy_system' => $this->planet['planet_system'],
            'galaxy_planet' => $this->planet['planet_planet'],
            'user_rank' => $this->getUserRank(),
        ];

        // DISPLAY THE RESULT PAGE
        $this->page->display(
            $this->template->set(
                'overview/overview_body',
                array_merge($this->langs->language, $data)
            )
        );
    }

    /**
     * method getPlanets
     * param $user_planet
     * param $is_current_planet
     * return building in progress or free text
     */
    private function getCurrentWork($user_planet, $is_current_planet = true)
    {
        // THE PLANET IS "FREE" BY DEFAULT
        $building_block = $this->langs->line('ov_free');

        if (!$is_current_planet) {
            // UPDATE THE PLANET INFORMATION FIRST, MAY BE SOMETHING HAS JUST FINISHED
            UpdatesLibrary::updateBuildingsQueue($user_planet, $this->user);
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
                    $building_block .= $this->langs->language[$this->objects->getObjects($building)] . ' (' . $level . ')';
                    $building_block .= "<br /><div id=\"blc\" class=\"z\">" . FormatLib::prettyTime($time_to_end) . "</div>";
                    $building_block .= "\n<script language=\"JavaScript\">";
                    $building_block .= "\n	pp = \"" . $time_to_end . "\";\n";
                    $building_block .= "\n	pk = \"" . 1 . "\";\n";
                    $building_block .= "\n	pm = \"cancel\";\n";
                    $building_block .= "\n	pl = \"" . $this->planet['planet_id'] . "\";\n";
                    $building_block .= "\n	t();\n";
                    $building_block .= "\n</script>\n";
                } else {
                    $building_block = '' . $this->langs->language[$this->objects->getObjects($building)] . ' (' . $level . ')';
                    $building_block .= '<br><font color="#7f7f7f">(' . FormatLib::prettyTime($time_to_end) . ')</font>';
                }
            }
        }

        // BACK TO THE PLANET!
        return $building_block;
    }

    /**
     * method getMessages
     * param
     * return messages row
     */
    private function getMessages()
    {
        $new_message = '';

        if ($this->user['new_message'] != 0) {
            $new_message = '<tr>';

            if ($this->user['new_message'] == 1) {
                $new_message .= '<th role="cell" colspan="4">' . UrlHelper::setUrl('game.php?page=messages', $this->langs->line('ov_have_new_message'), $this->langs->line('ov_have_new_message')) . '</th>';
            }

            if ($this->user['new_message'] > 1) {
                $link_text = str_replace('%m', FormatLib::prettyNumber($this->user['new_message']), $this->langs->line('ov_have_new_messages'));
                $new_message .= '<th role="cell" colspan="4">' . UrlHelper::setUrl('game.php?page=messages', $link_text, $link_text) . '</th>';
            }

            $new_message .= '</tr>';
        }

        return $new_message;
    }

    /**
     * method getFleetMovements
     * param
     * return fleets movements rows
     */
    private function getFleetMovements()
    {
        $fleet = '';
        $fleet_row = [];
        $record = 0;

        $own_fleets = $this->Overview_Model->getOwnFleets($this->user['user_id']);

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

            if ($fleets['fleet_owner'] == $this->user['user_id']) {
                $record++;

                $label = 'fs';
                $start_block_id = (string) $start_time . $id;
                $stay_block_id = (string) $stay_time . $id;
                $end_block_id = (string) $end_time . $id;

                $fleet_row[$start_block_id] = !isset($fleet_row[$start_block_id]) ? '' : $fleet_row[$start_block_id];
                $fleet_row[$stay_block_id] = !isset($fleet_row[$stay_block_id]) ? '' : $fleet_row[$stay_block_id];
                $fleet_row[$end_block_id] = !isset($fleet_row[$end_block_id]) ? '' : $fleet_row[$end_block_id];

                if ($start_time > time()) {
                    $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, true, $label, $record, $this->user);
                }

                if (($fleets['fleet_mission'] != 4) && ($fleets['fleet_mission'] != 10)) {
                    $label = 'ft';

                    if ($stay_time > time()) {
                        $fleet_row[$stay_block_id] = FleetsLib::flyingFleetsTable($fleets, 1, true, $label, $record, $this->user);
                    }

                    $label = 'fe';

                    if ($end_time > time()) {
                        $fleet_row[$end_block_id] = FleetsLib::flyingFleetsTable($fleets, 2, true, $label, $record, $this->user);
                    }
                }

                if ($fleets['fleet_mission'] == 4 && $start_time < time() && $end_time > time()) {
                    $fleet_row[$end_block_id] = FleetsLib::flyingFleetsTable($fleets, 2, true, 'none', $record, $this->user);
                }
            }

            ######################################
            #
            # incoming fleets
            #
            ######################################
            if ($fleets['fleet_owner'] != $this->user['user_id']) {
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
                            $this->user
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
                        $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->user);
                    }
                }
            }

            ######################################
            #
            # other fleets
            #
            ######################################

            if ($fleets['fleet_owner'] != $this->user['user_id']) {
                $acs_member = false;

                if (in_array($this->user['user_id'], explode(',', $fleets['acs_members'] ?? ''))) {
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
                        $fleet_row[$start_block_id] = FleetsLib::flyingFleetsTable($fleets, 0, false, 'ofs', $record, $this->user, $acs_member);
                    }
                    if ($fleets['fleet_mission'] == 5) {
                        if ($stay_time > time()) {
                            $fleet_row[$stay_block_id] = FleetsLib::flyingFleetsTable($fleets, 1, false, 'oft', $record, $this->user, $acs_member);
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
     * method getPlanetMoon
     * param
     * return the moon image and data for the current planet
     */
    private function getPlanetMoon()
    {
        $return['moon_img'] = '';
        $return['moon'] = '';

        if ($this->planet['moon_id'] != 0 && $this->planet['moon_destroyed'] == 0 && $this->planet['planet_type'] == PlanetTypesEnumerator::PLANET) {
            $moon_name = $this->planet['moon_name'] . " (" . $this->langs->line('moon') . ")";
            $url = 'game.php?page=overview&cp=' . $this->planet['moon_id'] . '&re=0';
            $image = DPATH . 'planets/' . $this->planet['moon_image'] . '.jpg';
            $attributes = 'height="50" width="50"';

            $return['moon_img'] = UrlHelper::setUrl($url, Functions::setImage($image, $moon_name, $attributes), $moon_name);
            $return['moon'] = $moon_name;
        }

        return $return;
    }

    /**
     * method getPlanets
     * param
     * return all the user planets
     */
    private function getPlanets()
    {
        $colony = 1;

        $planets_query = $this->Overview_Model->getPlanets($this->user['user_id']);
        $planet_block = '<tr>';

        foreach ($planets_query as $user_planet) {
            if ($user_planet['planet_id'] != $this->user['user_current_planet'] && $user_planet['planet_type'] != PlanetTypesEnumerator::MOON) {
                $url = 'game.php?page=overview&cp=' . $user_planet['planet_id'] . '&re=0';
                $image = DPATH . 'planets/small/s_' . $user_planet['planet_image'] . '.jpg';
                $attributes = 'height="50" width="50"';

                $planet_block .= '<th>' . $user_planet['planet_name'] . '<br>';
                $planet_block .= UrlHelper::setUrl($url, Functions::setImage($image, $user_planet['planet_name'], $user_planet['planet_name'], $attributes));
                $planet_block .= '<center>';
                $planet_block .= $this->getCurrentWork($user_planet, false);
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
        $total_rank = $this->user['user_statistic_total_rank'] == '' ? $this->planet['stats_users'] : $this->user['user_statistic_total_rank'];

        if ($this->_noob->isRankVisible($this->user['user_authlevel'])) {
            $user_rank = FormatLib::prettyNumber($this->user['user_statistic_total_points']) . " (" . $this->langs->line('ov_place') . ' ' . $total_rank . ' ' . $this->langs->line('ov_of') . ' ' . $this->planet['stats_users'] . ")";
        }

        return $user_rank;
    }
}
