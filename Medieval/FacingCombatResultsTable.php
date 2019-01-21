<?php
namespace Wargame\Medieval;
use \stdClass;
use \Wargame\Battle;
use \Wargame\Hexpart;
// crt.js

// Copyright (c) 2009-2011 Mark Butler
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
class FacingCombatResultsTable extends MedievalCombatResultsTable
{
    public $combatIndexCount;
    public $maxCombatIndex;
    public $dieSideCount;
    public $dieMaxValue;
    public $combatResultCount;

    public $combatResultsTable;
    public $combatResultsHeader;
    public $combatOddsTable;
    /* starting number for die roll */
    public $rowNum = 1;
    public $resultsNames;

    use AncientCRTResults;

    //     combatIndexeCount is 6; maxCombatIndex = 5
    //     index is 0 to 5;  dieSidesCount = 6

    function __construct()
    {
        global $results_name;
        $results_name[NE] = ".";
        $this->resultsNames = $results_name;
        $this->crts = new stdClass();
        $this->crts->melee = new stdClass();
        $this->crts->melee->header  = array("1:2",  "1:1",  "2:1", "3:1", "4:1", "5:1", "6:1", "7:1", "8:1");
//        $this->crts->melee->next = 'missile';
        $this->crts->melee->maxMinuses = 0;
        $this->crts->melee->maxPluses = 1;

        $this->crts->melee->table = array(
            array(NE, NE, NE, NE,     D,     D,     D,     HALFE, E),
            array(NE, NE, NE, NE,     D,     D,     HALFE, HALFE, E),
            array(NE, NE, D,  D,      D,     D,     HALFE, E,     E),
            array(NE, D,  D,  D,      HALFE, HALFE, E,     E,     E),
            array(NE, D,  D,  HALFE,  HALFE, E,     E,     E,     E),
            array(D,  D,  D,  HALFE,  E,     E,     E,     E,     E),
            array(D,  D,  E,  E,      E,     E,     E,     E,     E),
        );
        $this->crts->melee->maxCombatIndex = 7;
        $this->combatIndexCount = 8;
        $this->maxCombatIndex = $this->combatIndexCount - 1;
        $this->crts->melee->dieOffsetHelper = -1;

        $this->dieSideCount = 6;

//        $this->crts->missile = new stdClass();
//        $this->crts->missile->header =  array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12+");
//        $this->crts->missile->next = 'melee';
//        $this->crts->missile->table = array(
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, D),
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, D),
//            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, D,  D,  D),
//            array(NE, NE, NE, NE, NE, NE, NE, D,  D,  D,  D,  D),
//            array(NE, NE, NE, NE, NE, NE, NE, D,  D,  D,  D,  D),
//            array(NE, NE, NE, NE, NE,  D,  D, D,  D,  D,  D,  L),
//            array(NE, NE, NE,  D,  D,  D,  D, D,  D,  D,  D,  L),
//            array(NE,  D,  D,  D,  D,  D,  D, D,  D,  L,  L,  L),
//            array(NE,  D,  D,  D,  D,  D,  D, L,  L,  L,  L,  L),
//            array( D,  D,  D,  D,  D,  D,  D, L,  L,  L,  L,  L2),
//            array( D,  D,  D,  D,  D,  L,  L, L,  L,  L,  L,  L2),
//            array( D,  D,  D,  L,  L,  L,  L, L,  L,  L2,  L2,  L2),
//            array( D,  L,  L,  L,  L,  L,  L, L,  L2,  L2,  L2,  L2),
//        );
//        $this->crts->missile->maxCombatIndex = 11;

