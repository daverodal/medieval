@section('local-header')
<link rel="stylesheet" type="text/css" href="{{mix('/vendor/css/medieval/arsouf1191.css')}}">
@endsection

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