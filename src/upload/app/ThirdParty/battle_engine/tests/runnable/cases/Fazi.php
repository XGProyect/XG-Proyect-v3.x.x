<?php

require ("../../RunnableTest.php");
class Fazi extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(202, 1);
        $ships[] = $this->getShipType(203, 1);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet));
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(202, 1);
        $ships[] = $this->getShipType(203, 6546);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet));
        return new PlayerGroup(array($player));
    }
}
new Fazi();
