<?php

interface Lang
{
    public function getShipName($id);
    public function getAttackersAttackingDescr($amount, $damage);
    public function getDefendersDefendingDescr($damage);
    public function getDefendersAttackingDescr($amount, $damage);
    public function getAttackersDefendingDescr($damage);
    public function getAttackerHasWon();
    public function getDefendersHasWon();
    public function getDraw();
    public function getStoleDescr($metal, $crystal, $deuterium);
    public function getAttackersLostUnits($units);
    public function getDefendersLostUnits($units);
    public function getFloatingDebris($metal, $crystal);
    public function getMoonProb($prob);
    public function getNewMoon();
}

?>