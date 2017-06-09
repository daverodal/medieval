<?php
namespace Wargame\Medieval\Civitate1053;
use Wargame\Medieval\MedievalLandBattle;
use Wargame\Medieval\MedievalUnit;
use Wargame\Medieval\UnitFactory;
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



class Civitate1053 extends MedievalLandBattle
{
    
    const NORMAN_FORCE = 1;
    const LOMBARD_FORCE = 2;
    /* a comment */

    public $specialHexesMap = ['SpecialHexA'=>1, 'SpecialHexB'=>2, 'SpecialHexC'=>1];

    public static function buildUnit($data = false){
        return UnitFactory::build($data);
    }

    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "Norman", "Lombard"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainGen($mapDoc, $terrainDoc)
    {
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

    public function init()
    {
        UnitFactory::$injector = $this->force;


        $scenario = $this->scenario;
        $baseValue = 6;
        $reducedBaseValue = 3;
        if(!empty($scenario->weakerLoyalist)){
            $baseValue = 5;
            $reducedBaseValue = 2;
        }
        if(!empty($scenario->strongerLoyalist)){
            $baseValue = 7;
        }

        UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox",  3, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);

        for($i = 0;$i < 9;$i++){
            UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox", 6, 5,1,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'cavalry',1, 0, 'H');

        }

        for($i = 0;$i < 1;$i++){
            UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox", 4,  3,1,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'inf',1, 0, 'M');

        }

        for($i = 0;$i < 2;$i++){
            UnitFactory::create("lll", self::NORMAN_FORCE, "deployBox", 1, 3,2,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'inf',1, false, 'S', true);

        }

        UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  2, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);
        UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  1, 5,2,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);


        for($i = 0;$i < 8;$i++) {
            UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  4, 5,1,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'cavalry',1, 3, 'H');

        }
        for($i = 0;$i < 6;$i++) {
            UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  3, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'inf',1, 3, 'M');

        }
        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  2, 4,2,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'inf',1, 3, 'M', true);

        }

        UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  2, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "swabian", 'hq',1, false, 'H',false, MedievalUnit::BATTLE_READY, 1);

        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", self::LOMBARD_FORCE, "deployBox",  7, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "swabian", 'inf',1, 3, 'K');

        }

    }

    public static function myName(){
        echo __CLASS__;
    }
    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\Medieval\MedievalCombatResultsTable();
        $this->combatRules->injectCrt($crt);

//        $this->gameRules->gameHasCombatResolutionMode = false;
        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {

            $this->victory = new \Wargame\Victory("Wargame\\Medieval\\Civitate1053\\VictoryCore");
            
            // game data
            $this->gameRules->setMaxTurn(7);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */

            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

        }
    }
}