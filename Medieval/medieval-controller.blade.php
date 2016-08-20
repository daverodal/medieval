<script>
    var lobbyApp = angular.module('lobbyApp', ['ngRightClick']);
    lobbyApp.controller('LobbyController', ['$scope', '$http', 'sync', '$sce', function($scope, $http, sync, $sce){
        $scope.topCrt = angular.fromJson('{!!json_encode($topCrt)!!}');
        $scope.defaultCrt = $scope.curCrt = Object.keys($scope.topCrt.crts)[0];
        $scope.resultsNames = $scope.topCrt.resultsNames;

        $scope.units = angular.fromJson('{!!json_encode($units)!!}');
        $scope.hexesMap = {};
        $scope.unitsMap = {};
        $scope.mouseDown = function(id,event){
            DR.clickX = event.clientX;
            DR.clickY = event.clientY;
            DR.dragged = false;
        };

        $scope.rightClickMe = function(id, event){
            var hex = $scope.unitsMap[id];
            var hexesMap = $scope.hexesMap;
            if(hexesMap[hex] && hexesMap[hex].length > 0){
                var tmp = hexesMap[hex].shift();
                hexesMap[hex].push(tmp);

                for(var i in hexesMap[hex]){
                    var unit = $scope.units[hexesMap[hex][i]];
                    unit.wrapperstyle.zIndex = i + 1;
                    var shift = unit.shift;
                    var top = unit.wrapperstyle.top.replace(/px/,'');
                    var left = unit.wrapperstyle.left.replace(/px/,'');
                    unit.wrapperstyle.top = (top - shift + i * 5) + "px";
                    unit.wrapperstyle.left = (left - shift + i * 5) + "px";
                    unit.shift = i * 5;
                }
                return true;
            }
            return true;

        };

        $scope.clickMe = function(id, event){
            /* Right clicks are handled elsewhere */
            if(event.button == 2){
                return;
            }
            if(DR.dragged){
                DR.dragged = false;
                return;
            }
            doitUnit(id, event);
        };

        $scope.floatMessage = {};

        $scope.dieOffset = 0;
        $scope.showDetails = false;

        $scope.toggleDetails = function(){
            $scope.showDetails = !$scope.showDetails;
        };
        $scope.$watch('dieOffset', function(newVal, oldVal){
            $scope.topScreen = 'rows'+(3+(newVal-0));
            $scope.bottomScreen = 'rows'+(3-newVal);
        });

        $scope.showCrtTable = function(table){
            if($scope.topCrt.crts[table].next){
                $scope.curCrt = $scope.topCrt.crts[table].next;
            }
        }
        $scope.unHoverThis = function(unit){
            unit.style.border = '';
            unit.style.opacity = .3;
            var pathToHere = unit.pathToHere;
            for(path in pathToHere) {
                for (var i in $scope.moveUnits) {
                    if ($scope.moveUnits[i].hex == pathToHere[path]){
                        $scope.moveUnits[i].style.border= '';
                        $scope.moveUnits[i].style.opacity = .3;
                    }
                }
            }
        };

        $scope.hoverHq = function(unit){
            if(unit.class === 'hq'){
                $("#rangeHex"+unit.id).css({display:'block'});
            }
        }

        $scope.unHoverHq = function(unit){
            if(unit.class === 'hq'){
                $("#rangeHex"+unit.id).css({display:'none'});
            }
        }

        $scope.hoverThis = function(unit){
            unit.style.border = "3px solid purple";
            unit.style.opacity = 1.0;
            var pathToHere = unit.pathToHere;
            for(path in pathToHere) {
                for (var i in $scope.moveUnits) {
                    if ($scope.moveUnits[i].hex == pathToHere[path]){
                        $scope.moveUnits[i].style.border = "3px solid pink";
                        $scope.moveUnits[i].style.opacity = 1.0;
                    }
                }
            }
        };

        sync.register('mapUnits', function(mapUnits,data){
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
            for(var i in mapUnits) {
                var newUnit = $scope.units[i];
                newUnit.hq = mapUnits[i].class === "hq";
                newUnit.commandRadius = 0;
                if(mapUnits[i].class === "hq"){
                    var range = mapUnits[i].commandRadius;
                    newUnit.commandRadius = ".........".slice(0,range);
                }
                if(mapUnits[i].parent === 'gameImages') {
                    newUnit.shift = 0;
                    if (unitsMap[i] === undefined) {
                            unitsMap[i] = mapUnits[i].hexagon;
                            if(hexesMap[mapUnits[i].hexagon] === undefined){
                                hexesMap[mapUnits[i].hexagon] = [];
                            }
                        hexesMap[mapUnits[i].hexagon].push(i);
                    } else {

                        if (unitsMap[i] !== mapUnits[i].hexagon) {
                            /* unit moved */
                            var dead = hexesMap[unitsMap[i]].indexOf(i);
                            hexesMap[unitsMap[i]].splice(dead,1);
                            if(hexesMap[mapUnits[i].hexagon] === undefined){
                                hexesMap[mapUnits[i].hexagon] = [];
                            }
                            hexesMap[mapUnits[i].hexagon].push(i);
                            unitsMap[i] = mapUnits[i].hexagon;
                        }
                    }
                    if(Object.keys(hexesMap[mapUnits[i].hexagon]).length){
                        newUnit.shift = hexesMap[mapUnits[i].hexagon].indexOf(i) * 5;
                    }else{
                    }
                    newUnit.maxMove = mapUnits[i].maxMove;
                    newUnit.command = mapUnits[i].command;
                    newUnit.unitDesig = mapUnits[i].unitDesig;
                    newUnit.moveAmountUsed = mapUnits[i].moveAmountUsed;
                    newUnit.wrapperstyle = {};
//                        newUnit.facingstyle = {};
                    newUnit.wrapperstyle.transform = "rotate("+mapUnits[i].facing*60+"deg)";
                    newUnit.wrapperstyle.top = newUnit.shift + mapUnits[i].y-20+"px";
                    newUnit.wrapperstyle.left = newUnit.shift + mapUnits[i].x-20+"px";
                    /*
                     * Blaaaaaa Very non angular way to live one's life.........
                     * Should not be removed and reinserted every mouse click.
                     * only about 8 of them so for now :'( tears will stay this way.....
                     */
                    if(mapUnits[i].class === "hq"){

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
                    var orgDisp = newUnit.orgStatus == 0 ? 'B':'D';
                    newUnit.unitNumbers = newUnit.strength + ' ' + orgDisp + ' ' + (newUnit.maxMove - newUnit.moveAmountUsed);
                    newUnit.infoLen = "infoLen" + newUnit.unitNumbers.length;
                    gameUnits[i] = newUnit;

                }else{
                    if(unitsMap[i] !== undefined){
                        var dead = hexesMap[unitsMap[i]].indexOf(i);
                        hexesMap[unitsMap[i]].splice(dead,1);
                        unitsMap[i] = undefined;
                    }
                }
                if(mapUnits[i].parent === 'deployBox'){
                    newUnit.wrapperstyle = {};
                    newUnit.style = {float:'left'};
                    newUnit.oddsDisp = null;
                    newUnit.strength = mapUnits[i].strength;


                    newUnit.strength = mapUnits[i].strength;
                    newUnit.steps = mapUnits[i].steps;
                    newUnit.orgStatus = mapUnits[i].orgStatus;
                    var orgDisp = newUnit.orgStatus == 0 ? 'B':'D';

                    if(mapUnits[i].status == <?=STATUS_DEPLOYING?> || mapUnits[i].status == <?=STATUS_REINFORCING?>){
                        newUnit.style.boxShadow = "5px 5px 5px #333";
                    }

                    deployUnits.push(newUnit);
                }

                if(mapUnits[i].parent.match(/gameTurn/)){
                    if(reinforcements[mapUnits[i].parent] === undefined){
                        reinforcements[mapUnits[i].parent] = [];
                    }
                    reinforcements[mapUnits[i].parent].push(newUnit);
                }
                if(mapUnits[i].parent === 'deadpile'){
                    newUnit.style = {float:'left'};
                    newUnit.strength = mapUnits[i].strength;
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

        sync.register('force', function(force,data){
            var units = data.mapUnits;

            var showStatus = false;
            var totalAttackers = 0;
            var totalDefenders = 0;

            $scope.floatMessage.body = $scope.floatMessage.header = null;
//                $("#floatMessage").hide();

            for (i in units) {
                if(units[i].parent !== 'gameImages'){
                    continue;
                }
                color = "#ccc #666 #666 #ccc";
                style = "solid";
                boxShadow = "none";
                shadow = true;
                if (units[i].forceId !== force.attackingForceId) {
                    shadow = false;
                }
                if (units[i].forceMarch) {
                    $("#" + i + " .forceMarch").show();
                    $("#" + i + " .range").hide();
                } else {
                    $("#" + i + " .forceMarch").hide();
                    $("#" + i + " .range").show();
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
                    case <?=STATUS_CAN_REINFORCE?>:
                    case <?=STATUS_CAN_DEPLOY?>:
                        color = "#ccc #666 #666 #ccc";
                        shadow = false;
                        if (units[i].reinforceTurn) {
                            shadow = true;
                        }
                        break;
                    case <?=STATUS_READY?>:
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
                    case <?=STATUS_REINFORCING?>:
                    case <?=STATUS_DEPLOYING?>:
                        shadow = false;
                        boxShadow = '5px 5px 5px #333';


                        break;
                    case <?=STATUS_MOVING?>:
                        if (units[i].forceMarch) {
                            $("#" + i + " .forceMarch").show();
                            $("#" + i + " .range").hide();

                            color = "#f00 #666 #666 #f00";
                        } else {
                            $("#" + i + " .forceMarch").hide();
                            $("#" + i + " .range").show();

                            color = "#ccc #666 #666 #ccc";

                        }
                        $("#" + i).css({zIndex: 4});
                        color = "lightgreen";
                        shadow = false;
                        DR.lastMoved = i;
                        break;

                    case <?=STATUS_STOPPED?>:
                        if (i === DR.lastMoved) {
                            $("#" + i).css({zIndex: 4});
                        }
                        color = "#ccc #666 #666 #ccc";
                        break;
                    case <?=STATUS_DEFENDING?>:
                        color = "orange";

                        break;
                    case <?=STATUS_BOMBARDING?>:

                    case <?=STATUS_ATTACKING?>:

                        shadow = false;
                        break;

                    case <?=STATUS_CAN_RETREAT?>:
                        if (data.gameRules.mode == <?=RETREATING_MODE?>) {
                            status = "Click on the Purple Unit to start retreating";
                        }
                        color = "purple";
                        break;
                    case <?=STATUS_RETREATING?>:
                        color = "yellow";
                        if (data.gameRules.mode == <?=RETREATING_MODE?>) {

                            status = "Now click on a green unit. The yellow unit will retreat there. ";
                        }
                        break;
                    case <?=STATUS_CAN_ADVANCE?>:
                        if (data.gameRules.mode == <?=ADVANCING_MODE?>) {
                            status = 'Click on one of the black units to advance it.';
                        }
                        color = "black";
                        shadow = false;

                        break;
                    case <?=STATUS_ADVANCING?>:
                        if (data.gameRules.mode == <?=ADVANCING_MODE?>) {

                            status = 'Now click on one of the turquoise units to advance or stay put..';
                        }

                        shadow = false;
                        color = "cyan";
                        break;
                    case <?=STATUS_CAN_EXCHANGE?>:
                        if (data.gameRules.mode == <?=EXCHANGING_MODE?>) {
                            var result = data.combatRules.lastResolvedCombat.combatResult;
//                    $("#floatMessage header").html(result+' Exchanging Mode');
                            status = "Click on one of the red units to reduce it."
                        }
                    case <?=STATUS_CAN_ATTACK_LOSE?>:
                        if (data.gameRules.mode == <?=ATTACKER_LOSING_MODE?>) {
                            status = "Click on one of the red units to reduce it."
                        }
                        color = "red";
                        break;
                    case <?=STATUS_CAN_DEFEND_LOSE?>:
                        if (data.gameRules.mode == <?=DEFENDER_LOSING_MODE?>) {
                            status = "Click on one of the red units to reduce it."
                        }
                        color = "red";
                        break;
                    case <?=STATUS_REPLACED?>:
                        color = "blue";
                        break;
                    case <?=STATUS_REPLACING?>:
                        color = "orange";
                        break;
                    case <?=STATUS_CAN_UPGRADE?>:
                    case <?=STATUS_CAN_REPLACE?>:
                        if (units[i].forceId === force.attackingForceId) {
                            shadow = false;
                            color = "turquoise";
                        }
                        break;

                    case <?=STATUS_ELIMINATED?>:
                        break;

                }
                if(status){
                    showStatus = true;

                    var x = $scope.mapUnits[i].wrapperstyle.left.replace(/px/,'');
                    var y = $scope.mapUnits[i].wrapperstyle.top.replace(/px/,'');
                    y /= DR.globalZoom;
                    x /= DR.globalZoom;

                    var mapWidth = $("body").width();
                    var mapHeight = $("#gameViewer").height() / DR.globalZoom;


                    var mapOffset  = $("#gameImages").position().top;

                    if(mapOffset === "auto"){
                        mapOffset = 0;
                    }
                    var moveAmt;

                    if(mapOffset + y > 2*mapHeight/3){
                        moveAmt = (100 + (mapOffset + y)/3);
                        if(moveAmt > 250){
                            moveAmt = 250;
                        }
                        y -= moveAmt;


                    }else{
                        moveAmt = (mapHeight - (mapOffset + y ))/2;
                        if(moveAmt > 200){
                            moveAmt = 200;
                        }
                        y += moveAmt;
                    }

                    if(DR.floatMessageDragged != true){
                        DR.$floatMessagePanZoom.panzoom('reset');
//                            $("#floatMessage").css('top',y+"px");
//                            $("#floatMessage").css('left',x+"px");
                        $scope.floatMessage.top = y+"px";
                        $scope.floatMessage.left = x+"px";

                    }
//                        $("#floatMessage").show();
//                        $("#floatMessage p").html(status);
                    $scope.floatMessage.body = status;
                    $scope.floatMessage.header = status;
                    status = "";
                }

                $scope.mapUnits[i].style = {};
                $scope.mapUnits[i].style.borderColor = color;
                $scope.mapUnits[i].style.borderStyle = style;
                $scope.mapUnits[i].style.boxShadow = boxShadow;

                if(shadow){
                    $scope.mapUnits[i].shadow = 'shadowy';
                }else{
                    $scope.mapUnits[i].shadow = '';
                }
            }

        });
        x.register("gameRules", function(gameRules,data) {
            $(".dynamicButton").hide();
            if(gameRules.mode === <?= MOVING_MODE?>){
                $(".movementButton").show();
            }
            if(gameRules.mode === <?= COMBAT_SETUP_MODE?>){
                $(".combatButton").show();
            }
            if(gameRules.display) {
                if(gameRules.display.currentMessage){
                    $("#display").html(gameRules.display.currentMessage+"<button onclick='doitNext()'>Next</button>").show();
                }else{
                    $("#display").html("").hide();
                }
            }
            var status = "";
            var turn = gameRules.turn;
            var maxTurn = gameRules.maxTurn
            if("gameTurn"+turn != $("#turnCounter").parent().attr("id")){
                $("#gameTurn"+turn).prepend($("#turnCounter"));
            }

            var pix = turn  + (turn - 1) * 36 + 1;
            var playerName = "player"+(DR.players[gameRules.attackingForceId].replace(/ /g,'-').replace(/\//gi,'_'));
            $scope.playerName = playerName;
            var removeThese = "";
            $("#header").removeClass().addClass(playerName);
            $("#turnCounter").css("background","rgb(0,128,0)");
            $("#turnCounter").css("color","white");

            var alsoRemoveThese = DR.players.join('@@@').trim();
            alsoRemoveThese = alsoRemoveThese.replace(/ /g,'-');
            alsoRemoveThese = alsoRemoveThese.replace(/\//g,'_');
            alsoRemoveThese = alsoRemoveThese.replace(/@@@/g,' ');
                    alsoRemoveThese = alsoRemoveThese.replace(/([^ ]+)/g,"player$1");
            removeThese += " "+alsoRemoveThese;
            $("#crt").removeClass(removeThese).addClass(playerName);
            $(".row-1,.row1,.row3,.row5,.row7,.row9,.row11,.row13").removeClass(removeThese).addClass(playerName);
            $("#revolt-table").removeClass(removeThese).addClass(playerName);

            var html = "<span id='turn'>Turn "+turn+" of "+maxTurn+"</span> ";
            var phase = gameRules.phase_name[gameRules.phase];
            phase = phase.replace(/fNameOne/,DR.playerOne);
            phase = phase.replace(/playerOneFace/,"player"+DR.playerOne.replace(/ /g,'-')+"Face");
            phase = phase.replace(/playerTwoFace/,"player"+DR.playerTwo.replace(/ /g,'-')+"Face");
            phase = phase.replace(/playerThreeFace/,"player"+DR.playerThree.replace(/ /g,'-')+"Face");
            phase = phase.replace(/playerFourFace/,"player"+DR.playerFour.replace(/ /g,'-')+"Face");

            phase = phase.replace(/fNameTwo/,DR.playerTwo);
            phase = phase.replace(/fNameThree/,DR.playerThree);
            phase = phase.replace(/fNameFour/,DR.playerFour);
            html += "<span id='phase'>"+phase;
            if(gameRules.mode_name[gameRules.mode]){
                html += " "+gameRules.mode_name[gameRules.mode];
            }
            html += "</span>";

            switch(gameRules.phase){
                case <?=BLUE_REPLACEMENT_PHASE?>:
                case <?=RED_REPLACEMENT_PHASE?>:
                case <?=TEAL_REPLACEMENT_PHASE?>:
                case <?=PURPLE_REPLACEMENT_PHASE?>:
                    if(gameRules.replacementsAvail !== false && gameRules.replacementsAvail != null){
                        status = "There are "+gameRules.replacementsAvail+" available";
                    }
                    break;
            }
            switch(gameRules.mode){
                case <?=EXCHANGING_MODE?>:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

//                        $("#floatMessage header").html(result+": Exchanging Mode");
                    $scope.floatMessage.header = result+": Exchanging Mode";

                case <?=ATTACKER_LOSING_MODE?>:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result+": Attacker Loss Mode.";


//                        $("#floatMessage header").html(result+": Attacker Loss Mode.");
//                        var floatStat = $("#floatMessage p").html();

                    $scope.floatMessage.body += " Lose at least "+data.force.exchangeAmount+ " steps";
//                        $("#floatMessage p").html(floatStat);

//            html += "<br>Lose at least "+gameRules.exchangeAmount+" strength points from the units outlined in red";
                    break;

                case <?=DEFENDER_LOSING_MODE?>:
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result+": Defender Loss Mode.";


//                        $("#floatMessage header").html(result+": Attacker Loss Mode.");
//                        var floatStat = $("#floatMessage p").html();

                    $scope.floatMessage.body += " Lose at least "+data.force.defenderLoseAmount+ " steps";
//                        $("#floatMessage p").html(floatStat);

//            html += "<br>Lose at least "+gameRules.exchangeAmount+" strength points from the units outlined in red";
                    break
                case <?=ADVANCING_MODE?>:
//            html += "<br>Click on one of the black units to advance it.<br>then  click on a hex to advance, or the unit to stay put.";
                    var result = data.combatRules.lastResolvedCombat.combatResult;

                    $scope.floatMessage.header = result+": Advancing Mode";

//                        $("#floatMessage header").html(result+": Advancing Mode");
                    break;
                case <?=RETREATING_MODE?>:
                    var result = data.combatRules.lastResolvedCombat.combatResult;
                    $scope.floatMessage.header = result+": Retreating Mode";

//                        $("#floatMessage header").html(result+": Retreating Mode");
                    break;
            }
            $("#topStatus").html(html);
            if(status){
                $("#status").html(status);
                $("#status").show();

            }else{
                $("#status").html(status);
                $("#status").hide();

            }
        });

        function renderCrtDetails(combat){
            var atk = combat.attackStrength;
            var def = combat.defenseStrength;
            var div = atk / def;
            div = div.toPrecision(2);
            var ter = combat.terrainCombatEffect;
            var combatCol = combat.index + 1;

            var html = "<div id='crtDetails'>"+combat.combatLog+"</div>";
            if($scope.curCrt !== 'missile'){
                html += "<div class='clear'>Attack = " + atk + " / Defender " + def + " = " + div + "</div>";
            }
            /*+ atk + " - Defender " + def + " = " + diff + "</div>";*/
            return html;
        }
@section('combat-rules-controller')
        x.register("combatRules", function(combatRules, data){
            for(var arrowUnits in $scope.mapUnits){
                $scope.mapUnits[arrowUnits].arrows = {};
                $scope.mapUnits[arrowUnits].oddsDisp = null;
            }

            $scope.dieOffset = 0;
            for(var i in $scope.topCrt.crts){
                $scope.topCrt.crts[i].selected = null;
                $scope.topCrt.crts[i].pinned = null;
                $scope.topCrt.crts[i].combatRoll = null;
            }

            $scope.crtOdds = null;
            if(data.gameRules.phase == <?= BLUE_FIRE_COMBAT_PHASE_TWO?> || data.gameRules.phase == <?= RED_FIRE_COMBAT_PHASE_TWO?> || data.gameRules.phase == <?= BLUE_FIRE_COMBAT_PHASE?> || data.gameRules.phase == <?= RED_FIRE_COMBAT_PHASE?>){
                $scope.curCrt = 'missile';
                crtName = 'missile';
            }
            if(data.gameRules.phase == <?= BLUE_COMBAT_PHASE?> || data.gameRules.phase == <?= RED_COMBAT_PHASE?>){
                crtName = $scope.curCrt = $scope.defaultCrt;
            }

            $scope.$apply();

            var title = "Combat Results ";
            var cdLine = "";
            var activeCombat = false;
            var activeCombatLine = "<div></div>";
            var crtName = $scope.curCrt;
            var str = "";


            if(combatRules){
                cD = combatRules.currentDefender;

                if(combatRules.combats && Object.keys(combatRules.combats).length > 0){
                    if(cD !== false){
                        var defenders = combatRules.combats[cD].defenders;
                        if(combatRules.combats[cD].useAlt){
//                                showCrtTable($('#cavalryTable'));
                        }else{
                            if(combatRules.combats[cD].useDetermined){
//                                    showCrtTable($('#determinedTable'));
                            }else{
//                                    showCrtTable($('#normalTable'));
                            }
                        }


                        if(data.gameRules.phase == <?= BLUE_FIRE_COMBAT_PHASE?> || data.gameRules.phase == <?= RED_FIRE_COMBAT_PHASE?>){
                            $scope.curCrt = 'missile';
                            crtName = 'missile';
                        }

                        for(var loop in defenders){
                            $scope.mapUnits[loop].style.borderColor = 'yellow';
                        }

                        if(!chattyCrt){
                            $("#crt").show({effect:"blind",direction:"up"});
                            $("#crtWrapper").css('overflow', 'visible');
                            chattyCrt = true;
                        }
                        if(Object.keys(combatRules.combats[cD].attackers).length != 0){
                            $scope.dieOffset = combatRules.combats[cD].dieOffset;
                            if(combatRules.combats[cD].pinCRT !== false){
                                combatCol = combatRules.combats[cD].pinCRT;
                                $scope.topCrt.crts[crtName].pinned = combatCol;
                            }
                            combatCol = combatRules.combats[cD].index;
                            $scope.topCrt.crts[crtName].selected  = combatCol;
                        }
                    }

                    cdLine = "";
                    var combatIndex = 0;

                    for(i in combatRules.combats){
                        if(combatRules.combats[i].index !== null){


                            attackers = combatRules.combats[i].attackers;
                            defenders = combatRules.combats[i].defenders;
                            thetas = combatRules.combats[i].thetas;

                            var theta = 0;
                            for(var j in attackers){

                                var numDef = Object.keys(defenders).length;
                                for(k in defenders){


                                    theta = thetas[j][k];
                                    theta *= 15;
                                    theta += 180;
                                    if($scope.mapUnits[j].facing !== undefined){
                                        theta -= $scope.mapUnits[j].facing * 60;
                                    }

                                    $scope.mapUnits[j].arrows[k] = {};
                                    $scope.mapUnits[j].arrows[k].style = {transform:' scale(.55,.55) rotate(' + theta + "deg) translateY(45px)" };
                                }
                            }

                            var useAltColor = combatRules.combats[i].useAlt ? " altColor":"";

                            if(combatRules.combats[i].useDetermined){
                                useAltColor = " determinedColor";
                            }
                            var currentCombatCol = combatRules.combats[i].index;
                            if(combatRules.combats[i].pinCRT !== false){
                                currentCombatCol = combatRules.combats[i].pinCRT;
                                useAltColor = " pinnedColor";
                            }
                            var currentOddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];
                            $scope.mapUnits[i].oddsDisp = currentOddsDisp;
                            $scope.mapUnits[i].oddsColor = useAltColor;
                            $scope.$apply();


                            if(cD !== false && cD == i){
                                var details = renderCrtDetails(combatRules.combats[i]);
                                $scope.crtOdds = "odds = " + currentOddsDisp;
                                activeCombat = combatIndex;
                                activeCombatLine = details;
                            }
                            combatIndex++;
                        }

                    }
                    str += "There are " + combatIndex + " Combats";
                    if(cD !== false){
                        attackers = combatRules.combats[cD].attackers;
                    }
                    str += "";
                    $scope.topCrt.crts[crtName].crtOddsExp = $sce.trustAsHtml(activeCombatLine);
                    $("#status").html(cdLine + str);
                    if(DR.crtDetails){
                        $("#crtDetails").toggle();
                    }
                    $("#status").show();
                    $scope.$apply();

                }else{
                    chattyCrt = false;
                }


                var lastCombat = "";
                if(combatRules.combatsToResolve){
                    if(combatRules.lastResolvedCombat){
                        var finalRoll = combatRules.lastResolvedCombat.Die;
                        var orig = '';

                        if(combatRules.lastResolvedCombat.dieOffset !== undefined){
                            orig = combatRules.lastResolvedCombat.Die + " ";

                            finalRoll += combatRules.lastResolvedCombat.dieOffset;
                            if(combatRules.lastResolvedCombat.dieOffset < 0){
                                orig += "- " + Math.abs(combatRules.lastResolvedCombat.dieOffset);
                            }else{
                                orig += "+ " + combatRules.lastResolvedCombat.dieOffset;
                            }
                            orig += " = ";
                        }
                        title += orig+"<strong style='font-size:150%'>" + finalRoll + " " + combatRules.lastResolvedCombat.combatResult + "</strong>";
                        combatCol = combatRules.lastResolvedCombat.index + 1;

                        combatRoll = combatRules.lastResolvedCombat.Die;
                        $scope.dieOffset = combatRules.lastResolvedCombat.dieOffset;
                        $scope.$apply();

//                                $(".col" + combatCol).css('background-color', "rgba(255,255,1,.6)");
                        $scope.topCrt.crts[crtName].selected = combatCol  - 1;

                        $scope.$apply();

                        var pin = combatRules.lastResolvedCombat.pinCRT;
                        if(pin !== false){
                            pin++;
                            if(pin < combatCol){
                                combatCol = pin;
                                $(".col" + combatCol).css('background-color', "rgba(255, 0, 255, .6)");
                                $scope.topCrts.crts[crtName].pinned = combatCol;
                            }
                        }
                        var dieOffset = -1; /* for normal 0 based crt */

                        if(combatRules.lastResolvedCombat.dieOffset !== undefined){
                            dieOffset = combatRules.lastResolvedCombat.dieOffset + 2;
                        }

                        $scope.topCrt.crts[crtName].combatRoll  = combatRoll + dieOffset;

                        if(combatRules.lastResolvedCombat.useAlt){
//                                showCrtTable($('#cavalryTable'));
                        }else{
                            if(combatRules.lastResolvedCombat.useDetermined){
//                                    showCrtTable($('#determinedTable'));
                            }else{
//                                    showCrtTable($('#normalTable'));
                            }
                        }



                        var currentCombatCol = combatRules.lastResolvedCombat.index;
                        if(combatRules.lastResolvedCombat.pinCRT !== false){
                            currentCombatCol = combatRules.lastResolvedCombat.pinCRT;
                            useAltColor = " pinnedColor";
                        }
                        var oddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];

                        var details = renderCrtDetails(combatRules.lastResolvedCombat);

                        $scope.crtOdds = "odds = " + oddsDisp;
                        newLine = details;


                        $scope.topCrt.crts[crtName].crtOddsExp = $sce.trustAsHtml(newLine);

                    }
                    str += "";
                    var noCombats = false;
                    if(Object.keys(combatRules.combatsToResolve) == 0){
                        noCombats = true;
                        str += "0 combats to resolve";
                    }
                    var combatsToResolve = 0;
                    for(i in combatRules.combatsToResolve){
                        combatsToResolve++;
                        if(combatRules.combatsToResolve[i].index !== null){
                            attackers = combatRules.combatsToResolve[i].attackers;
                            defenders = combatRules.combatsToResolve[i].defenders;
                            thetas = combatRules.combatsToResolve[i].thetas;

                            var theta = 0;
                            for(var j in attackers){
                                var numDef = Object.keys(defenders).length;
                                for(k in defenders){
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
                            var useAltColor = combatRules.combatsToResolve[i].useAlt ? " altColor":"";

                            if(combatRules.combatsToResolve[i].pinCRT !== false){
                                combatCol = combatRules.combatsToResolve[i].pinCRT;
                            }
                            var odds = Math.floor(atk / def);
                            var useAltColor = combatRules.combatsToResolve[i].useAlt ? " altColor":"";
                            if(combatRules.combatsToResolve[i].useDetermined){
                                useAltColor = " determinedColor";
                            }
                            if(combatRules.combatsToResolve[i].pinCRT !== false){
                                useAltColor = " pinnedColor";
                            }

                            var currentCombatCol = combatRules.combatsToResolve[i].index;
                            if(combatRules.combatsToResolve[i].pinCRT !== false){
                                currentCombatCol = combatRules.combatsToResolve[i].pinCRT;
                                useAltColor = " pinnedColor";
                            }
                            var oddsDisp = $scope.topCrt.crts[crtName].header[currentCombatCol];

                            $scope.mapUnits[i].oddsDisp = oddsDisp;
                            $scope.mapUnits[i].oddsColor = useAltColor;
                            $scope.$apply();
//                                $("#"+i).attr('title',oddsDisp).prepend('<div class="unitOdds'+useAltColor+'">'+oddsDisp+'</div>');;
                            var details = renderCrtDetails(combatRules.combatsToResolve[i]);

                            $scope.crtOdds = "odds = " + oddsDisp;
                            newLine = details;
                        }

                    }
                    if(combatsToResolve){
//                str += "Combats To Resolve: " + combatsToResolve;
                    }
                    var resolvedCombats = 0;
                    for(i in combatRules.resolvedCombats){
                        resolvedCombats++;
                        if(combatRules.resolvedCombats[i].index !== null){
                            atk = combatRules.resolvedCombats[i].attackStrength;
                            atkDisp = atk;
                            ;
                            if(combatRules.storm){
                                atkDisp = atk * 2 + " halved for storm " + atk;
                            }
                            def = combatRules.resolvedCombats[i].defenseStrength;
                            ter = combatRules.resolvedCombats[i].terrainCombatEffect;
                            idx = combatRules.resolvedCombats[i].index + 1;
                            newLine = "";
                            if(combatRules.resolvedCombats[i].Die){
//                                    var x = $("#" + cD).css('left').replace(/px/, "");
//                                    var mapWidth = $("body").css('width').replace(/px/, "");
                            }
                            var oddsDisp = $(".col" + combatCol).html()
                            if($scope.curCrt !== 'missile') {
                                newLine += " Attack = " + atkDisp + " / Defender " + def + atk / def + "<br>odds = " + Math.floor(atk / def) + " : 1<br>Coooooombined Arms Shift " + ter + " = " + oddsDisp + "<br>";
                                newLine += "Roll: " + combatRules.resolvedCombats[i].Die + " result: " + combatRules.resolvedCombats[i].combatResult + "<br><br>";
                            }
                            if(cD === i){
                                newLine = "";
                            }
                        }

                    }
                    if(!noCombats){
                        str += "Combats: " + resolvedCombats + " of " + (resolvedCombats+combatsToResolve);
                    }
                    $("#status").html(lastCombat + str);
                    $("#status").show();

                }
            }
            $("#crt h3").html(title);

            $scope.$apply();

        });
@show




        sync.register('moveRules', function(moveRules,data){
            var moveUnits = [];
            var movingUnitId = moveRules.movingUnitId;
            mapUnits = moveRules.moves;
            for(var i in mapUnits) {
                if(mapUnits[i].isOccupied){
                    continue;
                }
                var newUnit = angular.copy($scope.units[moveRules.movingUnitId]);
                newUnit.pathToHere = mapUnits[i].pathToHere;
                newUnit.pointsLeft = mapUnits[i].pointsLeft;
                newUnit.style = {};
                newUnit.style.borderColor = 'rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204)';
                newUnit.style.opacity = .6;
                newUnit.style.transform = "rotate("+mapUnits[i].facing*60+"deg)";
                newUnit.style.top = mapUnits[i].pixY-20+"px";
                newUnit.style.left = mapUnits[i].pixX-20+"px";
                newUnit.hex = i;
                newUnit.id = moveRules.movingUnitId+"Hex"+i;

                moveUnits.push(newUnit);

            }
            $scope.moveUnits = moveUnits;
            $scope.$apply();
        });
    }]);

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

        var path = '<path stroke-dasharray="'+strokeDash+'" class="range-hex '+nat+' '+decoration+' '+cls+'" stroke="transparent" id="rangeHex'+id+'" fill="#000" fill-opacity="0" stroke-width="'+width+'" d="M '+x+' ' + (ac + y) + ' L ' + x + ' '+ (a + y) + ' L ' + (b + x) + ' ' + y;
        path += ' L ' + (2 * b + x) + ' ' + (a + y) + ' L ' + (2 * b + x) + ' ' + (ac + y) + ' L ' + (b + x) + ' '+ (2 * c + y)+' Z"></path>';

        $('#arrow-svg').append(path);
        $('#arrow-svg').html($('#arrow-svg').html());
    }


    function clearHexes(){
        $('#arrow-svg .range-hex').remove();
    }

    lobbyApp.directive('offmapUnit', function() {
        return {
            restrict: 'E',
            templateUrl: 'offmap-unit.html'
        }
    });

    lobbyApp.directive('unit', function() {
        return {
            restrict: 'E',
            templateUrl: 'unit.html'
        }
    });

    lobbyApp.directive('ghostUnit', function() {
        return {
            restrict: 'E',
            templateUrl: 'ghost-unit.html'
        }
    });

    lobbyApp.factory('sync',function(){
        var fetchUrl = '{{ url("wargame/fetch-lobby/") }}';

        var sync = new Sync(fetchUrl);
        return sync;
    });

    /* still doing this the non angular way :( */
    x.register("specialHexes", function(specialHexes, data) {
        var phase = data.gameRules.phase;
        var firePhase =  (phase === <?=BLUE_COMBAT_RES_PHASE?> || phase === <?=RED_COMBAT_RES_PHASE?>);
        var firePhaseClass = firePhase ? "fire-phase" : "";

        $('.specialHexesVP').remove();
        $('.specialHexes').remove();
        var lab = ['unowned','<?=$forceName[1]?>','<?=$forceName[2]?>'];

        var classLab = ['unowned','<?=preg_replace("/ /", '-', $forceName[1])?>','<?=preg_replace("/ /", '-', $forceName[2])?>'];
        for(var i in specialHexes){
            var newHtml = lab[specialHexes[i]];
            var curHtml = $("#special"+i).html();

            if(true || newHtml != curHtml){
                var hexPos = i.replace(/\.\d*/g,'');
                var x = hexPos.match(/x(\d*)y/)[1];
                var y = hexPos.match(/y(\d*)\D*/)[1];
                $("#special"+hexPos).remove();
                if(data.specialHexesChanges[i]){
                    $("#gameImages").append('<div id="special'+hexPos+'" style="border-radius:30px;border:10px solid black;top:'+y+'px;left:'+x+'px;font-size:205px;z-index:1000;" class="'+classLab[specialHexes[i]]+' specialHexes">'+lab[specialHexes[i]]+'</div>');
                    $('#special'+hexPos).animate({fontSize:"16px",zIndex:0,borderWidth:"0px",borderRadius:"0px"},1900,function(){
                        var id = $(this).attr('id');
                        id = id.replace(/special/,'');


                        if(data.specialHexesVictory[id]){
                            var hexPos = id.replace(/\.\d*/g,'');

                            var x = hexPos.match(/x(\d*)y/)[1];
                            var y = hexPos.match(/y(\d*)\D*/)[1];
                            var newVP = $('<div style="z-index:1000;border-radius:0px;border:0px;top:'+y+'px;left:'+x+'px;" class="'+firePhaseClass+' specialHexesVP">'+data.specialHexesVictory[id]+'</div>').insertAfter('#special'+i);
                            if(!firePhase) {
                                $(newVP).animate({top: y - 30, opacity: 0.0}, 1900, function () {
                                    $(this).remove();
                                });
                            }
                        }
                    });

                }else{

                        $("#gameImages").append('<div id="special'+i+'" class="specialHexes">'+lab[specialHexes[i]]+'</div>');
                        $("#special"+i).css({top:y+"px", left:x+"px"}).addClass(classLab[specialHexes[i]]);                    }

            }
        }

        for(var id in data.specialHexesVictory)
        {
            if(data.specialHexesChanges[id]){
                continue;
            }
            var hexPos = id.replace(/\.\d*/g,'');
            var x = hexPos.match(/x(\d*)y/)[1];
            var y = hexPos.match(/y(\d*)\D*/)[1];
            var newVP = $('<div  style="z-index:1000;border-radius:0px;border:0px;top:'+y+'px;left:'+x+'px;" class="'+firePhaseClass+' specialHexesVP">'+data.specialHexesVictory[id]+'</div>').appendTo('#gameImages');
            if(!firePhase){
                $(newVP).animate({top:y-30,opacity:0.0},1900,function(){
                    var id = $(this).attr('id');

                    $(this).remove();
                });
            }
        }


    });
</script>