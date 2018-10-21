<?php
/**
 *
 * Copyright 2012-2015 David Rodal
 * User: David Markarian Rodal
 * Date: 3/8/15
 * Time: 5:48 PM
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><body>
<style>
    <?php @include "playAs.css";?>
    body{
        background:url("<?=asset("vendor/wargame/medieval/montaperti1260/images/Battle_of_Montaperti.jpg")?>") #333 no-repeat;
        background-position:center center;
        background-size: contain;
    }
</style>

<h2 style="text-align:center;font-size:30px;"> Welcome to</h2>
<h1 style="text-align:center;font-size:90px;">Montaperti 1260</h1>
<div class="clear">&nbsp;</div>
<fieldset ><Legend>Play As </Legend>
    <a class="link"  href="<?=url("wargame/enter-hotseat");?>/<?=$wargame?>">Play Hotseat</a><br>
    <a class="link"  href="<?=url("wargame/enter-multi");?>/<?=$wargame?>">Play Multi Player </a><br>
    <a class="link" href="<?=url("wargame/leave-game");?>">Go to Lobby</a>
    <div class="attribution">
        By Giovanni Villani [Public domain or Public domain], <a  target="blank" href="https://commons.wikimedia.org/wiki/File:Battle_of_Montaperti.jpg">via Wikimedia Commons</a>    </div>
</fieldset>
