<?php
/**
 * Attack Language Library
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
namespace application\libraries\missions;

use Lang;

/**
 * Attack_lang Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Attack_lang implements Lang
{

    private $lang;

    /**
     * Constructor
     *
     * @param array $lang Language
     *
     * @return void
     */
    public function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * getShipName
     *
     * @param int $id ID
     *
     * @return string
     */
    public function getShipName($id)
    {
        return $this->lang['tech'][$id];
    }

    /**
     * getAttackersAttackingDescr
     *
     * @param int $amount Amount
     * @param int $damage Damage
     *
     * @return string
     */
    public function getAttackersAttackingDescr($amount, $damage)
    {
        return $this->lang['fleet_attack_1'] . ' ' . $damage . " " . $this->lang['damage'] . " " . $this->lang['with'] . " $amount " . $this->lang['shots'] . ". ";
    }

    /**
     * getDefendersDefendingDescr
     *
     * @param int $damage Damage
     *
     * @return string
     */
    public function getDefendersDefendingDescr($damage)
    {
        return $this->lang['fleet_attack_2'] . ' ' . $damage . ' ' . $this->lang['damage'] . '.';
    }

    /**
     * getDefendersAttackingDescr
     *
     * @param int $amount Amount
     * @param int $damage Damage
     *
     * @return string
     */
    public function getDefendersAttackingDescr($amount, $damage)
    {
        return $this->lang['fleet_defs_1'] . ' ' . $damage . " " . $this->lang['damage'] . " " . $this->lang['with'] . " $amount " . $this->lang['shots'] . ". ";
    }

    /**
     * getAttackersDefendingDescr
     *
     * @param int $damage Damage
     *
     * @return string
     */
    public function getAttackersDefendingDescr($damage)
    {
        return $this->lang['fleet_defs_2'] . ' ' . $damage . ' ' . $this->lang['damage'] . '.';
    }

    /**
     * getTechs
     *
     * @param type $weaponsTech
     * @param type $shieldsTech
     * @param type $armourTech
     *
     * @return string
     */
    public function getTechs($weaponsTech, $shieldsTech, $armourTech)
    {
        return $this->lang['cr_weapons'] . ': ' . ($weaponsTech * 10) . '% - ' . $this->lang['cr_shields'] . ': ' . ($shieldsTech * 10) . '% - ' . $this->lang['cr_armor'] . ': ' . ($armourTech * 10) . '%';
    }

    /**
     * getAttackerHasWon
     *
     * @return string
     */
    public function getAttackerHasWon()
    {
        return $this->lang['sys_attacker_won'];
    }

    /**
     * getDefendersHasWon
     *
     * @return string
     */
    public function getDefendersHasWon()
    {
        return $this->lang['sys_defender_won'];
    }

    /**
     * getDraw
     *
     * @return string
     */
    public function getDraw()
    {
        return $this->lang['sys_both_won'];
    }

    /**
     * getStoleDescr
     *
     * @param int $metal     Metal
     * @param int $crystal   Crystal
     * @param int $deuterium Deuterium
     *
     * @return string
     */
    public function getStoleDescr($metal, $crystal, $deuterium)
    {
        return $this->lang['sys_stealed_ressources'] . " $metal " . $this->lang['Metal'] .
        ", $crystal " . $this->lang['Crystal'] . " " . $this->lang['sys_and'] .
        " $deuterium " . $this->lang['Deuterium'];
    }

    /**
     * getAttackersLostUnits
     *
     * @param int $units Units
     *
     * @return string
     */
    public function getAttackersLostUnits($units)
    {
        return $this->lang['sys_attacker_lostunits'] . " $units " . $this->lang['sys_units'] . '.';
    }

    /**
     * getDefendersLostUnits
     *
     * @param int $units Units
     *
     * @return string
     */
    public function getDefendersLostUnits($units)
    {
        return $this->lang['sys_defender_lostunits'] . " $units " . $this->lang['sys_units'] . '.';
    }

    /**
     * getFloatingDebris
     *
     * @param int $metal   Metal
     * @param int $crystal Crystal
     *
     * @return string
     */
    public function getFloatingDebris($metal, $crystal)
    {
        return $this->lang['debree_field_1'] . ":  $metal " . $this->lang['Metal'] .
        " $crystal " . $this->lang['Crystal'] . ' ' . $this->lang['debree_field_2'] . '.';
    }

    /**
     * getMoonProb
     *
     * @param int $prob Probability
     *
     * @return string
     */
    public function getMoonProb($prob)
    {
        return $this->lang['sys_moonproba'] . " $prob%";
    }

    /**
     * getNewMoon
     *
     * @param int $name
     * @param int $galaxy
     * @param int $system
     * @param int $planet
     *
     * @return string
     */
    public function getNewMoon($name, $galaxy, $system, $planet)
    {
        return sprintf($this->lang['sys_moonbuilt'], $name, $galaxy, $system, $planet);
    }
}

/* end of Attack_lang.php */
