<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 8/25/16
 * Time: 11:53 AM
 *
 * /*
 * Copyright 2012-2016 David Rodal
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
 */
?>

@section('units')
    <div ng-mouseDown="mouseDown( unit.id,  $event)" ng-mouseUp="clickMe(unit.id,  $event)" ng-mouseover="hoverHq(unit)" ng-mouseleave="unHoverHq(unit)" class="a-unit-wrapper" ng-repeat="unit in mapUnits"  ng-style="unit.wrapperstyle">
        <unit right-click-me="rightClickMe(id)" unit="unit"></unit>
    </div>

    <div ng-mouseover="hoverThis(unit)" ng-mouseleave="unHoverThis(unit)" ng-click="clickMe(unit.id, $event)" ng-style="unit.style" ng-repeat="unit in moveUnits track by $index" class="unit" ng-class="[unit.nationality, unit.class]" >
        <ghost-unit unit="unit"></ghost-unit>

    </div>
@endsection

@section('outer-deploy-box')
    <div style="margin-right:3px;" class="left">Deploy/Staging area</div>
    <div id="deployBox">
        <div ng-mouseUp="clickMe(unit.id,  $event)" class="a-unit-wrapper" ng-repeat="unit in deployUnits"  ng-style="unit.wrapperstyle">
            <offmap-unit unit="unit"></offmap-unit>
        </div>
        <div class="clear"></div>
    </div>
@endsection

@section('ng-unit-template')
    <div id="@{{unit.id}}" ng-right-click="rightClickMe({id:unit.id})" ng-style="unit.style" class="unit rel-unit"
         ng-class="[unit.nationality, unit.class]">
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            <div ng-show="unit.bow" class="bow" style=""></div>
            <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>
            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>

        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow"
             src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

        <div ng-class="unit.infoLen" class="unit-numbers">@{{ unit.unitNumbers }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>
        <i ng-show="!unit.command" class="fa fa-star unit-command" aria-hidden="true"></i>

    </div>
@endsection

@section('ng-offmap-unit-template')
    <div id="@{{unit.id}}" ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            <div ng-show="unit.bow" class="bow" style=""></div>
            <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>

            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>
        <div class="unit-numbers">@{{ unit.strength }} @{{ unit.orgStatus == 0 ? 'B':'D' }} @{{ unit.maxMove - unit.moveAmountUsed }}</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>

    </div>
@endsection

@section('ng-ghost-unit-template')
    <div class="counterWrapper">
        <div ng-show="unit.bow" class="bow" style=""></div>
        <div ng-show="unit.hq" class="hq">@{{ unit.commandRadius }}</div>
        <div class="counter"></div>
    </div>
    <div class="range">@{{ unit.armorClass }}</div>
    <div class="unit-numbers">@{{ unit.strength }} - @{{ unit.pointsLeft }}</div>
    <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>
@endsection
