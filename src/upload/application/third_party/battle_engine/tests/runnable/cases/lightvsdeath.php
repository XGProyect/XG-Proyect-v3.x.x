<?php

require ("../../RunnableTest.php");
class lightvsdeath extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(204, 1111);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet),10,10,10);
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(209, 11);
        $ships[] = $this->getShipType(214, 1);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet),11,11,11);
        return new PlayerGroup(array($player));
    }
}
new lightvsdeath();