//        $this->combatIndexCount = 12;
//        $this->maxCombatIndex = $this->combatIndexCount - 1;
//        $this->dieSideCount = 10;
    }

    function getCombatResults(&$Die, $index, $combat)
    {
        $crt = $this->crts->melee;

        return $crt->table[$Die  + $combat->dieOffset][$index];
    }

    function getCombatDisplay()
    {
        return $this->combatResultsHeader;
    }

    public function setCombatIndex($defenderId)
    {
        $this->crrentCrt = $this->crts->melee;
        $fireCombat = false;

        $combatLog = "";
        /* @var JagCore $battle */
        $battle = Battle::getBattle();
        $scenario = $battle->scenario;
        $combats = $battle->combatRules->combats->$defenderId;
        $combats->dieShift = 0;
        if($battle->gameRules->phase === BLUE_FIRE_COMBAT_PHASE || $battle->gameRules->phase === RED_FIRE_COMBAT_PHASE ||
            $battle->gameRules->phase === BLUE_FIRE_COMBAT_PHASE_TWO || $battle->gameRules->phase === RED_FIRE_COMBAT_PHASE_TWO) {
            $fireCombat = true;
        }
        if (count((array)$combats->attackers) == 0) {
            $combats->index = null;
            $combats->attackStrength = null;
            $combats->defenseStrength = null;
            $combats->terrainCombatEffect = null;
            return;
        }

        $defenders = $combats->defenders;
        $isFrozenSwamp = $isTown = $isHill = $isForest = $isSwamp = $attackerIsSunkenRoad = $isRedoubt = $isElevated = false;

        $range = 1;
        foreach ($defenders as $defId => $defender) {

            $hexagon = $battle->force->units[$defId]->hexagon;
            $defendingUnit = $battle->force->units[$defId];

            $hexpart = new Hexpart();
            $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);
            $isTown |= $battle->terrain->terrainIs($hexpart, 'town');
            $isHill |= $battle->terrain->terrainIs($hexpart, 'hill');
            $isForest |= $battle->terrain->terrainIs($hexpart, 'forest');
            $isSwamp |= $battle->terrain->terrainIs($hexpart, 'swamp');
            if($battle->terrain->terrainIs($hexpart, 'frozenswamp')){
                if($battle->terrain->getDefenderTerrainCombatEffect($hexagon)){
                    $isFrozenSwamp |= true;
                }
            }

            if($battle->terrain->terrainIs($hexpart, 'elevation1')){
                $isElevated = 1;
            }
            if($battle->terrain->terrainIs($hexpart, 'elevation2')){
                $isElevated = 2;
            }
        }
        $isClear = true;
        if ($isTown || $isForest || $isHill || $isSwamp || $isFrozenSwamp) {
            $isClear = false;
        }

        $attackers = $combats->attackers;
        $attackStrength = 0;
        $attackersCav = false;

        $flankedDefenders = [];
        $combatLog .= "Attackers<br>";
        $isSpear = $isAx = $isCavalry = false;

        foreach ($attackers as $attackerId => $attacker) {
            $terrainReason = "";
            $unit = $battle->force->units[$attackerId];
            
            $los = new \Wargame\Los();
            $defenderFlanked = false;
            foreach($defenders as $defId=> $def) {
                $los->setOrigin($battle->force->getUnitHexagon($attackerId));
                $los->setEndPoint($battle->force->getUnitHexagon($defId));
                if($los->getRange() > 1){
                    $range = $los->getRange();
                }

                $defUnit =  $battle->force->units[$defId];

                if(isset($defUnit->facing) && $defUnit->checkLos($los)){
                    $flankedDefenders[$defId] = true;
                }

            }

            if($fireCombat){
                $unitStrength = $unit->fireStrength;
            }else{
                $unitStrength = $unit->attackStrength;
            }

            $hexagon = $unit->hexagon;
            $hexpart = new Hexpart();
            $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);

            $attackerIsSwamp = false;
            if(empty($scenario->wimpySwamps)){
                $attackerIsSwamp = $battle->terrain->terrainIs($hexpart, 'swamp');
            }
            $attackerIsFrozenSwamp = $battle->terrain->terrainIs($hexpart, 'frozenswamp');

            $attackerIsSunkenRoad = $battle->terrain->terrainIs($hexpart, 'sunkenroad');

            if($attackerIsSwamp){
                $terrainReason .= "attacker is in swamp ";
            }
            if($attackerIsSunkenRoad){
                $terrainReason .= "attacker is in sunken road ";
            }

            if($attackerIsFrozenSwamp){
                $terrainReason .= "attacker is frozen swamp ";
            }

            if($isFrozenSwamp){
                $terrainReason .= "Frozen Swamp ";
            }
            $attackerIsElevated = false;
            if($battle->terrain->terrainIs($hexpart, 'elevation1')){
                $attackerIsElevated = 1;
            }

            if($battle->terrain->terrainIs($hexpart, 'elevation2')){
                $attackerIsElevated = 2;
            }
            $attackUpHill = false;
            if($isElevated && ($isElevated > $attackerIsElevated)){
                /* Special case for elevation 2 and attack no elevated, can be from be behind */
                if($isElevated == 2  && $attackerIsElevated === false) {
                    if ($battle->combatRules->thisAttackAcrossTwoType($defId, $attackerId, "elevation1")) {
                        $terrainReason .= "attack uphill ";
                        $attackUpHill = true;
                    }
                }else{
                    $terrainReason .= "attack uphill ";
                    $attackUpHill = true;
                }
            }
            $attackDownHill = false;
            if($attackerIsElevated && ($isElevated < $attackerIsElevated)){
                /* Special case for elevation 2 and attack no elevated, can be from be behind */
                if($attackerIsElevated == 2  && $isElevated === false) {
//                    if ($battle->combatRules->thisAttackAcrossTwoType($defId, $attackerId, "elevation1")) {
//                        $terrainReason .= "attack downhill ";
//                        $attackUpHill = true;
//                    }
                }else{
                    $terrainReason .= "attack downhill ";
                    $attackDownHill = true;
                }
            }
            $acrossRiver = false;
            foreach ($defenders as $defId => $defender) {
                if ($battle->combatRules->thisAttackAcrossRiver($defId, $attackerId)) {
                    $terrainReason .= "attack across river or wadi";
                    $acrossRiver = true;
                }
            }

            $acrossRedoubt = false;
            foreach ($defenders as $defId => $defender) {
                $isRedoubt = false;
                $hexagon = $battle->force->units[$defId]->hexagon;
                $hexpart = new Hexpart();
                $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);
                $isRedoubt |= $battle->terrain->terrainIs($hexpart, 'redoubt');

                if ($isRedoubt && $battle->combatRules->thisAttackAcrossType($defId, $attackerId, "redoubt")) {
                    $acrossRedoubt = true;
                    $terrainReason .= "attack across redoubt ";
                }
            }

            if ($unit->class == "sword" || $unit->class == "ax") {
                $isAx = true;
                $combatLog .= "$unitStrength ".ucfirst($unit->class)." ";
            }

            if ($unit->class == "cavalry") {
                $isCavalry = true;
                $combatLog .= "$unitStrength Cavalry ";
                $attackersCav = true;
            }
            if ($unit->class == "milita" || $unit->class == "spear") {
                $combatLog .= "$unitStrength ".ucfirst($unit->class)." ";
                $isSpear = true;
            }
            $attackStrength += $unitStrength;
            $combatLog .= $unit->class." $unitStrength = $attackStrength<br>";
        }
