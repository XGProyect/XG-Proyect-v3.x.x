<?php
/**
 * GalaxyLib.php
 *
 * @author   XG Proyect Team
 * @license  https://www.xgproyect.org XG Proyect
 * @link     https://www.xgproyect.org
 * @since    3.2.0
 */
namespace application\libraries;

use application\core\enumerators\MissionsEnumerator as Missions;
use application\core\enumerators\PlanetTypesEnumerator;
use application\core\enumerators\ShipsEnumerator as Ships;
use application\core\Template;
use application\core\XGPCore;
use application\helpers\UrlHelper;

/**
 * GalaxyLib class
 */
class GalaxyLib extends XGPCore
{
    const PLANET_TYPE = 1;
    const DEBRIS_TYPE = 2;
    const MOON_TYPE = 3;

    private $langs;
    private $current_user;
    private $currentplanet;
    private $row_data;
    private $galaxy;
    private $system;
    private $planet;
    private $resource;
    private $pricelist;
    private $formula;
    private $noob;
    private $status = [];
    private $template;

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
        $this->currentplanet = $planet;
        $this->galaxy = $galaxy;
        $this->system = $system;
        $this->resource = parent::$objects->getObjects();
        $this->pricelist = parent::$objects->getPrice();
        $this->formula = FunctionsLib::loadLibrary('FormulaLib');
        $this->noob = FunctionsLib::loadLibrary('NoobsProtectionLib');

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

        $this->userStatuses();

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

