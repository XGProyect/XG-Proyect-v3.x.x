<?php

require ("../../RunnableTest.php");
class Fazi extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(214, 1);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet));
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(202, 1000);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet));
        return new PlayerGroup(array($player));
    }
}
new Fazi();
