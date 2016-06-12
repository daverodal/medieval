<?php
namespace Wargame\Medieval\Civitate1053;
use Wargame\Medieval\MedievalLandBattle;
use Wargame\Medieval\MedievalUnit;
use Wargame\Medieval\UnitFactory;
use Wargame\MoveRules;
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

define("NORMAN_FORCE", 1);
define("LOMBARD_FORCE", 2);


class Civitate1053 extends MedievalLandBattle
{
    /* a comment */

    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>1];

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

        for($i = 0;$i < 9;$i++){
            UnitFactory::create("lll", NORMAN_FORCE, "deployBox", 6, 5,1,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'cavalry',1, 0, 'H');

        }


        for($i = 0;$i < 1;$i++){
            UnitFactory::create("lll", NORMAN_FORCE, "deployBox", 4,  3,1,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'inf',1, 0, 'M');

        }

        for($i = 0;$i < 2;$i++){
            UnitFactory::create("lll", NORMAN_FORCE, "deployBox", 1, 5,2,  STATUS_CAN_DEPLOY, "A", 1, "norman", 'cavalry',1, false, 'S', true);

        }

        for($i = 0;$i < 8;$i++) {
            UnitFactory::create("lll", LOMBARD_FORCE, "deployBox",  4, 5,1,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'cavalry',1, 3, 'H');

        }
        for($i = 0;$i < 6;$i++) {
            UnitFactory::create("lll", LOMBARD_FORCE, "deployBox",  3, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'inf',1, 3, 'M');

        }
        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", LOMBARD_FORCE, "deployBox",  2, 4,2,  STATUS_CAN_DEPLOY, "B", 1, "lombard", 'inf',1, 3, 'M', true);

        }

        for($i = 0;$i < 2;$i++) {
            UnitFactory::create("lll", LOMBARD_FORCE, "deployBox",  7, 3,1,  STATUS_CAN_DEPLOY, "B", 1, "swabian", 'inf',1, 3, 'K');

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

        if ($data) {
            $this->specialHexA = $data->specialHexA;

        } else {

            $this->victory = new \Wargame\Victory("Wargame\\Medieval\\Civitate1053\\VictoryCore");
            if (!empty($scenario->supplyLen)) {
                $this->victory->setSupplyLen($scenario->supplyLen);
            }
//            $this->moveRules = new MoveRules($this->force, $this->terrain);
            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = 0;
            $this->moveRules->noZocZocOneHex = true;
            $this->moveRules->noZocZoc = true;
            $this->moveRules->retreatCannotOverstack = true;
            // game data
            $this->gameRules->setMaxTurn(7);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */

            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_FIRE_COMBAT_PHASE, FIRE_COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_FIRE_COMBAT_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_FIRE_COMBAT_PHASE, FIRE_COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_FIRE_COMBAT_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);
        }
    }
}