            // PARSE DATA
            $row['planet'] = $this->template->set($block['planet'], $this->planetBlock());
            $row['moon'] = $moon_block != '' ? $this->template->set($block['moon'], $moon_block) : '';
            $row['username'] = $this->template->set($block['username'], $this->usernameBlock());
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
            if ($this->currentplanet[$this->resource[214]] > 0) {
                $action['destroy'] = $this->destroyLink(self::MOON_TYPE);
            }
        }

        // ONLY IF IS THE CURRENT USER
        if ($this->row_data['user_id'] == $this->current_user['user_id']) {
            $action['deploy'] = $this->deployLink(self::MOON_TYPE);
        }

        // CHECK MOON STATUS AND COMPLETE DATA IF REQUIRED
        if ($this->row_data['destroyed_moon'] == 0 && $this->row_data['id_luna'] != 0) {
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

            return $parse;
        }

        return '';
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

            if ($recyclers_needed < $this->currentplanet['ship_recycler']) {
                $recyclers_sended = $recyclers_needed;
            } elseif ($recyclers_needed >= $this->currentplanet['ship_recycler']) {
                $recyclers_sended = $this->currentplanet['ship_recycler'];
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
        $MyGameLevel = $this->current_user['user_statistic_total_points'];
        $HeGameLevel = $this->row_data['user_statistic_total_points'];

        $status['banned'] = '';
        $status['vacation'] = '';
        $status['inactive'] = '';
        $status['noob_protection'] = '';

        if ($this->row_data['user_banned']) {
            $status['banned'] = UrlHelper::setUrl(
                'game.php?page=banned',
                $this->status['b']
            );
        }

        if ($this->row_data['preference_vacation_mode'] > 0) {
            $status['vacation'] = $this->status['v'];
        }

        if ($this->row_data['user_onlinetime'] < (time() - 60 * 60 * 24 * 7) && $this->row_data['user_onlinetime'] > (time() - 60 * 60 * 24 * 28)) {
            $status['inactive'] = $this->status['i'];
        }

        if ($this->row_data['user_onlinetime'] < (time() - 60 * 60 * 24 * 28)) {
            $status['inactive'] .= $this->status['I'];
        }

        if ($this->noob->isWeak($MyGameLevel, $HeGameLevel)) {
            $status['noob_protection'] = $this->status['w'];
        }

        if ($this->noob->isStrong($MyGameLevel, $HeGameLevel)) {
            $status['noob_protection'] = $this->status['s'];
        }

        // POP UP BLOCK DATA
        $parse = $this->langs->language;
        $parse['actions'] = '';
        $parse['username'] = $this->row_data['user_name'];
        $parse['current_rank'] = $this->row_data['user_statistic_total_rank'];
        $parse['start'] = (floor($this->row_data['user_statistic_total_rank'] / 100) * 100) + 1;

        if (!$this->noob->isRankVisible($this->row_data['user_authlevel'])) {
            $parse['current_rank'] = '-';
            $parse['start'] = 0;
        }

        if ($this->row_data['user_id'] != $this->current_user['user_id']) {
            $parse['actions'] = "<td>";
            $parse['actions'] .= str_replace('"', '', UrlHelper::setUrl(
                'game.php?page=chat&playerId=' . $this->row_data['user_id'],
                $this->langs->line('write_message')
            ));
            $parse['actions'] .= "</td></tr><tr><td>";
            $parse['actions'] .= str_replace('"', '', UrlHelper::setUrl(
                "game.php?page=buddies&mode=2&u=" . $this->row_data['user_id'],
                $this->langs->line('gl_buddy_request')
            ));
            $parse['actions'] .= "</td></tr><tr>";

            // USER STATUS AND NAME
            $parse['status'] = $this->row_data['user_name'];

            $statuses = [];
            foreach ($status as $to_parse) {
                if ($to_parse != '') {
                    $statuses[] = $to_parse;
                }
            }

            if (!empty($statuses)) {
                $parse['status'] .= '<font color="white"> (</font>' . join(' ', $statuses) . '<font color="white">)</font>';
            }
        } else {
            $parse['status'] = $this->row_data['user_name'];

            if ($status['vacation'] != '') {
                $parse['status'] .= '<font color="white"> (</font>' . $status['vacation'] . '<font color="white">)</font>';
            }
        }

        return $parse;
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
    private function actionsBlock()
    {
        $links = '';

        if ($this->row_data['user_id'] != $this->current_user['user_id']) {
            $image = FunctionsLib::setImage(DPATH . 'img/e.gif', $this->langs->line('gl_spy'));
            $attributes = "onclick=\"javascript:doit(6, " . $this->galaxy . ", " . $this->system . ", " .
            $this->planet . ", 1, " . $this->current_user['preference_spy_probes'] . ");\"";
            $links .= UrlHelper::setUrl('', $image, '', $attributes) . '&nbsp;';

            $image = FunctionsLib::setImage(DPATH . 'img/m.gif', $this->langs->line('write_message'));
            $url = 'game.php?page=chat&playerId=' . $this->row_data['user_id'];
            $links .= UrlHelper::setUrl($url, $image) . '&nbsp;';

            $image = FunctionsLib::setImage(DPATH . 'img/b.gif', $this->langs->line('gl_buddy_request'));
            $url = "game.php?page=buddies&mode=2&u=" . $this->row_data['user_id'];
            $links .= UrlHelper::setUrl($url, $image) . '&nbsp;';

            if ($this->isMissileActive()) {
                $image = FunctionsLib::setImage(DPATH . 'img/r.gif', $this->langs->line('gl_missile_attack'));
                $url = 'game.php?page=galaxy&mode=2&galaxy=' . $this->galaxy . '&system=' . $this->system .
                '&planet=' . $this->planet . '&current=' . $this->current_user['user_current_planet'];
                $links .= UrlHelper::setUrl($url, $image) . '&nbsp;';
            }
        }

        return $links;
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
        if (($this->currentplanet['defense_interplanetary_missile'] != 0)
            && ($this->row_data['user_id'] != $this->current_user['user_id'])
            && ($this->row_data['planet_galaxy'] == $this->currentplanet['planet_galaxy'])) {
            return $this->isInRange($this->formula->missileRange($this->current_user['research_impulse_drive']));
        }
    }

    /**
     * isPhalanxActive
     *
     * @return boolean
     */
    private function isPhalanxActive()
    {
        if (($this->currentplanet['building_phalanx'] != 0)
            && ($this->row_data['user_id'] != $this->current_user['user_id'])
            && ($this->row_data['planet_galaxy'] == $this->currentplanet['planet_galaxy'])
            && ($this->currentplanet['planet_type']) == PlanetTypesEnumerator::MOON) {
            return $this->isInRange($this->formula->phalanxRange($this->currentplanet['building_phalanx']));
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
        $minsystem = $this->currentplanet['planet_system'] - $range;
        $maxsystem = $this->currentplanet['planet_system'] + $range;

        $minsystem = ($minsystem < 1) ? 1 : $minsystem;
        $maxsystem = ($maxsystem > MAX_SYSTEM_IN_GALAXY) ? MAX_SYSTEM_IN_GALAXY : $maxsystem;

        return (($this->system <= $maxsystem) && ($this->system >= $minsystem));
    }

    /**
     * Build the user statuses
     *
     * @return void
     */
    private function userStatuses()
    {
        $this->status = [
            'a' => FormatLib::spanElement($this->langs->line('gl_a'), 'status_abbr_admin'),
            's' => FormatLib::spanElement($this->langs->line('gl_s'), 'status_abbr_strong'),
            'n' => FormatLib::spanElement($this->langs->line('gl_w'), 'status_abbr_noob'),
            'o' => FormatLib::spanElement($this->langs->line('gl_o'), 'status_abbr_outlaw'),
            'v' => FormatLib::spanElement($this->langs->line('gl_v'), 'status_abbr_vacation'),
            'b' => FormatLib::spanElement($this->langs->line('gl_b'), 'status_abbr_banned'),
            'i' => FormatLib::spanElement($this->langs->line('gl_i'), 'status_abbr_inactive'),
            'I' => FormatLib::spanElement($this->langs->line('gl_I'), 'status_abbr_longinactive'),
            'hp' => FormatLib::spanElement($this->langs->line('gl_hp'), 'status_abbr_honorableTarget'),
        ];
    }
}

/* end of GalaxyLib.php */
