<?php
/**
 * GalaxyLib.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @since    3.2.0
 */

namespace App\libraries;

use App\core\enumerators\MissionsEnumerator as Missions;
use App\core\enumerators\PlanetTypesEnumerator;
use App\core\enumerators\ShipsEnumerator as Ships;
use App\core\enumerators\UserRanksEnumerator as UserRanks;
use App\core\Template;
use App\core\XGPCore;
use App\helpers\StringsHelper;
use App\helpers\UrlHelper;
use App\libraries\FleetsLib;
use App\libraries\FormatLib;
use App\libraries\Formulas;
use App\libraries\Functions;

/**
 * GalaxyLib class
 */
class GalaxyLib extends XGPCore
{
    public const PLANET_TYPE = 1;
    public const DEBRIS_TYPE = 2;
    public const MOON_TYPE = 3;

    /**
     * @var mixed
     */
    private $langs;
    /**
     * @var mixed
     */
    private $current_user;
    /**
     * @var mixed
     */
    private $current_planet;
    /**
     * @var mixed
     */
    private $row_data;
    /**
     * @var mixed
     */
    private $galaxy;
    /**
     * @var mixed
     */
    private $system;
    /**
     * @var mixed
     */
    private $planet;
    /**
     * @var mixed
     */
    private $resource;
    /**
     * @var mixed
     */
    private $pricelist;
    /**
     * @var mixed
     */
    private $noob;
    /**
     * @var mixed
     */
    private $template;

    /**
     * Indicates if we should display the popup or not
     *
     * @var boolean
     */
    private $no_popup = false;

