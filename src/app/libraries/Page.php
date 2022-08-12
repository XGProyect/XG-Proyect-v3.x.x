<?php
/**
 * Page.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @version  3.2.0
 */

namespace App\libraries;

use App\core\Database;
use App\core\enumerators\PlanetTypesEnumerator;
use App\core\Language;
use App\core\Objects;
use App\core\Template;
use App\helpers\UrlHelper;
use App\libraries\FormatLib;
use App\libraries\Functions;
use App\libraries\OfficiersLib;
use App\libraries\ProductionLib as Production;
use App\libraries\TimingLibrary as Timing;

/**
 * Page Class
 */
class Page
{
    /**
     * @var array
     */
    private $current_user;

    /**
     * @var array
     */
    private $current_planet;

    /**
     * @var int
     */
    private $current_year;

    /**
     * @var \Template
     */
    private $template;

    /**
     * @var \Language
     */
    private $langs;

    /**
     * @var \Objects
     */
    private $objects;

    /**
     * Constructor
     *
     * @param object $users
     */
    public function __construct(object $users)
    {
        $this->current_user = $users->getUserData();
        $this->current_planet = $users->getPlanetData();
        $this->current_year = date('Y');

        $this->setTemplate();
        $this->setLanguage();
        $this->setObjects();
    }

    /**
     * Set template object
     *
     * @return void
     */
    private function setTemplate(): void
    {
        $this->template = new Template();
    }

    /**
     * Set language object
     *
     * @return void
     */
    private function setLanguage(): void
    {
        $this->langs = new Language();
    }

    /**
     * Set objects object
     *
     * @return void
     */
    private function setObjects(): void
    {
        $this->objects = new Objects();
    }

    /**
     * display
     *
     * @param string  $current_page Current page
     * @param boolean $topnav       Show topnav
     * @param string  $metatags     Meta tags
     * @param boolean $menu         Show menu
     *
     * @return void
     */
    public function display($current_page, $topnav = true, $metatags = '', $menu = true)
    {
        $page = '';

        if (!defined('IN_MESSAGE')) {
            // For the Home page
            if (defined('IN_LOGIN')) {
                die($current_page);
            }
        }

        // Anything else
        if ($page == '') {
            $page .= $this->gameHeader($metatags);
            $page .= $topnav ? $this->gameNavbar() : ''; // TOP NAVIGATION BAR
            $page .= $menu ? $this->gameMenu() : ''; // MENU
        }

        // Merge: Header + Topnav + Menu + Page
        if (!defined('IN_INSTALL')) {
            $page .= "\n<center>\n" . $current_page . "\n</center>\n";
        } else {
            if (defined('IN_MESSAGE')) {
                $page .= "\n<center>\n" . $current_page . "\n</center>\n";
            } else {
                $page .= $current_page;
            }
        }

        // Footer
        if (!defined('IN_INSTALL') && !defined('IN_LOGIN')) {
            // Is inside the game
            $page .= $this->template->set(
                'general/footer',
                []
            );
        }

        // Show result page
        die($page);
    }

    /**
     * Display the installation page
     *
     * @param string $current_page
     * @param array $langs
     * @return void
     */
    public function displayInstall($current_page, $langs): void
    {
        $page = $this->installHeader();
        $page .= $this->installMenu($langs); // MENU
        $page .= $this->installNavbar($langs); // TOP NAVIGATION BAR
        $page .= $current_page;
        $page .= $this->template->set(
            'install/simple_footer',
            ['year' => $this->current_year]
        );

        // Show result page
        die($page);
    }

    /**
     * Display the admin page
     *
     * @param string $current_page
     * @param boolean $sidebar
     * @param boolean $navigation
     * @param boolean $footer
     * @return void
     */
    public function displayAdmin(string $current_page, bool $sidebar = true, bool $navigation = true, bool $footer = true): void
    {
        if ($sidebar) {
            $parse['sidebar'] = $this->adminSidebar();
        }

        if ($navigation) {
            $parse['navigation'] = $this->adminNavigation();
        }

        if ($footer) {
            $parse['footer'] = $this->adminFooter();
        }

        $page = $this->adminSimpleHeader();
        $page .= $this->adminPage($current_page, ($parse ?? []), ($sidebar && $navigation && $footer));
        $page .= $this->adminSimpleFooter();

        // Show result page
        die($page);
    }

