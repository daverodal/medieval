<?php
namespace Wargame\Medieval\WayBack;
use Wargame\Medieval\AncientsLandBattle;
use Wargame\Medieval\MedievalUnit;
use Wargame\Medieval\AncientUnitFactory;
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
        return AncientUnitFactory::build($data);
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

    public function init()
    {
        AncientUnitFactory::$injector = $this->force;
        $scenario = $this->scenario;

        for($i = 0;$i < 10;$i++){
            AncientUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                4, 4,2,3,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'spear',1, 0, 'H');

        }

        for($i = 0;$i < 10;$i++){
            AncientUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                3, 3,2,3,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'milita',1, 0, 'H');

        }
        for($i = 0;$i < 4;$i++){
            AncientUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                1, 1,1,9,1,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'cavalry',1, 0, 'H');

        }
        for($i = 0;$i < 2;$i++){
            AncientUnitFactory::create("lll", self::TURKISH_FORCE, "deployBox",
                0, 1,.5,5,2,  STATUS_CAN_DEPLOY, "A", 1, "turkish", 'bow',1, 0, 'H', true, 2);
        }

        for($i = 0;$i < 5;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                5, 5,2, 4,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'spear',1, 3, 'H');

        }

        for($i = 0;$i < 9;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                2, 2,2, 3,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'milita',1, 3, 'H');

        }

        for($i = 0;$i < 6;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                6, 2,2, 4,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'sword',1, 3, 'H');

        }
        for($i = 0;$i < 2;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                1, 1,1, 9,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'cavalry',1, 3, 'H');

        }
        for($i = 0;$i < 2;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                2, 2,1, 11,1, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'cavalry',1, 3, 'H', true, 2);

        }


        for($i = 0;$i < 6;$i++) {
            AncientUnitFactory::create("lll", self::CRUSADER_FORCE, "deployBox",
                1, 1,1, 5,2, STATUS_CAN_DEPLOY, "B", 1, "crusader", 'bow',1, 3, 'H', true, 1);

        }

    }

    public static function myName(){
        echo __CLASS__;
    }

    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\Medieval\FacingCombatResultsTable();
        $this->combatRules->injectCrt($crt);

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