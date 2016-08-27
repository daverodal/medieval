<?php
/**
 *
 * Copyright 2012-2015 David Rodal
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
?>
    <li >
        <?= $forceName[1] ?> units are this color.
        <offmap-unit unit="{orgStatus: 0, strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'cavalry', armorClass:'K', maxMove: 5}" ></offmap-unit>
    </li>

    <li>
        <?= $forceName[2] ?> units are this color.
        <offmap-unit unit="{orgStatus: 0,strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[2])) ?>', class:'cavalry', armorClass:'K', maxMove: 5}" ></offmap-unit>

    </li>
    <li>
        The number on the left is the melee combat strength, the number on the right is the movement allowence.
        <offmap-unit unit="{orgStatus: 0, strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>

    </li>
    <li>
        The symbol above the numbers represents the unit type.
        This is Infantry (men on foot), represented by an X pattern.
        <offmap-unit unit="{orgStatus: 0, strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>

    </li>

    <li>
        This is Cavalry (men on horses) represented by a slash pattern.
        <offmap-unit unit="{orgStatus: 0, strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'cavalry', armorClass:'K', maxMove: 5}" ></offmap-unit>
    </li>

    <li>
        An arrow next to the unit symbol means armed with bows.
        <div class="clear"></div>
        <offmap-unit class="left" unit="{orgStatus: 0, bow: true, strength:2, steps: 2, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>
        <offmap-unit class="left" unit="{orgStatus: 0, bow: true, strength:2, steps: 2, nationality:'<?= str_replace(' ','-',strtolower($forceName[2])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>
        <div class="clear"></div>

    </li>
    <li>
        An number of dots along the bottom represent the number of steps, 3, 2 or 1 (when a unit's steps are reduced to 0 it's eliminated).
        It's combat strength is reduced as it's steps are lost in combat.
        <div class="clear"></div>
        <offmap-unit class="left" unit="{orgStatus: 0, bow: true, strength:6, steps: 3, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>
        <offmap-unit class="left" unit="{orgStatus: 0, bow: true, strength:3, steps: 2, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>
        <offmap-unit class="left" unit="{orgStatus: 0, bow: true, strength:1, steps: 1, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'inf', armorClass:'M', maxMove: 3}" ></offmap-unit>
        <div class="clear"></div>
    </li>
    <li>
        This is a Headquarters. (leaders) the number of dots in the middle top represend it's command radius.
        <div class="clear"></div>
        <offmap-unit class="left" unit="{orgStatus: 0,strength:3,steps: 1,  hq: true, commandRadius: '.....', nationality:'<?= str_replace(' ','-',strtolower($forceName[2])) ?>', class:'hq', armorClass:'K', maxMove: 5}" ></offmap-unit>
        <offmap-unit class="left" unit="{orgStatus: 0,strength:2,steps: 1,  hq: true, commandRadius: '...', nationality:'<?= str_replace(' ','-',strtolower($forceName[2])) ?>', class:'hq', armorClass:'K', maxMove: 5}" ></offmap-unit>
        <offmap-unit class="left" unit="{orgStatus: 0,strength:1,steps: 1,  hq: true, commandRadius: '.', nationality:'<?= str_replace(' ','-',strtolower($forceName[2])) ?>', class:'hq', armorClass:'K', maxMove: 5}" ></offmap-unit>
        <div class="clear"></div>

    </li>

<li>
    The Letter in the middle bottom is their Org Status. B means Battle Ready. D means Disordered (combat and movement halved)
    <div class="clear"></div>
    <offmap-unit class="left" unit="{orgStatus: 0, strength:6, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'cavalry', armorClass:'K', maxMove: 5}" ></offmap-unit>
    <offmap-unit class="left" unit="{orgStatus: 1, strength:3, nationality:'<?= str_replace(' ','-',strtolower($forceName[1])) ?>', class:'cavalry', armorClass:'K', maxMove: 2}" ></offmap-unit>
    <div class="clear"></div>
</li>