//        $combatLog .= "<br>";

        $defenseStrength = 0;
        $defendersAllCav = true;
        $combatLog .= " Total Attack = $attackStrength<br>";
        $combatLog .= "<br>Defenders<br>";
        if($fireCombat){
            $reason = "clear";
            $unitDefense = 2;
            if ($isTown) {
                $reason = 'town';
                $unitDefense = 4;
            }
            if ($isForest) {
                $reason = 'forest';
                $unitDefense = 3;
            }
            if ($isHill) {
                $reason = 'hill';
                $unitDefense = 3;
            }
            $combatLog .= "$unitDefense $reason ";
            $defenseStrength += $unitDefense;
        }else {
            foreach ($defenders as $defId => $defender) {

                $unit = $battle->force->units[$defId];
                $class = $unit->class;
                $unitDefense = $unit->defStrength;
                /*
                 * map made above of units being attacked on their flank
                 */
                if (!empty($flankedDefenders[$defId])) {
                    $combatLog .= " Defender Flanked, halved";
                    $unitDefense = $unit->flankStrength;
                }
                if ($fireCombat) {
                    $combatLog .= "$unitDefense " . $unit->class . " ";
                }
                /* set to true to disable for not scenario->doubleArt */
                $artInNonTown = false;
                $notClearHex = false;
                $hexagon = $unit->hexagon;
                $hexpart = new Hexpart();
                $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);
                $isTown = $battle->terrain->terrainIs($hexpart, 'town');
                $isHill = $battle->terrain->terrainIs($hexpart, 'hill');
                $isForest = $battle->terrain->terrainIs($hexpart, 'forest');
                $isSwamp = $battle->terrain->terrainIs($hexpart, 'swamp');

                $notClearHex = false;
                if ($isTown || $isForest || $isHill || $isSwamp) {
                    $notClearHex = true;
                }

                $clearHex = !$notClearHex;

                if ($unit->class != 'cavalry') {
                    $defendersAllCav = false;
                }

                $defMultiplier = 1;

                $defenseStrength += $unitDefense * $defMultiplier;
                $combatLog .= " = $defenseStrength<br>";
            }
        }

        $combatLog .= "Total Defense = $defenseStrength<br><br>";


        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);


        if ($combatIndex >= $this->crts->melee->maxCombatIndex) {
            $combatIndex = $this->crts->melee->maxCombatIndex;
        }


        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;

        if($fireCombat) {
            /* knight */
            $dieShift = 0;
            $combats->dieOffset = $dieShift;

        }else{
            $combats->dieOffset = 0;

        }

        if($combats->pinCRT !== false){
            $pinIndex = $combats->pinCRT;
            if($combatIndex > $pinIndex){
                $combatLog .= "<br>Pinned to {$this->combatResultsHeader[$pinIndex]} ";
            }else{
                $combats->pinCRT = false;
            }
        }
        $combatLog .= "Total Die Shift ".$combats->dieOffset."<br>";
        if($combatIndex < 0){
            $combatIndex = 0;
        }
        $combats->index = $combatIndex;
        $combats->useAlt = false;
        $combats->useDetermined = false;
        $combats->combatLog = $combatLog;
    }



    function getCombatIndex($attackStrength, $defenseStrength)
    {
        $battle = \Wargame\Battle::getBattle();

        $ratio = $attackStrength / $defenseStrength;
        if ($attackStrength >= $defenseStrength) {
            $combatIndex = floor($ratio);
        } else {
            $combatIndex = 2 - ceil($defenseStrength / $attackStrength);
        }
        return $combatIndex;
    }
}
