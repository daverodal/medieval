/**
 * Created by david on 2/5/17.
 */
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2/5/17
 * Time: 11:51 AM

 /*
 * Copyright 2012-2017 David Rodal

 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * import {doitUnit} from "./wargame-helpers/global-funcs";
 * import {fixHeader} from "./wargame-helpers";
 *
 * import {doitUnit} from "../../wargaming/Wargame/wargame-helpers/global-funcs";
 * import {fixHeader} from "../../wargaming/Wargame/wargame-helpers";
 */

import {doitUnit} from "./wargame-helpers/global-funcs";
import {fixHeader} from "./wargame-helpers";
export var flashMessages = [];



export class GameController {
    renderCrtDetails(combat) {
    var atk = combat.attackStrength;
    var def = combat.defenseStrength;
    var div = atk / def;
    div = div.toPrecision(2);
    var ter = combat.terrainCombatEffect;
    var combatCol = combat.index + 1;

    var html = "<div id='crtDetails'>" + combat.combatLog + "</div>";
    if (this.$scope.curCrt !== 'missile') {
        html += "<div class='clear'>Attack = " + atk + " / Defender " + def + " = " + div + "</div>";
    }
    /*+ atk + " - Defender " + def + " = " + diff + "</div>";*/
    return html;
    }
    constructor($scope, $http, sync, $sce) {
        this.sync = sync;
        this.$http = $http;
        this.$scope = $scope;
        this.$sce = $sce;
        $scope.topCrt = angular.fromJson(topCrtJson);
        $scope.defaultCrt = $scope.curCrt = Object.keys($scope.topCrt.crts)[0];
        $scope.resultsNames = $scope.topCrt.resultsNames;

        $scope.maxPluses = 3;
        $scope.maxMinuses = 3;
        if($scope.topCrt.crts[$scope.curCrt].maxMinuses !== undefined) {
            $scope.maxMinuses = $scope.topCrt.crts[$scope.curCrt].maxMinuses
        }
        if($scope.topCrt.crts[$scope.curCrt].maxPluses !== undefined) {
            $scope.maxPluses = $scope.topCrt.crts[$scope.curCrt].maxPluses
        }

        $scope.units = angular.fromJson(unitsJson);
        $scope.hexesMap = {};
        $scope.unitsMap = {};
//        $scope.mouseDown = function(id,event){
//            DR.clickX = event.clientX;
//            DR.clickY = event.clientY;
//            DR.dragged = false;
//        };

        $scope.ruleUnit1 = {strength: 4, nationality: 'loyalist', class: 'inf', armorClass: 'K', maxMove: 6};

        $scope.rightClickMe = function (id, event) {
            var hex = $scope.unitsMap[id];
            var hexesMap = $scope.hexesMap;
            if (hexesMap[hex] && hexesMap[hex].length > 0) {
                var tmp = hexesMap[hex].shift();
                hexesMap[hex].push(tmp);

                for (var i in hexesMap[hex]) {
                    var unit = $scope.units[hexesMap[hex][i]];
                    unit.wrapperstyle.zIndex = i + 1;
                }
                return true;
            }
            return true;

        };

        $scope.clickMe = function (id, event) {
            /* Right clicks are handled elsewhere */
            if (event.button == 2) {
                return;
            }
            if (DR.dragged) {
                DR.dragged = false;
                return;
            }
            doitUnit(id, event);
        };

        $scope.floatMessage = {};

        $scope.dieOffset = 0;
        $scope.showDetails = false;

        $scope.toggleDetails = function () {
            $scope.showDetails = !$scope.showDetails;
        };
        $scope.$watch('dieOffset', function (newVal, oldVal) {
            if($scope.curCrt.maxMinuses !== undefined) {
                $scope.maxMinuses = $scope.topCrt.maxMinuses
            }
            if($scope.curCrt.maxPluses !== undefined) {
                $scope.maxPluses = $scope.topCrt.maxPluses
            }
            $scope.topScreen = 'rows' + ($scope.maxMinuses + (newVal - 0));
            $scope.bottomScreen = 'rows' + ($scope.maxPluses - newVal);
        });

        $scope.showCrtTable = function (table) {
            if ($scope.topCrt.crts[table].next) {
                $scope.curCrt = $scope.topCrt.crts[table].next;
            }
        }
        $scope.unHoverThis = function (unit) {
            unit.style.border = '';
            unit.style.opacity = .3;
            var pathToHere = unit.pathToHere;
            var path;
            for (path in pathToHere) {
                for (var i in $scope.moveUnits) {
                    if ($scope.moveUnits[i].hex == pathToHere[path]) {
                        $scope.moveUnits[i].style.border = '';
                        $scope.moveUnits[i].style.opacity = .3;
                    }
                }
            }
        };

        $scope.hoverHq = function (unit) {
            if (unit.class === 'hq' || unit.class === "supply") {
                $("#rangeHex" + unit.id).css({display: 'block'});
            }
        }

        $scope.unHoverHq = function (unit) {
            if (unit.class === 'hq' || unit.class === "supply") {
                if (!unit.supplyUsed) {
                    $("#rangeHex" + unit.id).css({display: 'none'});
                }
            }
        }

        $scope.hoverThis = function (unit) {
            unit.style.border = "3px solid white";
            unit.style.opacity = 1.0;
            var pathToHere = unit.pathToHere;
            var path;
            for (path in pathToHere) {
                for (var i in $scope.moveUnits) {
                    if ($scope.moveUnits[i].hex == pathToHere[path]) {
                        $scope.moveUnits[i].style.border = "3px solid white";
                        $scope.moveUnits[i].style.opacity = 1.0;
                    }
                }
            }
        };

        this.flashMessages();

        this.mapUnits();
        this.force();
        this.gameRules();



        this.combatRules();


        this.moveRules();

        this.phaseClicks();

        this.click();

        this.users();

    }

