import "./ngGameMain";
import { GameController } from "./game-controller";
import { Sync } from "../../wargaming/Wargame/wargame-helpers/";

var gameApp = angular.module('gameApp', ['ngRightClick', 'ngSanitize']);

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
    var sync = new Sync(fetchUrl);
    return sync;
});