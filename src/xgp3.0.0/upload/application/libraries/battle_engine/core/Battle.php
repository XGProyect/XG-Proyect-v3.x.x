<?php

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version 21-03-2015
 * @link https://github.com/jstar88/opbe
 */
class Battle
{
    private $attackers;
    private $defenders;
    private $report;
    private $battleStarted;

    /**
     * Battle::__construct()
     * 
     * @param PlayerGroup $attackers
     * @param PlayerGroup $defenders
     * @return Battle
     */
    public function __construct(PlayerGroup $attackers, PlayerGroup $defenders)
    {
        $this->attackers = $attackers;
        $this->defenders = $defenders;
        $this->battleStarted = false;
        $this->report = new BattleReport();
    }
    /**
     * Battle::startBattle()
     * 
     * @return null
     */
    public function startBattle($debug = false)
    {
        if (!$debug)
            ob_start();
        $this->battleStarted = true;
        //only for initial fleets presentation
        log_var('attackers', $this->attackers);
        log_var('defenders', $this->defenders);
        $round = new Round($this->attackers, $this->defenders, 0);
        $this->report->addRound($round);
        for ($i = 1; $i <= ROUNDS; $i++)
        {
            $att_lose = $this->attackers->isEmpty();
            $deff_lose = $this->defenders->isEmpty();
            //if one of they are empty then battle is ended, so update the status
            if ($att_lose || $deff_lose)
            {
                $this->checkWhoWon($att_lose, $deff_lose);
                $this->report->setBattleResult($this->attackers->battleResult, $this->defenders->battleResult);
                if (!$debug)
                    ob_get_clean();
                return;
            }
            //initialize the round
            $round = new Round($this->attackers, $this->defenders, $i);
            $round->startRound();
            //add the round to the combatReport
            $this->report->addRound($round);
            //if($i==2) die('Round: '.$this->report->getRound(0)->getNumber()); // ERRORE
            //update the attackers and defenders after round
            $this->attackers = $round->getAfterBattleAttackers();
            $this->defenders = $round->getAfterBattleDefenders();
        }
        //check status after all rounds
        $this->checkWhoWon($this->attackers->isEmpty(), $this->defenders->isEmpty());
        if (!$debug)
            ob_get_clean();
        return true;
    }
    /**
     * Battle::checkWhoWon()
     * Assign to groups the status win,lose or draw
     * @param boolean $att_lose
     * @param boolean $deff_lose
     * @return null
     */
    private function checkWhoWon($att_lose, $deff_lose)
    {
        if ($att_lose && !$deff_lose)
        {
            $this->attackers->battleResult = BATTLE_LOSE;
            $this->defenders->battleResult = BATTLE_WIN;
        }
        elseif (!$att_lose && $deff_lose)
        {
            $this->attackers->battleResult = BATTLE_WIN;
            $this->defenders->battleResult = BATTLE_LOSE;
        }
        else
        {
            $this->attackers->battleResult = BATTLE_DRAW;
            $this->defenders->battleResult = BATTLE_DRAW;
        }
    }

    /**
     * Battle::__toString()
     * 
     * @return
     */
    public function __toString()
    {
        return $this->report->__toString();
    }
    /**
     * Battle::getReport()
     * Start the battle if not and return the report.
     * @return BattleReport
     */
    public function getReport()
    {
        if (!$this->battleStarted)
        {
            $this->startBattle();
        }
        return $this->report;
    }
}
