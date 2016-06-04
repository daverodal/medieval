<?php
namespace Wargame\Medieval\Grunwald1410;
use Wargame\Battle;
/**
 *
 * Copyright 2012-2015 David Rodal
 * User: David Markarian Rodal
 * Date: 3/8/15
 * Time: 5:48 PM
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
//include_once "victoryCore.php";

class VictoryCore extends \Wargame\Medieval\victoryCore
{

    protected $outgoingVP;

    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {

        } else {

        }
        $this->outgoingVP = [0,0,0,0,0];
    }

    public function setSupplyLen($supplyLen)
    {
        $this->supplyLen = $supplyLen[0];
    }

    public function save()
    {
        $ret = parent::save();
        return $ret;
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;


        if(in_array($mapHexName,$battle->specialHexA)){
            $vp = 1;

            $prevForceId = $battle->mapData->specialHexes->$mapHexName;
            if ($forceId == POLISH_FORCE) {
                $this->victoryPoints[POLISH_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='rebel'>+$vp Rebel vp</span>";
                $this->victoryPoints[TEUTONIC_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='rebel'> -$vp Teutonic vp</span>";
            }
            if ($forceId == TEUTONIC_FORCE) {
                $this->victoryPoints[TEUTONIC_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='loyalist'>+$vp Teutonic vp</span>";
                $this->victoryPoints[POLISH_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='loyalist'> -$vp Polish vp</span>";
            }
        }

    }

    public function reduceUnit($args)
    {
        $unit = $args[0];

        $vp = $unit->damage;

        if ($unit->forceId == 1) {
            $victorId = 2;
            $this->victoryPoints[$victorId] += $vp;
            $hex = $unit->hexagon;
            $battle = Battle::getBattle();
            if($hex->name) {
                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='loyalistVictoryPoints'>+$vp vp</span>";
            }
        } else {
            $victorId = 1;
            $hex  = $unit->hexagon;
            $vp += $this->outgoingVP[$victorId];
            $this->outgoingVP[$victorId] = $vp;
            $battle = Battle::getBattle();
            if($hex->name) {

                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='rebelVictoryPoints'>+$vp vp</span>";
            }
            $this->victoryPoints[$victorId] += $vp;
        }
    }

    public function incrementTurn()
    {
        $battle = Battle::getBattle();

        $theUnits = $battle->force->units;
        foreach ($theUnits as $id => $unit) {

            if ($unit->status == STATUS_CAN_REINFORCE && $unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox") {
//                $theUnits[$id]->status = STATUS_ELIMINATED;
                $theUnits[$id]->hexagon->parent = "deployBox";
            }
        }
    }

    public function gameEnded()
    {
        $battle = Battle::getBattle();
        if ($this->victoryPoints[TEUTONIC_FORCE] > $this->victoryPoints[POLISH_FORCE]) {
            $battle->gameRules->flashMessages[] = "Teutonic Player Wins";
            $this->winner = TEUTONIC_FORCE;
        }
        if ($this->victoryPoints[POLISH_FORCE] > $this->victoryPoints[TEUTONIC_FORCE]) {
            $battle->gameRules->flashMessages[] = "Polish Player Wins";
            $this->winner = POLISH_FORCE;
        }
        if ($this->victoryPoints[TEUTONIC_FORCE] == $this->victoryPoints[POLISH_FORCE]) {
            $battle->gameRules->flashMessages[] = "Tie Game";
        }
        $this->gameOver = true;
        return true;
    }

    public function phaseChange()
    {

        /* @var $battle MartianCivilWar */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $forceId = $gameRules->attackingForceId;
        $turn = $gameRules->turn;
        $force = $battle->force;

        if ($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE) {
            $gameRules->flashMessages[] = "@hide deployWrapper";
        } else {
            $gameRules->flashMessages[] = "@hide crt";

            /* Restore all un-supplied strengths */
            $force = $battle->force;
            $this->restoreAllCombatEffects($force);
        }
        if ($gameRules->phase == BLUE_REPLACEMENT_PHASE || $gameRules->phase == RED_REPLACEMENT_PHASE) {
            $gameRules->flashMessages[] = "@show deadpile";
            $forceId = $gameRules->attackingForceId;
        }
        if ($gameRules->phase == BLUE_MOVE_PHASE || $gameRules->phase == RED_MOVE_PHASE) {
            $gameRules->flashMessages[] = "@hide deadpile";
            if (!empty($battle->force->reinforceTurns->$turn->$forceId)) {
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }
        }
    }

    public function preRecoverUnits($args)
    {

    }



    public function playerTurnChange($arg)
    {
        $battle = Battle::getBattle();
        foreach($battle->force->units as $unit){
            $unit->rallyCheck();
        }
    }
}