    users(){

        this.sync.register("users",  (users) => {
            var str;
            $("#users").html("");
            for (i in users) {
                str = "<li>" + users[i] + "</li>";
                $("#users").append(str);
            }
        });
    }
    click(){
        this.sync.register("click",  (click) => {
            if (this.sync.timeTravel) {
                $("#clickCnt").html('time travel ' + click);
            } else {
                $("#clickCnt").html('realtime ' + click);
            }
            window.DR.currentClick = click;
        });
    }
    flashMessages(){

        this.sync.register("flashMessages",  (messages, data) => {
            window.flashMessages = messages;
            flashMessage(data.gameRules.playerStatus);
        });
    }

    force(){
        let $scope = this.$scope;
        this.sync.register('force',  (force, data) => {
            var units = data.mapUnits;

            var showStatus = false;
            var totalAttackers = 0;
            var totalDefenders = 0;
            var i;
            var color, style, boxShadow, shadow;

            $scope.floatMessage.body = $scope.floatMessage.header = null;
//                $("#floatMessage").hide();

            for (i in units) {

                if (units[i].parent !== 'gameImages') {
                    continue;
                }
                color = "#ccc #666 #666 #ccc";
                style = "solid";
                boxShadow = "none";
                shadow = true;
                if (units[i].forceId !== force.attackingForceId) {
                    shadow = false;
                }

                if (force.requiredDefenses && force.requiredDefenses[i] === true) {

                    color = "black";
                    style = "dotted";
                    totalDefenders++;
                }
                if (units[i].isImproved === true) {
                    style = 'dotted';
                    color = 'black';
                    var colour = $("#" + i).css('color');
                    if (colour === "rgb(255, 255, 255)") {
                        color = 'white';
                    }
                }

                switch (units[i].status) {
                    case STATUS_CAN_REINFORCE:
                    case STATUS_CAN_DEPLOY:
                        color = "#ccc #666 #666 #ccc";
                        shadow = false;
                        if (units[i].reinforceTurn) {
                            shadow = true;
                        }
                        break;
                    case STATUS_READY:
                        if (units[i].forceId === force.attackingForceId) {

                            shadow = false;
                        } else {
                        }
                        if (force.requiredAttacks && force.requiredAttacks[i] === true) {
                            color = "black";
                            style = "dotted";
                            totalAttackers++;
                        }
                        break;
                    case STATUS_REINFORCING:
                    case STATUS_DEPLOYING:
                        shadow = false;
                        boxShadow = '5px 5px 5px #333';


                        break;
                    case STATUS_MOVING:
                        $("#" + i).css({zIndex: 4});
                        color = "lightgreen";
                        shadow = false;
                        DR.lastMoved = i;
                        break;

                    case STATUS_STOPPED:
                        if (i === DR.lastMoved) {
                            $("#" + i).css({zIndex: 4});
                        }
                        color = "#ccc #666 #666 #ccc";
                        break;
                    case STATUS_DEFENDING:
                        color = "orange";

                        break;
                    case STATUS_BOMBARDING:

                    case STATUS_ATTACKING:

                        shadow = false;
                        break;

                    case STATUS_CAN_RETREAT:
                        if (data.gameRules.mode == RETREATING_MODE) {
                            status = "Click on the Purple Unit to start retreating";
                        }
                        color = "purple";
                        break;
                    case STATUS_RETREATING:
                        color = "yellow";
                        if (data.gameRules.mode == RETREATING_MODE) {

                            status = "Now click on a green unit. The yellow unit will retreat there. ";
                        }
                        break;
                    case STATUS_CAN_ADVANCE:
                    case STATUS_MUST_ADVANCE:
                        if (data.gameRules.mode == ADVANCING_MODE) {
                            status = 'Click on one of the black units to advance it.';
                        }
                        color = "black";
                        if(units[i].status === STATUS_MUST_ADVANCE){
                            color = "lime";
                            status = 'Click on one of the lime units to advance it.';
                        }
                        shadow = false;

                        break;
                    case STATUS_ADVANCING:
                        if (data.gameRules.mode == ADVANCING_MODE) {

                            status = 'Now click on one of the turquoise units to advance or stay put..';
                        }

                        shadow = false;
                        color = "cyan";
                        break;

                    case STATUS_CAN_LOAD:
                        color = "fuchsia";
                        shadow = false;
                        break;
                    case STATUS_CAN_TRANSPORT:
                        color = "lime";
                        shadow = false;
                        break;

                        case STATUS_CAN_EXCHANGE:
                        if (data.gameRules.mode == EXCHANGING_MODE) {
                            var result = data.combatRules.lastResolvedCombat.combatResult;
//                    $("#floatMessage header").html(result+' Exchanging Mode');
                            status = "Click on one of the red units to reduce it."
                        }
                    case STATUS_CAN_ATTACK_LOSE:
                        if (data.gameRules.mode == ATTACKER_LOSING_MODE) {
                            status = "Click on one of the red units to reduce it."
                        }
                        color = "red";
                        break;
                    case STATUS_CAN_DEFEND_LOSE:
                        if (data.gameRules.mode == DEFENDER_LOSING_MODE) {
                            status = "Click on one of the red units to reduce it."
                        }
                        color = "red";
                        break;
                    case STATUS_REPLACED:
                        color = "blue";
                        break;
                    case STATUS_REPLACING:
                        color = "orange";
                        break;
                    case STATUS_CAN_UPGRADE:
                    case STATUS_CAN_REPLACE:
                        if (units[i].forceId === force.attackingForceId) {
                            shadow = false;
                            color = "turquoise";
                        }
                        break;

                    case STATUS_ELIMINATED:
                        break;

                }
                if (status) {
                    showStatus = true;

                    var x = $scope.mapUnits[i].wrapperstyle.left.replace(/px/, '');
                    var y = $scope.mapUnits[i].wrapperstyle.top.replace(/px/, '');
                    y /= DR.globalZoom;
                    x /= DR.globalZoom;

                    var mapWidth = $("body").width();
                    var mapHeight = $("#gameViewer").height() / DR.globalZoom;


                    var mapOffset = $("#gameImages").position().top;

                    if (mapOffset === "auto") {
                        mapOffset = 0;
                    }
                    var moveAmt;

                    if (mapOffset + y > 2 * mapHeight / 3) {
                        moveAmt = (100 + (mapOffset + y) / 3);
                        if (moveAmt > 250) {
                            moveAmt = 250;
                        }
                        y -= moveAmt;


                    } else {
                        moveAmt = (mapHeight - (mapOffset + y )) / 2;
                        if (moveAmt > 200) {
                            moveAmt = 200;
                        }
                        y += moveAmt;
                    }

                    if (DR.floatMessageDragged != true) {
                        DR.$floatMessagePanZoom.panzoom('reset');
//                            $("#floatMessage").css('top',y+"px");
//                            $("#floatMessage").css('left',x+"px");
                        $scope.floatMessage.top = y + "px";
                        $scope.floatMessage.left = x + "px";

                    }
//                        $("#floatMessage").show();
//                        $("#floatMessage p").html(status);
                    $scope.floatMessage.body = status;
                    $scope.floatMessage.header = status;
                    status = "";
                }
                if ($scope.mapUnits[i].supplyUsed) {
                    color = 'red';
                    style = 'dotted';
                    $("#rangeHex" + i).css({display: 'block', stroke: 'red'});
                }

                if($scope.mapUnits[i].carriedBy){
                    color = 'turquoise';
                    style = 'dotted';
                }

                if ($scope.mapUnits) {
                    $scope.mapUnits[i].style = {};
                    $scope.mapUnits[i].style.borderColor = color;
                    $scope.mapUnits[i].style.borderStyle = style;
                    $scope.mapUnits[i].style.boxShadow = boxShadow;

                    if (shadow) {
                        $scope.mapUnits[i].shadow = 'shadowy';
                    } else {
                        $scope.mapUnits[i].shadow = '';
                    }
                }
            }

        });

    }

