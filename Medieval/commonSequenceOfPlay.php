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
?><span class="big">Sequence of Play</span>

<p>The game is played in turns each game turn consists of two player turns; one following the other.
    Each player turn is identical. The player whose turn it currently is referred to as the Phasing player the other as the Non Phasing player. Events in the phasing players turn are further referred to occasionally as the Friendly Phase
</p>
<ol>
    <li>
        <?= $forceName[1] ?> Player Turn
        <ol>
            <li>
                Movement Phase
                <p>Movement: The <?= $forceName[1] ?> player moves some none or all of their units following the rules for movement</p>
            </li>
            <li>
                Missile Combat Phase
                <p>Both sides execute missile combat with units armed with bows. First the phasing player allocates their attacks, then the defending player
                allocates their attacks. Then both sides resolve fire combat simultaneously.</p>
            </li>
            <li>
                Melee Combat Phase
                <p>The phasing player must allocate attacks for all their units that have enemy units within their zones of control. See mandatory attack in combat below.
                Phasing player units that participated in missile combat may not attack.</p>
            </li>
            <li>
                Rally Phase
                <p>All units of both sides eligible to rally from "Disordered" to "Battle Ready" in the <?= $forceName[1] ?> players turn do so.</p>
            </li>
        </ol>
    </li>
    <li>
        <?= $forceName[2] ?> Player Turn
        <ol>
            <li>
                Movement Phase
                <p>Movement: The <?= $forceName[2] ?> player moves some none or all of their units following the rules for movement</p>
            </li>
            <li>
                Missile Combat Phase
                <p>Both sides may execute missile combat with units armed with bows. First the phasing player allocates their attacks, then the defending player
                    allocates their attacks. Then both sides resolve fire combat simultaneously.</p>
            </li>
            <li>
                Melee Combat Phase
                <p>The phasing player must allocate attacks for all their units that have enemy units within their zones of control. See mandatory attack in combat below.
                    Phasing player units that participated in missile combat may not attack.</p>
            </li>
            <li>
                Rally Phase
                <p>All units of both sides eligible to rally from "Disordered" to "Battle Ready" in the <?= $forceName[2] ?> players turn do so.</p>
            </li>
        </ol>
    </li>

</ol>
<p>At the end of <span class="gameLength">7</span> game turns the game is over and victory is
    determined.
</p>
