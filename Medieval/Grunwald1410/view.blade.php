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
                    <div ng-show="unit.bow" class="bow" style=""></div>

                    <div class="counter"></div>
                </div>
                <div class="range">@{{ unit.armorClass }}</div>

                <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

                <div class="unit-numbers">@{{ unit.strength }} @{{ unit.orgStatus == 0 ? 'B':'D' }} @{{ unit.maxMove - unit.moveAmountUsed }}</div>
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
            <div ng-show="unit.bow" class="bow" style=""></div>
            <div class="counter"></div>
        </div>
        <div class="range">@{{ unit.armorClass }}</div>

        <img ng-repeat="arrow in unit.arrows" ng-style="arrow.style" class="arrow" src="{{asset('js/short-red-arrow-md.png')}}" class="counter">

        <div ng-class="unit.infoLen" class="unit-numbers">@{{ unit.unitNumbers }}</div>
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
