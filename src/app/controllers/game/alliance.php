<?php
/**
 * alliance.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @since    3.2.0
 */

namespace App\controllers\game;

use App\core\BaseController;
use App\core\enumerators\AllianceRanksEnumerator as AllianceRanks;
use App\core\enumerators\SwitchIntEnumerator as SwitchInt;
use App\helpers\StringsHelper;
use App\helpers\UrlHelper;
use App\libraries\alliance\Alliances;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\TimingLibrary as Timing;
use App\libraries\Users;

/**
 * Alliance Class
 */
class Alliance extends BaseController
{
    /**
     * The module ID
     *
     * @var int
     */
    public const MODULE_ID = 13;

    /**
     * Default alliance ranks
     *
     * @var array
     */
    public const DEFAULT_RANKS = [
        'founder' => 0,
        'newcomer' => 1,
    ];

    /**
     * Contains a BBCodeLib object
     *
     * @var \BBCodeLib
     */
    private $bbcode = null;

    /**
     * Contains an Alliance object
     *
     * @var \Alliance
     */
    private $alliance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // check if session is active
        Users::checkSession();

        // Check module access
        Functions::moduleMessage(Functions::isModuleAccesible(self::MODULE_ID));

        // load Model
        parent::loadModel('game/alliance');

        // load Language
        parent::loadLang(['game/alliance', 'game/global']);

        // load Library
        $this->bbcode = Functions::loadLibrary('BBCodeLib');

