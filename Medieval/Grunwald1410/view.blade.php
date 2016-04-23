@include('wargame::global-header')
@include('wargame::TMCW.Amph.amph-header')
<link rel="stylesheet" type="text/css" href="{{asset('vendor/wargame/medieval/grunwald1410/css/all.css')}}">
<style>
    .semi-circle {
        width: 48px;
        height: 24px;
        /* background: #eee; */
        border-color: red;
        border-style: solid;
        border-width: 25px 0px 0px 0px;
        border-radius: 100%;
        position: absolute;
        top:-5px;
        left:-5px;

    }



    .rel-unit{
        position:relative;
    }

</style>
</head>

@section('unitRules')
    @parent
    <li class="exclusive">No units may be receive replacements in this game.
    </li>
@endsection

@section('inner-crt')
    @include('wargame::Medieval.medieval-inner-crt', ['topCrt'=> new \Wargame\Medieval\MedievalCombatResultsTable()])
@endsection

@section('victoryConditions')
    @include('wargame::TMCW.Amph.victoryConditions')
@endsection

@section('commonRules')
    @include('wargame::TMCW.commonRules')
@endsection

@section('exclusiveRules')
    @include('wargame::TMCW.exclusiveRules')
@endsection

@section('obc')
    @include('wargame::TMCW.obc')
@endsection
@section('outer-deploy-box')
    <div id="deployBox">
        <div class="a-unit-wrapper" ng-repeat="unit in deployUnits"  ng-style="unit.wrapperstyle">
            <div id="@{{unit.id}}" ng-mouseUp="clickMe(unit.id, $event)" ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
                <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
                <div class="shadow-mask" ng-class="unit.shadow"></div>
                <div class="counterWrapper">
                    <div class="counter"></div>
                </div>
                <div class="range">@{{ unit.armorClass }}</div>

                <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

                <div class="unit-numbers">@{{ unit.strength }} @{{ unit.orgStatus == 0 ? 'B':'' }} @{{ unit.maxMove - unit.moveAmountUsed }}</div>
                <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>

            </div>
        </div>
        <div class="clear"></div>
    </div>
@endsection

