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
    <div  ng-click="clickMe(unit.id,  $event)" ng-mouseover="hoverHq(unit)" ng-mouseleave="unHoverHq(unit)" class="a-unit-wrapper" ng-repeat="unit in mapUnits"  ng-style="unit.wrapperstyle">
         <unit right-click-me="rightClickMe(id)" unit="unit"></unit>
    </div>

    <div ng-mouseover="hoverThis(unit)" ng-mouseleave="unHoverThis(unit)" ng-click="clickMe(unit.id, $event)" ng-style="unit.style" ng-repeat="unit in moveUnits track by $index" class="unit" ng-class="[unit.nationality, unit.class]" >
        <ghost-unit unit="unit"></ghost-unit>

    </div>
@endsection

@section('outer-deploy-box')
    <div style="margin-right:3px;" class="left">Deploy/Staging area</div>
    <div id="deployBox">
        <div ng-click="clickMe(unit.id,  $event)" class="a-unit-wrapper" ng-repeat="unit in deployUnits"  ng-style="unit.wrapperstyle">
            <offmap-unit unit="unit"></offmap-unit>
        </div>
        <div class="clear"></div>
    </div>
@endsection

@section('ng-unit-template')
    <div class="disrupted-unit" ng-if="unit.isDisrupted">D</div>
    <div id="@{{unit.id}}" ng-right-click="rightClickMe({id:unit.id})" ng-style="unit.style" class="unit rel-unit"
         ng-class="[unit.nationality, unit.class]">
        <div ng-show="unit.oddsDisp" class="unitOdds" ng-class="unit.oddsColor">@{{ unit.oddsDisp }}</div>
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            @{{ unit.attackStrength }} @{{ unit.defStrength }} @{{ unit.maxMove - unit.moveAmountUsed }}
        </div>
        <div class="type-wrapper"  ng-class="unit.class">
            @{{ unit.bow ? unit.fireStrength : '&nbsp;' }}&nbsp;
        </div>
        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow"
             src="{{url('assets/unit-images/short-red-arrow-md.png')}}" class="counter">
        <div ng-class="unit.infoLen" class="unit-numbers">@{{ unit.bow ? unit.fireStrength : '' }} -@{{ unit.flankStrength }}-</div>
    </div>
@endsection

@section('ng-offmap-unit-template')
    <div id="@{{unit.id}}" ng-style="unit.style" class="unit rel-unit" ng-class="[unit.nationality, unit.class]" >
        <div class="shadow-mask" ng-class="unit.shadow"></div>
        <div class="counterWrapper">
            @{{ unit.attackStrength }} @{{ unit.defStrength }} @{{ unit.maxMove - unit.moveAmountUsed }}
        </div>
        <div class="type-wrapper" ng-class="unit.class">
            @{{ unit.bow ? unit.fireStrength : '&nbsp;' }}
        </div>
        <div ng-class="unit.infoLen" class="unit-numbers"> -@{{ unit.flankStrength }}-</div>
        <div class="unit-steps">@{{ "...".slice(0, unit.steps) }}</div>

    </div>
@endsection

@section('ng-ghost-unit-template')
    <div class="shadow-mask" ng-class="unit.shadow"></div>
    <div class="counterWrapper">
        @{{ unit.attackStrength }} @{{ unit.defStrength }} @{{ unit.maxMove - unit.moveAmountUsed }}
    </div>
    <div class="type-wrapper" ng-class="unit.class">
        @{{ unit.bow ? unit.fireStrength : '&nbsp;' }}
    </div>
    <div ng-class="unit.infoLen" class="unit-numbers"> -@{{ unit.flankStrength }}-</div>
@endsection
