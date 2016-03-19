<?php
/* not happy about this part :( */
global $results_name;

/*
 * The template passes $topCrt
 * that is how a templates tells us which crt to render
 */
$crts = $topCrt->crts;
?>
<div id="crt-buttons">
        <div  ng-click="showCrtTable(crtName)" ng-repeat="(crtName, crt) in topCrt.crts"  ng-show="crtName == curCrt" class="crt-switch" id="@{{crtName}}Table">show  @{{ crt.next }} Table</div>
</div>
<h4>Armor Class Offset @{{ dieOffset > 0 ?  '+':'' }}@{{ dieOffset }}</h4>
@{{ topCrts }}
<div ng-show="crtName == curCrt" ng-repeat="(crtName, crt) in topCrt.crts">
    <h4>@{{ crtName }} Combat Results Table</h4>
    <div id="odds">
        <span>&nbsp;</span>
        <span ng-repeat="(colId, col) in crt.header" ng-class="{pinned:colId == crt.pinned, selected:colId == crt.selected}" > @{{ col }}</span>
        <div class="clear"></div>
    </div>
    <div class="shadow-wrapper">
        <div ng-class="topScreen" class="screen screen-one shadowy"></div>
        <div ng-repeat="(rowId, row) in crt.table" class="roll " ng-class="(rowId %2 == 0)? playerName:''">
            <span >@{{ rowId + topCrt.rowNum }}</span>
            <span ng-repeat="(cellId, cell) in row track by $index" ng-class="{pinned:cellId == crt.pinned, selected:cellId == crt.selected, 'die-roll':cellId == crt.selected && rowId == crt.combatRoll }" >@{{ resultsNames[cell] }}</span>
            <div class="clear"></div>
        </div>
        <div ng-class="bottomScreen" class="screen screen-two shadowy"></div>
    </div>
    <div ng-bind-html="crt.crtOddsExp"></div>
</div>

@foreach([] as $crtName => $crt)
    <div ng-show="curCrt == '{{$crtName}}'" class=" {{$crtName}}Table">
        <h4 class="crt-name">@{{curCrt}} combat table.</h4>

        <div id="odds">
            <span class="col0">@{{ crt.selected }}</span>
            <?php  $i = 1;
            $header = $crt->header;
            $headerName = $crtName."ResultsHeader";
           ?>
            @foreach ($header as $odds)
                <span class="col{{$i++}}">{{$odds}}</span>
            @endforeach
            <div class="clear"></div>
        </div>
        <div class="shadow-wrapper">
            <div ng-class="topScreen" class="screen screen-one shadowy"></div>

        <?php
        $rowNum = 1;
        if(isset($topCrt->rowNum)){
            $rowNum = $topCrt->rowNum;
        }?>
        @foreach ($crt->table as $row)
                <? $odd = ($rowNum & 1) ? "odd" : "even";?>
            <div class="roll {{"row$rowNum $odd"}}">
                <span class="col0">{{$rowNum++}}</span>
                <?php $col = 1;?>
                @foreach ($row as $cell)
                    <span class="col{{$col++}}">{{$results_name[$cell]}}</span>
                @endforeach
                <div class="clear"></div>
            </div>
        @endforeach
            <div ng-class="bottomScreen" class="screen screen-two shadowy"></div>
        </div>

    </div>

@endforeach