@section('units')
    <div class="a-unit-wrapper" ng-repeat="unit in mapUnits"  ng-style="unit.wrapperstyle">
    <div id="@{{unit.id}}" ng-mouseUp="clickMe(unit.id, $event)" ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>

        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

        <div class="unit-numbers">@{{ unit.strength }} @{{ unit.orgStatus == 0 ? 'B':'' }} @{{ unit.maxMove - unit.moveAmountUsed }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>


    </div>
    </div>

    <div ng-mouseover="hoverThis(unit)" ng-mouseleave="unHoverThis(unit)" ng-click="clickMe(unit.id, $event)" ng-style="unit.style" ng-repeat="unit in moveUnits track by $index" class="unit" ng-class="[unit.nationality, unit.class]" >
        <div class="counterWrapper">
            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>
        <div class="unit-numbers">@{{ unit.strength }} - @{{ unit.pointsLeft }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>

    </div>

<?php global $results_name;?>
    <script>
        var lobbyApp = angular.module('lobbyApp', []);
        lobbyApp.controller('LobbyController', ['$scope', '$http', 'sync', '$sce', function($scope, $http, sync, $sce){
            $scope.topCrt = angular.fromJson('{!!json_encode(new \Wargame\Medieval\MedievalCombatResultsTable())!!}');
            $scope.resultsNames = angular.fromJson('{!!json_encode($results_name)!!}');

            $scope.units = angular.fromJson('{!!json_encode($units)!!}');
            $scope.clickMe = function(id, event){
                doitUnit(id, event);
            };
            $scope.floatMessage = {};

//            var dieOffset = -2;
            $scope.dieOffset = 0;
            $scope.curCrt = 'melee';
            $scope.showDetails = false;

            $scope.toggleDetails = function(){
              $scope.showDetails = !$scope.showDetails;
            };
            $scope.$watch('dieOffset', function(newVal, oldVal){
                $scope.topScreen = 'rows'+(3+(newVal-0));
                $scope.bottomScreen = 'rows'+(3-newVal);
            });

            $scope.showCrtTable = function(table){
                if(table == 'melee'){
                    $scope.curCrt = 'missile';
                }else{
                    $scope.curCrt = 'melee';
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
                for(var i in mapUnits) {
                    var newUnit = $scope.units[i];
                    if(mapUnits[i].parent === 'gameImages'){
                        newUnit.maxMove = mapUnits[i].maxMove;
                        newUnit.moveAmountUsed = mapUnits[i].moveAmountUsed;
                        newUnit.wrapperstyle = {};
//                        newUnit.facingstyle = {};
                        newUnit.wrapperstyle.transform = "rotate("+mapUnits[i].facing*60+"deg)";
                        newUnit.wrapperstyle.top = mapUnits[i].y-20+"px";
                        newUnit.wrapperstyle.left = mapUnits[i].x-20+"px";
                        newUnit.facing = mapUnits[i].facing;
                        newUnit.strength = mapUnits[i].strength;
                        newUnit.steps = mapUnits[i].steps;
                        gameUnits[i] = newUnit;
                    }
                    if(mapUnits[i].parent === 'deployBox'){
                        newUnit.style = {float:'left'};
                        newUnit.strength = mapUnits[i].strength;
                        if(mapUnits[i].status == <?=STATUS_DEPLOYING?>){
                            newUnit.style.boxShadow = "5px 5px 5px #333";
                        }

                        deployUnits.push(newUnit);
                    }
                }
                $scope.mapUnits = gameUnits;
                $scope.deployUnits = deployUnits;

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
                var ter = combat.terrainCombatEffect;
                var combatCol = combat.index + 1;

                var html = "<div id='crtDetails'>"+combat.combatLog+"</div><div>Attack = " + atk + " / Defender " + def + " = " + div + "</div>"
                /*+ atk + " - Defender " + def + " = " + diff + "</div>";*/
                return html;
            }

            x.register("combatRules", function(combatRules, data){

                for(var arrowUnits in $scope.mapUnits){
                    $scope.mapUnits[arrowUnits].arrows = {};
                    $scope.mapUnits[arrowUnits].oddsDisp = null;
                }

                $scope.dieOffset = 0;
                $scope.topCrt.crts.melee.selected = null;
                $scope.topCrt.crts.melee.pinned = null;
                $scope.topCrt.crts.melee.combatRoll = null;
                $scope.crtOdds = null;

                $scope.$apply();

                var title = "Combat Results ";
                var cdLine = "";
                var activeCombat = false;
                var activeCombatLine = "<div></div>";
                var crtName = "melee";

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
                            crtName = 'melee';/* should decide this here */

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

                        var str = "";
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
                                        theta -= $scope.mapUnits[j].facing * 60;

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
                            var finalRoll = combatRules.lastResolvedCombat.Die + combatRules.lastResolvedCombat.dieOffset;
                            var orig = combatRules.lastResolvedCombat.Die + " ";
                            if(combatRules.lastResolvedCombat.dieOffset < 0){
                                orig += "- " + Math.abs(combatRules.lastResolvedCombat.dieOffset);
                            }else{
                                orig += "+ " + combatRules.lastResolvedCombat.dieOffset;
                            }
                            title += orig+" = <strong style='font-size:150%'>" + finalRoll + " " + combatRules.lastResolvedCombat.combatResult + "</strong>";
                            combatCol = combatRules.lastResolvedCombat.index + 1;

                            combatRoll = combatRules.lastResolvedCombat.Die;
                            $scope.dieOffset = combatRules.lastResolvedCombat.dieOffset;
                            $scope.$apply();

//                                $(".col" + combatCol).css('background-color', "rgba(255,255,1,.6)");
                                $scope.topCrt.crts.melee.selected = combatCol  - 1;

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

                                $scope.topCrt.crts[crtName].combatRoll  = combatRoll + combatRules.lastResolvedCombat.dieOffset + 2;

                            if(combatRules.lastResolvedCombat.useAlt){
//                                showCrtTable($('#cavalryTable'));
                            }else{
                                if(combatRules.lastResolvedCombat.useDetermined){
//                                    showCrtTable($('#determinedTable'));
                                }else{
//                                    showCrtTable($('#normalTable'));
                                }
                            }
                            crtName = 'melee';




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
                                newLine += " Attack = " + atkDisp + " / Defender " + def + atk / def + "<br>odds = " + Math.floor(atk / def) + " : 1<br>Coooooombined Arms Shift " + ter + " = " + oddsDisp + "<br>";
                                newLine += "Roll: "+combatRules.resolvedCombats[i].Die + " result: " + combatRules.resolvedCombats[i].combatResult+"<br><br>";
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

        lobbyApp.factory('sync',function(){
            var fetchUrl = '{{ url("wargame/fetch-lobby/") }}';

            var sync = new Sync(fetchUrl);
            return sync;
        });

    </script>
@endsection



@section('nounits')



    @foreach ($units as $unit)
        <div class="unit {{$unit['nationality']}}" id="{{$unit['id']}}" alt="0">
            <div class="shadow-mask"></div>
            <div class="unitSize">{{$unit['unitSize']}}</div>
            <img class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">
            <div class="counterWrapper">
                <img src="{{asset("js/".$unit['image'])}}" class="counter"><span class="unit-desig"><?=$unit['unitDesig']?></span>
            </div>
            <div class="unit-numbers">5 - 4</div>
        </div>
    @endforeach
@endsection
@include('wargame::Medieval.angular-view' )
<div id="yin-yang"></div>