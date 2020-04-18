<?php
global $results_name, $phase_name, $mode_name;
/**
 * Created by PhpStorm.
 * User: david
 * Date: 8/25/16
 * Time: 12:18 PM
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
<!doctype html>
<html>
<head>
    <?php
    global $phase_name, $mode_name;

    $oClass = new ReflectionClass('Wargame\Cnst');
    $constants = $oClass->getConstants();

    ?>
    <script>

        const resultsNames = JSON.parse('<?=json_encode($results_name)?>');
        const mode_name = JSON.parse('<?=json_encode($mode_name)?>');
            <?php foreach($constants as $k => $v){
                echo "const $k = $v;\n";
            }?>

            const phase_name = []
                <?php foreach($phase_name as $k => $v){
                    echo "phase_name[$k] = \"$v\";\n";
                }?>
        const fetchUrl = "<?=url("wargame/fetch/$wargame");?>";
        const mode_name = JSON.parse('<?=json_encode($mode_name)?>');
        ;
        const phase_name = []
        <?php foreach($phase_name as $k => $v){
            echo "phase_name[$k] = \"$v\";\n";
        }?>
            const fetchUrl = "<?=url("wargame/fetch/$wargame");?>";


        if (!window.PHP_INIT_VARS) {
            window.PHP_INIT_VARS = {};
        }


        window.legacy = {};
        window.PHP_INIT_VARS.playerOne = "{{$forceName[1]}}";
        window.PHP_INIT_VARS.playerTwo = "{{$forceName[2]}}";
        window.PHP_INIT_VARS.playerThree = "{{$forceName[3] ?? ''}}";
        window.PHP_INIT_VARS.playerFour = "{{$forceName[4] ?? ''}}";

    </script>

        @include('wargame::Medieval.ng-global-header')
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">


    <script type="text/javascript">



    </script>
    <link href='https://fonts.googleapis.com/css?family=Nosifer' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>














    <script src="{{mix("vendor/javascripts/medieval/medieval.js")}}"></script>
@extends('wargame::Medieval.angular-view',['topCrt'=> $topCrt] )
@include('wargame::Medieval.medieval-units')
<link href="https://fonts.googleapis.com/css?family=Lato:100,200,300,400,500,600,700,800,900" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:100,200,300,400,500,600,700,800,900" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis:100,200,300,400,500,600,700,800,900" rel="stylesheet">

@section('local-header')
@show

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
