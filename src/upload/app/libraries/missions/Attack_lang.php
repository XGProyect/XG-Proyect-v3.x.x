<?php
/**
 * Attack Language Library
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace App\libraries\missions;

use Lang;

/**
 * Attack_lang Class
 */
class Attack_lang implements Lang
{
    /**
     * @var mixed
     */
    private $lang;
    /**
     * @var mixed
     */
    private $objects;

    /**
     * Constructor
     *
     * @param array $lang Language
     *
     * @return void
     */
    public function __construct($lang, $objects)
    {
        $this->lang = $lang;
        $this->objects = $objects;
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
        return $this->lang->language[$this->objects[$id]];
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
        return sprintf($this->lang->line('cr_fleet_attack_1'), $amount, $damage);
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
        return sprintf($this->lang->line('cr_fleet_attack_2'), $damage);
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
        return sprintf($this->lang->line('cr_fleet_defs_1'), $amount, $damage);
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
        return sprintf($this->lang->line('cr_fleet_defs_2'), $damage);
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
        return sprintf($this->lang->line('cr_technologies'), ($weaponsTech * 10), ($shieldsTech * 10), ($armourTech * 10));
    }

    /**
     * getAttackerHasWon
     *
     * @return string
     */
    public function getAttackerHasWon()
    {
        return $this->lang->line('cr_attacker_won');
    }

    /**
     * getDefendersHasWon
     *
     * @return string
     */
    public function getDefendersHasWon()
    {
        return $this->lang->line('cr_defender_won');
    }

    /**
     * getDraw
     *
     * @return string
     */
    public function getDraw()
    {
        return $this->lang->line('cr_both_won');
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
        return sprintf($this->lang->line('cr_stealed_ressources'), $metal, $crystal, $deuterium);
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
        return sprintf($this->lang->line('cr_attacker_lostunits'), $units);
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
        return sprintf($this->lang->line('cr_defender_lostunits'), $units);
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
        return sprintf($this->lang->line('cr_debris_units'), $metal, $crystal);
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
        return sprintf($this->lang->line('cr_moonproba'), $prob);
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
        return sprintf($this->lang->line('cr_moonbuilt'), $name, $galaxy, $system, $planet);
    }
}
