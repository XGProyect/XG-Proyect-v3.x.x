<?php
/**
 * Statistics Controller
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
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;
use App\libraries\Users;

/**
 * Statistics Class
 */
class Statistics extends BaseController
{
    public const MODULE_ID = 16;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // load Model
        parent::loadModel('game/statistics');

        // load Language
        parent::loadLang(['game/global', 'game/statistics']);
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
    private function buildPage()
    {
        $parse = $this->langs->language;
        $who = (isset($_POST['who'])) ? $_POST['who'] : ((isset($_GET['who'])) ? $_GET['who'] : 1);
        $type = (isset($_POST['type'])) ? $_POST['type'] : ((isset($_GET['type'])) ? $_GET['type'] : 1);
        $range = (isset($_POST['range'])) ? $_POST['range'] : ((isset($_GET['range'])) ? $_GET['range'] : 1);

        $parse['who'] = "<option value=\"1\"" . (($who == "1") ? " SELECTED" : "") . ">" . $this->langs->line('st_player') . "</option>";
        $parse['who'] .= "<option value=\"2\"" . (($who == "2") ? " SELECTED" : "") . ">" . $this->langs->line('st_alliance') . "</option>";

        $parse['type'] = "<option value=\"1\"" . (($type == "1") ? " SELECTED" : "") . ">" . $this->langs->line('st_points') . "</option>";
        $parse['type'] .= "<option value=\"2\"" . (($type == "2") ? " SELECTED" : "") . ">" . $this->langs->line('st_fleets') . "</option>";
        $parse['type'] .= "<option value=\"3\"" . (($type == "3") ? " SELECTED" : "") . ">" . $this->langs->line('st_researh') . "</option>";
        $parse['type'] .= "<option value=\"4\"" . (($type == "4") ? " SELECTED" : "") . ">" . $this->langs->line('st_buildings') . "</option>";
        $parse['type'] .= "<option value=\"5\"" . (($type == "5") ? " SELECTED" : "") . ">" . $this->langs->line('st_defenses') . "</option>";

        $data = $this->ranking_type($type);
        $Order = $data['order'];
        $Points = $data['points'];
        $Rank = $data['rank'];
        $OldRank = $data['oldrank'];

        if ($who == 2) {
            $MaxAllys = $this->Statistics_Model->countAlliances();

            $parse['range'] = $this->build_range_list($MaxAllys, $range);
            $parse['stat_header'] = $this->template->set(
                'stat/stat_alliancetable_header',
                $parse
            );

            $start = floor(intval($range / 100) % 100) * 100;
            $query = $this->Statistics_Model->getAlliances($Order, $start);

            $start++;

            $parse['stat_date'] = Timing::formatExtendedDate(Functions::readConfig('stat_last_update'));
            $parse['stat_values'] = "";

            foreach ($query as $StatRow) {
                $parse['ally_rank'] = $start;
                $ranking = $StatRow['alliance_statistic_' . $OldRank] - $StatRow['alliance_statistic_' . $Rank];
                $parse['ally_rankplus'] = $this->rank_difference($ranking);
                $parse['ally_id'] = $StatRow['alliance_id'];
                $parse['alliance_name'] = $StatRow['alliance_name'];
                $parse['ally_members'] = $StatRow['ally_members'];
                $parse['ally_action'] = $StatRow['alliance_request_notallow'] == 1 ? '<a href="game.php?page=alliance&mode=apply&allyid=' . $StatRow['alliance_id'] . '"><img src="' . DPATH . 'img/m.gif" border="0" title="' . $this->langs->line('st_ally_request') . '" /></a>' : '';
                $parse['ally_points'] = FormatLib::prettyNumber($StatRow['alliance_statistic_' . $Order]);
                $parse['ally_members_points'] = FormatLib::prettyNumber(floor($StatRow['alliance_statistic_' . $Order] / $StatRow['ally_members']));
                $parse['stat_values'] .= $this->template->set(
                    'stat/stat_alliancetable',
                    $parse
                );

                $start++;
            }
        } else {
            $parse['range'] = $this->build_range_list($this->planet['stats_users'], $range);
            $parse['stat_header'] = $this->template->set(
                'stat/stat_playertable_header',
                $parse
            );

            $start = floor(intval($range / 100) % 100) * 100;
            $query = $this->Statistics_Model->getUsers($Order, $start);

            $start++;
            $parse['stat_date'] = Timing::formatExtendedDate(Functions::readConfig('stat_last_update'));
            $parse['stat_values'] = "";
            $previusId = 0;

            foreach ($query as $StatRow) {
                $parse['player_rank'] = $start;
                $ranking = $StatRow['user_statistic_' . $OldRank] - $StatRow['user_statistic_' . $Rank];

                if ($StatRow['user_id'] == $this->user['user_id']) {
                    $parse['player_name'] = "<font color=\"lime\">" . $StatRow['user_name'] . "</font>";
                } else {
                    $parse['player_name'] = $StatRow['user_name'];
                }

                if ($StatRow['user_id'] != $this->user['user_id']) {
                    $parse['player_mes'] = '<a href="game.php?page=chat&playerId=' . $StatRow['user_id'] . '"><img src="' . DPATH . 'img/m.gif" border="0" title="' . $this->langs->line('write_message') . '" /></a>';
                } else {
                    $parse['player_mes'] = "";
                }

                if ($StatRow['alliance_name'] != '') {
                    if ($StatRow['alliance_name'] == $this->user['alliance_name']) {
                        $parse['player_alliance'] = '<a href="game.php?page=alliance&mode=ainfo&allyid=' . $StatRow['user_ally_id'] . '"><font color="#33CCFF">[' . $StatRow['alliance_name'] . ']</font></a>';
                    } else {
                        $parse['player_alliance'] = '<a href="game.php?page=alliance&mode=ainfo&allyid=' . $StatRow['user_ally_id'] . '">[' . $StatRow['alliance_name'] . ']</a>';
                    }
                } else {
                    $parse['player_alliance'] = '';
                }

                $parse['player_rankplus'] = $this->rank_difference($ranking);
                $parse['player_points'] = FormatLib::prettyNumber($StatRow['user_statistic_' . $Order]);
                $parse['stat_values'] .= $this->template->set(
                    'stat/stat_playertable',
                    $parse
                );
                $start++;
            }
        }

        $this->page->display(
            $this->template->set(
                'stat/stat_body',
                $parse
            )
        );
    }

