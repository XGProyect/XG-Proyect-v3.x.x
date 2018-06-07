<?php
/**
 * Template Library
 *
 * PHP Version 5.5+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries;

/**
 * TemplateLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
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

        // For the Admin page
        if (defined('IN_ADMIN')) {

            $page .= $this->adminHeader($metatags);
            $page .= $menu ? $this->adminMenu() : ''; // MENU
            $page .= $topnav ? $this->adminNavbar() : ''; // TOP NAVIGATION BAR
        }

        // Anything else
        if ($page == '') {

            $page .= $this->gameHeader($metatags);
            $page .= $topnav ? $this->gameNavbar() : ''; // TOP NAVIGATION BAR
            $page .= $menu ? $this->gameMenu() : ''; // MENU
        }

        // Merge: Header + Topnav + Menu + Page
        if (!defined('IN_INSTALL') && !defined('IN_ADMIN')) {

            $page .= "\n<center>\n" . $current_page . "\n</center>\n";
        } else {

            if (defined('IN_MESSAGE')) {

                $page .= "\n<center>\n" . $current_page . "\n</center>\n";
            } else {

                $page .= $current_page;
            }
        }

        // Footer
        if (!defined('IN_INSTALL') && !defined('IN_ADMIN') && !defined('IN_LOGIN')) {

            // Is inside the game
            if (isset($_GET['page']) && $_GET['page'] != 'galaxy') {

                $page .= $this->parseTemplate($this->getTemplate('general/footer'), '');
            }
        }

        if (defined('IN_ADMIN')) {

            $page .= $this->parseTemplate(
                $this->getTemplate('adm/simple_footer'), ['version' => SYSTEM_VERSION, 'year' => $this->current_year]
            );
        }

        if (defined('IN_INSTALL') && !defined('IN_MESSAGE')) {

            $page .= $this->parseTemplate(
                $this->getTemplate('install/simple_footer'), ['year' => $this->current_year]
            );
        }

        // Show result page
        die($page);
    }

    /**
     * parseTemplate
     *
     * @param string $template Template
     * @param array $array     Values to parse
     *
     * @return void
     * 
     * @deprecated since version v3.0.2, will be removed on v3.4.x
     */
    public function parseTemplate($template, $array)
    {
        return preg_replace_callback(
            '#\{([a-z0-9\-_]*?)\}#Ssi', function ($matches) use ($array) {
                return ((isset($array[$matches[1]])) ? $array[$matches[1]] : '');
            }, $template
        );
    }

    /**
     * getTemplate
     *
     * @param string $template_name Template name
     *
     * @return string
     * 
     * @deprecated since version v3.0.2, will be removed on v3.4.x
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
     * @deprecated since version v3.1.0, will be removed on v3.4.x
     */
    public function parse($array = array(), $template = '')
    {
        return preg_replace_callback(
            '#\{([a-z0-9\-_]*?)\}#Ssi', function ($matches) use ($array) {
                return ((isset($array[$matches[1]])) ? $array[$matches[1]] : '');
            }, ($template == '' ? $this->template : $template)
        );
    }

    /**
     * get the provided template
     *
     * @param string $template_name Template name
     *
     * @return string
     * 
     * @deprecated since version v3.1.0, will be removed on v3.4.x
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
        $parse['title'] = 'Install';
        $parse['js_path'] = '../js/';
        $parse['css_path'] = '../css/';

        return $this->parseTemplate($this->getTemplate('install/simple_header'), $parse);
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
            2 => array('installation', $this->langs['ins_install'], 'step1')
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

        return $this->parseTemplate($this->getTemplate('install/topnav_view'), $parse);
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
            4 => ['step5', $this->langs['ins_step5']]
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

        return $this->parseTemplate($this->getTemplate('install/menu_view'), $parse);
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

        return $this->parseTemplate($this->getTemplate('general/simple_header'), $parse);
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
        if ($this->current_user['setting_vacations_status']) {

            $parse['color'] = '#1DF0F0';
            $parse['message'] = $this->langs['tn_vacation_mode'] . date(
                    FunctionsLib::readConfig('date_format_extended'), $this->current_user['setting_vacations_until']
            );
            $parse['jump_line'] = '<br/>';

            $parse['show_umod_notice'] = $this->parseTemplate($this->getTemplate('general/notices_view'), $parse);
        }

        if ($this->current_user['setting_delete_account']) {

            // When it is in delete mode
            $parse['color'] = '#FF0000';
            $parse['message'] = $this->langs['tn_delete_mode'] . date(
                    FunctionsLib::readConfig('date_format_extended'), $this->current_user['setting_delete_account'] + (60 * 60 * 24 * 7)
            );
            $parse['jump_line'] = '';

            $parse['show_umod_notice'] = $this->parseTemplate($this->getTemplate('general/notices_view'), $parse);
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
        if (( $this->current_planet['planet_metal'] >= $this->current_planet['planet_metal_max'])) {

            $metal = FormatLib::colorRed($metal);
        }

        // CRYSTAL
        if (( $this->current_planet['planet_crystal'] >= $this->current_planet['planet_crystal_max'])) {

            $crystal = FormatLib::colorRed($crystal);
        }

        // DEUTERIUM
        if (( $this->current_planet['planet_deuterium'] >= $this->current_planet['planet_deuterium_max'])) {

            $deuterium = FormatLib::colorRed($deuterium);
        }

        // ENERGY
        if (( $this->current_planet['planet_energy_max'] + $this->current_planet['planet_energy_used'] ) < 0) {

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

        return $this->parseTemplate($this->getTemplate('general/topnav'), $parse);
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
        $sub_template = $this->getTemplate('general/left_menu_row_view');
        $tota_rank = $this->current_user['user_statistic_total_rank'] == '' ?
            $this->current_planet['stats_users'] : $this->current_user['user_statistic_total_rank'];
        $pages = [
            ['changelog', SYSTEM_VERSION, '', 'FFF', '', '0', '0'],
            ['overview', $this->langs['lm_overview'], '', 'FFF', '', '1', '1'],
            ['imperium', $this->langs['lm_empire'], '', 'FFF', '', '1', '2'],
            ['resources', $this->langs['lm_resources'], '', 'FFF', '', '1', '3'],
            ['resourceSettings', $this->langs['lm_resources_settings'], '', 'FFF', '', '1', '4'],
            ['station', $this->langs['lm_station'], '', 'FFF', '', '1', '3'],
            ['trader', $this->langs['lm_trader'], '', 'FF8900', '', '1', '5'],
            ['research', $this->langs['lm_research'], '', 'FFF', '', '1', '6'],
            ['shipyard', $this->langs['lm_shipyard'], '', 'FFF', '', '1', '7'],
            ['fleet1', $this->langs['lm_fleet'], '', 'FFF', '', '1', '8'],
            ['movement', $this->langs['lm_movement'], '', 'FFF', '', '1', '9'],
            ['techtree', $this->langs['lm_technology'], '', 'FFF', '', '1', '10'],
            ['galaxy', $this->langs['lm_galaxy'], 'mode=0', 'FFF', '', '1', '11'],
            ['defense', $this->langs['lm_defenses'], '', 'FFF', '', '1', '12'],
            ['alliance', $this->langs['lm_alliance'], '', 'FFF', '', '2', '13'],
            ['forums', $this->langs['lm_forums'], '', 'FFF', '', '2', '14'],
            ['officier', $this->langs['lm_officiers'], '', 'FF8900', '', '2', '15'],
            ['statistics', $this->langs['lm_statistics'], 'range=' . $tota_rank, 'FFF', '', '2', '16'],
            ['search', $this->langs['lm_search'], '', 'FFF', '', '2', '17'],
            ['messages', $this->langs['lm_messages'], '', 'FFF', '', '3', '18'],
            ['notes', $this->langs['lm_notes'], '', 'FFF', 'true', '3', '19'],
            ['buddies', $this->langs['lm_buddylist'], '', 'FFF', '', '3', '20'],
            ['options', $this->langs['lm_options'], '', 'FFF', '', '3', '21'],
            ['banned', $this->langs['lm_banned'], '', 'FFF', '', '3', '22'],
            ['logout', $this->langs['lm_logout'], '', 'FFF', '', '3', '']
        ];

        // BUILD THE MENU
        foreach ($pages as $key => $data) {

            // IF THE MODULE IT'S NOT ENABLED, CONTINUE!
            if (isset($modules_array[$data[6]]) && $modules_array[$data[6]] == 0 && $modules_array[$data[6]] != '') {

                continue;
            }

            if (!OfficiersLib::isOfficierActive($this->current_user['premium_officier_commander']) && $data[0] == 'imperium') {

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
                    <font color="' . ( ( $data[3] != 'FFF' ) ? $data[3] : '' ) . '">' . $data[1] . '</font></a>';
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
                    $menu_block1 .= $this->parseTemplate($sub_template, $parse);

                    break;

                case '2':
                    $menu_block2 .= $this->parseTemplate($sub_template, $parse);

                    break;

                case '3':
                    $menu_block3 .= $this->parseTemplate($sub_template, $parse);

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

        return $this->parseTemplate($this->getTemplate('general/left_menu_view'), $parse);
    }

    /**
     * adminHeader
     *
     * @param string $metatags Meta tags
     *
     * @return string
     */
    private function adminHeader($metatags = '')
    {
        $parse['title'] = 'Admin CP';
        $parse['js_path'] = JS_PATH;
        $parse['css_path'] = CSS_PATH;
        $parse['-meta-'] = $metatags ? $metatags : '';

        return $this->parseTemplate($this->getTemplate('adm/simple_header'), $parse);
    }

    /**
     * adminNavbar
     *
     * @return string
     */
    private function adminNavbar()
    {
        // PARSE THE MENU AND OTHER DATA
        $parse = $this->langs;
        $parse['version'] = FunctionsLib::readConfig('version');

        return $this->parseTemplate($this->getTemplate('adm/topnav_view'), $parse);
    }

    /**
     * adminMenu
     *
     * @return string
     */
    private function adminMenu()
    {
        $current_page = isset($_GET['page']) ? $_GET['page'] : null;
        $items = '';
        $flag = '';
        $pages = array(
            ['home', $this->langs['mn_index'], '1'],
            ['moderation', $this->langs['mn_permissions'], '1'],
            ['reset', $this->langs['mn_reset_universe'], '1'],
            ['queries', $this->langs['mn_sql_queries'], '1'],
            ['server', $this->langs['mn_config_server'], '2'],
            ['modules', $this->langs['mn_config_modules'], '2'],
            ['planets', $this->langs['mn_config_planets'], '2'],
            ['registration', $this->langs['mn_config_registrations'], '2'],
            ['statistics', $this->langs['mn_config_stats'], '2'],
            ['premium', $this->langs['mn_premium'], '2'],
            ['editor', $this->langs['mn_config_changelog'], '2'],
            ['information', $this->langs['mn_info_general'], '3'],
            ['errors', $this->langs['mn_info_db'], '3'],
            ['fleetmovements', $this->langs['mn_info_fleets'], '3'],
            ['messages', $this->langs['mn_info_messages'], '3'],
            ['maker', $this->langs['mn_edition_maker'], '4'],
            ['users', $this->langs['mn_edition_users'], '4'],
            ['alliances', $this->langs['mn_edition_alliances'], '4'],
            ['backup', $this->langs['mn_tools_backup'], '5'],
            ['encrypter', $this->langs['mn_tools_encrypter'], '5'],
            ['globalmessage', $this->langs['mn_tools_global_message'], '5'],
            ['ban', $this->langs['mn_tools_ban'], '5'],
            ['buildstats', $this->langs['mn_tools_manual_update'], '5'],
            ['update', $this->langs['mn_tools_update'], '5'],
            ['migrate', $this->langs['mn_tools_migrate'], '5'],
            ['repair', $this->langs['mn_maintenance_db'], '6'],
        );
        // BUILD THE MENU
        foreach ($pages as $key => $data) {

            if ($data[2] != $flag) {

                $flag = $data[2];
                $items = '';
            }

            if ($data[0] == 'buildstats') {

                $extra = 'onClick="return confirm(\'' . $this->langs['mn_tools_manual_update_confirm'] . '\');"';
            } else {

                $extra = '';
            }

            $items .= '<li' . ($current_page == $data[0] ? ' class="active"' : '') . '>
                <a href="' . ADM_URL . 'admin.php?page=' . $data[0] . '" ' . $extra . '>' . $data[1] . '</a></li>';

            $parse_block[$data[2]] = $items;
        }

        // PARSE THE MENU AND OTHER DATA
        $parse = $this->langs;
        $parse['username'] = $this->current_user['user_name'];
        $parse['menu_block_1'] = $parse_block[1];
        $parse['menu_block_2'] = $parse_block[2];
        $parse['menu_block_3'] = $parse_block[3];
        $parse['menu_block_4'] = $parse_block[4];
        $parse['menu_block_5'] = $parse_block[5];
        $parse['menu_block_6'] = $parse_block[6];

        return $this->parseTemplate($this->getTemplate('adm/menu_view'), $parse);
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
