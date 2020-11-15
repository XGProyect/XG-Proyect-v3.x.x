<?php

require ("../../RunnableTest.php");
class Fazi extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(205, 20);
        $ships[] = $this->getShipType(211, 30);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet),7,6,6);
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(401, 1);
        $ships[] = $this->getShipType(402, 2);
        $ships[] = $this->getShipType(403, 1);
        $ships[] = $this->getShipType(407, 1);
        $ships[] = $this->getShipType(408, 1);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet),5,5,5);
        return new PlayerGroup(array($player));
    }
}
new Fazi(false);