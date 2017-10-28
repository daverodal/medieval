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
        for ($id = 0; $id < count($this->units); $id++) {
            $unit = $this->units[$id];
            $victory->preRecoverUnit($this->units[$id]);

            switch ($this->units[$id]->status) {


                case STATUS_ELIMINATED:
                    if ($mode === REPLACING_MODE) {
                        if ($this->units[$id]->forceId == $this->attackingForceId) {
                            $this->units[$id]->status = STATUS_CAN_REPLACE;
                        }
                    }
                    break;
                case STATUS_CAN_DEPLOY:
                    if ($mode == DEPLOY_MODE) {
                        continue;
                    }
                    if ($this->units[$id]->isDeploy()) {
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
                        if ($this->units[$id]->status === STATUS_CAN_COMBINE) {
                            $status = STATUS_READY;
                        }
                    }
                    /*
                     * Active Locking Zoc rules
                     */
//                    if($this->unitIsZOC($id)){
//                        $status = STATUS_STOPPED;
//                    }

                    if ($phase == BLUE_MECH_PHASE && $this->units[$id]->forceId == BLUE_FORCE && $this->units[$id]->class != "mech") {
                        $status = STATUS_STOPPED;
                    }
                    if ($phase == RED_MECH_PHASE && $this->units[$id]->forceId == RED_FORCE && $this->units[$id]->class != "mech") {
                        $status = STATUS_STOPPED;
                    }
                    if ($phase == BLUE_REPLACEMENT_PHASE || $phase == RED_REPLACEMENT_PHASE || $phase == TEAL_REPLACEMENT_PHASE || $phase == PURPLE_REPLACEMENT_PHASE) {
                        $status = STATUS_STOPPED;
                        /* TODO Hack Hack Hack better answer is not isReduced, but canReduce */
                        if ($this->units[$id]->forceId == $this->attackingForceId &&
                            $this->units[$id]->isReduced && $this->units[$id]->class !== "gorilla"
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
                            if ($this->units[$id]->forceId == $this->attackingForceId && ($isZoc || $isAdjacent)) {
                                $status = STATUS_READY;
                            }
                            if($this->units[$id]->usedFireCombat()){
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                                $this->units[$id]->clearFireCombat();
                            }
                            if($victory->isFlankedAttacker($id)){
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                            }
                        }
                        if ($mode == FIRE_COMBAT_SETUP_MODE) {
                            $status = STATUS_UNAVAIL_THIS_PHASE;
                            /* unitIsZoc has Side Effect */
                            $isZoc = $this->unitIsZoc($id);

                            $isAdjacent = $this->unitIsAdjacent($id);
                            if ($this->units[$id]->forceId == $this->attackingForceId && ($isZoc || $isAdjacent || $this->unitIsInRange($id))) {
                                $status = STATUS_READY;
                            }

                            if($unit->isBow()){
                                /* make sure then can fire in fire phase, maybe not good to do here */
                                $this->units[$id]->clearFireCombat();
                            }else{
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                            }

                            if($victory->isFlankedAttacker($id)){
                                $status = STATUS_UNAVAIL_THIS_PHASE;
                            }
                        }
                        if ($mode == COMBAT_RESOLUTION_MODE || $mode == FIRE_COMBAT_RESOLUTION_MODE) {
                            $status = STATUS_UNAVAIL_THIS_PHASE;
                            if ($this->units[$id]->status == STATUS_ATTACKING ||
                                $this->units[$id]->status == STATUS_DEFENDING
                            ) {
                                $status = $this->units[$id]->status;
                            }

                        }
                    }


                    if ($mode == MOVING_MODE && $moveRules->stickyZoc) {
                        if ($this->units[$id]->forceId == $this->attackingForceId &&
                            $this->unitIsZOC($id)
                        ) {
                            $status = STATUS_STOPPED;
                        }
                    }

                    $this->units[$id]->status = $status;
                    $this->units[$id]->moveAmountUsed = 0;
                    break;

                default:
                    break;
            }
            if ($phase === BLUE_MOVE_PHASE || $phase === RED_MOVE_PHASE || $phase == TEAL_MOVE_PHASE || $phase == PURPLE_MOVE_PHASE) {
                $this->units[$id]->moveAmountUnused = $this->units[$id]->getMaxMove();
            }
            $this->units[$id]->combatIndex = 0;
            $this->units[$id]->combatNumber = 0;
            $this->units[$id]->combatResults = NE;
            $victory->postRecoverUnit($this->units[$id]);

        }
        $victory->postRecoverUnits();

    }

    function exchangingAreAdvancing()
    {
        $areAdvancing = false;
        $b = Battle::getBattle();
        /*
         * Todo should not assign to status, should set status
         */
        for ($id = 0; $id < count($this->units); $id++) {
            if ($this->units[$id]->status == STATUS_CAN_EXCHANGE) {
                if(count($this->retreatHexagonList)){
                        $this->units[$id]->status = STATUS_CAN_ADVANCE;
                        $areAdvancing = true;
                }else{
                    $this->units[$id]->status = STATUS_ATTACKED;
                }
            }
        }

        return $areAdvancing;
    }

    function attackingAreAdvancing()
    {
        $areAdvancing = false;
        $b = Battle::getBattle();
        /*
         * Todo should not assign to status, should set status
         */
        for ($id = 0; $id < count($this->units); $id++) {
            if ($this->units[$id]->status == STATUS_CAN_ATTACK_LOSE) {
                if (count($this->retreatHexagonList)) {
                        $this->units[$id]->status = $b->combatRules->crt->calcAdvance($this->units[$id]);
                        $areAdvancing = true;
                } else {
                    $this->units[$id]->status = STATUS_ATTACKED;
                }
            }
        }
        return $areAdvancing;
    }

}