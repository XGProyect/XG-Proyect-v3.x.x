<?php
/**
 * Template Library
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries;

use application\core\Template;
use application\libraries\ProductionLib as Production;
use application\libraries\TimingLibrary as Timing;

/**
 * TemplateLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class TemplateLib
{

    private $current_user;
    private $current_planet;
    private $langs;
    private $current_year;
    private $template;

    /**
     * __construct
     *
     * @param array $lang  Language
     * @param array $users Users
     *
     * @return void
     */
    public function __construct($lang, $users)
    {
        $this->current_user = $users->getUserData();
        $this->current_planet = $users->getPlanetData();
        $this->langs = $lang;
        $this->current_year = date('Y');

        $this->setTemplate();
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

            // For the Install page
            if (defined('IN_INSTALL')) {
                $page .= $this->installHeader($metatags);
                $page .= $menu ? $this->installMenu() : ''; // MENU
                $page .= $topnav ? $this->installNavbar() : ''; // TOP NAVIGATION BAR
            }
        }

        // For the Install page
        if (defined('IN_INSTALL') && defined('IN_MESSAGE')) {
            $page .= $this->installHeader($metatags);
            $page .= $menu ? $this->installMenu() : ''; // MENU
            $page .= $topnav ? $this->installNavbar() : ''; // TOP NAVIGATION BAR
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

        if (defined('IN_INSTALL') && !defined('IN_MESSAGE')) {
            $page .= $this->template->set(
                'general/footer',
                ['year' => $this->current_year]
            );
        }

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
            array_merge($this->langs, $parse, ['page_content' => $page])
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
        $current_page = isset($_GET['page']) ? $_GET['page'] : null;
        $items = '';
        $flag = '';
        $pages = array(
            ['moderation', $this->langs['mn_permissions'], '5'],
            ['server', $this->langs['mn_config_server'], '2'],
            ['modules', $this->langs['mn_config_modules'], '2'],
            ['planets', $this->langs['mn_config_planets'], '2'],
            ['registration', $this->langs['mn_config_registrations'], '2'],
            ['statistics', $this->langs['mn_config_stats'], '2'],
            ['premium', $this->langs['mn_premium'], '2'],
            ['tasks', $this->langs['mn_info_tasks'], '3'],
            ['errors', $this->langs['mn_info_db'], '3'],
            ['fleets', $this->langs['mn_info_fleets'], '3'],
            ['messages', $this->langs['mn_info_messages'], '3'],
            ['maker', $this->langs['mn_edition_maker'], '4'],
            ['users', $this->langs['mn_edition_users'], '4'],
            ['alliances', $this->langs['mn_edition_alliances'], '4'],
            ['languages', $this->langs['mn_edition_languages'], '4'],
            ['backup', $this->langs['mn_tools_backup'], '5'],
            ['encrypter', $this->langs['mn_tools_encrypter'], '5'],
            ['announcement', $this->langs['mn_tools_global_message'], '5'],
            ['ban', $this->langs['mn_tools_ban'], '5'],
            ['rebuildhighscores', $this->langs['mn_tools_manual_update'], '5'],
            ['update', $this->langs['mn_tools_update'], '5'],
            ['migrate', $this->langs['mn_tools_migrate'], '5'],
            ['repair', $this->langs['mn_maintenance_db'], '6'],
            ['reset', $this->langs['mn_reset_universe'], '6'],
        );
        $active_block = 1;

        // BUILD THE MENU
        foreach ($pages as $key => $data) {
            $extra = '';
            $active = '';

            if ($data[2] != $flag) {
                $flag = $data[2];
                $items = '';
            }

            if ($data[0] == 'rebuildhighscores') {
                $extra = 'onClick="return confirm(\'' . $this->langs['mn_tools_manual_update_confirm'] . '\');"';
            }

            if ($data[0] == $current_page) {
                $active = ' active';
                $active_block = $data[2];
            }

            $items .= '<a class="collapse-item' . $active . '" href="' . ADM_URL . 'admin.php?page=' . $data[0] . '"  ' . $extra . '>' . $data[1] . '</a>';

            $parse_block[$data[2]] = $items;
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $this->langs;
        $parse['username'] = $this->current_user['user_name'];
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
                $this->langs,
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
     * parseTemplate
     *
     * @param string $template Template
     * @param array $array     Values to parse
     *
     * @return void
     *
     * @deprecated since version v3.0.2, will be removed on v3.2.0
     */
    public function parseTemplate($template, $array)
    {
        return preg_replace_callback(
            '#\{([a-z0-9\-_]*?)\}#Ssi',
            function ($matches) use ($array) {
                return ((isset($array[$matches[1]])) ? $array[$matches[1]] : '');
            },
            $template
        );
    }

    /**
     * getTemplate
     *
     * @param string $template_name Template name
     *
     * @return string
     *
     * @deprecated since version v3.0.2, will be removed on v3.2.0
     */
    public function getTemplate($template_name)
    {
        $route = XGP_ROOT . TEMPLATE_DIR . $template_name . '.php';
        $template = @file_get_contents($route);

        if ($template) { // We got something
            return $template; // Return
        } else {
            // Throw Exception
            die('Template not found or empty: <strong>' . $template_name . '</strong><br />
    Location: <strong>' . $route . '</strong>');
        }
    }

    /**
     * parse the data into the provided template
     *
     * @param array  $array    Values to parse
     * @param string $template Template
     *
     * @return void
     *
     * @deprecated since version v3.1.0, will be removed on v3.2.0
     */
    public function parse($array = array(), $template = '')
    {
        return preg_replace_callback(
            '#\{([a-z0-9\-_]*?)\}#Ssi',
            function ($matches) use ($array) {
                return ((isset($array[$matches[1]])) ? $array[$matches[1]] : '');
            },
            ($template == '' ? $this->template : $template)
        );
    }

    /**
     * get the provided template
     *
     * @param string $template_name Template name
     *
     * @return string
     *
     * @deprecated since version v3.1.0, will be removed on v3.2.0
     */
    public function get($template_name)
    {
        try {
            $route = XGP_ROOT . TEMPLATE_DIR . $template_name . '.php';
            $template = @file_get_contents($route);

            if ($template) { // We got something
                $this->template = $template;

                return $this;
            }

            // not found
            throw new \Exception(
                'Template not found or empty: <strong>' . $template_name . '</strong><br />
    Location: <strong>' . $route . '</strong>'
            );
        } catch (\Exception $e) {
            // Throw Exception
            die($e->getMessage());
        }
    }

    /**
     * installHeader
     *
     * @return string
     */
    private function installHeader()
    {
        return $this->template->set(
            'install/simple_header',
            [
                'title' => 'Install',
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
    private function installNavbar()
    {
        // Update config language to the new setted value
        if (isset($_POST['language'])) {
            FunctionsLib::setCurrentLanguage($_POST['language']);
            FunctionsLib::redirect(XGP_ROOT . 'install/');
        }

        $current_page = isset($_GET['page']) ? $_GET['page'] : null;
        $items = '';

        $pages = [
            0 => array('installation', $this->langs['ins_overview'], 'overview'),
            1 => array('installation', $this->langs['ins_license'], 'license'),
            2 => array('installation', $this->langs['ins_install'], 'step1'),
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
        $parse = $this->langs;
        $parse['menu_items'] = $items;
        $parse['language_select'] = FunctionsLib::getLanguages(FunctionsLib::getCurrentLanguage());

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
    private function installMenu()
    {
        $current_mode = isset($_GET['mode']) ? $_GET['mode'] : null;
        $items = '';
        $steps = [
            0 => ['step1', $this->langs['ins_step1']],
            1 => ['step2', $this->langs['ins_step2']],
            2 => ['step3', $this->langs['ins_step3']],
            3 => ['step4', $this->langs['ins_step4']],
            4 => ['step5', $this->langs['ins_step5']],
        ];

        // BUILD THE MENU
        foreach ($steps as $key => $data) {
            // URL
            $items .= '<li' . ($current_mode == $data[0] ? ' class="active"' : '') .
                '><a href="#">' . $data[1] . '</a></li>';
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $this->langs;
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
        $parse['game_title'] = FunctionsLib::readConfig('game_name');
        $parse['version'] = SYSTEM_VERSION;
        $parse['css_path'] = CSS_PATH;
        $parse['skin_path'] = DPATH;
        $parse['js_path'] = JS_PATH;
        $parse['meta_tags'] = ($metatags) ? $metatags : "";

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
        $parse = $this->langs;
        $parse['dpath'] = DPATH;
        $parse['image'] = $this->current_planet['planet_image'];
        $parse['planetlist'] = FunctionsLib::buildPlanetList($this->current_user);

        $parse['show_umod_notice'] = '';

        // When vacation mode did not expire
        if ($this->current_user['preference_vacation_mode'] > 0) {
            $parse['color'] = '#1DF0F0';
            $parse['message'] = $this->langs['tn_vacation_mode'] . Timing::formatExtendedDate($this->current_user['preference_vacation_mode']);
            $parse['jump_line'] = '<br/>';

            $parse['show_umod_notice'] = $this->template->set(
                'general/notices_view',
                $parse
            );
        }

        if ($this->current_user['preference_delete_mode'] > 0) {
            // When it is in delete mode
            $parse['color'] = '#FF0000';
            $parse['message'] = $this->langs['tn_delete_mode'] . Timing::formatExtendedDate($this->current_user['preference_delete_mode'] + (60 * 60 * 24 * 7));
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

        // OFFICERS AVAILABILITY
        $commander = OfficiersLib::isOfficierActive($this->current_user['premium_officier_commander']) ? '' : '_un';
        $admiral = OfficiersLib::isOfficierActive($this->current_user['premium_officier_admiral']) ? '' : '_un';
        $engineer = OfficiersLib::isOfficierActive($this->current_user['premium_officier_engineer']) ? '' : '_un';
        $geologist = OfficiersLib::isOfficierActive($this->current_user['premium_officier_geologist']) ? '' : '_un';
        $technocrat = OfficiersLib::isOfficierActive($this->current_user['premium_officier_technocrat']) ? '' : '_un';

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

        $parse['metal'] = $metal;
        $parse['crystal'] = $crystal;
        $parse['deuterium'] = $deuterium;
        $parse['darkmatter'] = $darkmatter;
        $parse['energy'] = $energy;
        $parse['img_commander'] = $commander;
        $parse['img_admiral'] = $admiral;
        $parse['img_engineer'] = $engineer;
        $parse['img_geologist'] = $geologist;
        $parse['img_technocrat'] = $technocrat;

        return $this->template->set(
            'general/topnav',
            $parse
        );
    }

    /**
     * gameMenu
     *
     * @return string
     */
    private function gameMenu()
    {
        $menu_block1 = '';
        $menu_block2 = '';
        $menu_block3 = '';
        $modules_array = explode(';', FunctionsLib::readConfig('modules'));
        $tota_rank = $this->current_user['user_statistic_total_rank'] == '' ?
        $this->current_planet['stats_users'] : $this->current_user['user_statistic_total_rank'];
        $pages = [
            ['changelog', SYSTEM_VERSION, '', 'FFF', '', '0', '0'],
            ['overview', $this->langs['lm_overview'], '', 'FFF', '', '1', '1'],
            ['empire', $this->langs['lm_empire'], '', 'FFF', '', '1', '2'],
            ['resources', $this->langs['lm_resources'], '', 'FFF', '', '1', '3'],
            ['resourceSettings', $this->langs['lm_resources_settings'], '', 'FFF', '', '1', '4'],
            ['station', $this->langs['lm_station'], '', 'FFF', '', '1', '3'],
            ['traderOverview', $this->langs['lm_trader'], '', 'FF8900', '', '1', '5'],
            ['research', $this->langs['lm_research'], '', 'FFF', '', '1', '6'],
            ['techtree', $this->langs['lm_technology'], '', 'FFF', '', '1', '10'],
            ['shipyard', $this->langs['lm_shipyard'], '', 'FFF', '', '1', '7'],
            ['defense', $this->langs['lm_defenses'], '', 'FFF', '', '1', '12'],
            ['fleet1', $this->langs['lm_fleet'], '', 'FFF', '', '1', '8'],
            ['movement', $this->langs['lm_movement'], '', 'FFF', '', '1', '9'],
            ['galaxy', $this->langs['lm_galaxy'], 'mode=0', 'FFF', '', '1', '11'],
            ['alliance', $this->langs['lm_alliance'], '', 'FFF', '', '1', '13'],
            ['officier', $this->langs['lm_officiers'], '', 'FF8900', '', '1', '15'],
            ['messages', $this->langs['lm_messages'], '', 'FFF', '', '1', '18'],
            ['statistics', $this->langs['lm_statistics'], 'range=' . $tota_rank, 'FFF', '', '2', '16'],
            ['notes', $this->langs['lm_notes'], '', 'FFF', 'true', '2', '19'],
            ['buddies', $this->langs['lm_buddylist'], '', 'FFF', '', '2', '20'],
            ['search', $this->langs['lm_search'], '', 'FFF', '', '2', '17'],
            ['preferences', $this->langs['lm_options'], '', 'FFF', '', '2', '21'],
            ['logout', $this->langs['lm_logout'], '', 'FFF', '', '2', ''],
            ['forums', $this->langs['lm_forums'], '', 'FFF', '', '3', '14'],
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
        $parse['version'] = SYSTEM_VERSION;
        $parse['servername'] = FunctionsLib::readConfig('game_name');
        $parse['year'] = $this->current_year;
        $parse['menu_block1'] = $menu_block1;
        $parse['menu_block2'] = $menu_block2;
        $parse['menu_block3'] = $menu_block3;
        $parse['admin_link'] = (($this->current_user['user_authlevel'] > 0) ?
            "<tr><td><div align=\"center\"><a href=\"admin.php\" target=\"_blank\">
            <font color=\"lime\">" . $this->langs['lm_administration'] . "</font></a></div></td></tr>" : "");

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

        return implode($new_lines);
    }
}

/* end of TemplateLib.php */
