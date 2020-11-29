<?php
/**
 * Alliances Controller
 *
 * @category Controller
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\controllers\adm;

use App\core\BaseController;
use App\core\enumerators\AllianceRanksEnumerator as AllianceRanks;
use App\core\enumerators\SwitchIntEnumerator as SwitchInt;
use App\libraries\adm\AdministrationLib as Administration;
use App\libraries\alliance\Ranks;
use App\libraries\Functions;

/**
 * Alliances Class
 */
class Alliances extends BaseController
{
    /**
     * @var mixed
     */
    private $_edit;
    /**
     * @var mixed
     */
    private $_planet;
    /**
     * @var mixed
     */
    private $_moon;
    /**
     * @var mixed
     */
    private $_id;
    /**
     * @var mixed
     */
    private $_alert_info;
    /**
     * @var mixed
     */
    private $_alert_type;
    /**
     * @var mixed
     */
    private $_user_query;

    /**
     * Contains the alliance ranks
     *
     * @var Ranks
     */
    private $ranks = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Administration::checkSession();

        // load Model
        parent::loadModel('adm/alliances');

        // load Language
        parent::loadLang(['adm/global', 'adm/alliances']);
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // check if the user is allowed to access
        if (!Administration::authorization(__CLASS__, (int) $this->user['user_authlevel'])) {
            die(Administration::noAccessMessage($this->langs->line('no_permissions')));
        }

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
        $parse = $this->langs->language;
        $parse['alert'] = '';
        $alliance = isset($_GET['alliance']) ? trim($_GET['alliance']) : null;
        $type = isset($_GET['type']) ? trim($_GET['type']) : null;
        $this->_edit = isset($_GET['edit']) ? trim($_GET['edit']) : null;

        if ($alliance != '') {
            if (!$this->checkAlliance($alliance)) {
                $parse['alert'] = Administration::saveMessage('error', $this->langs->line('al_nothing_found'));
                $alliance = '';
            } else {
                $this->_alliance_query = $this->Alliances_Model->getAllAllianceDataById($this->_id);
                $this->ranks = new Ranks($this->_alliance_query['alliance_ranks']);

                if ($_POST) {
                    // save the data
                    $this->saveData($type);
                }
            }
        }

        $parse['al_sub_title'] = '';
        $parse['type'] = ($type != '') ? $type : 'info';
        $parse['alliance'] = ($alliance != '') ? $alliance : '';
        $parse['status'] = ($alliance != '') ? '' : ' disabled';
        $parse['status_box'] = ($alliance != '') ? '' : ' disabled';
        $parse['tag'] = ($alliance != '') ? 'a' : 'button';
        $parse['content'] = ($alliance != '' && $type != '') ? $this->getData($type) : '';

