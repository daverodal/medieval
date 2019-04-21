import { Sync } from "./wargame-helpers/imported/Sync";
import {DR} from './wargame-helpers/imported/DR'
window.x = new Sync(fetchUrl);
var x = window.x;

x.register("vp", function (vp, data) {


    var p1 = DR.playerOne.replace(/ /g, '-');
    var p2 = DR.playerTwo.replace(/ /g, '-');

    var p1 = 'player' + p1.replace(/\//ig, '_') + 'Face';
    var p2 = 'player' + p2.replace(/\//ig, '_') + 'Face';

    $("#victory").html(" Victory: <span class='" + p1 + "'>" + DR.playerOne + " </span>" + vp[1] + " <span class='" + p2 + "'>" + DR.playerTwo + " </span>" + vp[2] + "");
    if (typeof victoryExtend === 'function') {
        victoryExtend(vp, data);
    }

});

/* still doing this the non angular way :( */
x.register("specialHexes", function(specialHexes, data) {
    return;
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
            var x = hexPos.match(/x([-]\d*)y/)[1];
            var y = hexPos.match(/y(\d*)\D*/)[1];
            $("#special"+hexPos).remove();
            if(data.specialHexesChanges[i]){
                $("#gameImages").append('<div id="special'+hexPos+'" style="border-radius:30px;border:10px solid black;top:'+y+'px;left:'+x+'px;font-size:205px;z-index:1000;" class="'+lab[specialHexes[i]]+' specialHexes">'+lab[specialHexes[i]]+'</div>');
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

                $("#gameImages").append('<div id="special'+i+'" class="'+lab[specialHexes[i]]+' specialHexes">'+lab[specialHexes[i]]+'</div>');
                $("#special"+i).css({top:y+"px", left:x+"px"}).addClass(classLab[specialHexes[i]]);                    }

        }
    }

    for(var id in data.specialHexesVictory)
    {
        if(data.specialHexesChanges[id]){
            continue;
        }
        var hexPos = id.replace(/\.\d*/g,'');
        var xstr = hexPos.match(/x(-?\d*)y/);
        var x = xstr[1];
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