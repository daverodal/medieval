<?php
namespace Wargame\Medieval\WayBack;
use Wargame\Medieval\AncientsLandBattle;
use Wargame\Medieval\MedievalUnit;
use Wargame\Medieval\FacingUnitFactory;
use Wargame\FacingMoveRules;
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

/*
 * <div>Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>
 */

class WayBack extends AncientsLandBattle
{
    
    const TURKISH_FORCE = 1;
    const CRUSADER_FORCE = 2;
    /* a comment */

    public $specialHexesMap = ['SpecialHexA'=>1, 'SpecialHexB'=>2, 'SpecialHexC'=>1];

    public static function buildUnit($data = false){
        return FacingUnitFactory::build($data);
    }

    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "Turkish", "Crusader"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainGen($mapDoc, $terrainDoc)
    {
        $this->terrain->addTerrainFeature("orchard", "orchard", "t", 0, 0,0, false, true);

        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("town", "town", "t", 0, 0, 1, false);
    }
    function save()
    {
        $data = parent::save();
        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;
        return $data;
    }

    public function scenInit(){


        $scenario = $this->scenario;
        $unitSets = $scenario->units;

        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== WayBack::TURKISH_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                if($unitSet->hq){
                    FacingUnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->commandRadius, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  "hq", 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow,MedievalUnit::BATTLE_READY, $unitSet->steps);
                }else{
                    FacingUnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->range, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  $unitSet->class, 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow);
                }
            }
        }
        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== WayBack::CRUSADER_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                if($unitSet->hq){
                    FacingUnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->commandRadius, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality, "hq", 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow,MedievalUnit::BATTLE_READY, $unitSet->steps );
                }else{
                    FacingUnitFactory::create("lll", $unitSet->forceId, "deployBox", $unitSet->combat, $unitSet->movement, $unitSet->range, STATUS_CAN_DEPLOY,  $unitSet->reinforce, 1,  $unitSet->nationality,  $unitSet->class, 1, $unitSet->facing, $unitSet->armorClass, $unitSet->bow);
                }
            }
        }
    }
    public function init()
    {
        FacingUnitFactory::$injector = $this->force;
        $scenario = $this->scenario;

        if(isset($scenario->units)){
//            return $this->scenInit();
        }


        $baseValue = 6;
        $reducedBaseValue = 3;
        if(!empty($scenario->weakerLoyalist)){
            $baseValue = 5;
            $reducedBaseValue = 2;
        }
        if(!empty($scenario->strongerLoyalist)){
            $baseValue = 7;
        }

//        FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",  3, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
//        FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
//        FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);

        for($i = 0;$i < 10;$i++){
            FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                4, 4,2,3,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'spear',1, 0, 'H');

        }

        for($i = 0;$i < 10;$i++){
            FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                3, 3,2,3,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'milita',1, 0, 'H');

        }
        for($i = 0;$i < 4;$i++){
            FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                1, 1,1,9,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'cavalry',1, 0, 'H');

        }
        for($i = 0;$i < 2;$i++){
            FacingUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                0, 1,.5,5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'bow',1, 0, 'H', true);

        }

        for($i = 0;$i < 5;$i++) {
            FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                5, 5,2, 4,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'spear',1, 3, 'H');

        }

        for($i = 0;$i < 9;$i++) {
            FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                2, 2,2, 3,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'milita',1, 3, 'H');

        }

        for($i = 0;$i < 6;$i++) {
            FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                6, 2,2, 4,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'sword',1, 3, 'H');

        }
        for($i = 0;$i < 2;$i++) {
            FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                1, 1,1, 9,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'cavalry',1, 3, 'H');

        }
        for($i = 0;$i < 2;$i++) {
            FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                2, 2,1, 11,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'cavalry',1, 3, 'H', true);

        }
//        FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
//        FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
//        FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);



//        FacingUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",  2, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "crusader", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);



    }

    public static function myName(){
        echo __CLASS__;
    }
    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\Medieval\FacingCombatResultsTable();
        $this->combatRules->injectCrt($crt);

//        $this->gameRules->gameHasCombatResolutionMode = false;
        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {

            $this->victory = new \Wargame\Victory("Wargame\\Medieval\\WayBack\\VictoryCore");
            
            // game data
            $this->gameRules->setMaxTurn(8);
            $this->deployFirstMoveSecond();
        }
    }
}