        parent::$page->displayAdmin(
            $this->getTemplate()->set('adm/alliances_view', $parse)
        );
    }

    /**
     * method getData
     * param $type
     * return the page for the current type
     */
    private function getData($type)
    {
        switch ($type) {
            case 'info':
            case '':
            default:
                return $this->getDataInfo();

                break;

            case 'ranks':
                return $this->getDataRanks();

                break;

            case 'members':
                return $this->getDataMembers();

                break;
        }
    }

    /**
     * method saveData
     * param $type
     * return save data for the current type
     */
    private function saveData($type)
    {
        switch ($type) {
            case 'info':
            case '':
            default:
                // save the data
                if (isset($_POST['send_data']) && $_POST['send_data']) {
                    $this->saveInfo();
                }

                break;

            case 'ranks':
                $this->saveRanks();

                break;

            case 'members':
                $this->saveMembers();

                break;
        }
    }
    ######################################
    #
    # getData methods
    #
    ######################################

    /**
     * method getDataInfo
     * param
     * return the information page for the current alliance
     */
    private function getDataInfo()
    {
        $parse = $this->langs->language;
        $parse += (array) $this->_alliance_query;
        $parse['al_alliance_information'] = str_replace('%s', $this->_alliance_query['alliance_name'], $this->langs->line('al_alliance_information'));
        $parse['alliance_register_time'] = ($this->_alliance_query['alliance_register_time'] == 0) ? '-' : date(Functions::readConfig('date_format_extended'), $this->_alliance_query['alliance_register_time']);
        $parse['alliance_owner_picker'] = $this->buildUsersCombo($this->_alliance_query['alliance_owner']);
        $parse['sel1'] = $this->_alliance_query['alliance_request_notallow'] == 1 ? 'selected' : '';
        $parse['sel0'] = $this->_alliance_query['alliance_request_notallow'] == 0 ? 'selected' : '';
        $parse['alert_info'] = ($this->_alert_type != '') ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set("adm/alliances_information_view", $parse);
    }

    /**
     * method getDataRanks
     * param
     * return the ranks page for the current alliance
     */
    private function getDataRanks()
    {
        $parse = $this->langs->language;
        $parse['al_alliance_ranks'] = str_replace('%s', $this->_alliance_query['alliance_name'], $this->langs->line('al_alliance_ranks'));
        $parse['image_path'] = DEFAULT_SKINPATH;
        $alliance_ranks = $this->ranks->getAllRanksAsArray();
        $i = 0;
        $rank_row = '';

        // build the UI
        $rank_data = [];

        if (is_array($alliance_ranks)) {
            foreach ($alliance_ranks as $rank_id => $details) {
                $rank_data['name'] = $details['rank'];
                $rank_data['delete'] = (($details['rights'][AllianceRanks::DELETE] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['kick'] = (($details['rights'][AllianceRanks::KICK] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['bewerbungen'] = (($details['rights'][AllianceRanks::APPLICATIONS] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['memberlist'] = (($details['rights'][AllianceRanks::VIEW_MEMBER_LIST] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['bewerbungenbearbeiten'] = (($details['rights'][AllianceRanks::APPLICATION_MANAGEMENT] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['administrieren'] = (($details['rights'][AllianceRanks::ADMINISTRATION] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['onlinestatus'] = (($details['rights'][AllianceRanks::ONLINE_STATUS] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['mails'] = (($details['rights'][AllianceRanks::SEND_CIRCULAR] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['rechtehand'] = (($details['rights'][AllianceRanks::RIGHT_HAND] == SwitchInt::on) ? ' checked="checked"' : '');
                $rank_data['i'] = $i++;

                $rank_row .= $this->getTemplate()->set("adm/alliances_ranks_row_view", $rank_data);
            }
        }

        $parse['ranks_table'] = empty($rank_row) ? $this->langs->line('al_no_ranks') : $rank_row;
        $parse['alert_info'] = ($this->_alert_type != '') ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set("adm/alliances_ranks_view", $parse);
    }

    /**
     * method getDataMembers
     * param
     * return the research page for the current user
     */
    private function getDataMembers()
    {
        $parse = $this->langs->language;
        $parse['al_alliance_members'] = str_replace(
            '%s',
            $this->_alliance_query['alliance_name'],
            $this->langs->line('al_alliance_members')
        );
        $all_members = $this->Alliances_Model->getAllianceMembers($this->_id);

        $members = '';

        if (!empty($all_members)) {
            foreach ($all_members as $member) {
                $member['alliance_request'] = ($member['user_ally_request']) ? $this->langs->line('al_request_yes') : $this->langs->line('al_request_no');
                $member['ally_request_text'] = ($member['user_ally_request_text']) ? $this->langs->line('ally_request_text') : '-';
                $member['alliance_register_time'] = date(Functions::readConfig('date_format_extended'), $member['user_ally_register_time']);

                if (isset($member['user_ally_rank_id'])) {
                    $member['ally_rank'] = $this->ranks->getRankById($member['user_ally_rank_id'])['rank'];
                } else {
                    $member['ally_rank'] = $this->langs->line('al_rank_not_defined');
                }

                $members .= $this->getTemplate()->set('adm/alliances_members_row_view', $member);
            }
        }

        $parse['members_table'] = empty($members) ? '<tr><td colspan="6" class="align_center text-error">' . $this->langs->line('al_no_ranks') . '</td></tr>' : $members;
        $parse['alert_info'] = ($this->_alert_type != '') ? Administration::saveMessage($this->_alert_type, $this->_alert_info) : '';

        return $this->getTemplate()->set("adm/alliances_members_view", $parse);
    }
    ######################################
    #
    # save / update methods
    #
    ######################################

    /**
     * method saveInfo
     * param
     * return save information for the current user
     */
    private function saveInfo()
    {
        $alliance_name = isset($_POST['alliance_name']) ? $_POST['alliance_name'] : '';
        $alliance_name_orig = isset($_POST['alliance_name_orig']) ? $_POST['alliance_name_orig'] : '';
        $alliance_tag = isset($_POST['alliance_tag']) ? $_POST['alliance_tag'] : '';
        $alliance_tag_orig = isset($_POST['alliance_tag_orig']) ? $_POST['alliance_tag_orig'] : '';
        $alliance_owner = isset($_POST['alliance_owner']) ? $_POST['alliance_owner'] : '';
        $alliance_owner_orig = isset($_POST['alliance_owner_orig']) ? $_POST['alliance_owner_orig'] : '';
        $alliance_web = isset($_POST['alliance_web']) ? $_POST['alliance_web'] : '';
        $alliance_image = isset($_POST['alliance_image']) ? $_POST['alliance_image'] : '';
        $alliance_description = isset($_POST['alliance_description']) ? $_POST['alliance_description'] : '';
        $alliance_text = isset($_POST['alliance_text']) ? $_POST['alliance_text'] : '';
        $alliance_request = isset($_POST['alliance_request']) ? $_POST['alliance_request'] : '';
        $alliance_request_notallow = isset($_POST['alliance_request_notallow']) ? $_POST['alliance_request_notallow'] : '';

        $alliance_owner = (int) $alliance_owner;
        $alliance_request_notallow = (int) $alliance_request_notallow;
        $errors = '';

        if ($alliance_name != $alliance_name_orig) {
            if ($alliance_name == '' or !$this->Alliances_Model->checkAllianceName($alliance_name)) {
                $errors .= $this->langs->line('al_error_alliance_name') . '<br />';
            }
        }

        if ($alliance_tag != $alliance_tag_orig) {
            if ($alliance_tag == '' or !$this->Alliances_Model->checkAllianceTag($alliance_tag)) {
                $errors .= $this->langs->line('al_error_alliance_tag') . '<br />';
            }
        }

        if ($alliance_owner != $alliance_owner_orig) {
            if ($alliance_owner <= 0 or $this->Alliances_Model->checkAllianceFounder($alliance_owner)) {
                $errors .= $this->langs->line('al_error_founder') . '<br />';
            }
        }

        if ($errors != '') {
            $this->_alert_info = $errors;
            $this->_alert_type = 'warning';
        } else {
            $this->Alliances_Model->updateAllianceData([
                'alliance_name' => $alliance_name,
                'alliance_tag' => $alliance_tag,
                'alliance_owner' => $alliance_owner,
                'alliance_web' => $alliance_web,
                'alliance_image' => $alliance_image,
                'alliance_description' => $alliance_description,
                'alliance_text' => $alliance_text,
                'alliance_request' => $alliance_request,
                'alliance_request_notallow' => $alliance_request_notallow,
                'alliance_id' => $this->_id,
            ]);

            $this->_alert_info = $this->langs->line('al_all_ok_message');
            $this->_alert_type = 'ok';
        }
    }

    /**
     * method saveRanks
     * param
     * return save ranks for the current alliance
     */
    private function saveRanks()
    {
        if (isset($_POST['create_rank'])) {
            if (!empty($_POST['rank_name'])) {
                $this->ranks->addNew(
                    $_POST['rank_name']
                );

                $this->Alliances_Model->updateAllianceRanks(
                    $this->_id,
                    $this->ranks->getAllRanksAsJsonString()
                );

                $this->_alert_info = $this->langs->line('al_rank_added');
                $this->_alert_type = 'ok';
            } else {
                $this->_alert_info = $this->langs->line('al_required_name');
                $this->_alert_type = 'warning';
            }
        }

        // edit rights for each rank
        if (isset($_POST['save_ranks'])) {
            foreach ($_POST['id'] as $id) {
                $this->ranks->editRankById(
                    $id,
                    [
                        AllianceRanks::DELETE => isset($_POST['u' . $id . 'r1']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::KICK => isset($_POST['u' . $id . 'r2']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::APPLICATIONS => isset($_POST['u' . $id . 'r3']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::VIEW_MEMBER_LIST => isset($_POST['u' . $id . 'r4']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::APPLICATION_MANAGEMENT => isset($_POST['u' . $id . 'r5']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::ADMINISTRATION => isset($_POST['u' . $id . 'r6']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::ONLINE_STATUS => isset($_POST['u' . $id . 'r7']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::SEND_CIRCULAR => isset($_POST['u' . $id . 'r8']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::RIGHT_HAND => isset($_POST['u' . $id . 'r9']) ? SwitchInt::on : SwitchInt::off,
                    ]
                );
            }

            $this->Alliances_Model->updateAllianceRanks(
                $this->_id,
                $this->ranks->getAllRanksAsJsonString()
            );

            $this->_alert_info = $this->langs->line('al_rank_saved');
            $this->_alert_type = 'ok';
        }

        // delete a rank
        if (isset($_POST['delete_ranks'])) {
            foreach ($_POST['id'] as $rank_id) {
                $this->ranks->deleteRankById($rank_id);
            }

            $this->Alliances_Model->updateAllianceRanks(
                $this->_id,
                $this->ranks->getAllRanksAsJsonString()
            );

            $this->_alert_info = $this->langs->line('al_rank_removed');
            $this->_alert_type = 'ok';
        }

        Functions::redirect('admin.php?' . $_SERVER['QUERY_STRING']);
    }

    /**
     * method save_research
     * param
     * return save research for the current user
     */
    private function saveMembers()
    {
        if (isset($_POST['delete_members'])) {
            $ids_string = '';

            if (isset($_POST['delete_message'])) {
                foreach ($_POST['delete_message'] as $user_id => $delete_status) {
                    if ($delete_status == 'on' && $user_id > 0 && is_numeric($user_id)) {
                        $ids_string .= $user_id . ',';
                    }
                }

                $amount = $this->Alliances_Model->countAllianceMembers($this->_id);

                if ($amount['Amount'] > 1) {
                    $this->Alliances_Model->removeAllianceMembers($ids_string);

                    // RETURN THE ALERT
                    $this->_alert_info = $this->langs->line('us_all_ok_message');
                    $this->_alert_type = 'ok';
                } else {
                    // RETURN THE ALERT
                    $this->_alert_info = $this->langs->line('al_cant_delete_last_one');
                    $this->_alert_type = 'warning';
                }
            }
        }
    }
    ######################################
    #
    # build combo methods
    #
    ######################################

    /**
     * method buildUsersCombo
     * param $user_id
     * return the list of users
     */
    private function buildUsersCombo($user_id)
    {
        $combo_rows = '';
        $users = $this->Alliances_Model->getAllUsers();

        foreach ($users as $users_row) {
            $combo_rows .= '<option value="' . $users_row['user_id'] . '" ' . ($users_row['user_id'] == $user_id ? ' selected' : '') . '>' . $users_row['user_name'] . '</option>';
        }

        return $combo_rows;
    }
    ######################################
    #
    # other required methods
    #
    ######################################

    /**
     * method checkAlliance
     * param $alliance
     * return true if alliance exists, false if alliance doesn't exist
     */
    private function checkAlliance($alliance)
    {
        if ($alliance_query = $this->Alliances_Model->checkAllianceByNameOrTag($alliance)) {
            $this->_id = $alliance_query['alliance_id'];

            return true;
        }

        return false;
    }
}
