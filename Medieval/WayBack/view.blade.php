@section('local-header')
<link rel="stylesheet" type="text/css" href="{{mix('/vendor/css/medieval/wayback.css')}}">
@endsection

@section('inner-crt')
    @include('wargame::Medieval.medieval-inner-crt')
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

@include('wargame::Medieval.facing-units')

@extends('wargame::Medieval.medieval', ['topCrt'=> new \Wargame\Medieval\FacingCombatResultsTable()])