    /**
     * Set the admin page
     *
     * @param string $page
     * @param array $parse
     * @return string
     */
    private function adminPage(string $page, array $parse, bool $full): string
    {
        return $this->template->set(
            ($full ? 'adm/admin_page_view' : 'adm/simple_admin_page_view'),
            array_merge($this->langs->loadLang('adm/popups', true)->language, $parse, ['page_content' => $page])
        );
    }

    /**
     * Set the admin meta header
     *
     * @return string
     */
    private function adminSimpleHeader(): string
    {
        return $this->template->set(
            'adm/simple_header',
            [
                'title' => 'Admin CP',
                'admin_public_path' => ADMIN_PUBLIC_PATH,
            ]
        );
    }

    /**
     * Set the admin sidebar
     *
     * @return string
     */
    private function adminSidebar(): string
    {
        $lang = $this->langs->loadLang('adm/menu', true);

        $current_page = isset($_GET['page']) ? $_GET['page'] : null;
        $items = '';
        $flag = '';
        $pages = [
            ['server', '2'],
            ['mailing', '2'],
            ['modules', '2'],
            ['planets', '2'],
            ['registration', '2'],
            ['statistics', '2'],
            ['premium', '2'],
            ['tasks', '3'],
            ['errors', '3'],
            ['fleets', '3'],
            ['messages', '3'],
            ['maker', '4'],
            ['users', '4'],
            ['alliances', '4'],
            ['languages', '4'],
            ['changelog', '4'],
            ['permissions', '4'],
            ['backup', '5'],
            ['encrypter', '5'],
            ['announcement', '5'],
            ['ban', '5'],
            ['rebuildhighscores', '5'],
            ['update', '5'],
            ['migrate', '5'],
            ['repair', '6'],
            ['reset', '6'],
        ];
        $active_block = 1;

        // BUILD THE MENU
        foreach ($pages as $key => $data) {
            $extra = '';
            $active = '';

            if ($data[1] != $flag) {
                $flag = $data[1];
                $items = '';
            }

            if ($data[0] == 'rebuildhighscores') {
                $extra = 'onClick="return confirm(\'' . $lang->line('tools_manual_update_confirm') . '\');"';
            }

            if ($data[0] == $current_page) {
                $active = ' active';
                $active_block = $data[1];
            }

            $items .= '<a class="collapse-item' . $active . '" href="' . ADM_URL . 'admin.php?page=' . $data[0] . '"  ' . $extra . '>' . $lang->line($data[0]) . '</a>';

            $parse_block[$data[1]] = $items;
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $lang->language;
        $parse['menu_block_2'] = $parse_block[2];
        $parse['menu_block_3'] = $parse_block[3];
        $parse['menu_block_4'] = $parse_block[4];
        $parse['menu_block_5'] = $parse_block[5];
        $parse['menu_block_6'] = $parse_block[6];
        $parse['active_1'] = '';
        $parse['active_1_show'] = '';
        $parse['active_2'] = '';
        $parse['active_2_show'] = '';
        $parse['active_3'] = '';
        $parse['active_3_show'] = '';
        $parse['active_4'] = '';
        $parse['active_4_show'] = '';
        $parse['active_5'] = '';
        $parse['active_5_show'] = '';
        $parse['active_6'] = '';
        $parse['active_6_show'] = '';
        $parse['active_' . $active_block] = ' active';
        $parse['active_' . $active_block . '_show'] = ' show';

        return $this->template->set(
            'adm/sidebar_view',
            $parse
        );
    }

    /**
     * Set the admin navigation
     *
     * @return string
     */
    private function adminNavigation(): string
    {
        return $this->template->set(
            'adm/navigation_view',
            array_merge(
                $this->langs->loadLang('adm/navigation', true)->language,
                [
                    'user_name' => $this->current_user['user_name'],
                    'current_date' => Timing::formatShortDate(time()),
                ]
            )
        );
    }

    /**
     * Set the admin footer
     *
     * @return string
     */
    private function adminFooter(): string
    {
        return $this->template->set(
            'adm/footer_view',
            [
                'version' => SYSTEM_VERSION,
                'year' => $this->current_year,
            ]
        );
    }

    /**
     * Set admin simple footer
     *
     * @return string
     */
    private function adminSimpleFooter(): string
    {
        return $this->template->set(
            'adm/simple_footer',
            [
                'admin_public_path' => ADMIN_PUBLIC_PATH,
                'version' => SYSTEM_VERSION,
            ]
        );
    }

    /**
     * installHeader
     *
     * @return string
     */
    private function installHeader()
    {
        $lang = $this->langs->loadLang(['installation/installation'], true);
        return $this->template->set(
            'install/simple_header',
            [
                'title' => 'Install',
                'lang_code' => $lang->line('lang_code'),
                'js_path' => '../js/',
                'css_path' => '../css/',
            ]
        );
    }

    /**
     * installNavbar
     *
     * @return string
     */
    private function installNavbar($langs)
    {
        // Update config language to the new setted value
        if (isset($_POST['language'])) {
            Functions::setCurrentLanguage($_POST['language']);
            Functions::redirect(SYSTEM_ROOT . DIRECTORY_SEPARATOR);
        }

        $current_page = isset($_GET['page']) ? $_GET['page'] : null;
        $items = '';

        $pages = [
            0 => ['installation', $langs['ins_overview'], 'overview'],
            1 => ['installation', $langs['ins_license'], 'license'],
            2 => ['installation', $langs['ins_install'], 'step1'],
        ];

        // BUILD THE MENU
        foreach ($pages as $key => $data) {
            if ($data[2] != '') {
                // URL
                $items .= '<li' . ($current_page == $data[0] ? ' class="active"' : '') .
                    '><a href="index.php?page=' . $data[0] . '&mode=' . $data[2] . '">' . $data[1] . '</a></li>';
            } else {
                // URL
                $items .= '<li' . ($current_page == $data[0] ? ' class="active"' : '') .
                    '><a href="index.php?page=' . $data[0] . '">' . $data[1] . '</a></li>';
            }
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $langs;
        $parse['menu_items'] = $items;
        $parse['language_select'] = Functions::getLanguages(Functions::getCurrentLanguage());

        return $this->template->set(
            'install/topnav_view',
            $parse
        );
    }

    /**
     * installMenu
     *
     * @return string
     */
    private function installMenu($langs)
    {
        $current_mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $items = '';
        $steps = [
            0 => ['step1', $langs['ins_step1']],
            1 => ['step2', $langs['ins_step2']],
            2 => ['step3', $langs['ins_step3']],
            3 => ['step4', $langs['ins_step4']],
            4 => ['step5', $langs['ins_step5']],
        ];

        // BUILD THE MENU
        foreach ($steps as $key => $data) {
            // URL
            $items .= '<li' . ($current_mode == $data[0] ? ' class="active"' : '') .
                '><a href="#">' . $data[1] . '</a></li>';
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $langs;
        $parse['menu_items'] = $items;

        return $this->template->set(
            'install/menu_view',
            $parse
        );
    }

    /**
     * gameHeader
     *
     * @param string $metatags Meta tags
     *
     * @return string
     */
    private function gameHeader($metatags = '')
    {
        $lang = $this->langs->loadLang(['game/global'], true);
        $parse['game_title'] = Functions::readConfig('game_name');
        $parse['version'] = SYSTEM_VERSION;
        $parse['css_path'] = CSS_PATH;
        $parse['skin_path'] = DPATH;
        $parse['js_path'] = JS_PATH;
        $parse['meta_tags'] = ($metatags) ? $metatags : "";
        $parse['lang_code'] = $lang->line('lang_code');
        return $this->template->set(
            'general/simple_header',
            $parse
        );
    }

    /**
     * gameNavbar
     *
     * @return string
     */
    private function gameNavbar()
    {
        $lang = $this->langs->loadLang(['game/global', 'game/navigation', 'game/officier'], true);

        $parse['dpath'] = DPATH;
        $parse['image'] = $this->current_planet['planet_image'];
        $parse['planetlist'] = $this->buildPlanetList();
        $parse['show_umod_notice'] = '';

        // When vacation mode did not expire
        if ($this->current_user['preference_vacation_mode'] > 0) {
            $parse['color'] = '#1DF0F0';
            $parse['message'] = $lang->line('tn_vacation_mode') . Timing::formatExtendedDate($this->current_user['preference_vacation_mode']);
            $parse['jump_line'] = '<br/>';

            $parse['show_umod_notice'] = $this->template->set(
                'general/notices_view',
                $parse
            );
        }

        if ($this->current_user['preference_delete_mode'] > 0) {
            // When it is in delete mode
            $parse['color'] = '#FF0000';
            $parse['message'] = $lang->line('tn_delete_mode') . Timing::formatExtendedDate($this->current_user['preference_delete_mode'] + (60 * 60 * 24 * 7));
            $parse['jump_line'] = '';

            $parse['show_umod_notice'] = $this->template->set(
                'general/notices_view',
                $parse
            );
        }

        // RESOURCES FORMAT
        $metal = FormatLib::prettyNumber($this->current_planet['planet_metal']);
        $crystal = FormatLib::prettyNumber($this->current_planet['planet_crystal']);
        $deuterium = FormatLib::prettyNumber($this->current_planet['planet_deuterium']);
        $darkmatter = FormatLib::prettyNumber($this->current_user['premium_dark_matter']);
        $energy = FormatLib::prettyNumber(
            $this->current_planet['planet_energy_max'] + $this->current_planet['planet_energy_used']
        ) . "/" . FormatLib::prettyNumber($this->current_planet['planet_energy_max']);

        // METAL
        if ($this->current_planet['planet_metal'] >= Production::maxStorable($this->current_planet['building_metal_store'])) {
            $metal = FormatLib::colorRed($metal);
        }

        // CRYSTAL
        if ($this->current_planet['planet_crystal'] >= Production::maxStorable($this->current_planet['building_crystal_store'])) {
            $crystal = FormatLib::colorRed($crystal);
        }

        // DEUTERIUM
        if ($this->current_planet['planet_deuterium'] >= Production::maxStorable($this->current_planet['building_deuterium_tank'])) {
            $deuterium = FormatLib::colorRed($deuterium);
        }

        // ENERGY
        if (($this->current_planet['planet_energy_max'] + $this->current_planet['planet_energy_used']) < 0) {
            $energy = FormatLib::colorRed($energy);
        }

        $parse['re_metal'] = $metal;
        $parse['re_crystal'] = $crystal;
        $parse['re_deuterium'] = $deuterium;
        $parse['re_darkmatter'] = $darkmatter;
        $parse['re_energy'] = $energy;

        return $this->template->set(
            'general/topnav',
            array_merge(
                $lang->language,
                $parse,
                $this->buildOfficersBlock($lang)
            )
        );
    }

    /**
     * gameMenu
     *
     * @return string
     */
    private function gameMenu()
    {
        $lang = $this->langs->loadLang('game/menu', true);

        $menu_block1 = '';
        $menu_block2 = '';
        $menu_block3 = '';
        $modules_array = explode(';', Functions::readConfig('modules'));
        $tota_rank = $this->current_user['user_statistic_total_rank'] == '' ?
        $this->current_planet['stats_users'] : $this->current_user['user_statistic_total_rank'];
        $pages = [
            ['overview', $lang->line('lm_overview'), '', 'FFF', '', '1', '1'],
            ['empire', $lang->line('lm_empire'), '', 'FFF', '', '1', '2'],
            ['resources', $lang->line('lm_resources'), '', 'FFF', '', '1', '3'],
            ['resourceSettings', $lang->line('lm_resources_settings'), '', 'FFF', '', '1', '4'],
            ['station', $lang->line('lm_station'), '', 'FFF', '', '1', '3'],
            ['traderOverview', $lang->line('lm_trader'), '', 'FF8900', '', '1', '5'],
            ['research', $lang->line('lm_research'), '', 'FFF', '', '1', '6'],
            ['techtree', $lang->line('lm_technology'), '', 'FFF', '', '1', '10'],
            ['shipyard', $lang->line('lm_shipyard'), '', 'FFF', '', '1', '7'],
            ['defense', $lang->line('lm_defenses'), '', 'FFF', '', '1', '12'],
            ['fleet1', $lang->line('lm_fleet'), '', 'FFF', '', '1', '8'],
            ['movement', $lang->line('lm_movement'), '', 'FFF', '', '1', '9'],
            ['galaxy', $lang->line('lm_galaxy'), 'mode=0', 'FFF', '', '1', '11'],
            ['alliance', $lang->line('lm_alliance'), '', 'FFF', '', '1', '13'],
            ['officier', $lang->line('lm_officiers'), '', 'FF8900', '', '1', '15'],
            ['messages', $lang->line('lm_messages'), '', 'FFF', '', '1', '18'],
            ['statistics', $lang->line('lm_statistics'), 'range=' . $tota_rank, 'FFF', '', '2', '16'],
            ['notes', $lang->line('lm_notes'), '', 'FFF', 'true', '2', '19'],
            ['buddies', $lang->line('lm_buddylist'), '', 'FFF', '', '2', '20'],
            ['search', $lang->line('lm_search'), '', 'FFF', '', '2', '17'],
            ['preferences', $lang->line('lm_options'), '', 'FFF', '', '2', '21'],
            ['logout', $lang->line('lm_logout'), '', 'FFF', '', '2', ''],
            ['forums', $lang->line('lm_forums'), '', 'FFF', '', '3', '14'],
        ];

        // BUILD THE MENU
        foreach ($pages as $key => $data) {
            // IF THE MODULE IT'S NOT ENABLED, CONTINUE!
            if (isset($modules_array[$data[6]]) && $modules_array[$data[6]] == 0 && $modules_array[$data[6]] != '') {
                continue;
            }

            if (!OfficiersLib::isOfficierActive($this->current_user['premium_officier_commander']) && $data[0] == 'empire') {
                continue;
            }

            // BUILD URL
            if ($data[2] != '') {
                $link = 'game.php?page=' . $data[0] . '&' . $data[2];
            } else {
                $link = 'game.php?page=' . $data[0];
            }

            // POP UP OR NOT
            if ($data[4] == 'true') {
                $link_type = '<a href="#" onClick="f(\'' . $link . '\', \'' . $data[1] . '\')">
                    <font color="' . (($data[3] != 'FFF') ? $data[3] : '') . '">' . $data[1] . '</font></a>';
            } else {
                $link_type = '<a href="' . $link . '">
                    <font color="' . (($data[3] != 'FFF') ? $data[3] : '') . '">' . $data[1] . '</font></a>';
            }

            // COLOR AND URL
            $parse['color'] = $data[3];
            $parse['menu_link'] = $link_type;

            // ONLY FOR THE CHANGELOG
            if ($data[5] == 0) {
                $parse['changelog'] = '(' . $link_type . ')';
            }

            // MENU BLOCK [1 - 2 - 3]
            switch ($data[5]) {
                case '1':
                    $menu_block1 .= $this->template->set(
                        'general/left_menu_row_view',
                        $parse
                    );

                    break;

                case '2':
                    $menu_block2 .= $this->template->set(
                        'general/left_menu_row_view',
                        $parse
                    );

                    break;

                case '3':
                    $menu_block3 .= $this->template->set(
                        'general/left_menu_row_view',
                        $parse
                    );

                    break;
            }
        }

        // PARSE THE MENU AND OTHER DATA
        $parse['dpath'] = DPATH;
        $parse['lm_players'] = $lang->line('lm_players');
        $parse['user_name'] = UrlHelper::setUrl('game.php?page=preferences', $this->current_user['user_name']);
        $parse['menu_block1'] = $menu_block1;
        $parse['menu_block2'] = $menu_block2;
        $parse['menu_block3'] = $menu_block3;
        $parse['admin_link'] = (($this->current_user['user_authlevel'] > 0) ?
            "<tr><td><div align=\"center\"><a href=\"admin.php\" target=\"_blank\">
            <font color=\"lime\">" . $lang->line('lm_administration') . "</font></a></div></td></tr>" : "");
        $parse['servername'] = Functions::readConfig('game_name');
        $parse['changelog'] = UrlHelper::setUrl('game.php?page=changelog', SYSTEM_VERSION);
        $parse['version'] = SYSTEM_VERSION;
        $parse['year'] = $this->current_year;

        return $this->template->set(
            'general/left_menu_view',
            $parse
        );
    }

    /**
     * Removes speacial chars like tabs, new lines and carriage return.
     *
     * @param string $template Template
     *
     * @return string
     */
    public function jsReady($template = '')
    {
        $output = str_replace(["\r\n", "\r"], "\n", $template);
        $lines = explode("\n", $output);
        $new_lines = [];

        foreach ($lines as $i => $line) {
            if (!empty($line)) {
                $new_lines[] = trim($line);
            }
        }

        return join($new_lines);
    }

    /**
     * Build the officers block for the game topnav
     *
     * @param array $lang
     * @return array
     */
    private function buildOfficersBlock(\CI_Lang $lang): array
    {
        $objects = $this->objects->getObjects();
        $officers = $this->objects->getObjectsList('officier');
        $list_of_officiers = [];

        foreach ($officers as $officer) {
            $inactive = '_un';
            $details = $lang->language['of_add_' . $objects[$officer]];
            $expiration = $this->current_user[$objects[$officer]];

            if (OfficiersLib::isOfficierActive($expiration)) {
                $inactive = '';
                $details = OfficiersLib::getOfficierTimeLeft($expiration, $lang->language);
            }

            $list_of_officiers['img_' . $objects[$officer]] = $inactive;
            $list_of_officiers['add_' . $objects[$officer]] = $details;
        }

        return $list_of_officiers;
    }

    /**
     * Build the list of planet
     *
     * @return void
     */
    private function buildPlanetList()
    {
        $lang = $this->langs->loadLang('game/global', true);

        $db = new Database();
        $list = '';
        $user_planets = $this->sortPlanets();

        $page = isset($_GET['page']) ? $_GET['page'] : '';
        $gid = isset($_GET['gid']) ? $_GET['gid'] : '';
        $mode = isset($_GET['mode']) ? $_GET['mode'] : '';

        if ($user_planets) {
            while ($planets = $db->fetchArray($user_planets)) {
                $list .= "\n<option ";
                $list .= (($planets['planet_id'] == $this->current_user['user_current_planet']) ?
                    'selected="selected" ' : '');

                $list .= "value=\"game.php?page=" . $page . "&gid=" .
                    $gid . "&cp=" . $planets['planet_id'] . "";
                $list .= "&amp;mode=" . $mode;
                $list .= "&amp;re=0\">";

                $list .= (($planets['planet_type'] != PlanetTypesEnumerator::MOON) ? $planets['planet_name'] : $planets['planet_name'] . ' (' . $lang->line('moon') . ')');
                $list .= "&nbsp;[" . $planets['planet_galaxy'] . ":";
                $list .= $planets['planet_system'] . ":";
                $list .= $planets['planet_planet'];
                $list .= "]&nbsp;&nbsp;</option>";
            }
        }

        // IF THE LIST OF PLANETS IS EMPTY WE SHOULD RETURN false
        if ($list !== '') {
            return $list;
        } else {
            return false;
        }
    }

    /**
     * Sort planets
     *
     * @return void
     */
    private function sortPlanets()
    {
        $db = new Database();
        $order = $this->current_user['preference_planet_sort_sequence'] == 1 ? "DESC" : "ASC"; // up or down
        $sort = $this->current_user['preference_planet_sort'];

        $planets = "SELECT `planet_id`, `planet_name`, `planet_galaxy`, `planet_system`, `planet_planet`, `planet_type`
                    FROM " . PLANETS . "
                    WHERE `planet_user_id` = '" . (int) $this->current_user['user_id'] . "'
                        AND `planet_destroyed` = 0 ORDER BY ";

        switch ($sort) {
            case 0: // emergence
            default:
                $planets .= "`planet_id` " . $order;
                break;
            case 1: // coordinates
                $planets .= "`planet_galaxy` " . $order . ", `planet_system` " . $order . ", `planet_planet` " . $order . ", `planet_type` " . $order;
                break;
            case 2: // alphabet
                $planets .= "`planet_name` " . $order;
                break;
            case 3: // size
                $planets .= "`planet_diameter` " . $order;
                break;
            case 4: // used_fields
                $planets .= "`planet_field_current` " . $order;
                break;
        }

        return $db->query($planets);
    }
}
