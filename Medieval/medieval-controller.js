    var flashMessages;
import GameController from "./game-controller.js";

    function flashMessage(playerStatus) {
        var x = 100;
        var y = 200;
        fixHeader();
        var mess = flashMessages.shift();
//    var mess = flashMessages.shift();
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
                    game = mess.match(showRegex);
                    id = game[1];
                    $("#" + id).show({effect: "blind", direction: "up", complete: flashMessage});
                    return;
                }
                if (mess.match(/^@hide/)) {
                    game = mess.match(/^@hide ([^,]*)/);
                    id = game[1];
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

    var gameApp = angular.module('gameApp', ['ngRightClick']);
    gameApp.controller('GameController',  GameController);

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

    gameApp.directive('offmapUnit', function() {
        return {
            restrict: 'E',
            templateUrl: 'offmap-unit.html',
            scope:{
                unit: "<"
            }
        }
    });

    gameApp.directive('unit', function() {
        return {
            restrict: 'E',
            templateUrl: 'unit.html',
            scope:{
                unit: "<",
                rightClickMe: '&'
            }
        }
    });

    gameApp.directive('ghostUnit', function() {
        return {
            restrict: 'E',
            templateUrl: 'ghost-unit.html',
            scope:{
                unit: "<"
            }
        }
    });

    gameApp.factory('sync',function(){
        var fetchUrl = '{{ url("wargame/fetch-lobby/") }}';

        var sync = new Sync(fetchUrl);
        return sync;
    });

    /* still doing this the non angular way :( */
    x.register("specialHexes", function(specialHexes, data) {
        var phase = data.gameRules.phase;
        var firePhase =  (phase === BLUE_COMBAT_RES_PHASE || phase === RED_COMBAT_RES_PHASE);
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