    phaseClicks(){
        let $scope = this.$scope;
        this.sync.register("phaseClicks",  (clicks, data) => {
            var str = "";
            var phaseClickNames = data.gameRules.phaseClickNames;
            if (this.sync.timeTravel) {
                clicks = DR.clicks;
                phaseClickNames = DR.phaseClickNames;
            } else {
                DR.phaseClickNames = phaseClickNames;
                DR.clicks = clicks;
                DR.maxClick = data.click;
                DR.playTurnClicks = data.gameRules.playTurnClicks;
            }
            var maxClick = DR.maxClick;

            var i;
            var num = clicks.length;
            var ticker;
            ticker = clicks[0];
            var q = 0;
            for (i = 0; i < num; i++) {
                str += '<div class="newPhase"><a class="phaseClick" data-click="' + ticker + '">';
                if (data.gameRules.phaseClickNames) {
                    str += phaseClickNames[q++];
                    str += '</a><br><div class="newTick tickShim"></div>';

                }
                if (i + 1 < num) {
                    while (ticker < clicks[i + 1]) {
                        str += '<div class="newTick" data-click="' + ticker + '"><a class="phaseClick" data-click="' + ticker + '">' + ticker + '</a></div>';
                        ticker++;
                    }
                } else {
                    while (ticker <= maxClick) {
                        str += '<div class="newTick" data-click="' + ticker + '"><a class="phaseClick" data-click="' + ticker + '">' + ticker + '</a></div>';
                        ticker++;
                    }
                    if (this.sync.timeTravel) {
                        str += '<div class="newTick"><a class="phaseClick realtime" >realtime</a></div>';
                    }
                }
                str += '</div>';

            }
            $("#phaseClicks").html(str);
            var click = data.click;
            if (this.sync.timeTravel) {
                $(".newTick[data-click='" + click + "']").addClass('activeTick');
            }
        });

    }
    combatRules(){
        let $scope = this.$scope;
        let $sce = this.$sce;
        this.sync.register("combatRules",  (combatRules, data) => {
            var chattyCrt;
            var attackers;
            var i, thetas;
            for (var arrowUnits in $scope.mapUnits) {
                $scope.mapUnits[arrowUnits].arrows = {};
                $scope.mapUnits[arrowUnits].oddsDisp = null;
            }

            $scope.dieOffset = 0;
            for (var i in $scope.topCrt.crts) {
                $scope.topCrt.crts[i].selected = null;
                $scope.topCrt.crts[i].pinned = null;
                $scope.topCrt.crts[i].combatRoll = null;
            }

            $scope.crtOdds = null;
            if (data.gameRules.phase == BLUE_FIRE_COMBAT_PHASE_TWO || data.gameRules.phase == RED_FIRE_COMBAT_PHASE_TWO || data.gameRules.phase == BLUE_FIRE_COMBAT_PHASE || data.gameRules.phase == RED_FIRE_COMBAT_PHASE) {
                $scope.curCrt = 'missile';
                crtName = 'missile';
            }
            if (data.gameRules.phase == BLUE_COMBAT_PHASE || data.gameRules.phase == RED_COMBAT_PHASE) {
                crtName = $scope.curCrt = $scope.defaultCrt;
            }

            $scope.$apply();

            var title = "Combat Results ";
            var cdLine = "";
            var activeCombat = false;
            var activeCombatLine = "<div></div>";
            var crtName = $scope.curCrt;
            var str = "";


            if (combatRules) {
                var cD = combatRules.currentDefender;

                if (combatRules.combats && Object.keys(combatRules.combats).length > 0) {
                    if (cD !== false) {
                        var defenders = combatRules.combats[cD].defenders;
                        if (combatRules.combats[cD].useAlt) {
//                                showCrtTable($('#cavalryTable'));
                        } else {
                            if (combatRules.combats[cD].useDetermined) {
//                                    showCrtTable($('#determinedTable'));
                            } else {
//                                    showCrtTable($('#normalTable'));
                            }
                        }


                        if (data.gameRules.phase == BLUE_FIRE_COMBAT_PHASE || data.gameRules.phase == RED_FIRE_COMBAT_PHASE) {
                            $scope.curCrt = 'missile';
                            crtName = 'missile';
                        }

                        for (var loop in defenders) {
                            $scope.mapUnits[loop].style.borderColor = 'yellow';
                        }

                        if (!chattyCrt) {
                            $("#crt").show({effect: "blind", direction: "up"});
                            $("#crtWrapper").css('overflow', 'visible');
                            chattyCrt = true;
                        }
                        if (Object.keys(combatRules.combats[cD].attackers).length != 0) {
                            $scope.dieOffset = combatRules.combats[cD].dieOffset;
                            if (combatRules.combats[cD].pinCRT !== false) {
                                combatCol = combatRules.combats[cD].pinCRT;
                                $scope.topCrt.crts[crtName].pinned = combatCol;
                            }
                            combatCol = combatRules.combats[cD].index;
                            $scope.topCrt.crts[crtName].selected = combatCol;
                        }
                    }

                    cdLine = "";
                    var combatIndex = 0;

                    for (i in combatRules.combats) {
                        if (combatRules.combats[i].index !== null) {

                            attackers = combatRules.combats[i].attackers;
                            defenders = combatRules.combats[i].defenders;
                            thetas = combatRules.combats[i].thetas;

                            var theta = 0;
                            for (var j in attackers) {

                                var numDef = Object.keys(defenders).length;
                                for (var k in defenders) {


                                    theta = thetas[j][k];
                                    theta *= 15;
                                    theta += 180;
                                    if ($scope.mapUnits[j].facing !== undefined) {
                                        theta -= $scope.mapUnits[j].facing * 60;
                                    }

                                    $scope.mapUnits[j].arrows[k] = {};
                                    $scope.mapUnits[j].arrows[k].style = {transform: ' scale(.55,.55) rotate(' + theta + "deg) translateY(45px)"};
                                }
                            }

                            var useAltColor = combatRules.combats[i].useAlt ? " altColor" : "";

                            if (combatRules.combats[i].useDetermined) {
                                useAltColor = " determinedColor";
                            }
                            var currentCombatCol = combatRules.combats[i].index;
                            if (combatRules.combats[i].pinCRT !== false) {
                                currentCombatCol = combatRules.combats[i].pinCRT;
                                useAltColor = " pinnedColor";
                            }
                            var currentOddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];
                            for(let defender in defenders){
                                $scope.mapUnits[defender].oddsDisp = currentOddsDisp;
                                $scope.mapUnits[defender].oddsColor = useAltColor;
                            }

                            $scope.$apply();


                            if (cD !== false && cD == i) {
                                var details = this.renderCrtDetails(combatRules.combats[i]);
                                $scope.crtOdds = "odds = " + currentOddsDisp;
                                activeCombat = combatIndex;
                                activeCombatLine = details;
                            }
                            combatIndex++;
                        }

                    }
                    str += "There are " + combatIndex + " Combats";
                    if (cD !== false) {
                        attackers = combatRules.combats[cD].attackers;
                    }
                    str += "";
                    $scope.topCrt.crts[crtName].crtOddsExp = $sce.trustAsHtml(activeCombatLine);
                    $("#status").html(cdLine + str);
                    if (DR.crtDetails) {
                        $("#crtDetails").toggle();
                    }
                    $("#status").show();
                    $scope.$apply();

                } else {
                    chattyCrt = false;
                }


                var lastCombat = "";
                if (combatRules.combatsToResolve) {
                    if (combatRules.lastResolvedCombat) {
                        var finalRoll = combatRules.lastResolvedCombat.Die;
                        var orig = '';

                        if (combatRules.lastResolvedCombat.dieOffset !== undefined) {
                            orig = combatRules.lastResolvedCombat.Die + " ";

                            finalRoll += combatRules.lastResolvedCombat.dieOffset;
                            if (combatRules.lastResolvedCombat.dieOffset < 0) {
                                orig += "- " + Math.abs(combatRules.lastResolvedCombat.dieOffset);
                            } else {
                                orig += "+ " + combatRules.lastResolvedCombat.dieOffset;
                            }
                            orig += " = ";
                        }
                        title += orig + "<strong style='font-size:150%'>" + finalRoll + " " + combatRules.lastResolvedCombat.combatResult + "</strong>";
                        combatCol = combatRules.lastResolvedCombat.index + 1;

                        var combatRoll = combatRules.lastResolvedCombat.Die;
                        $scope.dieOffset = combatRules.lastResolvedCombat.dieOffset;
                        $scope.$apply();

//                                $(".col" + combatCol).css('background-color', "rgba(255,255,1,.6)");
                        $scope.topCrt.crts[crtName].selected = combatCol - 1;

                        $scope.$apply();

                        var pin = combatRules.lastResolvedCombat.pinCRT;
                        if (pin !== false) {
                            pin++;
                            if (pin < combatCol) {
                                combatCol = pin;
                                $(".col" + combatCol).css('background-color', "rgba(255, 0, 255, .6)");
                                $scope.topCrts.crts[crtName].pinned = combatCol;
                            }
                        }
                        var dieOffset = -1;
                        /* for normal 0 based crt */

                        if (combatRules.lastResolvedCombat.dieOffset !== undefined) {
                            let dieOffsetHelper = 2;

                            if(typeof $scope.topCrt.crts.normal.dieOffsetHelper !== 'undefined'){
                               dieOffsetHelper = $scope.topCrt.crts.normal.dieOffsetHelper
                            }
                            dieOffset = combatRules.lastResolvedCombat.dieOffset + dieOffsetHelper;
                        }

                        $scope.topCrt.crts[crtName].combatRoll = combatRoll + dieOffset;

                        if (combatRules.lastResolvedCombat.useAlt) {
//                                showCrtTable($('#cavalryTable'));
                        } else {
                            if (combatRules.lastResolvedCombat.useDetermined) {
//                                    showCrtTable($('#determinedTable'));
                            } else {
//                                    showCrtTable($('#normalTable'));
                            }
                        }


                        var currentCombatCol = combatRules.lastResolvedCombat.index;
                        if (combatRules.lastResolvedCombat.pinCRT !== false) {
                            currentCombatCol = combatRules.lastResolvedCombat.pinCRT;
                            useAltColor = " pinnedColor";
                        }
                        var oddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];

                        var details = this.renderCrtDetails(combatRules.lastResolvedCombat);

                        $scope.crtOdds = "odds = " + oddsDisp;
                        var newLine = details;


                        $scope.topCrt.crts[crtName].crtOddsExp = $sce.trustAsHtml(newLine);

                    }
                    str += "";
                    var noCombats = false;
                    if (Object.keys(combatRules.combatsToResolve) == 0) {
                        noCombats = true;
                        str += "0 combats to resolve";
                    }
                    var combatsToResolve = 0;
                    for (i in combatRules.combatsToResolve) {
                        combatsToResolve++;
                        if (combatRules.combatsToResolve[i].index !== null) {
                            attackers = combatRules.combatsToResolve[i].attackers;
                            defenders = combatRules.combatsToResolve[i].defenders;
                            thetas = combatRules.combatsToResolve[i].thetas;

                            var theta = 0;
                            for (var j in attackers) {
                                var numDef = Object.keys(defenders).length;
                                for (k in defenders) {
//                                        $("#"+j+ " .arrow").clone().addClass('arrowClone').addClass('arrow'+k).insertAfter("#"+j+ " .arrow").removeClass('arrow');
//                                        theta = thetas[j][k];
//                                        theta *= 15;
//                                        theta += 180;
//                                        $("#"+j+ " .arrow"+k).css({opacity: "1.0"});
//                                        $("#"+j+ " .arrow"+k).css({webkitTransform: ' scale(.55,.55) rotate('+theta+"deg) translateY(45px)"});
//                                        $("#"+j+ " .arrow"+k).css({transform: ' scale(.55,.55) rotate('+theta+"deg) translateY(45px)"});
                                }
                            }

                            var atk = combatRules.combatsToResolve[i].attackStrength;
                            var atkDisp = atk;
                            ;

                            var def = combatRules.combatsToResolve[i].defenseStrength;
                            var ter = combatRules.combatsToResolve[i].terrainCombatEffect;
                            var combatCol = combatRules.combatsToResolve[i].index + 1;
                            var useAltColor = combatRules.combatsToResolve[i].useAlt ? " altColor" : "";

                            if (combatRules.combatsToResolve[i].pinCRT !== false) {
                                combatCol = combatRules.combatsToResolve[i].pinCRT;
                            }
                            var odds = Math.floor(atk / def);
                            var useAltColor = combatRules.combatsToResolve[i].useAlt ? " altColor" : "";
                            if (combatRules.combatsToResolve[i].useDetermined) {
                                useAltColor = " determinedColor";
                            }
                            if (combatRules.combatsToResolve[i].pinCRT !== false) {
                                useAltColor = " pinnedColor";
                            }

                            var currentCombatCol = combatRules.combatsToResolve[i].index;
                            if (combatRules.combatsToResolve[i].pinCRT !== false) {
                                currentCombatCol = combatRules.combatsToResolve[i].pinCRT;
                                useAltColor = " pinnedColor";
                            }
                            var oddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];

                            $scope.mapUnits[i].oddsDisp = oddsDisp;
                            $scope.mapUnits[i].oddsColor = useAltColor;
                            $scope.$apply();
//                                $("#"+i).attr('title',oddsDisp).prepend('<div class="unitOdds'+useAltColor+'">'+oddsDisp+'</div>');;
                            var details = this.renderCrtDetails(combatRules.combatsToResolve[i]);

                            $scope.crtOdds = "odds = " + oddsDisp;
                            newLine = details;
                        }

                    }
                    if (combatsToResolve) {
//                str += "Combats To Resolve: " + combatsToResolve;
                    }
                    var resolvedCombats = 0;
                    for (i in combatRules.resolvedCombats) {
                        resolvedCombats++;
                        if (combatRules.resolvedCombats[i].index !== null) {
                            atk = combatRules.resolvedCombats[i].attackStrength;
                            atkDisp = atk;
                            ;
                            if (combatRules.storm) {
                                atkDisp = atk * 2 + " halved for storm " + atk;
                            }
                            def = combatRules.resolvedCombats[i].defenseStrength;
                            ter = combatRules.resolvedCombats[i].terrainCombatEffect;
                            var idx = combatRules.resolvedCombats[i].index + 1;
                            newLine = "";
                            if (combatRules.resolvedCombats[i].Die) {
//                                    var x = $("#" + cD).css('left').replace(/px/, "");
//                                    var mapWidth = $("body").css('width').replace(/px/, "");
                            }
                            var oddsDisp = $(".col" + combatCol).html()
                            if ($scope.curCrt !== 'missile') {
                                newLine += " Attack = " + atkDisp + " / Defender " + def + atk / def + "<br>odds = " + Math.floor(atk / def) + " : 1<br>Coooooombined Arms Shift " + ter + " = " + oddsDisp + "<br>";
                                newLine += "Roll: " + combatRules.resolvedCombats[i].Die + " result: " + combatRules.resolvedCombats[i].combatResult + "<br><br>";
                            }
                            if (cD === i) {
                                newLine = "";
                            }
                        }

                    }
                    if (!noCombats) {
                        str += "Combats: " + resolvedCombats + " of " + (resolvedCombats + combatsToResolve);
                    }
                    $("#status").html(lastCombat + str);
                    $("#status").show();

                }
            }
            $scope.title = title;
            // $("#crt h3").html(title);

