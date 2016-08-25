@include('wargame::global-header')
<link rel="stylesheet" type="text/css" href="{{elixir('vendor/wargame/medieval/css/Civitate1053.css')}}">
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

@extends('wargame::Medieval.medieval')