        // init a new buddy object
        $this->setUpAlliances();
    }

    /**
     * Users land here
     *
     * @return void
     */
    public function index(): void
    {
        // build the page
        $this->buildPage();
    }

    /**
     * Creates a new alliance object that will handle all the alliance
     * creation methods and actions
     *
     * @return void
     */
    private function setUpAlliances()
    {
        $this->alliance = new Alliances(
            $this->Alliance_Model->getAllianceDataById($this->getAllianceId()),
            $this->user['user_id'],
            $this->user['user_ally_rank_id']
        );
    }

    /**
     * Check if the page that the user is trying to access is allowed
     *
     * @return boolean
     */
    private function isPageAllowed()
    {
        $allowed_pages = [
            'public' => [
                'default', 'ainfo', 'make', 'search', 'apply',
            ],
            'awaitingApproval' => [
                'default', 'ainfo',
            ],
            'isMember' => [
                'default', 'ainfo', 'exit', 'memberslist', 'circular', 'admin',
            ],
        ];

        return in_array(
            $this->getCurrentSection(),
            array_filter($allowed_pages[$this->getUserAccess()], function ($value) {
                return $value;
            })
        );
    }

    /**
     * Get user level based on their alliance status
     *
     * @return string
     */
    private function getUserAccess()
    {
        // not in an alliance
        if ((int) $this->user['user_ally_id'] === 0) {
            // doesn't have a request
            if ((int) $this->user['user_ally_request'] === 0) {
                // it's public then
                return 'public';
            }

            // supposedly a request was sent
            return 'awaitingApproval';
        }

        // any other case
        return 'isMember';
    }

    /**
     * Get current alliance ID
     *
     * @return int
     */
    private function getAllianceId()
    {
        $alliance_id = filter_input(INPUT_GET, 'allyid', FILTER_VALIDATE_INT);

        if (!empty($alliance_id) && (int) $alliance_id != 0) {
            return $alliance_id;
        }

        if ($this->user['user_ally_id'] != 0) {
            return $this->user['user_ally_id'];
        }

        if ($this->user['user_ally_request'] != 0) {
            return $this->user['user_ally_request'];
        }
    }

    /**
     * Determine the current page and validate it
     *
     * @return string
     */
    private function getCurrentSection()
    {
        $mode = filter_input(INPUT_GET, 'mode');

        return (isset($mode) ? $mode : 'default');
    }

    /**
     * Build the page
     *
     * @return void
     */
    private function buildPage()
    {
        if (!$this->isPageAllowed()) {
            Functions::redirect('game.php?page=alliance');
        }

        $this->page->display(
            $this->{'get' . ucfirst($this->getCurrentSection()) . 'Section'}()
        );
    }

    /**
     *
     * PUBLIC / MEMBERS SECTIONS
     *
     */

    /**
     * Get alliance default section, calling the right method
     *
     * @return string
     */
    private function getDefaultSection()
    {
        return $this->{'getDefault' . ucfirst($this->getUserAccess()) . 'Section'}();
    }

    /**
     * Get alliance default menu for search and create
     *
     * @return type
     */
    private function getDefaultPublicSection()
    {
        return $this->template->set('alliance/alliance_default_menu_view', $this->langs->language);
    }

    /**
     * Get alliance default message for awaiting approval
     *
     * @return string
     */
    private function getDefaultAwaitingApprovalSection()
    {
        $cancel = filter_input(INPUT_POST, 'bcancel');
        $request_text = $this->langs->line('al_request_wait_message');
        $button_text = $this->langs->line('al_delete_request');

        if (!empty($cancel)) {
            $this->Alliance_Model->cancelUserRequestById($this->user['user_id']);
            $request_text = $this->langs->line('al_request_deleted');
            $button_text = $this->langs->line('al_continue');
        }

        return $this->template->set(
            'alliance/alliance_awaiting_view',
            array_merge(
                [
                    'request_text' => str_replace('%s', $this->alliance->getCurrentAlliance()->getAllianceTag(), $request_text),
                    'button_text' => $button_text,
                ],
                $this->langs->language
            )
        );
    }

    /**
     * Get default section for a member user
     *
     * @return string
     */
    private function getDefaultIsMemberSection()
    {
        $blocks = [
            'tag', 'name', 'members', 'rank', 'requests', 'circular', 'web',
        ];
        $details = [];

        foreach ($blocks as $block) {
            $data = $this->{'build' . ucfirst($block) . 'Block'}();

            if (empty($data['detail_content'])) {
                continue;
            }

            $details[] = $data;
        }

        return $this->template->set(
            'alliance/alliance_front_page_view',
            array_merge([
                'image' => $this->buildImageBlock(),
                'details' => $details,
                'description' => $this->buildDescriptionBlock(),
                'text' => $this->buildTextBlock(),
                'leave' => $this->buildLeaveBlock(),
            ], $this->langs->language)
        );
    }

    /**
     * Get alliance information section
     *
     * @return string
     */
    private function getAinfoSection()
    {
        return $this->template->set(
            'alliance/alliance_ainfo',
            array_merge(
                $this->langs->language,
                [
                    'image' => $this->buildImageBlock(),
                    'tag' => $this->alliance->getCurrentAlliance()->getAllianceTag(),
                    'name' => $this->alliance->getCurrentAlliance()->getAllianceName(),
                    'members' => $this->alliance->getCurrentAlliance()->getAllianceMembers(),
                    'description' => $this->buildDescriptionBlock(),
                    'web' => $this->buildWebBlock()['detail_content'],
                    'requests' => $this->buildPublicRequestsBlock(),
                ]
            )
        );
    }

    /**
     * Get alliance search section
     *
     * @return string
     */
    private function getSearchSection()
    {
        $search_string = filter_input(INPUT_POST, 'searchtext');
        $search_page = $this->template->set(
            'alliance/alliance_search_form_view',
            array_merge(['searchtext' => $search_string], $this->langs->language)
        );

        if (!empty($search_string)) {
            $list_of_results = [];
            $results = new Alliances(
                $this->Alliance_Model->searchAllianceByNameTag($search_string),
                $this->user['user_id']
            );

            foreach ($results->getAlliances() as $result) {
                $list_of_results[] = [
                    'ally_tag' => UrlHelper::setUrl('game.php?page=alliance&mode=apply&allyid=' . $result->getAllianceId(), $result->getAllianceTag()),
                    'alliance_name' => $result->getAllianceName(),
                    'ally_members' => $result->getAllianceMembers(),
                ];
            }

            $search_page .= $this->template->set(
                'alliance/alliance_search_results_view',
                array_merge(['list_of_results' => $list_of_results], $this->langs->language)
            );
        }

        return $search_page;
    }

    /**
     * Get alliance make section
     *
     * @return string
     */
    private function getMakeSection()
    {
        $action = filter_input_array(INPUT_POST);

        if (is_array($action)) {
            $alliance_tag = $action['atag'];
            $alliance_name = $action['aname'];

            if (strlen($alliance_tag) < 3 or strlen($alliance_tag) > 8) {
                Functions::message($this->langs->line('al_tag_required'), 'game.php?page=alliance&mode=make', 3);
            }

            if ($this->allianceTagExists($alliance_tag)) {
                Functions::message(strtr($this->langs->line('al_tag_already_exists'), ['%s' => $alliance_tag]), 'game.php?page=alliance&mode=make', 3);
            }

            if (strlen($alliance_name) < 3 or strlen($alliance_name) > 30) {
                Functions::message($this->langs->line('al_name_required'), 'game.php?page=alliance&mode=make', 3);
            }

            if ($this->allianceNameExists($alliance_name)) {
                Functions::message(strtr($this->langs->line('al_name_already_exists'), ['%s' => $alliance_name]), 'game.php?page=alliance&mode=make', 3);
            }

            $this->Alliance_Model->createNewAlliance(
                $alliance_name,
                $alliance_tag,
                $this->user['user_id'],
                $this->langs->line('al_founder_rank_text'),
                $this->langs->line('al_new_member_rank_text')
            );

            $message = str_replace(['%s', '%d'], [$alliance_name, $alliance_tag], $this->langs->line('al_created'));
            return Functions::messageBox(
                $message,
                $message . "<br/><br/>",
                'game.php?page=alliance',
                $this->langs->line('al_continue')
            );
        } else {
            return $this->template->set('alliance/alliance_make_view', $this->langs->language);
        }
    }

    /**
     * Get alliance apply section
     *
     * @return string
     */
    private function getApplySection()
    {
        if (!$this->alliance->getCurrentAlliance()->getAllianceRequestNotAllow()) {
            Functions::message($this->langs->line('al_alliance_closed'), 'game.php?page=alliance', 3);
        }

        $request = filter_input_array(INPUT_POST);

        if (isset($request)) {
            if ($request['send'] != null && !empty($request['text'])) {
                $this->Alliance_Model->createNewUserRequest(
                    $this->getAllianceId(),
                    $request['text'],
                    $this->user['user_id']
                );

                Functions::message($this->langs->line('al_request_confirmation_message'), 'game.php?page=alliance', 3);
            }
        }

        return $this->template->set('alliance/alliance_apply_form_view', array_merge(
            $this->langs->language,
            [
                'js_path' => JS_PATH,
                'allyid' => $this->getAllianceId(),
                'text_apply' => (!empty($this->alliance->getCurrentAlliance()->getAllianceRequest())) ? $this->alliance->getCurrentAlliance()->getAllianceRequest() : $this->langs->line('al_default_request_text'),
                'write_to_alliance' => strtr(
                    $this->langs->line('al_write_request'),
                    ['%s' => $this->alliance->getCurrentAlliance()->getAllianceTag()]
                ),
            ]
        ));
    }

    /**
     * Get alliance member list section
     *
     * @return string
     */
    private function getMemberslistSection()
    {
        if (!$this->alliance->hasAccess(AllianceRanks::VIEW_MEMBER_LIST)) {
            Functions::redirect('game.php?page=alliance');
        }

        $sort_by_field = filter_input(INPUT_GET, 'sort1');
        $sort_by_order = filter_input(INPUT_GET, 'sort2');
        $sort_by_order_rules = [1 => 2, 2 => 1];

        $members = $this->Alliance_Model->getAllianceMembers(
            $this->user['user_ally_id'],
            $sort_by_field,
            $sort_by_order
        );

        $position = 0;
        $members_list = [];

        foreach ($members as $member) {
            $position++;

            $members_list[] = [
                'position' => $position,
                'user_name' => $member['user_name'],
                'user_id' => $member['user_id'],
                'dpath' => DPATH,
                'write_message' => $this->langs->line('write_message'),
                'user_ally_range' => $this->getUserRank($member['user_id'], $member['user_ally_rank_id']),
                'points' => FormatLib::prettyNumber($member['user_statistic_total_points']),
                'user_galaxy' => $member['user_galaxy'],
                'user_system' => $member['user_system'],
                'coords' => FormatLib::prettyCoords($member['user_galaxy'], $member['user_system'], $member['user_planet']),
                'user_ally_register_time' => Timing::formatExtendedDate($member['user_ally_register_time']),
                'online_time' => $this->alliance->hasAccess(AllianceRanks::ONLINE_STATUS) ? Timing::setOnlineStatus($member['user_onlinetime']) : '-',
            ];
        }

        return $this->template->set(
            'alliance/alliance_members_view',
            array_merge(
                $this->langs->language,
                [
                    'total' => $position,
                    's' => isset($sort_by_order_rules[$sort_by_order]) ? $sort_by_order_rules[$sort_by_order] : 1,
                    'list_of_members' => $members_list,
                ]
            )
        );
    }

    /**
     * Get alliance circular section
     *
     * @return string
     */
    private function getCircularSection()
    {
        if (!$this->alliance->hasAccess(AllianceRanks::SEND_CIRCULAR)) {
            Functions::redirect('game.php?page=alliance');
        }

        if ((bool) filter_input(INPUT_GET, 'sendmail', FILTER_VALIDATE_INT)) {
            $post = filter_input_array(INPUT_POST, [
                'r' => FILTER_SANITIZE_NUMBER_INT,
                'text' => FILTER_UNSAFE_RAW,
            ]);

            $members_list = [];

            if (!(bool) $post['r']) {
                $members = $this->Alliance_Model->getAllianceMembersById(
                    $this->user['user_ally_id']
                );
            } else {
                $members = $this->Alliance_Model->getAllianceMembersByIdAndRankId(
                    $this->user['user_ally_id'],
                    $post['r']
                );
            }

            if (count($members) > 0) {
                foreach ($members as $member) {
                    Functions::sendMessage(
                        $member['user_id'],
                        $this->user['user_id'],
                        '',
                        3,
                        $this->alliance->getCurrentAlliance()->getAllianceTag(),
                        $this->user['user_name'],
                        $post['text']
                    );

                    $members_list[] = $member['user_name'];
                }
            }

            return Functions::messageBox(
                $this->langs->line('al_circular_sended'),
                join("<br/>", $members_list),
                'game.php?page=alliance',
                $this->langs->line('al_continue'),
                true
            );
        }

        $ranks = $this->alliance->getCurrentAllianceRankObject();
        $list_of_ranks = $ranks->getAllRanksAsArray();
        $ranks_list = [];

        if (is_array($list_of_ranks)) {
            foreach ($list_of_ranks as $id => $rank) {
                $ranks_list[] = [
                    'value' => $id + 1,
                    'name' => $rank['rank'],
                ];
            }
        }

        return $this->template->set(
            'alliance/alliance_circular_view',
            array_merge(
                $this->langs->language,
                [
                    'js_path' => JS_PATH,
                    'ranks_list' => $ranks_list,
                ]
            )
        );
    }

    /**
     * Get alliance exit section
     *
     * @return string
     */
    private function getExitSection()
    {
        if ($this->alliance->isOwner()) {
            Functions::message($this->langs->line('al_founder_cant_leave_alliance'), 'game.php?page=alliance', 3);
        }

        if ((bool) filter_input(INPUT_GET, 'yes', FILTER_VALIDATE_INT)) {
            $this->Alliance_Model->exitAlliance(
                $this->getAllianceId(),
                $this->user['user_id']
            );

            return Functions::messageBox(
                strtr($this->langs->line('al_leave_sucess'), ['%s' => $this->alliance->getCurrentAlliance()->getAllianceName()]),
                '<br>',
                'game.php?page=alliance',
                $this->langs->line('al_continue')
            );
        }

        return Functions::messageBox(
            strtr($this->langs->line('al_do_you_really_want_to_go_out'), ['%s' => $this->alliance->getCurrentAlliance()->getAllianceName()]),
            '<br/>',
            'game.php?page=alliance&mode=exit&yes=1',
            $this->langs->line('al_go_out_yes')
        );
    }

    /**
     *
     * ADMINS SECTION
     *
     */

    /**
     * Get alliance admin section
     *
     * @return string
     */
    private function getAdminSection()
    {
        $edit = filter_input(INPUT_GET, 'edit');

        $admin_sections = [
            'ally' => AllianceRanks::ADMINISTRATION,
            'exit' => AllianceRanks::DELETE,
            'members' => AllianceRanks::ADMINISTRATION,
            'name' => AllianceRanks::ADMINISTRATION,
            'requests' => AllianceRanks::APPLICATION_MANAGEMENT,
            'rights' => AllianceRanks::RIGHT_HAND,
            'tag' => AllianceRanks::ADMINISTRATION,
            'transfer' => '',
        ];

        if (isset($admin_sections[$edit]) && $this->alliance->hasAccess($admin_sections[$edit])) {
            return $this->{'getAdmin' . ucfirst($edit) . 'Section'}();
        }

        Functions::redirect('game.php?page=alliance');
    }

    /**
     * Get admin main section
     *
     * @return string
     */
    private function getAdminAllySection()
    {
        $t = filter_input(INPUT_GET, 't', FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1,
                'max_range' => 3,
            ],
        ]);

        $post = filter_input_array(INPUT_POST, [
            't' => [
                'filter' => FILTER_SANITIZE_NUMBER_INT,
                'options' => ['default' => 1, 'min_range' => 1, 'max_range' => 3],
            ],
            'text' => FILTER_UNSAFE_RAW,
            'options' => FILTER_UNSAFE_RAW,
            'owner_range' => FILTER_UNSAFE_RAW,
            'newcomer_range' => FILTER_UNSAFE_RAW,
            'web' => FILTER_VALIDATE_URL,
            'image' => FILTER_VALIDATE_URL,
            'request_notallow' => [
                'filter' => FILTER_SANITIZE_NUMBER_INT,
                'options' => ['default' => 1, 'min_range' => 0, 'max_range' => 1],
            ],
        ]);

        if (isset($post['options'])) {
            $this->Alliance_Model->updateAllianceSettings(
                $this->getAllianceId(),
                [
                    'alliance_owner_range' => ($post['owner_range'] ? StringsHelper::escapeString($post['owner_range']) : ''),
                    'alliance_web' => ($post['web'] ? StringsHelper::escapeString($post['web']) : ''),
                    'alliance_image' => ($post['image'] ? StringsHelper::escapeString($post['image']) : ''),
                    'alliance_request_notallow' => $post['request_notallow'],
                ]
            );

            $ranks = $this->alliance->getCurrentAllianceRankObject();

            // edit owner rank name
            if (isset($post['owner_range'])) {
                $ranks->editRankNameById(
                    self::DEFAULT_RANKS['founder'],
                    $post['owner_range']
                );
            }

            // edit newcomer rank name
            if (isset($post['newcomer_range'])) {
                $ranks->editRankNameById(
                    self::DEFAULT_RANKS['newcomer'],
                    $post['newcomer_range']
                );
            }

            $this->Alliance_Model->updateAllianceRanks(
                $this->getAllianceId(),
                $ranks->getAllRanksAsJsonString()
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=ally');
        }

        if (isset($post['t'])) {
            $callback = [
                1 => 'Description',
                2 => 'Text',
                3 => 'RequestText',
            ];

            $this->Alliance_Model->{'updateAlliance' . $callback[$t]}(
                $this->getAllianceId(),
                StringsHelper::escapeString($post['text'])
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=ally&t=' . $t);
        }

        $request_type = [
            1 => $this->langs->line('al_outside_text'),
            2 => $this->langs->line('al_inside_text'),
            3 => $this->langs->line('al_request_text'),
        ];

        $text = [
            1 => $this->alliance->getCurrentAlliance()->getAllianceDescription(),
            2 => $this->alliance->getCurrentAlliance()->getAllianceText(),
            3 => $this->alliance->getCurrentAlliance()->getAllianceRequest(),
        ];

        $ranks = $this->alliance->getCurrentAllianceRankObject();

        return $this->template->set(
            'alliance/alliance_admin',
            array_merge(
                $this->langs->language,
                [
                    'js_path' => JS_PATH,
                    'dpath' => DPATH,
                    't' => $t,
                    'request_type' => $request_type[$t],
                    'text' => $text[$t],
                    'alliance_web' => $this->alliance->getCurrentAlliance()->getAllianceWeb(),
                    'alliance_image' => $this->alliance->getCurrentAlliance()->getAllianceImage(),
                    'alliance_request_notallow_0' => $this->alliance->getCurrentAlliance()->getAllianceRequestNotAllow() == SwitchInt::off ? 'selected' : '',
                    'alliance_request_notallow_1' => $this->alliance->getCurrentAlliance()->getAllianceRequestNotAllow() == SwitchInt::on ? 'selected' : '',
                    'alliance_owner_range' => $ranks->getRankById(self::DEFAULT_RANKS['founder'])['rank'],
                    'alliance_newcomer_range' => $ranks->getRankById(self::DEFAULT_RANKS['newcomer'])['rank'],
                ]
            )
        );
    }

    /**
     * Get admin delete section
     *
     * @return string
     */
    private function getAdminExitSection()
    {
        $this->Alliance_Model->deleteAlliance($this->getAllianceId());

        Functions::redirect('game.php?page=alliance');
    }

    /**
     * Get admin members section
     *
     * @return string
     */
    private function getAdminMembersSection()
    {
        $kick = filter_input(INPUT_GET, 'kick', FILTER_VALIDATE_INT);
        $rank = filter_input(INPUT_GET, 'rank', FILTER_VALIDATE_INT);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $new_rank = filter_input(INPUT_POST, 'newrang', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);

        if (isset($kick)
            && $this->alliance->hasAccess(AllianceRanks::KICK)
            && $kick != $this->alliance->getCurrentAlliance()->getAllianceOwner()) {
            $this->Alliance_Model->exitAlliance(
                $this->getAllianceId(),
                $kick
            );
        }

        if (isset($new_rank)
            && isset($id)
            && $id != $this->alliance->getCurrentAlliance()->getAllianceOwner()) {
            $ranks = $this->alliance->getCurrentAllianceRankObject();

            if ($ranks->getRankById($new_rank) != null or $new_rank == 0) {
                $this->Alliance_Model->updateUserRank($id, $new_rank);
            }
        }

        $sort_by_field = filter_input(INPUT_GET, 'sort1');
        $sort_by_order = filter_input(INPUT_GET, 'sort2');
        $sort_by_order_rules = [1 => 2, 2 => 1];

        $members = $this->Alliance_Model->getAllianceMembers(
            $this->user['user_ally_id'],
            $sort_by_field,
            $sort_by_order
        );

        $position = 0;
        $members_list = [];

        foreach ($members as $member) {
            $position++;

            $members_list[] = [
                'position' => $position,
                'user_name' => $member['user_name'],
                'user_id' => $member['user_id'],
                'dpath' => DPATH,
                'write_message' => $this->langs->line('write_message'),
                'user_ally_range' => $this->buildAdminMembersRankBlock($member['user_id'], $member['user_ally_rank_id'], $rank),
                'points' => FormatLib::prettyNumber($member['user_statistic_total_points']),
                'user_galaxy' => $member['user_galaxy'],
                'user_system' => $member['user_system'],
                'coords' => FormatLib::prettyCoords($member['user_galaxy'], $member['user_system'], $member['user_planet']),
                'user_ally_register_time' => Timing::formatExtendedDate($member['user_ally_register_time']),
                'online_time' => Timing::formatDaysTime($member['user_onlinetime']),
                'actions' => $this->buildAdminMembersActionBlock($member['user_id'], $member['user_name'], $rank),
            ];
        }

        return $this->template->set(
            'alliance/alliance_admin_members_view',
            array_merge(
                $this->langs->language,
                [
                    'total' => $position,
                    's' => isset($sort_by_order_rules[$sort_by_order]) ? $sort_by_order_rules[$sort_by_order] : 1,
                    'list_of_members' => $members_list,
                ]
            )
        );
    }

    /**
     * Get admin name section
     *
     * @return string
     */
    private function getAdminNameSection()
    {
        $name = filter_input(INPUT_POST, 'nametag', FILTER_UNSAFE_RAW);

        if (isset($name)) {
            if (strlen($name) < 3 or strlen($name) > 30) {
                Functions::message($this->langs->line('al_name_required'), 'game.php?page=alliance&mode=admin&edit=name', 3);
            }

            if ($this->allianceNameExists($name)) {
                Functions::message(strtr($this->langs->line('al_name_already_exists'), ['%s' => $name]), 'game.php?page=alliance&mode=admin&edit=name', 3);
            }

            $this->Alliance_Model->updateAllianceName(
                $this->getAllianceId(),
                $name
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=ally');
        }

        return $this->template->set(
            'alliance/alliance_admin_rename',
            array_merge(
                $this->langs->language,
                [
                    'case' => strtr(
                        $this->langs->line('al_change_title'),
                        ['%s' => $this->alliance->getCurrentAlliance()->getAllianceName()]
                    ),
                    'title' => $this->langs->line('al_new_name'),
                ]
            )
        );
    }

    /**
     * Get admin requests section
     *
     * @return string
     */
    private function getAdminRequestsSection()
    {
        $show = filter_input(INPUT_GET, 'show', FILTER_VALIDATE_INT);
        $accept = filter_input(INPUT_POST, 'accept');
        $cancel = filter_input(INPUT_POST, 'cancel');
        $text = filter_input(INPUT_POST, 'text');

        if (isset($accept) && $show != 0) {
            $this->Alliance_Model->addUserToAlliance($show, $this->getAllianceId());

            Functions::sendMessage(
                $show,
                $this->user['user_id'],
                '',
                3,
                $this->alliance->getCurrentAlliance()->getAllianceTag(),
                $this->langs->line('al_you_was_acceted') . $this->alliance->getCurrentAlliance()->getAllianceName(),
                $this->langs->line('al_hi_the_alliance') . $this->alliance->getCurrentAlliance()->getAllianceName() . $this->langs->line('al_has_accepted') . $text
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=requests');
        }

        if (isset($cancel) && $show != 0) {
            $this->Alliance_Model->removeUserFromAlliance($show);

            Functions::sendMessage(
                $show,
                $this->user['user_id'],
                '',
                3,
                $this->alliance->getCurrentAlliance()->getAllianceTag(),
                $this->langs->line('al_you_was_declined') . $this->alliance->getCurrentAlliance()->getAllianceName(),
                $this->langs->line('al_hi_the_alliance') . $this->alliance->getCurrentAlliance()->getAllianceName() . $this->langs->line('al_has_declined') . $text
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=requests');
        }

        $requests = $this->Alliance_Model->getAllianceRequests($this->getAllianceId());

        $amount_of_requests = count($requests);
        $list_of_requests = [];
        $request_form = '';

        if ($requests) {
            foreach ($requests as $request) {
                $list_of_requests[$request['user_id']] = [
                    'id' => $request['user_id'],
                    'username' => $request['user_name'],
                    'time' => Timing::formatExtendedDate($request['user_ally_register_time']),
                    'ally_request_text' => nl2br($request['user_ally_request_text']),
                ];
            }

            if (isset($show) && isset($list_of_requests[$show])) {
                $request_form = $this->template->set(
                    'alliance/alliance_admin_request_form',
                    array_merge(
                        $this->langs->language,
                        [
                            'js_path' => JS_PATH,
                            'id' => $list_of_requests[$show]['id'],
                            'request_from' => strtr($this->langs->line('al_request_from'), ['%s' => $list_of_requests[$show]['username']]),
                            'request_text' => $list_of_requests[$show]['ally_request_text'],
                        ]
                    )
                );
            }
        }

        return $this->template->set(
            'alliance/alliance_admin_request_view',
            array_merge(
                $this->langs->language,
                [
                    'request_form' => $request_form,
                    'pending_message' => strtr($this->langs->line('al_no_request_pending'), ['%n' => $amount_of_requests]),
                    'list_of_requests' => $list_of_requests,
                    'no_requests' => $amount_of_requests <= 0 ? '<tr><th colspan="2">' . $this->langs->line('al_no_requests') . '</th></tr>' : '',
                ]
            )
        );
    }

    /**
     * Get admin rights section
     *
     * @return string
     */
    private function getAdminRightsSection()
    {
        $post = filter_input_array(INPUT_POST);
        $delete = filter_input(INPUT_GET, 'd', FILTER_VALIDATE_INT);

        $ranks = $this->alliance->getCurrentAllianceRankObject();

        // Create a new rank
        if (isset($post['newrangname'])) {
            $ranks->addNew(
                $post['newrangname']
            );

            $this->Alliance_Model->updateAllianceRanks(
                $this->getAllianceId(),
                $ranks->getAllRanksAsJsonString()
            );
        }

        // edit rights for each rank
        if (isset($post['id'])) {
            foreach ($post['id'] as $id) {
                $ranks->editRankById(
                    $id,
                    [
                        AllianceRanks::DELETE => (isset($post['u' . $id . 'r1']) && $this->alliance->isOwner()) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::KICK => isset($post['u' . $id . 'r2']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::APPLICATIONS => isset($post['u' . $id . 'r3']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::VIEW_MEMBER_LIST => isset($post['u' . $id . 'r4']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::APPLICATION_MANAGEMENT => isset($post['u' . $id . 'r5']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::ADMINISTRATION => isset($post['u' . $id . 'r6']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::ONLINE_STATUS => isset($post['u' . $id . 'r7']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::SEND_CIRCULAR => isset($post['u' . $id . 'r8']) ? SwitchInt::on : SwitchInt::off,
                        AllianceRanks::RIGHT_HAND => isset($post['u' . $id . 'r9']) ? SwitchInt::on : SwitchInt::off,
                    ]
                );
            }

            $this->Alliance_Model->updateAllianceRanks(
                $this->getAllianceId(),
                $ranks->getAllRanksAsJsonString()
            );
        }

        // delete a rank
        if (isset($delete)) {
            $ranks->deleteRankById($delete);

            $this->Alliance_Model->updateAllianceRanks(
                $this->getAllianceId(),
                $ranks->getAllRanksAsJsonString()
            );
        }

        // build the UI
        $list_of_ranks = [];

        if (is_array($ranks->getAllRanksAsArray())) {
            foreach ($ranks->getAllRanksAsArray() as $rank_id => $details) {
                $delete = '<a href="game.php?page=alliance&mode=admin&edit=rights&d=' . $rank_id . '"><img src="' . DPATH . 'alliance/abort.gif" border="0" alt="'.$this->langs->line('rank_delete').'"/></a>';
                $disabled = '';
                if ($rank_id == 0 or $rank_id == 1) {
                    $delete = '';
                    $disabled = ' disabled="disabled"';
                }

                $right_hand = '<b>-</b>';

                if ($this->alliance->isOwner()) {
                    $right_hand = '<input type="checkbox" name="u' . $rank_id . 'r1"' . (($details['rights'][AllianceRanks::DELETE] == SwitchInt::on) ? ' checked="checked"' : '') . $disabled . '>';
                }

                $list_of_ranks[] = [
                    'dpath' => DPATH,
                    'rank_id' => $rank_id,
                    'rank_delete' => $delete,
                    'rank_name' => $details['rank'],
                    'r1' => $right_hand,
                    'checked_r2' => (($details['rights'][AllianceRanks::KICK] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r3' => (($details['rights'][AllianceRanks::APPLICATIONS] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r4' => (($details['rights'][AllianceRanks::VIEW_MEMBER_LIST] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r5' => (($details['rights'][AllianceRanks::APPLICATION_MANAGEMENT] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r6' => (($details['rights'][AllianceRanks::ADMINISTRATION] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r7' => (($details['rights'][AllianceRanks::ONLINE_STATUS] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r8' => (($details['rights'][AllianceRanks::SEND_CIRCULAR] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'checked_r9' => (($details['rights'][AllianceRanks::RIGHT_HAND] == SwitchInt::on) ? ' checked="checked"' : ''),
                    'edit_check' => $disabled,
                ];
            }
        }

        return $this->template->set(
            'alliance/alliance_admin_laws_view',
            array_merge(
                $this->langs->language,
                [
                    'dpath' => DPATH,
                    'list_of_ranks' => $list_of_ranks,
                ]
            )
        );
    }

    /**
     * Get admin tag section
     *
     * @return string
     */
    private function getAdminTagSection()
    {
        $tag = filter_input(INPUT_POST, 'nametag', FILTER_UNSAFE_RAW);

        if (isset($tag)) {
            if (strlen($tag) < 3 or strlen($tag) > 8) {
                Functions::message($this->langs->line('al_tag_required'), 'game.php?page=alliance&mode=admin&edit=tag', 3);
            }

            if ($this->allianceTagExists($tag)) {
                Functions::message(strtr($this->langs->line('al_tag_already_exists'), ['%s' => $tag]), 'game.php?page=alliance&mode=admin&edit=tag', 3);
            }

            $this->Alliance_Model->updateAllianceTag(
                $this->getAllianceId(),
                $tag
            );

            Functions::redirect('game.php?page=alliance&mode=admin&edit=ally');
        }

        return $this->template->set(
            'alliance/alliance_admin_rename',
            array_merge(
                $this->langs->language,
                [
                    'case' => strtr(
                        $this->langs->line('al_change_title'),
                        ['%s' => $this->alliance->getCurrentAlliance()->getAllianceTag()]
                    ),
                    'title' => $this->langs->line('al_new_tag'),
                ]
            )
        );
    }

    /**
     * Get admin transfer section
     *
     * @return string
     */
    private function getAdminTransferSection()
    {
        $new_leader = filter_input(INPUT_POST, 'newleader', FILTER_VALIDATE_INT);

        if (isset($new_leader) && $new_leader != 0) {
            $this->Alliance_Model->transferAlliance(
                $this->user['user_ally_id'],
                $this->user['user_id'],
                $new_leader
            );

            Functions::redirect('game.php?page=alliance');
        }

        $ranksObject = $this->alliance->getCurrentAllianceRankObject();

        $users = array_filter(
            $this->Alliance_Model->getAllianceMembersById(
                $this->getAllianceId()
            ),
            function ($user) {
                return $user['user_ally_rank_id'] != 0;
            }
        );

        $list_of_members = [];

        if (isset($users)) {
            foreach ($users as $user) {
                $rank_name = $ranksObject->getRankById($user['user_ally_rank_id'])['rank'];
                $rights = $ranksObject->getRankById($user['user_ally_rank_id'])['rights'];

                if (isset($rights[AllianceRanks::RIGHT_HAND]) && $rights[AllianceRanks::RIGHT_HAND] == SwitchInt::on) {
                    $list_of_members[] = [
                        'user_id' => $user['user_id'],
                        'user_name' => $user['user_name'],
                        'user_rank' => $rank_name,
                    ];
                }
            }
        }

        return $this->template->set(
            'alliance/alliance_admin_transfer_view',
            array_merge(
                $this->langs->language,
                [
                    'list_of_members' => $list_of_members,
                ]
            )
        );
    }

    /**
     *
     * BLOCKS
     *
     */

    /**
     *
     * @return string
     */
    private function buildPublicRequestsBlock()
    {
        if (!$this->user['user_ally_id']
            && !$this->user['user_ally_request']
            && $this->alliance->getCurrentAlliance()->getAllianceRequestNotAllow()) {
            $url = UrlHelper::setUrl(
                'game.php?page=alliance&mode=apply&allyid=' . $this->getAllianceId(),
                $this->langs->line('al_click_to_send_request'),
                $this->langs->line('al_click_to_send_request')
            );

            return '<tr><th>' . $this->langs->line('al_request') . '</th><th>' . $url . '</th></tr>';
        }

        return '';
    }

    /**
     * Build the image block
     *
     * @return array
     */
    private function buildImageBlock()
    {
        $image = $this->alliance->getCurrentAlliance()->getAllianceImage();

        if (!empty($image)) {
            return '<tr><th colspan="2">' . Functions::setImage($image, $image) . '</td></tr>';
        }

        return '';
    }

    /**
     * Build the tag block
     *
     * @return array
     */
    private function buildTagBlock()
    {
        return [
            'detail_title' => $this->langs->line('al_ally_info_tag'),
            'detail_content' => $this->alliance->getCurrentAlliance()->getAllianceTag(),
        ];
    }

    /**
     * Build the name block
     *
     * @return array
     */
    private function buildNameBlock()
    {
        return [
            'detail_title' => $this->langs->line('al_ally_info_name'),
            'detail_content' => $this->alliance->getCurrentAlliance()->getAllianceName(),
        ];
    }

    /**
     * Build the members block
     *
     * @return array
     */
    private function buildMembersBlock()
    {
        $list_of_members = '';

        if ($this->alliance->hasAccess(AllianceRanks::VIEW_MEMBER_LIST)) {
            $list_of_members = ' (' . UrlHelper::setUrl('game.php?page=alliance&mode=memberslist', $this->langs->line('al_user_list')) . ')';
        }

        return [
            'detail_title' => $this->langs->line('al_ally_info_members'),
            'detail_content' => $this->alliance->getCurrentAlliance()->getAllianceMembers() . $list_of_members,
        ];
    }

    /**
     * Build the ranks block
     *
     * @return array
     */
    private function buildRankBlock()
    {
        $rank = $this->getUserRank($this->user['user_id'], $this->user['user_ally_rank_id']);
        $admin_area = '';

        if ($this->alliance->hasAccess(AllianceRanks::ADMINISTRATION)) {
            $admin_area = ' (' . UrlHelper::setUrl('game.php?page=alliance&mode=admin&edit=ally', $this->langs->line('al_manage_alliance')) . ')';
        }

        return [
            'detail_title' => $this->langs->line('al_rank'),
            'detail_content' => $rank . $admin_area,
        ];
    }

    /**
     * Build the requests block
     *
     * @return array
     */
    private function buildRequestsBlock()
    {
        $requests = '';
        $count = $this->Alliance_Model->getAllianceRequestsCount(
            $this->alliance->getCurrentAlliance()->getAllianceId()
        )['total_requests'];

        if ($this->alliance->hasAccess(AllianceRanks::APPLICATION_MANAGEMENT) && $count != 0) {
            $requests = UrlHelper::setUrl(
                'game.php?page=alliance&mode=admin&edit=requests',
                $count . ' ' . $this->langs->line('al_new_requests')
            );
        }

        return [
            'detail_title' => $this->langs->line('al_requests'),
            'detail_content' => $requests,
        ];
    }

    /**
     * Build the circular message block
     *
     * @return array
     */
    private function buildCircularBlock()
    {
        if ($this->alliance->hasAccess(AllianceRanks::SEND_CIRCULAR)) {
            return [
                'detail_title' => $this->langs->line('al_circular_message'),
                'detail_content' => UrlHelper::setUrl('game.php?page=alliance&mode=circular', $this->langs->line('al_send_circular_message')),
            ];
        }
    }

    /**
     * Build the description block
     *
     * @return array
     */
    private function buildDescriptionBlock()
    {
        $description = $this->langs->line('al_description_message');
        $alliance_description = $this->alliance->getCurrentAlliance()->getAllianceDescription();

        if ($alliance_description != '') {
            $description = nl2br($this->bbcode->bbCode($alliance_description)) . '</th></tr>';
        }

        return '<tr><th colspan="2" height="100px">' . $description . '</th></tr>';
    }

    /**
     * Build the web block
     *
     * @return array
     */
    private function buildWebBlock()
    {
        $alliance_web = '-';
        $alliance_web_url = $this->alliance->getCurrentAlliance()->getAllianceWeb();

        if ($alliance_web_url != '') {
            $url = UrlHelper::prepUrl($alliance_web_url);
            $alliance_web = UrlHelper::setUrl($url, '', $url, 'target="_blank"');
        }

        return [
            'detail_title' => $this->langs->line('al_web_text'),
            'detail_content' => $alliance_web,
        ];
    }

    /**
     * Build the description block
     *
     * @return array
     */
    private function buildTextBlock()
    {
        return nl2br($this->bbcode->bbCode($this->alliance->getCurrentAlliance()->getAllianceText()));
    }

    /**
     * Build the leave block
     *
     * @return array
     */
    private function buildLeaveBlock()
    {
        if (!$this->alliance->isOwner()) {
            return $this->template->set('alliance/alliance_leave_view', $this->langs->language);
        }

        return '';
    }

    /**
     *
     * OTHER METHODS
     *
     */

    /**
     *
     * @param string $name Alliance Name
     *
     * @return bool
     */
    private function allianceNameExists($name)
    {
        return $this->Alliance_Model->checkAllianceName($name);
    }

    /**
     *
     * @param string $tag Alliance Tag
     *
     * @return bool
     */
    private function allianceTagExists($tag)
    {
        return $this->Alliance_Model->checkAllianceTag($tag);
    }

    /**
     * Get user rank
     *
     * @param int $member_id      Member ID
     * @param int $member_rank_id Member Rank ID
     *
     * @return string
     */
    private function getUserRank($member_id, $member_rank_id)
    {
        $ranks = $this->alliance->getCurrentAllianceRankObject();

        if (!isset($ranks->getRankById($member_rank_id)['rank'])) {
            if ($this->alliance->getCurrentAlliance()->getAllianceOwner() == $member_id) {
                return $this->langs->line('al_founder_rank_text');
            } else {
                return $this->langs->line('al_new_member_rank_text');
            }
        }

        return $ranks->getRankById($member_rank_id)['rank'];
    }

    /**
     * Build the admin members rank block
     *
     * @param int $member_id
     * @param int $member_rank_id
     * @param int $requested_rank
     *
     * @return string
     */
    private function buildAdminMembersRankBlock($member_id, $member_rank_id, $requested_rank = 0)
    {
        $rank = $this->getUserRank($member_id, $member_rank_id);

        if ($requested_rank != $member_id) {
            return $rank;
        }

        $ranks = $this->alliance->getCurrentAllianceRankObject();
        $options = '';

        if (is_array($ranks->getAllRanksAsArray())) {
            foreach ($ranks->getAllRanksAsArray() as $id => $rank) {
                $selected = '';

                if ($member_rank_id == $id) {
                    $selected = ' selected=selected';
                }

                $options .= '<option onclick="document.edit_user_rank.submit();" value="' . $id . '"' . $selected . '>' . $rank['rank'] . '</option>';
            }
        }

        return $this->template->set(
            'alliance/alliance_admin_members_edit',
            array_merge(
                $this->langs->language,
                [
                    'user_id' => $member_id,
                    'options' => $options,
                ]
            )
        );
    }

    /**
     * Build the admin members action block
     *
     * @param int    $member_id      Member ID
     * @param string $member_name    Member Name
     * @param int    $requested_rank Requested Rank
     *
     * @return string
     */
    private function buildAdminMembersActionBlock($member_id, $member_name, $requested_rank = 0)
    {
        $kick_user = '';
        $change_rank = '';

        if ($this->alliance->getCurrentAlliance()->getAllianceOwner() == $member_id
            or $requested_rank == $member_id) {
            return '-';
        }

        if ($this->alliance->hasAccess(AllianceRanks::KICK)) {
            $action = 'game.php?page=alliance&mode=admin&edit=members&kick=' . $member_id;
            $content = Functions::setImage(DPATH . 'alliance/abort.gif');
            $attributes = 'onclick="javascript:return confirm(\'' . strtr($this->langs->line('al_confirm_remove_member'), ['%s' => $member_name]) . '\');"';
            $kick_user = UrlHelper::setUrl($action, $content, '', $attributes);
        }

        if ($this->alliance->hasAccess(AllianceRanks::ADMINISTRATION)) {
            $action = 'game.php?page=alliance&mode=admin&edit=members&rank=' . $member_id;
            $content = Functions::setImage(DPATH . 'alliance/key.gif');
            $change_rank = UrlHelper::setUrl($action, $content);
        }

        if (empty($kick_user) && empty($change_rank)) {
            return '-';
        }

        return $kick_user . $change_rank;
    }
}
