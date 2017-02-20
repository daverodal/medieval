import initialize from "../../wargaming/Wargame/initialize.js";
import fixHeader from "../../wargaming/Wargame/fix-header.js";

import Sync from "../../wargaming/Wargame/Sync.js";
import GameController from "./game-controller.js";

window.x = new Sync(fetchUrl);
var x = window.x;


    var gameApp = angular.module('gameApp', ['ngRightClick']);
    gameApp.controller('GameController',  GameController);




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
        debugger;
        var phase = data.gameRules.phase;
        var firePhase =  (phase === BLUE_COMBAT_RES_PHASE || phase === RED_COMBAT_RES_PHASE);
        var firePhaseClass = firePhase ? "fire-phase" : "";

        $('.specialHexesVP').remove();
        $('.specialHexes').remove();
        var lab = ['unowned',DR.playerOne,DR.playerTwo];

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
debugger;
DR.globalZoom = 1;
DR.playerNameMap = ["Zero", "One", "Two", "Three", "Four"];

DR.players = ["observer", DR.playerOne, DR.playerTwo, DR.playerThree, DR.playerFour];
DR.crtDetails = false;
DR.showArrows = false;

document.addEventListener("DOMContentLoaded",function() {

    var $panzoom = $('#gameImages').panzoom({
        cursor: "normal", animate: true, maxScale: 2.0, minScale: .3, onPan: function (e, panzoom) {

            var xDrag;
            var yDrag;
            if (event.type === 'touchmove') {
                xDrag = Math.abs(event.touches[0].clientX - DR.clickX);
                yDrag = Math.abs(event.touches[0].clientY - DR.clickY);
                if (xDrag > 40 || yDrag > 40) {
//                            DR.dragged = true;
                }
            } else {
                xDrag = Math.abs(event.clientX - DR.clickX);
                yDrag = Math.abs(event.clientY - DR.clickY);
                if (xDrag > 4 || yDrag > 4) {
//                            DR.dragged = true;
                }
            }
        },
        onZoom: function (e, p, q) {
            DR.globalZoom = q;
            var out = DR.globalZoom.toFixed(1);

            $("#zoom .defaultZoom").html(out);
        }
    });


    $panzoom.parent().on('mousewheel DOMMouseScroll MozMousePixelScroll', function (e) {
        e.preventDefault();
        var delta = e.delta || e.originalEvent.wheelDelta;

        var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;

        $panzoom.panzoom('zoom', zoomOut, {
            increment: 0.1,
            animate: false,
            focal: e
        });
    });
    DR.$panzoom = $panzoom;




    initialize();
    x.fetch(0);
    fixHeader();

});