    /**
     * method rank_difference
     * param $ranking
     * return return the rank difference between update and update and returns it formated
     */
    private function rank_difference($ranking)
    {
        if ($ranking == 0) {
            return '<font color="#87CEEB">*</font>';
        }

        if ($ranking < 0) {
            return '<font color="red">' . $ranking . '</font>';
        }

        if ($ranking > 0) {
            return '<font color="green">+' . $ranking . '</font>';
        }
    }

    /**
     * method build_range_list
     * param $count
     * param $range
     * return the list of range values
     */
    private function build_range_list($count, $range)
    {
        $range_list = '';
        $last_page = 0;

        // SET LAST PAGE
        if ($count > 100) {
            $last_page = floor($count / 100);
        }

        // LOOP TO BUILD THE VALUES LIST
        for ($page = 0; $page <= $last_page; $page++) {
            $page_value = $page * 100 + 1;
            $page_range = $page_value + 99;
            $range_list .= "<option value=\"" . $page_value . "\"" . (($range >= $page_value && $range <= $page_range) ? " SELECTED" : "") . ">" . $page_value . "-" . $page_range . "</option>";
        }

        return $range_list; // RETURN THE LIST
    }

    /**
     * method ranking_type
     * param $type
     * return the configurations or values for the current statistics type
     */
    private function ranking_type($type)
    {
        // SWITCH TYPE
        switch ($type) {
            case 1: // TOTAL POINTS
            default:
                $return['order'] = "total_points";
                $return['points'] = "total_points";
                $return['rank'] = "total_rank";
                $return['oldrank'] = "total_old_rank";
                break;

            case 2: // SHIPS
                $return['order'] = "ships_points";
                $return['points'] = "ships_points";
                $return['rank'] = "ships_rank";
                $return['oldrank'] = "ships_old_rank";
                break;

            case 3: // TECHNOLOGY
                $return['order'] = "technology_points";
                $return['points'] = "technology_points";
                $return['rank'] = "technology_rank";
                $return['oldrank'] = "technology_old_rank";
                break;

            case 4: // BUILDINGS
                $return['order'] = "buildings_points";
                $return['points'] = "buildings_points";
                $return['rank'] = "buildings_rank";
                $return['oldrank'] = "buildings_old_rank";
                break;

            case 5: // DEFENSE
                $return['order'] = "defenses_points";
                $return['points'] = "defenses_points";
                $return['rank'] = "defenses_rank";
                $return['oldrank'] = "defenses_old_rank";
                break;
        }

        return $return;
    }
}
