<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct()
    {
        global $LNG;
        $this->lang = $LNG; 
    }
    
    public function getShipName($id)
    {
        return $this->lang['tech'][$id];
    }
    public function getAttackersAttackingDescr($amount, $damage)
    {
        return "The attacking fleet fires a total of " . $amount . ' times with the power of ' . $damage . " upon the defender.<br />";
    }
    public function getDefendersDefendingDescr($damage)
    {
        return "The defender's shields absorb " . $damage . " damage points.<br />";
    }
    public function getDefendersAttackingDescr($amount, $damage)
    {
        return "The defending fleet fires a total of " . $amount . ' times with the power of ' . $damage . " upon the attacker.<br />";
    }
    public function getAttackersDefendingDescr($damage)
    {
        return "The attacker's shields absorb " . $damage . " damage points.<br />";
    }
    public function getAttackerHasWon()
    {
        return "The attacker has won the battle.";
    }
    public function getDefendersHasWon()
    {
        return "The defender has won the battle.";
    }
    public function getDraw()
    {
        return "The battle ended in a draw.";
    }
    public function getStoleDescr($metal, $crystal, $deuterium)
    {
        return "He captured<br> $metal metal, $crystal and $deuterium deuterium.";
    }
    public function getAttackersLostUnits($units)
    {
        return "The attacker lost a total of $units units.";
    }
    public function getDefendersLostUnits($units)
    {
        return "The defender lost a total of $units units.";
    }
    public function getFloatingDebris($metal, $crystal)
    {
        return "At these space coordinates now float $metal metal and $crystal crystal.";
    }
    public function getMoonProb($prob)
    {
        return "The probability that a moon emerge from the rubble is $prob% .";
    }
    public function getNewMoon()
    {
        return "The huge amount of metal and glass are functioning and form a lunar satellite in orbit the planet.";
    }
}

LangManager::getInstance()->setImplementation(new LangImplementation());
?>