    /**
     * __construct
     *
     * @param array $user   User
     * @param array $planet Planet
     * @param int   $galaxy Galaxy
     * @param int   $system System
     *
     * @return void
     */
    public function __construct($user = '', $planet = '', $galaxy = '', $system = '', $langs = '')
    {
        parent::__construct();

        $this->langs = $langs;
        $this->current_user = $user;
        $this->current_planet = $planet;
        $this->galaxy = $galaxy;
        $this->system = $system;
        $this->resource = parent::$objects->getObjects();
        $this->pricelist = parent::$objects->getPrice();
        $this->noob = Functions::loadLibrary('NoobsProtectionLib');

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

    ######################################
    #
    # main methods
    #
    ######################################

    /**
     * buildRow
     *
     * @param array $row_data Row data
     * @param array $planet   Planet
     *
     * @return array
     */
    public function buildRow($row_data, $planet)
    {
        // SOME DATA THAT WE ARE GOING TO REQUIRE FOR EACH COLUMN
        $this->row_data = $row_data;
        $this->planet = $planet;

        // BLOCK TEMPLATES
        $block['planet'] = 'galaxy/galaxy_planet_block';
        $block['moon'] = 'galaxy/galaxy_moon_block';
        $block['debris'] = 'galaxy/galaxy_debris_block';
        $block['username'] = 'galaxy/galaxy_username_block';
        $block['alliance'] = 'galaxy/galaxy_alliance_block';

        // PRE CREATED BLOCK TO PREVENT REDUNDANCY
        $debris_block = $this->debrisBlock();

        // POSITION COLUMN, VALUES BY DEFAULT
        $row['pos'] = $planet;
        $row['planet'] = '';
        $row['planetname'] = $this->planetNameBlock();
        $row['moon'] = '';
        $row['debris'] = $debris_block != '' ? $this->template->set($block['debris'], $debris_block) : '';
        $row['username'] = '';
        $row['alliance'] = '';
        $row['actions'] = '';

        // ALL OTHER COLUMNS
        if ($row_data['planet_destroyed'] == 0) { // IF THE PLANET ON THIS POSITION IS ACTIVE
            // PRE CREATED BLOCK TO PREVENT REDUNDANCY
            $moon_block = $this->moonBlock();
            $user_block = $this->usernameBlock();

            // PARSE DATA
            $row['planet'] = $this->template->set($block['planet'], $this->planetBlock());
            $row['moon'] = $moon_block != '' ? $this->template->set($block['moon'], $moon_block) : '';
            $row['username'] = $this->no_popup ? $user_block['status'] : $this->template->set($block['username'], $user_block);
            $row['alliance'] = $this->template->set($block['alliance'], $this->allyBlock());
            $row['actions'] = $this->actionsBlock();
        }

        // RETURN DATA
        return $row;
    }
    ######################################
    #
    # blocks methods
    #
    ######################################

    /**
     * planetBlock
     *
     * @return array
     */
    private function planetBlock()
    {
        $action['spy'] = '';
        $action['phalanx'] = '';
        $action['attack'] = '';
        $action['hold_position'] = '';
        $action['deploy'] = '';
        $action['transport'] = '';
        $action['missile'] = '';

        // GLOBAL
        $action['transport'] = $this->transportLink(self::PLANET_TYPE);

        // ONLY IF IS NOT THE CURRENT USER
        if ($this->row_data['user_id'] != $this->current_user['user_id']) {
            $action['attack'] = $this->attackLink(self::PLANET_TYPE);
            $action['spy'] = $this->spyLink(self::PLANET_TYPE);

            // HOLD POSITION ONLY IF IS A FRIEND
            if ($this->isFriendly()) {
                $action['hold_position'] = $this->holdPositionLink(self::PLANET_TYPE);
            }
        }

        // ONLY IF IS THE CURRENT USER
        if ($this->row_data['user_id'] == $this->current_user['user_id']) {
            $action['deploy'] = $this->deployLink(self::PLANET_TYPE);
        }

        // MISSILE
        if ($this->isMissileActive()) {
            $action['missile'] = $this->missileLink(self::PLANET_TYPE);
        }

        // PHALANX
        if ($this->isPhalanxActive()) {
            $action['phalanx'] = $this->phalanxLink(self::PLANET_TYPE);
        }

        // PARSE THE DATA
        $parse = $this->langs->language;
        $parse['name'] = $this->row_data['planet_name'];
        $parse['galaxy'] = $this->galaxy;
        $parse['system'] = $this->system;
        $parse['planet'] = $this->planet;
        $parse['image'] = strtr(DPATH, ['\\' => '/']) . 'planets/small/s_' . $this->row_data['planet_image'] . '.jpg';
        $parse['links'] = '';

        // LOOP THRU ACTIONS
        foreach ($action as $to_parse) {
            if ($to_parse != '') { // SKIP EMPTY ACTIONS
                $parse['links'] .= $to_parse . '<br>';
            }
        }

        $this->row_data['planet_type'] = self::PLANET_TYPE;
        if (Functions::isCurrentPlanet($this->current_planet, $this->row_data)) {
            $parse['links'] = $this->langs->line('gl_no_action');
        }

        if ($this->row_data['user_authlevel'] >= UserRanks::GO
            && $this->row_data['user_id'] != $this->current_user['user_id']) {
            $parse['links'] = $this->transportLink(self::PLANET_TYPE);
        }

        if ($this->row_data['preference_vacation_mode'] > 0) {
            $parse['links'] = $this->langs->line('gl_player_vacation_mode');
        }

        return $parse;
    }

    /**
     * planetNameBlock
     *
     * @return void
     */
    private function planetNameBlock()
    {
        $phalanx_link = stripslashes($this->row_data['planet_name']);

        if ($this->row_data['planet_destroyed'] == 0) {
            if ($this->isPhalanxActive()) {
                $attributes = "onclick=fenster('game.php?page=phalanx&galaxy=" . $this->galaxy .
                "&amp;system=" . $this->system . "&amp;planet=" . $this->planet .
                "&amp;planettype=" . self::PLANET_TYPE . "')";
                $phalanx_link = UrlHelper::setUrl('', $this->row_data['planet_name'], 'Phalanx', $attributes);
            }

            $planetname = $phalanx_link;

            if ($this->row_data['planet_last_update'] > (time() - 59 * 60) && $this->row_data['user_id'] != $this->current_user['user_id']) {
                if ($this->row_data['planet_last_update'] > (time() - 10 * 60) && $this->row_data['user_id'] != $this->current_user['user_id']) {
                    $planetname .= "(*)";
                } else {
                    $planetname .= " (" . FormatLib::prettyTimeHour(
                        time() - $this->row_data['planet_last_update']
                    ) . ")";
                }
            }
        } else {
            $planetname = $this->langs->line('gl_planet_destroyed');
        }

        return $planetname;
    }

    /**
     * moonBlock
     *
     * @return array
     */
    private function moonBlock()
    {
        if ($this->row_data['destroyed_moon'] != 0 or $this->row_data['id_luna'] == 0) {
            return '';
        }

        $action['spy'] = '';
        $action['attack'] = '';
        $action['transport'] = '';
        $action['deploy'] = '';
        $action['hold_position'] = '';
        $action['destroy'] = '';

        // GLOBAL
        $action['transport'] = $this->transportLink(self::MOON_TYPE);

        // ONLY IF IS NOT THE CURRENT USER
        if ($this->row_data['user_id'] != $this->current_user['user_id']) {
            $action['spy'] = $this->spyLink(self::MOON_TYPE);
            $action['attack'] = $this->attackLink(self::MOON_TYPE);

            // HOLD POSITION ONLY IF IS A FRIEND
            if ($this->isFriendly()) {
                $action['hold_position'] = $this->holdPositionLink(self::MOON_TYPE);
            }

            // DESTROY
            if ($this->current_planet[$this->resource[214]] > 0) {
                $action['destroy'] = $this->destroyLink(self::MOON_TYPE);
            }
        }

        // ONLY IF IS THE CURRENT USER
        if ($this->row_data['user_id'] == $this->current_user['user_id']) {
            $action['deploy'] = $this->deployLink(self::MOON_TYPE);
        }

        // CHECK MOON STATUS AND COMPLETE DATA IF REQUIRED
        $parse = $this->langs->language;
        $parse['name_moon'] = $this->row_data['name_moon'];
        $parse['galaxy'] = $this->galaxy;
        $parse['system'] = $this->system;
        $parse['planet'] = $this->planet;
        $parse['image'] = strtr(DPATH, ['\\' => '/']) . 'planets/small/s_mond.jpg';
        $parse['planet_diameter'] = FormatLib::prettyNumber($this->row_data['planet_diameter']);
        $parse['links'] = '';

        // LOOP THRU ACTIONS
        foreach ($action as $to_parse) {
            // SKIP EMPTY ACTIONS
            if ($to_parse != '') {
                $parse['links'] .= $to_parse . '<br>';
            }
        }
        $this->row_data['planet_type'] = self::MOON_TYPE;
        if (Functions::isCurrentPlanet($this->current_planet, $this->row_data)) {
            $parse['links'] = $this->langs->line('gl_no_action');
        }

        if ($this->row_data['user_authlevel'] >= UserRanks::GO
            && $this->row_data['user_id'] != $this->current_user['user_id']) {
            $parse['links'] = $this->transportLink(self::PLANET_TYPE);
        }

        if ($this->row_data['preference_vacation_mode'] > 0) {
            $parse['links'] = $this->langs->line('gl_player_vacation_mode');
        }

        return $parse;
    }

    /**
     * debrisBlock
     *
     * @return array
     */
    private function debrisBlock()
    {
        if ($this->row_data['metal'] + $this->row_data['crystal'] >= DEBRIS_MIN_VISIBLE_SIZE) {
            $recyclers_storage = FleetsLib::getMaxStorage(
                $this->pricelist[Ships::ship_recycler]['capacity'],
                $this->current_user['research_hyperspace_technology']
            );

            $recyclers_needed = ceil(
                ($this->row_data['metal'] + $this->row_data['crystal']) / $recyclers_storage
            );

            if ($recyclers_needed < $this->current_planet['ship_recycler']) {
                $recyclers_sended = $recyclers_needed;
            } elseif ($recyclers_needed >= $this->current_planet['ship_recycler']) {
                $recyclers_sended = $this->current_planet['ship_recycler'];
            }

            $parse = $this->langs->language;
            $parse['galaxy'] = $this->galaxy;
            $parse['system'] = $this->system;
            $parse['planet'] = $this->planet;
            $parse['image'] = strtr(DPATH, ['\\' => '/']) . 'planets/debris.jpg';
            $parse['planettype'] = self::PLANET_TYPE;
            $parse['recsended'] = $recyclers_sended;
            $parse['planet_debris_metal'] = FormatLib::prettyNumber($this->row_data['metal']);
            $parse['planet_debris_crystal'] = FormatLib::prettyNumber($this->row_data['crystal']);

            return $parse;
        }

        return '';
    }

    /**
     * usernameBlock
     *
     * @return array
     */
    private function usernameBlock()
    {
        $this->no_popup = false;

        if ($this->row_data['user_id'] == $this->current_user['user_id']) {
            $this->no_popup = true;

            return [
                'status' => $this->row_data['user_name'],
            ];
        }

        $current_user_points = $this->current_user['user_statistic_total_points'];
        $row_user_points = $this->row_data['user_statistic_total_points'];

        $statuses = [];

        if ($this->row_data['user_authlevel'] >= UserRanks::GO) {
            $this->no_popup = true;

            $statuses['admin'] = [
                'class' => $this->getUserStatusClass('a'),
                'shortcut' => $this->langs->line('gl_a'),
            ];
        }

        if ($this->row_data['user_banned']) {
            $statuses['banned'] = [
                'class' => $this->getUserStatusClass('b'),
                'shortcut' => $this->langs->line('gl_b'),
            ];
        }

        if ($this->row_data['preference_vacation_mode'] > 0) {
            $statuses['vacation'] = [
                'class' => $this->getUserStatusClass('v'),
                'shortcut' => $this->langs->line('gl_v'),
            ];
        }

        if ($this->row_data['user_onlinetime'] < (time() - ONE_WEEK)) {
            $statuses['inactive'] = [
                'class' => $this->getUserStatusClass('i'),
                'shortcut' => $this->langs->line('gl_i'),
            ];
        }

        if ($this->row_data['user_onlinetime'] < (time() - ONE_DAY * 28)) {
            $statuses['inactive'] = [
                'class' => $this->getUserStatusClass('I'),
                'shortcut' => $this->langs->line('gl_I'),
            ];
        }

        if (!isset($statuses['admin']) && !isset($statuses['banned'])) {
            if ($this->noob->isWeak(intval($current_user_points), intval($row_user_points))) {
                $statuses['protection'] = [
                    'class' => $this->getUserStatusClass('w'),
                    'shortcut' => $this->langs->line('gl_w'),
                ];
            }

            if ($this->noob->isStrong(intval($current_user_points), intval($row_user_points))) {
                $statuses['protection'] = [
                    'class' => $this->getUserStatusClass('s'),
                    'shortcut' => $this->langs->line('gl_s'),
                ];
            }
        }

        $user_name = '';
        $user_status = [];
        foreach ($statuses as $status => $details) {
            if (empty($user_name)) {
                $user_name = FormatLib::spanElement($this->row_data['user_name'], $details['class']);
            }

            $user_status[] = FormatLib::spanElement($details['shortcut'], $details['class']);
        }

        if (count($user_status) > 0) {
            $formated_username = StringsHelper::parseReplacements(
                '%s (%s)',
                [$user_name, join(' ', $user_status)]
            );
        }

        $actions = "<td>";
        $actions .= str_replace('"', '', UrlHelper::setUrl(
            'game.php?page=chat&playerId=' . $this->row_data['user_id'],
            $this->langs->line('write_message')
        ));
        $actions .= "</td></tr><tr><td>";
        $actions .= str_replace('"', '', UrlHelper::setUrl(
            "game.php?page=buddies&mode=2&u=" . $this->row_data['user_id'],
            $this->langs->line('gl_buddy_request')
        ));
        $actions .= "</td></tr><tr>";

        return array_merge(
            [
                'status' => $formated_username ?? $this->row_data['user_name'],
                'username' => $this->row_data['user_name'],
                'current_rank' => $this->row_data['user_statistic_total_rank'],
                'start' => (floor($this->row_data['user_statistic_total_rank'] / 100) * 100) + 1,
                'actions' => $actions,
            ],
            $this->langs->language
        );
    }

    /**
     * allyBlock
     *
     * @return string
     */
    private function allyBlock()
    {
        $parse = ['tag' => ''];
        $add = '';

        if ($this->row_data['user_ally_id'] != 0) {
            if ($this->row_data['ally_members'] > 1) {
                $add = $this->langs->line('gl_member_add');
            }

            $parse = $this->langs->language;
            $parse['alliance_name'] = str_replace(
                "'",
                "\'",
                htmlspecialchars($this->row_data['alliance_name'], ENT_COMPAT)
            );

            $parse['web'] = '';
            $parse['ally_members'] = $this->row_data['ally_members'];
            $parse['add'] = $add;
            $parse['ally_id'] = $this->row_data['user_ally_id'];

            if ($this->row_data['alliance_web'] != '') {
                $web_url = UrlHelper::setUrl(
                    UrlHelper::prepUrl($this->row_data['alliance_web']),
                    $this->langs->line('gl_alliance_web_page'),
                    '',
                    'target="_new"'
                );

                $parse['web'] = '</tr><tr>';
                $parse['web'] .= '<td>' . str_replace('"', '', $web_url) . '</td>';
            }

            if ($this->current_user['user_ally_id'] == $this->row_data['user_ally_id']) {
                $parse['tag'] = '<span class="allymember">' . $this->row_data['alliance_tag'] . '</span>';
            } else {
                $parse['tag'] = $this->row_data['alliance_tag'];
            }
        }

        return $parse;
    }

    /**
     * actionsBlock
     *
     * @return string
     */
    private function actionsBlock(): string
    {
        if ($this->row_data['user_id'] == $this->current_user['user_id']) {
            return '';
        }

        $links = [];
        $actions = [
            'spy' => [
                'image' => Functions::setImage(DPATH . 'img/e.gif', $this->langs->line('gl_spy')),
                'attributes' => "onclick=\"javascript:doit(6, " . $this->galaxy . ", " . $this->system . ", " . $this->planet . ", 1, " . $this->current_user['preference_spy_probes'] . ");\"",
            ],
            'write' => [
                'image' => Functions::setImage(DPATH . 'img/m.gif', $this->langs->line('write_message')),
                'url' => 'game.php?page=chat&playerId=' . $this->row_data['user_id'],
            ],
            'buddy' => [
                'image' => Functions::setImage(DPATH . 'img/b.gif', $this->langs->line('gl_buddy_request')),
                'url' => 'game.php?page=buddies&mode=2&u=' . $this->row_data['user_id'],
            ],
            'missile' => [
                'image' => Functions::setImage(DPATH . 'img/r.gif', $this->langs->line('gl_missile_attack')),
                'url' => 'game.php?page=galaxy&mode=2&galaxy=' . $this->galaxy . '&system=' . $this->system . '&planet=' . $this->planet . '&current=' . $this->current_user['user_current_planet'],
            ],
        ];

        $available_actions = ['spy', 'write', 'buddy'];

        if ($this->isMissileActive()) {
            array_push($available_actions, 'missile');
        }

        if ($this->row_data['user_authlevel'] >= UserRanks::GO) {
            $available_actions = ['write'];
        }

        if ($this->row_data['preference_vacation_mode'] > 0) {
            $available_actions = ['write', 'buddy'];
        }

        foreach ($available_actions as $action) {
            if (isset($actions[$action]['url'])) {
                $links[] = UrlHelper::setUrl($actions[$action]['url'], $actions[$action]['image']);
            } else {
                $links[] = UrlHelper::setUrl('', $actions[$action]['image'], '', $actions[$action]['attributes']);
            }
        }

        return join('&nbsp;', $links);
    }
    ######################################
    #
    # missions methods
    #
    ######################################

    /**
     * attackLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function attackLink($planet_type)
    {
        $url = "game.php?page=fleet1&galaxy=" . $this->galaxy . "&amp;system=" . $this->system . "&amp;planet=" .
        $this->planet . "&amp;planettype=" . $planet_type . "&amp;target_mission=1";
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['type_mission'][Missions::ATTACK]));
    }

    /**
     * transportLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function transportLink($planet_type)
    {
        $url = "game.php?page=fleet1&galaxy=" . $this->galaxy . "&system=" . $this->system .
        "&planet=" . $this->planet . "&planettype=" . $planet_type . "&target_mission=3";
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['type_mission'][Missions::TRANSPORT]));
    }

    /**
     * deployLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function deployLink($planet_type)
    {
        $url = "game.php?page=fleet1&galaxy=" . $this->galaxy . "&system=" . $this->system .
        "&planet=" . $this->planet . "&planettype=" . $planet_type . "&target_mission=4";
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['type_mission'][Missions::DEPLOY]));
    }

    /**
     * holdPositionLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function holdPositionLink($planet_type)
    {
        $url = "game.php?page=fleet1&galaxy=" . $this->galaxy . "&system=" . $this->system .
        "&planet=" . $this->planet . "&planettype=" . $planet_type . "&target_mission=5";
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['type_mission'][Missions::STAY]));
    }

    /**
     * spyLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function spyLink($planet_type)
    {
        $attributes = "onclick=&#039javascript:doit(6, " . $this->galaxy . ", " . $this->system . ", " .
        $this->planet . ", " . $planet_type . ", " . $this->current_user['preference_spy_probes'] . ");&#039";
        return str_replace('"', '', UrlHelper::setUrl('', $this->langs->language['type_mission'][Missions::SPY], '', $attributes));
    }

    /**
     * destroyLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function destroyLink($planet_type)
    {
        $url = "game.php?page=fleet1&galaxy=" . $this->galaxy . "&system=" . $this->system . "&planet=" .
        $this->planet . "&planettype=" . $planet_type . "&target_mission=9";
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['type_mission'][Missions::DESTROY]));
    }

    /**
     * missileLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function missileLink($planet_type)
    {
        $url = "game.php?page=galaxy&mode=2&galaxy=" . $this->galaxy . "&system=" . $this->system . "&planet=" .
        $this->planet . "&current=" . $this->current_user['user_current_planet'];
        return str_replace('"', '', UrlHelper::setUrl($url, $this->langs->language['gl_missile_attack']));
    }

    /**
     * phalanxLink
     *
     * @param string $planet_type Planet type
     *
     * @return string
     */
    private function phalanxLink($planet_type)
    {
        $attributes = "onclick=fenster(&#039;game.php?page=phalanx&galaxy=" . $this->galaxy . "&amp;system=" .
        $this->system . "&amp;planet=" . $this->planet . "&amp;planettype=" . $planet_type . "&#039;)";
        return str_replace('"', '', UrlHelper::setUrl('', $this->langs->line('gl_phalanx'), '', $attributes));
    }
    ######################################
    #
    # other methods
    #
    ######################################

    /**
     * Check if it is a friendly (buddy or alliance)
     *
     * @return boolean
     */
    private function isFriendly(): bool
    {
        $is_buddy = false;

        if (isset($this->row_data['buddys'])) {
            $friends = explode(',', $this->row_data['buddys']);
            $is_buddy = in_array($this->row_data['user_id'], $friends);
        }

        if (!$is_buddy
            && (
                ($this->row_data['user_ally_id'] == 0 && $this->current_user['user_ally_id'] == 0)
                or ($this->row_data['user_ally_id'] != $this->current_user['user_ally_id'])
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * isMissileActive
     *
     * @return boolean
     */
    private function isMissileActive()
    {
        if (($this->current_planet['defense_interplanetary_missile'] != 0)
            && ($this->row_data['user_id'] != $this->current_user['user_id'])
            && ($this->row_data['planet_galaxy'] == $this->current_planet['planet_galaxy'])) {
            return $this->isInRange(Formulas::missileRange($this->current_user['research_impulse_drive']));
        }
    }

    /**
     * isPhalanxActive
     *
     * @return boolean
     */
    private function isPhalanxActive()
    {
        if (($this->current_planet['building_phalanx'] != 0)
            && ($this->row_data['user_id'] != $this->current_user['user_id'])
            && ($this->row_data['planet_galaxy'] == $this->current_planet['planet_galaxy'])
            && ($this->current_planet['planet_type']) == PlanetTypesEnumerator::MOON) {
            return $this->isInRange(Formulas::phalanxRange($this->current_planet['building_phalanx']));
        }
    }

    /**
     * isInRange
     *
     * @param int $range Range
     *
     * @return boolean
     */
    private function isInRange($range)
    {
        $minsystem = $this->current_planet['planet_system'] - $range;
        $maxsystem = $this->current_planet['planet_system'] + $range;

        $minsystem = ($minsystem < 1) ? 1 : $minsystem;
        $maxsystem = ($maxsystem > MAX_SYSTEM_IN_GALAXY) ? MAX_SYSTEM_IN_GALAXY : $maxsystem;

        return (($this->system <= $maxsystem) && ($this->system >= $minsystem));
    }

    /**
     * Get the css class for each user status
     *
     * @return void
     */
    private function getUserStatusClass(string $status)
    {
        return [
            'a' => 'status_abbr_admin',
            's' => 'status_abbr_strong',
            'w' => 'status_abbr_noob',
            'o' => 'status_abbr_outlaw',
            'v' => 'status_abbr_vacation',
            'b' => 'status_abbr_banned',
            'i' => 'status_abbr_inactive',
            'I' => 'status_abbr_longinactive',
            'hp' => 'status_abbr_honorableTarget',
        ][$status];
    }
}
