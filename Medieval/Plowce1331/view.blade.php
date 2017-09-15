@section('inner-crt')
    @include('wargame::Medieval.medieval-inner-crt', ['topCrt'=> new \Wargame\Medieval\MedievalCombatResultsTable()])
@endsection

@section('victoryConditions')
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

@section('local-header')
<link rel="stylesheet" type="text/css" href="{{elixir('vendor/wargame/medieval/css/Plowce1331.css')}}">
@endsection
