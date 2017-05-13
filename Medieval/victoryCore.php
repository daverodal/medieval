<?php
namespace Wargame\Medieval;
use Wargame\Battle;
use Wargame\Cnst;
use Wargame\MapData;
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


            if ($gameRules->mode === MOVING_MODE && $unit->forceId !== $battle->force->attackingForceId && $unit->class === 'hq' && $unit->hexagon->parent === "deadpile") {

                $unit->hexagon->parent = "deployBox";
                $unit->commandRadius = ceil($unit->commandRadius/2);
                $unit->origStrength = ceil($unit->origStrength/2);
                $unit->orgStatus = MedievalUnit::BATTLE_READY;
                $unit->steps = $unit->origSteps;
                $unit->status = STATUS_CAN_REINFORCE;
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

    public function postUnsetAttacker($args){
        $this->calcFromAttackers();
        list($unit) = $args;
        $id = $unit->id;
    }
    public function postUnsetDefender($args){
        $this->calcFromAttackers();

        list($unit) = $args;
    }
    public function postSetAttacker($args){
        $this->calcFromAttackers();

        list($unit) = $args;
    }
    public function postSetDefender($args){
        $this->calcFromAttackers();

    }

    public function calcFromAttackers(){
        $mapData = MapData::getInstance();

        $battle = Battle::getBattle();
        /* @var CombatRules $cR */
        $cR = $battle->combatRules;
        /* @var Force $force */
        $force = $battle->force;
        $gameRules = $battle->gameRules;
        if (!($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE)) {
            return;
        }


        $force->clearRequiredCombats();
        $defenderForceId = $force->defendingForceId;
        $force->requiredAttacks = [];
        foreach($force->units as $unit){
            if($unit->forceId === $force->defendingForceId){
                if($unit->isOnMap()) {
                    $mapHex = $mapData->getHex($unit->hexagon->name);
                    $unitId = $unit->id;
                    if ($mapHex->isZoc($force->attackingForceId)) {
                        $combatId = $cR->defenders->$unitId ?? null ;
                        $requiredVal = true;
                        if($combatId !== null){
                            if(isset($cR->combats->$combatId)) {
                                $attackers = $cR->combats->$combatId->attackers;
                                if ($attackers) {
                                    if (count((array)$attackers) > 0) {
                                        $requiredVal = false;
                                    }
                                }
                            }
                        }



                        $attackers = $mapHex->getZocUnits($force->attackingForceId);
                        $attackers = $this->filterFlankedAttackers($attackers);
                        if(count((array)$attackers) === 0){
                            $requiredVal = false;
                        }



                        $allInf = true;
                        foreach ($attackers as $attacker) {
                            if ($force->units[$attacker]->class !== 'inf') {
                                $allInf = false;
                            }
                        }
                        if ($unit->class === 'cavalry') {
                            if ($allInf) {
                                $requiredVal = false;
                            }
                        }

                        $force->requiredDefenses->$unitId = $requiredVal;


                        $attackers = array_map(  function($val) use ($cR, $unit, $force) {
                            if($unit->class === 'cavalry' && $force->units[$val]->class === 'inf'){
                                return false;
                            }
                            if(isset($cR->attackers->$val)){
                               return false;
                            }
                            return true;
                            },(array)$attackers);
                        $force->requiredAttacks = array_merge($force->requiredAttacks,$attackers);
                    }
                }
            }
        }
        /*
         * snip here remove flanked requiredAttacks
         */

        foreach($cR->attackers as $attackId => $combatId){
            $unit = $battle->force->units[$attackId];
            $mapHex = $mapData->getHex($force->getUnitHexagon($attackId)->name);
            $zocHexes = $mapHex->getZocNeighbors($unit);
            foreach($zocHexes as $zocHex){
                $zocMapHex = $mapData->getHex($zocHex);
                if($zocMapHex->isOccupied($defenderForceId)){
                    $units = $zocMapHex->forces[$defenderForceId];
                    foreach($units as $unitId=>$unitVal){
                        $requiredVal = true;
                        $combatId = isset($cR->defenders->$unitId) ? $cR->defenders->$unitId : null ;
                        if($combatId !== null){
                            $attackers = $cR->combats->$combatId->attackers;
                            if($attackers){
                                if(count((array)$attackers) > 0){
                                    $requiredVal = false;
                                }
                            }

                        }

                        $force->requiredDefenses->$unitId = $requiredVal;
                    }
                }
            }
//            var_dump($force->requiredDefenses);
//            $neighbors = $mapHex->getZocUnits($defenderForceId);
//            foreach($neighbors as $neighbor){
//                /* @var MapHex $hex */
//                $hex = $mapData->getHex($force->getUnit($neighbor)->hexagon->name);
//                if($hex->isOccupied($defenderForceId)){
//                    $units = $hex->forces[$defenderForceId];
//                    foreach($units as $unitId=>$unitVal){
//                        $requiredVal = true;
//                        $combatId = isset($cR->defenders->$unitId) ? $cR->defenders->$unitId : null ;
//                        if($combatId !== null){
//                            $attackers = $cR->combats->$combatId->attackers;
//                            if($attackers){
//                                if(count((array)$attackers) > 0){
//                                    $requiredVal = false;
//                                }
//                            }
//
//                        }
//
//                        $force->requiredDefenses->$unitId = $requiredVal;
//                    }
//                }
//            }
        }
    }

    public function filterFlankedAttackers($attackers){
        $retAttackers = new stdClass();
        foreach($attackers as $aId => $requireAttack){
            if(!$this->isFlankedAttack($aId)){
                $retAttackers->$aId = $requireAttack;
            }
        }
        return $retAttackers;
    }

    public function isFlankedAttacker($args){
        list($aId) = $args;
        return $this->isFlankedAttack($aId);
    }

    protected function isFlankedAttack($aId){
        $mapData = MapData::getInstance();
        $battle = Battle::getBattle();
        /* @var CombatRules $cR */
        $cR = $battle->combatRules;
        /* @var Force $force */
        $force = $battle->force;

        $isFlanked = false;
        $mapHex = $mapData->getHex($force->units[$aId]->hexagon->name);
        $defZocs = $mapHex->getZocUnits($force->defendingForceId);
        foreach($defZocs as $defZoc){
            $mapHex = $mapData->getHex($force->units[$defZoc]->hexagon->name);
            $attZocs = $mapHex->getZocUnits($force->attackingForceId);
            if(!isset($attZocs->$aId)){
                $isFlanked = true;
            }
        }
        if($isFlanked
        ){
            return true;
        }

        return false;
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
            $this->calcFromAttackers();
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

        $battle->mapData->specialHexesVictory->{$hex->name} = "D";


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

    public function noEffectUnit($args)
    {
        list($unit) = $args;
        $hex = $unit->hexagon;
        $battle = Battle::getBattle();
        $pData = $battle->getPlayerData(false);

        $playerOne = $pData['forceName'][1];
        $playerTwo = $pData['forceName'][2];

        if ($hex->name) {
            $battle->mapData->specialHexesVictory->{$hex->name} = "NE";
        }
    }

}