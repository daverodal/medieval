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

class MedievalCombatResultsTable
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
    public $rowNum = -2;
    public $resultsNames;

    use CRTResults;

    //     combatIndexeCount is 6; maxCombatIndex = 5
    //     index is 0 to 5;  dieSidesCount = 6

    function __construct()
    {
        global $results_name;
        $this->resultsNames = $results_name;
        $this->crts = new stdClass();
        $this->crts->melee = new stdClass();
        $this->crts->melee->header  = array("1:4", "1:3", "1:2", "1:1.5", "1:1", "1.5:1", "2:1", "3:1", "4:1", "5:1", "6:1");
        $this->crts->melee->next = 'missile';
        $this->crts->melee->table = array(
            array(AE,  AE,    AE,   AE,   AL,  ALR,  NE,   NE,   DLF,  DEAL, DEAL),
            array(AE,  AE,    AE,   AL2F, AL,  AL,   ALR,  BLDR, DLF,  DEAL, DEAL),
            array(AE,  AE,    AE,   AL2F, AL,  AL,   AR,   BLDR, DEAL, DEAL, DE),
            array(AE,  AE,    AL2F, AL2F, AL,  AR,   BL,   BLDR, DEAL, DE,   DE),
            array(AE,  AE,    AL2F, AL2F, AR,  AR,   BLDR, DLR,  DEAL, DE,   DE),
            array(AE,  AE,    AL2F, AL2F, AR,  NE,   BLDR, DLR,  DEAL, DE,   DE),
            array(AE,  AE,    AL2R, ALR,  AR,  NE,   BLDR, DLR,  DE,   DE,   DE),
            array(AE,  AE,    AL2R, ALR,  NE,  BL,   DLR,  DL2R, DE,   DE,   DE),
            array(AE,  AE,    ALR,  ALR,  NE,  BL,   DLR,  DL2R, DE,   DE,   DE),
            array(AE,  AE,    ALR,  ALR,  NE,  DL,   DL2R, DL2F, DE,   DE,   DE),
            array(AE,  ALF,   AL,   AL,   BL,  DL,   DL2R, DL2F, DE,   DE,   DE),
            array(AE,  ALF,   AL,   AL,   BL,  DLR,  DL2F, DL2F, DE,   DE,   DE),
            array(AE,  AL2F,  NE,   NE,   DL,  DLR,  DL2F, DL2F, DE,   DE,   DE),
            array(ALF, AL2F,  NE,   NE,   DL,  DLF,  DL2F, DL2F, DE,   DE,   DE),
            array(ALF, NE,    BL,   NE,   DLR, DLF,  DE,   DE,   DE,   DE,   DE),
            array(ALF, NE,    BL,   NE,   DLR, DEAL, DE,   DE,   DE,   DE,   DE),
        );
        $this->crts->melee->maxCombatIndex = 10;

        $this->crts->missile = new stdClass();
        $this->crts->missile->header =  array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12+");
        $this->crts->missile->next = 'melee';
        $this->crts->missile->table = array(
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE),
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, D),
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, NE, D),
            array(NE, NE, NE, NE, NE, NE, NE, NE, NE, D,  D,  D),
            array(NE, NE, NE, NE, NE, NE, NE, D,  D,  D,  D,  D),
            array(NE, NE, NE, NE, NE, NE, NE, D,  D,  D,  D,  D),
            array(NE, NE, NE, NE, NE,  D,  D, D,  D,  D,  D,  L),
            array(NE, NE, NE,  D,  D,  D,  D, D,  D,  D,  D,  L),
            array(NE,  D,  D,  D,  D,  D,  D, D,  D,  L,  L,  L),
            array(NE,  D,  D,  D,  D,  D,  D, L,  L,  L,  L,  L),
            array( D,  D,  D,  D,  D,  D,  D, L,  L,  L,  L,  L2),
            array( D,  D,  D,  D,  D,  L,  L, L,  L,  L,  L,  L2),
            array( D,  D,  D,  L,  L,  L,  L, L,  L,  L2,  L2,  L2),
            array( D,  L,  L,  L,  L,  L,  L, L,  L2,  L2,  L2,  L2),
        );
        $this->crts->missile->maxCombatIndex = 11;

        $this->combatIndexCount = 12;
        $this->maxCombatIndex = $this->combatIndexCount - 1;
        $this->dieSideCount = 10;
    }

    function getCombatResults(&$Die, $index, $combat)
    {
//        $Die += $combat->dieShift;
        $battle = \Wargame\Battle::getBattle();
        $crt = $this->crts->melee;
        if (($battle->gameRules->phase == BLUE_COMBAT_RES_PHASE) || ($battle->gameRules->phase == RED_COMBAT_RES_PHASE)) {
            $crt = $this->crts->missile;
        }

        return $crt->table[$Die + 3 + $combat->dieOffset][$index];
    }

    function getCombatDisplay()
    {
        return $this->combatResultsHeader;
    }

    public function setCombatIndex($defenderId)
    {

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

        $defArmor = -1;
        $defArmorClass = 'L';
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

;
            if($this->armorValue($defendingUnit->armorClass) > $defArmor){
                $defArmor = $this->armorValue($defendingUnit->armorClass);
                $defArmorClass = $defendingUnit->armorClass;
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
        $attackerArmor = 0;

        $flankedDefenders = [];
        $combatLog .= "Attackers<br>";
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

//                var_dump($bearing);
//                var_dump($attackerBearing);
//                var_dump($unit->facing);
            }

//            var_dump($bearing);



            if($this->armorValue($unit->armorClass) > $attackerArmor){
                $attackerArmor = $this->armorValue($unit->armorClass);
            }
            $unitStrength = $unit->strength;
            if($unit->class === "wagon"){
                $unitStrength = 0;
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

            if ($unit->class == "inf" || $unit->class == "hq") {
                $combatLog .= "$unitStrength ".ucfirst($unit->class)." ";

                if ($isSwamp || $isFrozenSwamp || $attackerIsFrozenSwamp ||  $attackerIsSwamp || $acrossRiver || $attackerIsSunkenRoad || $acrossRedoubt || $attackUpHill) {
                    if(!$terrainReason){
                        $terrainReason = " terrain ";
                    }
                    if(($attackUpHill || $isFrozenSwamp || $attackerIsFrozenSwamp) && !($isSwamp || $attackerIsSwamp || $acrossRiver || $attackerIsSunkenRoad || $acrossRedoubt)){
//                        $unitStrength *= .75;
//                        $combats->dieShift = -1;
                        $unitStrength -= 1;
                        $combatLog .= "unit strength -1  for $terrainReason ";
                    }else{
                        if(empty($scenario->weakRedoubts)){
                            $unitStrength /= 2;
                            $combatLog .= "attacker halved for $terrainReason ";
                        }
                    }
                }
                if($attackDownHill && !$fireCombat){
                    $unitStrength += 1;
                    $combatLog .= "unit strength +1  for $terrainReason ";
                }
            }

            if ($unit->class == "cavalry") {
                $combatLog .= "$unitStrength Cavalry ";
                $attackersCav = true;

                if ($attackerIsSwamp || $acrossRiver || !$isClear || $attackerIsSunkenRoad || $acrossRedoubt) {

                    if(!$terrainReason){
                        $terrainReason = " terrain ";
                    }

                    $unitStrength /= 2;
                    $combatLog .= "attacker halved for $terrainReason ";


                }elseif ( $attackUpHill || $attackerIsFrozenSwamp ) {

//                    $unitStrength *= .75;
//                    $combats->dieShift = -1;
                    $unitStrength -= 1;
                    $combatLog .= "unit strength -1 for $terrainReason ";
                }else{
                    if($attackDownHill && !$fireCombat){
                        $unitStrength += 1;
                        $combatLog .= "unit strength +1  for $terrainReason ";
                    }
                }
            }
            if ($unit->class == "artillery" || $unit->class == "horseartillery") {
                $combatLog .= "$unitStrength ".ucfirst($unit->class)." ";
                if($isSwamp || $acrossRedoubt || $attackUpHill || $isFrozenSwamp || $attackerIsFrozenSwamp){
                    if($attackUpHill || $isFrozenSwamp || $attackerIsFrozenSwamp){
//                        $unitStrength *= .75;
//                        $combats->dieShift = -1;
                        $unitStrength -= 1;
                        $combatLog .= "unit strength -1 for $terrainReason ";
                    }else{
                        $unitStrength /= 2;
                        $combatLog .= "attacker halved for $terrainReason ";
                    }
                    if(!$terrainReason){
                        $terrainReason = " terrain ";
                    }
                }
                $class = $unit->class;
                if($class == 'horseartillery'){
                    $class = 'artillery';
                }

            }
            $attackStrength += $unitStrength;
            $combatLog .= $unit->class." $unitStrength = $attackStrength<br>";
        }
//        $combatLog .= "<br>";

        $defenseStrength = 0;
        $defendersAllCav = true;
        $combatLog .= " Total Attack = $attackStrength<br>";
        if(!$fireCombat){
            $combatLog .= "<br>Defenders<br>";
        }
        foreach ($defenders as $defId => $defender) {

            $unit = $battle->force->units[$defId];
            $class = $unit->class;
            $unitDefense = $unit->strength;
            if(!$fireCombat){
                $combatLog .= "$unitDefense ".$unit->class." ";
            }
            /* set to true to disable for not scenario->doubleArt */
            $clearHex = false;
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
            if(($isTown && $class !== 'cavalry') || $artInNonTown || $isHill){
                $defMultiplier = 2.0;
                if(($isTown && $class !== 'cavalry') || $isHill){
                    $defMultiplier = 2;
                    $combatLog .= "defender doubled for terrain ";
                }
            }
            if(!empty($flankedDefenders[$defId])){
                $combatLog .= " Defender Flanked, halved";
                $unitDefense /= 2;
            }
            $defenseStrength += $unitDefense * $defMultiplier;
            $combatLog .= " = $defenseStrength<br>";
        }

        $combatLog .= "Total Defense = $defenseStrength<br><br>";


        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);

        if($fireCombat) {
            if ($combatIndex >= $this->crts->missile->maxCombatIndex) {
                $combatIndex = $this->crts->missile->maxCombatIndex;
            }
        }else{
            if ($combatIndex >= $this->crts->melee->maxCombatIndex) {
                $combatIndex = $this->crts->melee->maxCombatIndex;
            }
        }

        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;

        if($fireCombat) {
            /* knight */
            $dieShift = 0;
            if($defArmorClass === 'K'){
                $dieShift = -2;
                $combatLog .= "Kinghts Die Shift -2<br>";
            }
            if($defArmorClass === 'M'){
                $combatLog .= "Medium Die Shift +1<br>";

                $dieShift = 1;
            }
            if($defArmorClass === 'L'){
                $combatLog .= "Light Die Shift +2<br>";

                $dieShift = 2;
            }
            if($defArmorClass === 'S'){
                $combatLog .= "Skirmisher Die Shift -2<br>";

                $dieShift = -2;
            }
            /* for adjacent */
            if($range === 1){
                $dieShift++;
                $combatLog .= "Adjacent Die Shift +1<br>";
            }
            $combats->dieOffset = $dieShift;

        }else{
            $combats->dieOffset = $attackerArmor - $defArmor;

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

    function armorDiff($a, $d){
//        $strMap = ['K'=>3, 'H'=>2, 'M'=>1, 'L'=>0];
        return $this->armorValue($a) - $this->armorValue($d);
    }

    function armorValue($class){
        return  ['K'=>3, 'H'=>2, 'M'=>1, 'L'=>0, 'S'=>0][$class];
    }

    function getCombatIndex($attackStrength, $defenseStrength)
    {
        $battle = \Wargame\Battle::getBattle();

        if (($battle->gameRules->phase == BLUE_FIRE_COMBAT_PHASE) || ($battle->gameRules->phase == RED_FIRE_COMBAT_PHASE) ||
            $battle->gameRules->phase === BLUE_FIRE_COMBAT_PHASE_TWO || $battle->gameRules->phase === RED_FIRE_COMBAT_PHASE_TWO) {
            return $attackStrength - 1;
        }
        $ratio = $attackStrength / $defenseStrength;
        if ($attackStrength >= $defenseStrength) {
            $combatIndex = floor($ratio) + 3;
            if ($ratio >= 1.5) {
                $combatIndex++;
            }
        } else {
            $combatIndex = 4 - ceil($defenseStrength / $attackStrength);
            if($ratio > .67){
                $combatIndex++;
            }
        }
        return $combatIndex;
    }
}
