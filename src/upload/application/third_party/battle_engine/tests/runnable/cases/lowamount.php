<?php

require ("../../RunnableTest.php");
class LowAmount extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(202, 1);
        $ships[] = $this->getShipType(203, 1);
        $ships[] = $this->getShipType(204, 1);
        $ships[] = $this->getShipType(205, 1);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet));
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(401, 1);
        $ships[] = $this->getShipType(402, 1);
        $ships[] = $this->getShipType(403, 1);
        $ships[] = $this->getShipType(404, 1);
        $ships[] = $this->getShipType(405, 1);
        $ships[] = $this->getShipType(409, 1);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet));
        return new PlayerGroup(array($player));
    }
}
new LowAmount();