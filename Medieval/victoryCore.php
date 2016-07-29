<?php
namespace Wargame\Medieval;
use Wargame\Battle;
use stdClass;
/**
 *
 * Copyright 2012-2015 David Rodal
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
//include "supplyRulesTraits.php";

class victoryCore extends \Wargame\VictoryCore
{
    use Command;
    public $victoryPoints;
    protected $movementCache;
    protected $combatCache;
    protected $supplyLen = false;
    
    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->movementCache = $data->victory->movementCache;
            $this->combatCache = $data->victory->combatCache;
            $this->supplyLen = $data->victory->supplyLen;
            $this->headQuarters = $data->victory->headQuarters;
        } else {
            $this->victoryPoints = array(0, 0, 0);
            $this->movementCache = new stdClass();
            $this->combatCache = new stdClass();
        }
    }

    public function save()
    {
        $ret = parent::save();
        $ret->victoryPoints = $this->victoryPoints;
        $ret->movementCache = $this->movementCache;
        $ret->combatCache = $this->combatCache;
        $ret->supplyLen = $this->supplyLen;
        $ret->headQuarters = $this->headQuarters;
        return $ret;
    }

    public function incrementTurn()
    {

    }

    protected function checkVictory($attackingId, $battle){
        if(!$this->gameOver){
        }
        return false;
    }

    public function playerTurnChange($arg){
        $attackingId = $arg[0];
        $battle = Battle::getBattle();

        /* @var GameRules $gameRules */
        $gameRules = $battle->gameRules;
        $gameRules->flashMessages[] = "@hide crt";

        $battle = Battle::getBattle();


        $gameRules = $battle->gameRules;

        $theUnits = $battle->force->units;
        foreach ($theUnits as $id => $unit) {


            if ($unit->forceId !== $battle->force->attackingForceId && $unit->class === 'hq' && $unit->hexagon->parent === "deadpile") {

                $theUnits[$id]->hexagon->parent = "deployBox";
                $theUnits[$id]->commandRadius = ceil($theUnits[$id]->commandRadius/2);
                $theUnits[$id]->origStrength = ceil($theUnits[$id]->origStrength/2);
                $theUnits[$id]->status = STATUS_CAN_REINFORCE;
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }
        }

        foreach($battle->force->units as $unit){
            $unit->rallyCheck();
        }
        
        if($this->checkVictory($attackingId,$battle)){
            return;
        }
    }

    public function phaseChange()
    {

        /* @var $battle MartianCivilWar */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $forceId = $gameRules->attackingForceId;
        $turn = $gameRules->turn;

        if ($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE) {
            $gameRules->gameHasCombatResolutionMode = true;
        } else {
            $gameRules->gameHasCombatResolutionMode = false;
            $gameRules->flashMessages[] = "@hide crt";
        }
    }

    public function preRecoverUnits(){

        $this->initHeadquarters();

    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $battle = Battle::getBattle();

        $vp = $unit->damage;

        $pData = $battle->getPlayerData(false);

        if ($unit->forceId == 1) {
            $victorId = 2;
            $class = preg_replace("/ /", "-",$pData['forceName'][$victorId]);

            $this->victoryPoints[$victorId] += $vp;
            $hex = $unit->hexagon;
            if($hex->name) {
                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='${class}VictoryPoints'>+$vp vp</span>";
            }
        } else {
            $victorId = 1;
            $class = preg_replace("/ /", "-",$pData['forceName'][$victorId]);
            $hex  = $unit->hexagon;
            $vp += $this->outgoingVP[$victorId];
            $this->outgoingVP[$victorId] = $vp;
            if($hex->name) {

                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='${class}VictoryPoints'>+$vp vp</span>";
            }
            $this->victoryPoints[$victorId] += $vp;
        }
    }

    public function disorderUnit($args){
        list($unit) = $args;
        $battle = Battle::getBattle();
        $hex = $unit->hexagon;

        $battle->mapData->specialHexesVictory->{$hex->name} = "DISORDERED!";


    }
    public function postRecoverUnit($args)
    {
        list($unit) = $args;
        $b = Battle::getBattle();
        
        $this->checkCommand($unit);

        if($b->gameRules->phase === BLUE_FIRE_COMBAT_PHASE || $b->gameRules->phase === RED_FIRE_COMBAT_PHASE){
            if(empty($unit->bow)){
                $unit->status = STATUS_UNAVAIL_THIS_PHASE;
            }
        }
    }
}