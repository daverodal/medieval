<?php
namespace Wargame\Medieval\Lewes1264;
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


        if(in_array($mapHexName,$battle->specialHexA)){
            $vp = 1;

            $prevForceId = $battle->mapData->specialHexes->$mapHexName;
            if ($forceId == LOYALIST_FORCE) {
                $this->victoryPoints[LOYALIST_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='rebel'>+$vp Loyalist vp</span>";
                $this->victoryPoints[REBEL_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='rebel'> -$vp Loyalist vp</span>";
            }
            if ($forceId == REBEL_FORCE) {
                $this->victoryPoints[REBEL_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='loyalist'>+$vp Rebel vp</span>";
                $this->victoryPoints[LOYALIST_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='loyalist'> -$vp Rebel vp</span>";
            }
        }

    }

    public function reduceUnit($args)
    {
        $unit = $args[0];

        $vp = $unit->damage;

        if ($unit->forceId == 1) {
            $victorId = 2;
            $this->victoryPoints[$victorId] += $vp;
            $hex = $unit->hexagon;
            $battle = Battle::getBattle();
            if($hex->name) {
                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='loyalistVictoryPoints'>+$vp vp</span>";
            }
        } else {
            $victorId = 1;
            $hex  = $unit->hexagon;
            $vp += $this->outgoingVP[$victorId];
            $this->outgoingVP[$victorId] = $vp;
            $battle = Battle::getBattle();
            if($hex->name) {

                $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='rebelVictoryPoints'>+$vp vp</span>";
            }
            $this->victoryPoints[$victorId] += $vp;
        }
    }

    public function gameEnded()
    {
        $battle = Battle::getBattle();
        if ($this->victoryPoints[REBEL_FORCE] > $this->victoryPoints[LOYALIST_FORCE]) {
            $battle->gameRules->flashMessages[] = "Rebel Player Wins";
            $this->winner = REBEL_FORCE;
        }
        if ($this->victoryPoints[LOYALIST_FORCE] > $this->victoryPoints[REBEL_FORCE]) {
            $battle->gameRules->flashMessages[] = "Loyalist Player Wins";
            $this->winner = LOYALIST_FORCE;
        }
        if ($this->victoryPoints[REBEL_FORCE] == $this->victoryPoints[LOYALIST_FORCE]) {
            $battle->gameRules->flashMessages[] = "Tie Game";
        }
        $this->gameOver = true;
        return true;
    }

}