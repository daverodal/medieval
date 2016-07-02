<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 7/2/16
 * Time: 11:33 AM
 */

namespace Wargame\Medieval;
use Wargame\Battle;

trait Command
{

    public $headQuarters;

    public function initHeadquarters(){
        $this->headQuarters = [];
        $b = Battle::getBattle();
        $units = $b->force->units;
        foreach($units as $unit){
            if($unit->class == 'hq' && $unit->hexagon->name && $unit->forceId == $b->force->attackingForceId){
                $this->headQuarters[] = $unit->id;
            }
        }
    }

    public function checkCommand($unit){
        $id = $unit->id;
        $b = Battle::getBattle();


        if($unit->class === "hq"){
            $unit->command = true;
            return;
        }
        if(($b->gameRules->phase == RED_MOVE_PHASE || $b->gameRules->phase == BLUE_MOVE_PHASE)){
            if($unit->forceId !== $b->force->attackingForceId){
                return;
            }
            foreach($this->headQuarters as $hq){
                $cmdRange = $b->force->units[$hq]->commandRadius;
                if($id == $hq){
                    return;
                }
                $los = new \Wargame\Los();

                $los->setOrigin($b->force->getUnitHexagon($id));
                $los->setEndPoint($b->force->getUnitHexagon($hq));
                $range = $los->getRange();
                if($range <= $cmdRange){
                    $unit->command = true;
                    return;
                }
            }
            $unit->command = false;
            return;
        }

    }
}