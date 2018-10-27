<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 5/25/16
 * Time: 9:35 AM
 */

namespace Wargame\Medieval;


trait AncientCRTResults
{
    public $oddEven = 0;
    function calcAdvance( $attUnit){
        $b = \Wargame\Battle::getBattle();

        if($attUnit->status === STATUS_MUST_ADVANCE){
            return $attUnit->status;
        }
        $ret = STATUS_CAN_ADVANCE;
        if($attUnit->class === "cavalry"){
            $ret = STATUS_MUST_ADVANCE;
        }
        if($b->combatRules->combatDefenders->hasInf && $attUnit->class === 'inf'){
            $ret = STATUS_MUST_ADVANCE;
        }
        if($attUnit->bow){
            $ret = STATUS_CAN_ADVANCE;
        }
        if($attUnit->armorClass === "S"){
            $ret = STATUS_CAN_ADVANCE;
        }
        return $ret;
    }

    function applyCRTResults($defenderId, $attackers, $combatResults, $dieRoll, $force)
    {
        $battle = \Wargame\Battle::getBattle();
        $this->oddEven++;
        list($defenderId, $attackers, $combatResults, $dieRoll) = $battle->victory->preCombatResults($defenderId, $attackers, $combatResults, $dieRoll);

        $defUnit = $force->units[$defenderId];
        $numDefenders = $battle->combatRules->numDefenders($defenderId);

        $defUnit->dieRoll = $dieRoll;
        switch ($combatResults) {

            case NE:
                $defUnit->status = STATUS_DEFENDED;
                $defUnit->retreatCountRequired = 0;
                break;
            case D:
                if($battle->gameRules->phase === RED_COMBAT_PHASE || $battle->gameRules->phase === BLUE_COMBAT_PHASE){
                    $defUnit->status = STATUS_CAN_RETREAT;
                    $defUnit->retreatCountRequired = $defUnit->getMaxMove();
                }else{
                    $defUnit->status = STATUS_DEFENDED;
                    $defUnit->retreatCountRequired = 0;
                }
                $defUnit->disruptUnit($battle->gameRules->phase);
                $battle->victory->disruptUnit($defUnit);
                break;

            case E:
                $defUnit->status = STATUS_ELIMINATING;
                $defUnit->retreatCountRequired = 0;
                $defUnit->moveCount = 0;
                break;

            case HALFE:
                if($this->oddEven & 1){
                    $defUnit->status = STATUS_ELIMINATING;
                    $defUnit->retreatCountRequired = 0;
                    $defUnit->moveCount = 0;
                }else{
                    if($battle->gameRules->phase === RED_COMBAT_PHASE || $battle->gameRules->phase === BLUE_COMBAT_PHASE){
                        $defUnit->status = STATUS_CAN_RETREAT;
                        $defUnit->retreatCountRequired = $defUnit->getMaxMove();
                    }else{
                        $defUnit->status = STATUS_DEFENDED;
                        $defUnit->retreatCountRequired = 0;
                    }
                    $defUnit->disruptUnit($battle->gameRules->phase);
                    $battle->victory->disruptUnit($defUnit);
                };
                break;

            default:
                break;
        }
        $defUnit->combatResults = $combatResults;
        $defUnit->combatNumber = 0;
        $defUnit->moveCount = 0;


        $numAttackers = count((array)$attackers);
        foreach ($attackers as $attacker => $val) {
            $attUnit = $force->units[$attacker];
            if ($battle->gameRules->phase === BLUE_COMBAT_RES_PHASE || $battle->gameRules->phase === RED_COMBAT_RES_PHASE) {
                    $attUnit->status = STATUS_ATTACKED;
                    $attUnit->setFireCombat();
                    $attUnit->combatResults = $combatResults;
                    $attUnit->dieRoll = $dieRoll;
                    $attUnit->combatNumber = 0;
                    $attUnit->moveCount = 0;
                    continue;
                
            }
            if ($attUnit->status == STATUS_ATTACKING) {

                $attUnit->combatResults = $combatResults;
                $attUnit->dieRoll = $dieRoll;
                $attUnit->combatNumber = 0;
                $attUnit->moveCount = 0;
            }
        }
        $gameRules = $battle->gameRules;
        $mapData = $battle->mapData;
        $mapData->breadcrumbCombat($defenderId, $force->attackingForceId, $gameRules->turn, $gameRules->phase, $gameRules->mode, $combatResults, $dieRoll, $force->getUnitHexagon($defenderId)->name);

        $battle->victory->postCombatResults($defenderId, $attackers, $combatResults, $dieRoll);

        $force->removeEliminatingUnits();
    }

}