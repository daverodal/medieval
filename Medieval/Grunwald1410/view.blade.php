@include('wargame::global-header')
<link rel="stylesheet" type="text/css" href="{{elixir('vendor/wargame/medieval/css/Grunwald1410.css')}}">
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

@section('inner-crt')
    @include('wargame::Medieval.medieval-inner-crt', ['topCrt'=> new \Wargame\Medieval\MedievalCombatResultsTable()])
@endsection

@section('commonRules')
    @include('wargame::Medieval.commonRules')
@endsection

@section('exclusiveRules')
    @include('wargame::Medieval.exclusiveRules')
@endsection

@section('obc')
    @include('wargame::Medieval.obc')
@endsection


@extends('wargame::Medieval.medieval')