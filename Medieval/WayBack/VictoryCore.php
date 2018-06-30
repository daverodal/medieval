<?php
namespace Wargame\Medieval\WayBack;
use Wargame\Battle;
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
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
//include_once "victoryCore.php";

class VictoryCore extends \Wargame\Medieval\victoryCore
{

    protected $outgoingVP;

    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {

        } else {

        }
        $this->outgoingVP = [0,0,0,0,0];
    }

    function checkCommand($unit){
        return;
    }
    protected function checkVictory($attackingId, $battle){
        if(!$this->gameOver){

            $crusWin = $turkWin = false;
            $winScore = 40;
            if($this->victoryPoints[WayBack::TURKISH_FORCE] >= $winScore){
                $turkWin = true;
            }
            if($this->victoryPoints[WayBack::CRUSADER_FORCE] >= $winScore){
                $crusWin = true;
            }
            if($turkWin && $crusWin){
                $battle->gameRules->flashMessages[] = "Tie Game";
                $this->winner = 0;
                $this->gameOver = true;
                return true;
            }
            if($turkWin){
                $battle->gameRules->flashMessages[] = "Turkish Win";
                $this->winner = WayBack::TURKISH_FORCE;
                $this->gameOver = true;
                return true;
            }
            if($turkWin){
                $battle->gameRules->flashMessages[] = "Crusader Win";
                $this->winner = WayBack::CRUSADER_FORCE;
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }

    public function setSupplyLen($supplyLen)
    {
        $this->supplyLen = $supplyLen[0];
    }

    public function save()
    {
        $ret = parent::save();
        return $ret;
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;

    }

    public function gameEnded()
    {
        $battle = Battle::getBattle();

        $victory = $this->checkVictory(null, null);
        if(!$victory){
            $battle->gameRules->flashMessages[] = "Tie Game";
        }
        $this->gameOver = true;
        return true;
    }

    public function phaseChange()
    {

        /* @var $battle MartianCivilWar */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $forceId = $gameRules->attackingForceId;
        $turn = $gameRules->turn;

        if ($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE) {
            $gameRules->gameHasCombatResolutionMode = true;
        } else {
            $gameRules->gameHasCombatResolutionMode = false;
            $gameRules->flashMessages[] = "@hide crt";
        }


        $theUnits = $battle->force->units;


        if ($gameRules->phase == BLUE_MOVE_PHASE || $gameRules->phase == RED_MOVE_PHASE) {
            $gameRules->flashMessages[] = "@hide deadpile";
            if (!empty($battle->force->reinforceTurns->$turn->$forceId)) {
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }

            foreach ($theUnits as $id => $unit) {

                if ($unit->status == STATUS_CAN_REINFORCE ) {
                    if ($unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox") {
                        $theUnits[$id]->hexagon->parent = "deployBox";
                    }
                }
            }
        }
    }

}