            $scope.$apply();

        });

    }



    gameRules(){
        let $scope = this.$scope;
        this.sync.register("gameRules",  (gameRules, data) => {
            $(".dynamicButton").hide();
            if(DR.hasHq){
                $('#showHexes').show();
                $('#showHexes1').show();
                $('#showHexes2').show();
            }
            if (gameRules.mode === MOVING_MODE) {
                $(".movementButton").show();
            }
            if (gameRules.mode === COMBAT_SETUP_MODE) {
                $(".combatButton").show();
            }
            if (gameRules.display) {
                if (gameRules.display.currentMessage) {
                    $("#display").html(gameRules.display.currentMessage + "<button onclick='doitNext()'>Next</button>").show();
                } else {
                    $("#display").html("").hide();
                }
            }
            var status = "";
            var turn = gameRules.turn;
            var maxTurn = gameRules.maxTurn
            if ("gameTurn" + turn != $("#turnCounter").parent().attr("id")) {
                $("#gameTurn" + turn).prepend($("#turnCounter"));
            }

            var pix = turn + (turn - 1) * 36 + 1;
            var playerName = "player" + (DR.players[gameRules.attackingForceId].replace(/ /g, '-').replace(/\//gi, '_'));
            $scope.playerName = playerName;
            $scope.turn = gameRules.turn;
            $scope.maxTurn = gameRules.maxTurn;
            var removeThese = "";
            $("#header").removeClass().addClass(playerName);
            $("#turnCounter").css("background", "rgb(0,128,0)");
            $("#turnCounter").css("color", "white");

            var alsoRemoveThese = DR.players.join('@@@').trim();
            alsoRemoveThese = alsoRemoveThese.replace(/ /g, '-');
            alsoRemoveThese = alsoRemoveThese.replace(/\//g, '_');
            alsoRemoveThese = alsoRemoveThese.replace(/@@@/g, ' ');
            alsoRemoveThese = alsoRemoveThese.replace(/([^ ]+)/g, "player$1");
            removeThese += " " + alsoRemoveThese;
            $("#crt").removeClass(removeThese).addClass(playerName);
            $(".row-1,.row1,.row3,.row5,.row7,.row9,.row11,.row13").removeClass(removeThese).addClass(playerName);
            $("#revolt-table").removeClass(removeThese).addClass(playerName);

            var html = "<span id='turn'>Turn " + turn + " of " + maxTurn + "</span> ";
            var phase = gameRules.phase_name[gameRules.phase];
            phase = phase.replace(/fNameOne/, DR.playerOne);
            phase = phase.replace(/playerOneFace/, "player" + DR.playerOne.replace(/ /g, '-') + "Face");
            phase = phase.replace(/playerTwoFace/, "player" + DR.playerTwo.replace(/ /g, '-') + "Face");
            phase = phase.replace(/playerThreeFace/, "player" + DR.playerThree.replace(/ /g, '-') + "Face");
            phase = phase.replace(/playerFourFace/, "player" + DR.playerFour.replace(/ /g, '-') + "Face");

            phase = phase.replace(/fNameTwo/, DR.playerTwo);
            phase = phase.replace(/fNameThree/, DR.playerThree);
            phase = phase.replace(/fNameFour/, DR.playerFour);
            html += "<span id='phase'>" + phase;
            if (gameRules.mode_name[gameRules.mode]) {
                html += " " + gameRules.mode_name[gameRules.mode];
            }
            html += "</span>";

            switch (gameRules.phase) {
                case BLUE_REPLACEMENT_PHASE:
                case RED_REPLACEMENT_PHASE:
                case TEAL_REPLACEMENT_PHASE:
                case PURPLE_REPLACEMENT_PHASE:
                    if (gameRules.replacementsAvail !== false && gameRules.replacementsAvail != null) {
                        status = "There are " + gameRules.replacementsAvail + " available";
                    }
                    break;
            }
            switch (gameRules.mode) {
                case EXCHANGING_MODE:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

//                        $("#floatMessage header").html(result+": Exchanging Mode");
                    $scope.floatMessage.header = result + ": Exchanging Mode";

                case ATTACKER_LOSING_MODE:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result + ": Attacker Loss Mode.";


//                        $("#floatMessage header").html(result+": Attacker Loss Mode.");
//                        var floatStat = $("#floatMessage p").html();

                    $scope.floatMessage.body += " Lose at least " + data.force.exchangeAmount + " steps";
//                        $("#floatMessage p").html(floatStat);

//            html += "<br>Lose at least "+gameRules.exchangeAmount+" strength points from the units outlined in red";
                    break;

                case DEFENDER_LOSING_MODE:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result + ": Defender Loss Mode.";


//                        $("#floatMessage header").html(result+": Attacker Loss Mode.");
//                        var floatStat = $("#floatMessage p").html();

                    $scope.floatMessage.body += " Lose at least " + data.force.defenderLoseAmount + " steps";
//                        $("#floatMessage p").html(floatStat);

//            html += "<br>Lose at least "+gameRules.exchangeAmount+" strength points from the units outlined in red";
                    break
                case ADVANCING_MODE:
//            html += "<br>Click on one of the black units to advance it.<br>then  click on a hex to advance, or the unit to stay put.";
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result + ": Advancing Mode";

//                        $("#floatMessage header").html(result+": Advancing Mode");
                    break;
                case RETREATING_MODE:
                    var result = data.combatRules.lastResolvedCombat.combatResult;
                    $scope.floatMessage.header = result + ": Retreating Mode";

//                        $("#floatMessage header").html(result+": Retreating Mode");
                    break;
            }
            $("#topStatus").html(html);
            if (status) {
                $("#status").html(status);
                $("#status").show();

            } else {
                $("#status").html(status);
                $("#status").hide();

            }
        });

    }
    moveRules(){
        let $scope = this.$scope;
        this.sync.register('moveRules',  (moveRules, data) => {
            var moveUnits = [];
            var movingUnitId = moveRules.movingUnitId;
            var mapUnits = moveRules.moves;
            var newUnit;
            for (var i in mapUnits) {
                if (mapUnits[i].isOccupied) {
                    continue;
                }
                newUnit = angular.copy($scope.units[moveRules.movingUnitId]);
                newUnit.pathToHere = mapUnits[i].pathToHere;
                newUnit.pointsLeft = mapUnits[i].pointsLeft;
                newUnit.style = {};
                newUnit.style.borderColor = 'rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204)';
                newUnit.style.opacity = .6;
                newUnit.style.transform = "rotate(" + mapUnits[i].facing * 60 + "deg)";
                newUnit.style.top = mapUnits[i].pixY - 15 + "px";
                newUnit.style.left = mapUnits[i].pixX - 15 + "px";
                newUnit.hex = i;
                newUnit.id = moveRules.movingUnitId + "Hex" + i;

                moveUnits.push(newUnit);

            }
            $scope.moveUnits = moveUnits;
            $scope.$apply();
        });

    }
    mapUnits(){
        let $scope = this.$scope;
        this.sync.register('mapUnits',  (mapUnits, data) => {
            var gameUnits = {};
            var deployUnits = [];
            var retiredUnits = [];
            var notUsedUnits = [];
            var reinforcements = {};
            clearHexes();


            var hexesMap = $scope.hexesMap;
            var newUnitHexes = {};
            var unitsMap = $scope.unitsMap;
            var newHexUnits = {};
            for (var i in mapUnits) {
                var newUnit = $scope.units[i];
                Object.keys(mapUnits[i]).forEach(function (cur, index, arr) {
                    newUnit[cur] = mapUnits[i][cur];
                });
                newUnit.hq = mapUnits[i].class === "hq";
                newUnit.commandRadius = 0;
                var range = 0;
                if (mapUnits[i].class === "hq") {
                    range = mapUnits[i].commandRadius;
                    newUnit.commandRadius = ".........".slice(0, range);
                }
                newUnit.supplyRadius = 0;
                if (mapUnits[i].class === "supply") {
                    range = mapUnits[i].supplyRadius;
                }
                if (mapUnits[i].parent === 'gameImages') {
                    newUnit.shift = 0;
                    if (unitsMap[i] === undefined) {
                        unitsMap[i] = mapUnits[i].hexagon;
                        if (hexesMap[mapUnits[i].hexagon] === undefined) {
                            hexesMap[mapUnits[i].hexagon] = [];
                        }
                        hexesMap[mapUnits[i].hexagon].push(i);
                    } else {

                        if (unitsMap[i] !== mapUnits[i].hexagon) {
                            /* unit moved */
                            var dead = hexesMap[unitsMap[i]].indexOf(i);
                            hexesMap[unitsMap[i]].splice(dead, 1);
                            if (hexesMap[mapUnits[i].hexagon] === undefined) {
                                hexesMap[mapUnits[i].hexagon] = [];
                            }
                            hexesMap[mapUnits[i].hexagon].push(i);
                            unitsMap[i] = mapUnits[i].hexagon;
                        }
                    }
                    if (Object.keys(hexesMap[mapUnits[i].hexagon]).length) {
                        newUnit.shift = hexesMap[mapUnits[i].hexagon].indexOf(i) * 5;
                    } else {
                    }
                    newUnit.maxMove = mapUnits[i].maxMove;
                    newUnit.name = mapUnits[i].name;
                    newUnit.command = mapUnits[i].command;
                    newUnit.unitDesig = mapUnits[i].unitDesig;
                    newUnit.moveAmountUsed = mapUnits[i].moveAmountUsed;
                    newUnit.wrapperstyle = {};
//                        newUnit.facingstyle = {};
                    newUnit.wrapperstyle.transform = "rotate(" + mapUnits[i].facing * 60 + "deg)";
                    newUnit.wrapperstyle.top = newUnit.shift + mapUnits[i].y - 20 + "px";
                    newUnit.wrapperstyle.left = newUnit.shift + mapUnits[i].x - 20 + "px";
                    /*
                     * Blaaaaaa Very non angular way to live one's life.........
                     * Should not be removed and reinserted every mouse click.
                     * only about 8 of them so for now :'( tears will stay this way.....
                     */
                    if (mapUnits[i].class === "hq" || mapUnits[i].class === "supply") {
                        DR.hasHq = true;

                        var hexSideLen = 32.0;
                        var b = hexSideLen * .866;

                        /* jquery way */
                        drawHex(b * (range * 2 + 1), mapUnits[i]);
                    }
                    newUnit.wrapperstyle.zIndex = newUnit.shift + 1;
                    newUnit.facing = mapUnits[i].facing;
                    newUnit.strength = mapUnits[i].strength;
                    newUnit.steps = mapUnits[i].steps;
                    newUnit.orgStatus = mapUnits[i].orgStatus;
                    var orgDisp = newUnit.orgStatus == 0 ? 'B' : 'D';
                    if(mapUnits[i].forceMarch){
                        orgDisp = 'M';
                    }
                    newUnit.unitNumbers = newUnit.strength + ' ' + orgDisp + ' ' + (newUnit.maxMove - newUnit.moveAmountUsed);
                    newUnit.infoLen = "infoLen" + newUnit.unitNumbers.length;
                    gameUnits[i] = newUnit;

                } else {
                    if (unitsMap[i] !== undefined) {
                        var dead = hexesMap[unitsMap[i]].indexOf(i);
                        hexesMap[unitsMap[i]].splice(dead, 1);
                        unitsMap[i] = undefined;
                    }
                }
                if (mapUnits[i].parent === 'deployBox') {
                    newUnit.wrapperstyle = {};
                    newUnit.style = {};
                    newUnit.oddsDisp = null;
                    newUnit.strength = mapUnits[i].strength;


                    newUnit.strength = mapUnits[i].strength;
                    newUnit.steps = mapUnits[i].steps;
                    newUnit.orgStatus = mapUnits[i].orgStatus;
                    var orgDisp = newUnit.orgStatus == 0 ? 'B' : 'D';

                    if (mapUnits[i].status == STATUS_DEPLOYING || mapUnits[i].status == STATUS_REINFORCING) {
                        newUnit.style.boxShadow = "5px 5px 5px #333";
                    }

                    deployUnits.push(newUnit);
                }

                if (mapUnits[i].parent.match(/gameTurn/)) {
                    if (reinforcements[mapUnits[i].parent] === undefined) {
                        reinforcements[mapUnits[i].parent] = [];
                    }
                    reinforcements[mapUnits[i].parent].push(newUnit);
                }
                if (mapUnits[i].parent === 'deadpile') {
                    newUnit.style = {};
                    newUnit.strength = mapUnits[i].strength;
                    newUnit.style.borderColor = 'rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204)';
                    retiredUnits.push(newUnit);
                }
            }
            $scope.mapUnits = gameUnits;
            $scope.deployUnits = deployUnits;
            $scope.retiredUnits = retiredUnits;
            $scope.notUsedUnits = notUsedUnits;
            $scope.reinforcements = reinforcements;

            $scope.$apply();
        });

    }


}

function clearHexes(){
    $('#arrow-svg .range-hex').remove();
}
function drawHex(hexside, unit, isShort){

    var decoration = isShort || "";
    var c = hexside - 0;
    var a = (c / 2);
    var b = .866 * c;
    var ac = a+c;
    var x = unit.x;
    var y = unit.y;
    var id = unit.id+decoration;
    var nat = DR.players[unit.forceId];
    nat = nat.replace(/ /g,'-').replace(/\//gi,'_');
    var type= nat+'-'+unit.class;
    var cls = unit.class;
    var width = 2;
    var strokeDash = "1,0";

    if(unit.range > 7){
        width = 4;
        strokeDash = "5,5";
    }
    if(unit.range > 11){
        width = 6;
        strokeDash = "1,10";
    }

    x = x - b;
    y = y - c;

    var hClass = '';
    if(unit.forceId === 1 && DR.showHexes1){
        hClass = 'hovering';
    }
    if(unit.forceId === 2 && DR.showHexes2){
        hClass = 'hovering';
    }
    var path = '<path stroke-dasharray="'+strokeDash+'" class="range-hex '+nat+' '+decoration+' '+hClass+' '+cls+' forceId'+unit.forceId+'" stroke="transparent" id="rangeHex'+id+'" fill="#000" fill-opacity="0" stroke-width="'+width+'" d="M '+x+' ' + (ac + y) + ' L ' + x + ' '+ (a + y) + ' L ' + (b + x) + ' ' + y;
    path += ' L ' + (2 * b + x) + ' ' + (a + y) + ' L ' + (2 * b + x) + ' ' + (ac + y) + ' L ' + (b + x) + ' '+ (2 * c + y)+' Z"></path>';

    $('#arrow-svg').append(path);
    $('#arrow-svg').html($('#arrow-svg').html());
}
function flashMessage(playerStatus) {
    var x = 100;
    var y = 200;
    fixHeader();
    var mess = window.flashMessages.shift();
    $("#FlashMessage").remove();
    var fadeOut = 2800;
    while (mess) {

        if (mess.match(/^@/)) {
            if (mess.match(/^@hex/)) {

                var hexPos = mess.replace(/\.\d*/g, '');
                var x = hexPos.match(/x(\d*)y/)[1] - 0;
                var y = hexPos.match(/y(\d*)\D*/)[1] - 0;

                var newHtml;
                newHtml = '<img src="'+Const_line21+'" class="row-hex">';
                $("#gameImages").append('<div id="FlashMessage" class="mapFlashSymbols">' + newHtml + '</div>');
                $("#FlashMessage").css({top: y + "px", left: x + "px"});
                $("#FlashMessage img").animate({
                    opacity: 0.2,
                    width: 190,
                    marginLeft: (190 - 71) / -2 + "px",
                    marginTop: (190 - 71) / -2 + "px"
                }, fadeOut)
                    .animate({opacity: 1, width: 71, marginLeft: 0, marginTop: 0}, 0).animate({
                    opacity: 0.2,
                    width: 190,
                    marginLeft: (190 - 71) / -2 + "px",
                    marginTop: (190 - 71) / -2 + "px"
                }, fadeOut)
                    .animate({opacity: 1, width: 71, marginLeft: 0, marginTop: 0}, 0).animate({
                    opacity: 0.2,
                    width: 190,
                    marginLeft: (190 - 71) / -2 + "px",
                    marginTop: (190 - 71) / -2 + "px"
                }, fadeOut)
                    .animate({opacity: 1, width: 71, marginLeft: 0, marginTop: 0}, 0).animate({
                    opacity: 0.2,
                    width: 190,
                    marginLeft: (190 - 71) / -2 + "px",
                    marginTop: (190 - 71) / -2 + "px"
                }, fadeOut, flashMessage);
                return;
            }
            var showRegex = new RegExp('^@'+'show');
            if (mess.match(showRegex)) {
                showRegex = new RegExp('^@'+'show ([^,]*)');
                var game = mess.match(showRegex);
                id = game[1];
                $("#" + id).show({effect: "blind", direction: "up", complete: flashMessage});
                return;
            }
            if (mess.match(/^@hide/)) {
                game = mess.match(/^@hide ([^,]*)/);
                var id = game[1];
                $("#" + id).hide({effect: "blind", direction: "up", complete: flashMessage});
                return;
            }
            if (mess.match(/^@gameover/)) {
                $("#gameViewer").append('<div id="FlashMessage" style="top:' + y + 'px;left:' + x + 'px;" class="flashMessage">' + "Game Over" + '</div>');
                $("#FlashMessage").animate({opacity: 0}, fadeOut, flashMessage);
                return;
            }
        }
        $("#main-viewer").append('<div id="FlashMessage" style="top:' + y + 'px;left:' + x + 'px;" class="flashMessage">' + mess + '</div>');
        $("#FlashMessage").animate({opacity: 0}, fadeOut, flashMessage);
        return;
    }
}

GameController.$inject =     ['$scope', '$http', 'sync', '$sce']

export  class SubGameController  extends GameController{
    moveRules(){
        console.log("SUper");
        super.moveRules();
    }

}
