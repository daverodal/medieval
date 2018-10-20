<?php
namespace Wargame\Medieval;
use stdClass;
use Wargame\Force;
// force.js

// Copyright (c) 20092011 Mark Butler
/*
Copyright 2012-2015 David Rodal

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version

This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
   */
use Wargame\Battle;
use Wargame\RetreatStep;
class MedievalForce extends Force
{
    /* @var  unit $units */
    public $victor;
    public $ZOCrule;

    public $landForce = true;
    public $retreatHexagonList;
    public $exchangeAmount;
    public $requiredAttacks;
    public $requiredDefenses;
    public $combatRequired;
    public $exchangesKill = false;
    public $anyCombatsPossible = false;

    function __construct($data = null)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ($k == "units") {
                    $this->units = array();
                    foreach ($v as $unit) {
//                        $this->units[] = UnitFactory::build($unit);
                    }
                    continue;
                }
                if ($k == "retreatHexagonList") {
                    $this->retreatHexagonList = array();
                    foreach ($v as $retreatStep) {
                        $this->retreatHexagonList[] = new RetreatStep($retreatStep);
                    }
                    continue;
                }
                $this->$k = $v;
            }
            $this->units = [];
        } else {

            $this->reinforceTurns = new stdClass();
            $this->units = array();
            $this->victor = RED_FORCE;
            $this->ZOCrule = true;

            $this->retreatHexagonList = array();
            $this->requiredAttacks = new stdClass();
            $this->requiredDefenses = new stdClass();
            $this->combatRequired = false;
        }
    }


    /*
     * Combat Rule
     */

    function applyCRTResults($defenderId, $attackers, $combatResults, $dieRoll)
    {
        /* Should not even get here */
        throw new Exception("Bad call to apply CrtResults ");

    }

    function defenderLoseUnit($unit)
    {
        $this->defenderLoseAmount -= 1;
    }

    function exchangeUnit($unit)
    {
        $this->exchangeAmount--;
    }

    /*
     * GAME RULES
     */

    function recoverUnits($phase, $moveRules, $mode)
    {
        $battle = Battle::getBattle();
        $victory = $battle->victory;
        $victory->preRecoverUnits();
        $this->anyCombatsPossible = false;

        for ($id = 0; $id < count($this->units); $id++) {
            $unit = $this->units[$id];
            $victory->preRecoverUnit($unit);

            switch ($unit->status) {


                case STATUS_ELIMINATED:
                    if ($mode === REPLACING_MODE) {
                        if ($unit->forceId == $this->attackingForceId) {
                            $unit->status = STATUS_CAN_REPLACE;
                        }
                    }
                    break;
                case STATUS_CAN_DEPLOY:
                    if ($mode == DEPLOY_MODE) {
                        continue;
                    }
                    if ($unit->isDeploy()) {
                        continue;
                    }

                case STATUS_UNAVAIL_THIS_PHASE:
                case STATUS_STOPPED:
                case STATUS_DEFENDED:
                case STATUS_DEFENDING:
                case STATUS_ATTACKED:
                case STATUS_ATTACKING:
                case STATUS_RETREATED:
                case STATUS_ADVANCED:
                case STATUS_CAN_ADVANCE:
                case STATUS_REPLACED:
                case STATUS_READY:
                case STATUS_REPLACED:
                case STATUS_CAN_UPGRADE:
                case STATUS_NO_RESULT:
                case STATUS_EXCHANGED:
                case STATUS_CAN_ATTACK_LOSE:
                case STATUS_CAN_COMBINE:


                    $status = STATUS_READY;


                    if ($mode === COMBINING_MODE) {
                        $status = STATUS_UNAVAIL_THIS_PHASE;
                        if ($unit->status === STATUS_CAN_COMBINE) {
                            $status = STATUS_READY;
                        }
                    }
                    /*
                     * Active Locking Zoc rules
                     */
//                    if($this->unitIsZOC($id)){
//                        $status = STATUS_STOPPED;
//                    }

                    if ($phase == BLUE_MECH_PHASE && $unit->forceId == BLUE_FORCE && $unit->class != "mech") {
                        $status = STATUS_STOPPED;
                    }
                    if ($phase == RED_MECH_PHASE && $unit->forceId == RED_FORCE && $unit->class != "mech") {
                        $status = STATUS_STOPPED;
                    }
                    if ($phase == BLUE_REPLACEMENT_PHASE || $phase == RED_REPLACEMENT_PHASE || $phase == TEAL_REPLACEMENT_PHASE || $phase == PURPLE_REPLACEMENT_PHASE) {
                        $status = STATUS_STOPPED;
                        /* TODO Hack Hack Hack better answer is not isReduced, but canReduce */
                        if ($unit->forceId == $this->attackingForceId &&
                            $unit->isReduced && $unit->class !== "gorilla"
                        ) {
                            $status = STATUS_CAN_UPGRADE;
                        }
                    }

                    if ($phase == RED_FIRE_COMBAT_PHASE_TWO || $phase == BLUE_FIRE_COMBAT_PHASE_TWO || $phase == RED_FIRE_COMBAT_PHASE || $phase == BLUE_FIRE_COMBAT_PHASE || $phase == BLUE_COMBAT_PHASE || $phase == RED_COMBAT_PHASE || $phase == TEAL_COMBAT_PHASE || $phase == PURPLE_COMBAT_PHASE) {
                        if ($mode == COMBAT_SETUP_MODE ) {
                            $status = STATUS_UNAVAIL_THIS_PHASE;
                            /* unitIsZoc has Side Effect */
                            $isZoc = $this->unitIsZoc($id);

                            $isAdjacent = $this->unitIsAdjacent($id);
                            if ($unit->forceId == $this->attackingForceId && ($isZoc || $isAdjacent)) {
                                $status = STATUS_READY;
                                $this->anyCombatsPossible = true;
                            }
                            if($unit->usedFireCombat()){
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                                $unit->clearFireCombat();
                            }
//                            if($victory->isFlankedAttacker($id)){
//                                $status = STATUS_UNAVAIL_THIS_PHASE;
//                                $this->anyCombatsPossible = false;
//                            }
                        }
                        if ($mode == FIRE_COMBAT_SETUP_MODE) {
                            $status = STATUS_UNAVAIL_THIS_PHASE;
                            /* unitIsZoc has Side Effect */
                            $isZoc = $this->unitIsZoc($id);

                            $isAdjacent = $this->unitIsAdjacent($id);
                            if ($unit->forceId == $this->attackingForceId && $unit->isBow() && ($isZoc || $isAdjacent || $this->unitIsInRange($id))) {
                                $status = STATUS_READY;
                                $this->anyCombatsPossible = true;
                            }

                            if($unit->isBow()){
                                /* make sure then can fire in fire phase, maybe not good to do here */
                                $unit->clearFireCombat();
                            }else{
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                            }

                            if($victory->isFlankedAttacker($id)){
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                            }
                        }
                        if ($mode == COMBAT_RESOLUTION_MODE || $mode == FIRE_COMBAT_RESOLUTION_MODE) {
                            $status = STATUS_UNAVAIL_THIS_PHASE;
                            if ($unit->status == STATUS_ATTACKING ||
                                $unit->status == STATUS_DEFENDING
                            ) {
                                $status = $unit->status;
                            }

                        }
                    }


                    if ($mode == MOVING_MODE && $moveRules->stickyZoc) {
                        if ($unit->forceId == $this->attackingForceId &&
                            $this->unitIsZOC($id)
                        ) {
                            $status = STATUS_STOPPED;
                        }
                    }

                    $unit->status = $status;
                    $unit->moveAmountUsed = 0;
                    break;

                default:
                    break;
            }
            if ($phase === BLUE_MOVE_PHASE || $phase === RED_MOVE_PHASE || $phase == TEAL_MOVE_PHASE || $phase == PURPLE_MOVE_PHASE) {
                $unit->moveAmountUnused = $unit->getMaxMove();
            }
            $unit->combatIndex = 0;
            $unit->combatNumber = 0;
            $unit->combatResults = NE;
            $victory->postRecoverUnit($unit);

        }
        $victory->postRecoverUnits();

    }

    function exchangingAreAdvancing()
    {
        $areAdvancing = false;
        $b = Battle::getBattle();
        $this->groomRetreatList();
        /*
         * Todo should not assign to status, should set status
         */
        for ($id = 0; $id < count($this->units); $id++) {
            $unit = $this->units[$id];
            if ($unit->status == STATUS_CAN_EXCHANGE) {
                if(count($this->retreatHexagonList)){
                    $unit->status = $b->combatRules->crt->calcAdvance($unit);
                    $areAdvancing = true;
                }else{
                    $unit->status = STATUS_ATTACKED;
                }
            }
        }

        return $areAdvancing;
    }

    function attackingAreAdvancing()
    {
        $areAdvancing = false;
        $b = Battle::getBattle();
        $this->groomRetreatList();
        $this->groomRetreatList();

        /*
         * Todo should not assign to status, should set status
         */
        for ($id = 0; $id < count($this->units); $id++) {
            $unit = $this->units[$id];
            if ($unit->status == STATUS_CAN_ATTACK_LOSE) {
                if (count($this->retreatHexagonList)) {
                    $unit->status = $b->combatRules->crt->calcAdvance($unit);
                    $areAdvancing = true;
            }else{
                    $unit->status = STATUS_ATTACKED;
                }
            }
        }
        return $areAdvancing;
    }

    function unitsAreAdvancing()
    {
        $areAdvancing = false;
        /* @var $b ModernLandBattle */
        $b = Battle::getBattle();
        $this->groomRetreatList();
        $b->combatRules->groomAdvancing();

        for ($id = 0; $id < count($this->units); $id++) {
            $unit = $this->units[$id];
            if ($unit->status == STATUS_CAN_ADVANCE
                || $unit->status == STATUS_ADVANCING
                || $unit->status == STATUS_MUST_ADVANCE
            ) {
                $unit->status = $b->combatRules->crt->calcAdvance($unit);
                $areAdvancing = true;
            }
        }
        return $areAdvancing;
    }
}