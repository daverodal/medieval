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
?><style type="text/css">
    #header {
        /*display:none;*/
    }

    .exclusive {
        color: green;
    }

    .game-rules {
        font-family: sans-serif;
    }

    .game-rules table, .game-rules th, .game-rules td {
        border: 1px solid black;
    }

    .game-rules h1 {
        color: #338833;
        font-size: 60px;

    }
    #GR {
        width:600px;
    }


</style>
<div class="dropDown" id="GRWrapper" style="font-weight:normal">
    <h4 class="WrapperLabel" title="Game Rules">Rules</h4>

    <div id="GR" style="display:none">
        <div class="close">X</div>
        <div class="game-rules">
            <h1>    {{ $gameName }}   </h1>

            <h2>Rules of Play</h2>

            <ol class="topNumbers">
                <li id="contentsRules">
                    @include('wargame::Medieval.commonContents')
                </li>
                <li id="unitsRules">
                    <span class="big">UNITS</span>
                    <ol>
                        <li><span class="big">Units appear as follows:
                            </span>
                            <ol>
                            @section('unitRules')
                                @include('wargame::Medieval.commonUnitsRules')
                            @show
                            </ol>
                        </li>
                        <li><span class="big">UNIT TYPES</span>
                            <p>There are three main Unit types, Cavalry, Infantry and artillery. Cavalry and Infantry may be bow or cross bow armed this is indicated by a small arrow through the center of the unit’s tactical symbol. Cavalry and Infantry are further differentiated by a number of grades. These are</p>

                        </li>
                        <li><span class="big">Grades</span>
                            <p>Grades can have an effect on both defense against fire and Melee combat (See Combat results table.)</p>
                            <ol>
                                <li><span class="big">Knights:</span> Designated by a K on the Unit. These represent the best and most totally armored troops.</li>
                                <li><span class="big">Heavy:</span> Designated by an H on the unit. They are inferior in protection to knights. But still well protected often with large shields if infantry.</li>
                                <li><span class="big">Medium:</span> Designated with an M. These may have some protection aside from a shield</li>
                                <li><span class="big">Light:</span> Designated with an L. These have no or minimal protection.</li>

                            </ol>
                        </li>
                        <li><span class="big">Skirmish Grades</span>
                            <p>Skirmishers can again be Cavalry or Infantry. They are not graded for their weight of protection as all are generally mediums or lights.
                                They depend on their loose formations and agility for protection from missiles and generally have low melee combat factors.
                                They are graded for their weaponry. These all are good at passing through forests and some other restrictive terrain per scenario.</p>
                            <ol>
                                <li><span class="big">Skirmishers: </span>
                                    <p>Designated with an S. These will most often be Bow armed designated with an arrow as above.
                                        However when not bow armed they represent bands of raiders or ruffians with
                                        little discipline or types specialized in running off the opponents Skirmishers.</p>
                                </li>
                                <li><span class="big">Guns:</span>
                                    <p>Designated with a G. these have early fire arms.</p>
                                </li>
                                <li><span class="big">Javelins: </span>
                                    <p>Designated with a J. Javelin armed skirmishers</p>
                                </li>
                            </ol>
                        </li>
                        <li>
                            <span class="big">Unit Facing</span>
                            <p>
                                Each Unit (with some exceptions) faces a particular hex side at all times.
                                That hex side and the two immediately adjacent to it and the unit constitute the units “Front”.
                                The remaining hexes adjacent to the unit are the units “Rear” hexes. For some Units all of their adjacent hexes count as front,
                                Skirmishers and Wagons (or units in Hedgehog, see advanced Unit Statuses).
                            </p>
                        </li>
                        <li>
                            <span class="big">Stacking</span>
                            <p>
                                Leaders May stack with other units and then only one leader to a hex with one other unit which may be another leader.
                                One other unit of any type may stack with an Artillery Unit.
                            </p>
                        </li>
                        <li>
                            <span class="big">
                                                            Unit Status

                            </span>
                            <p>A Unit is always in one of the following two Statuses. Battle Ready is the default. A unit becomes disordered as a result of combat and some types of movement.</p>
                            <p>Battle Ready: This can be thought of as the default. Battle Ready units may not move through each other. Exception: Skirmishers and Artillery may move through and be moved through by all units.</p>
                            <p>Disordered: Unit’s Combat and movement Factors are halved. Units become disordered as a consequence of missile fire or some types of movement. A Unit becomes un-disordered in the Rally phase of the turn following the turn in which it became disordered. Disordered Units May move through other units and be moved through by them.</p>

                        </li>
                        <li><span class="big">Leaders</span>
                        <p>
                            <p>Leader Units are rated for Combat factor and Movement factor and Command Radius.  Each Leader unit is assigned to a particular command (See Commands). A Unit of that Command within a number of hexes equal to or less than the Leaders Command radius is in “Command Control” and may function normally. Units not in Command Control have their movement rate halved rounded down. Though they may always change facing by one hex side or 180 degrees and not move.</p>
                            <p>Leader units may stack with other friendly Units adding their combat factor in melee given the same weight rating as the unit they are stacked with. Leaders can be eliminated by normal combat; Leaders are also eliminated if a unit they are stacked with is eliminated. Eliminated Leaders are replaced at the beginning of the next friendly movement phase. Replacement leaders have half the combat bonus and half the comand Radius of leader it is replacing rounded up. They must be placed stacked with a friendly unit of the same original command. They may then remain in place, move with or away from that unit in the movement phase as desired. Make leaders so they can be worth an assigned number of Victory points the first time they are killed but not subsequently.</p>

                        </p></li>
                    </ol>


                </li>

                <li id="moveRules">
                    @include('wargame::Medieval.commonMoveRules')
                </li>
                <li id="zocRules">
                    @include('wargame::Medieval.commonZocRules')
                </li>
                <li id="missileCombatRules"><span class="big">MISSILE COMBAT</span>
                    <p>Missile Combat is the first type of combat executed each turn. Units that are capable of missile combat are most skirmishers, all Bow armed, Artillery and some Wagons.</p>
                    <ol><li><span class="big">Line of Sight and Range</span>
                        <p>In order for a missile unit to shoot at another unit, that unit must be in a clear Line Of Site, traceable from one of the shooting units front hex sides.
                            A unit that is in the shooting units Zone of Control is always in the units Line Of Sight.
                            A missile unit may never fire over another unit Hostile or friendly.  See the Terrain Effects Chart for other obstructions to line of Site.
                            A Missile unit must also be in range of its target in order to shoot at it. The range for Bow armed units, other skirmishers,
                            Artillery and Wagons will be found in the special rules for each game. The Range of a unit is the distance in hexes not counting the hex the shooter
                            is in that the shooter may be away from its target in order to shoot.</p>
                        </li>
                        <li><span class="big">Procedure</span>
                            <p>
                                All Missile Combat is voluntary. All missile combat is declared before any is resolved.
                                </p>
                                <p>Step 1: The Phasing Player selects a target then assigns all missile units firing at that target. Then he moves on to another target. He may alter targets and Units shooting at them until he is satisfied with the arrangement.</p>
                                <p>Step 2: The Non Phasing Player does the same.</p>
                                <p>Step 3: The phasing player executes all shooting for both sides in any order that he pleases applying any results before moving on to the next combat.</p>

                            </p>
                        </li>
                        <li>
                            <span class="big">Missile Combat Results and Modifiers</span>
                            <p>
                                See the Missile Combat Table.
                            </p>
                        </li>
                    </ol>


                </li>

                <li id="meleeCombatRules">
                    <span class="big">MELEE COMBAT</span>
                    <p>Melee combat is the second type of Combat resolved each turn.
                        Some melee combat is voluntary some is obligatory.
                        Melee combat occurs between units that are directly adjacent to each other.
                        In Melee Combat the Phasing player is the Attacker and the Non Phasing player the Defender.
                    </p>
                    <ol>
                        <li>
                            <span class="big">Eligibility to Attack and Obligation</span>
                            <p>
                                An Infantry Cavalry or Skirmisher unit is eligible to Melee attack any Unit or Units in its Zone of control.
                                And Must attack at least one such unit if there are any (and the Units Status allows it (See Advance Unit Status above is using those rules).
                                All Hostile units that project a Zone of Control into the attacking unit must themselves be attacked though they may be attacked by other friendly units.
                                Exceptions: Bow Armed and Skirmisher units are not required to melee attack. Infantry units are not required to melee attack Cavalry units.
                                Cavalry Units are not required to attack Wagons or units not in clear terrain.
                                A unit may not attack if it is in the ZOC of a Hostile unit that is in one of its rear or flank hexes unless that hostile unit is being
                                attacked by another friendly unit or is itself attacking another friendly unit (is that too much of a mess?
                                Problem I see is that it does not let a powerful Knight that is surrounded by chumps break out Input requested.)

                            </p>
                        </li>
                        <li>
                            <span class="big">Rear Attack Bonus</span>
                            <p>If a unit is being attacked through one of its rear or flank hex sides its melee combat factor is cut in half rounded down.
                                (Optionally if a hostile merely projects a ZOC into a unit from its rear or flank hex sides it is reduced in half
                                The reason for doing it that way would be because then a flanker helps attacker from the front attacking multiple units.
                                What do you think?)
                            </p>
                        </li>
                        <li><span class="big">Melee Attack procedure</span>
                            <p>
                                The attacker selects a unit or units that he is eligible to attack.
                                He then selects all of his units that will be attacking that unit.
                                The Melee factor of the defending unit is then divided into the combined melee factors of all attacking units.
                                This then expresses the attack as odds which will be displayed on the Combat results table.
                                Example Attackers total factors = 10 defenders total factor = 4 odds then = 2 to 1 as remainders are discarded.
                                There are also modifiers to the die roll see the combat results table.
                                And modifiers to attacker and defender strength see the combat results table and combat rules above.
                                The attacker may select and deselect units until he is satisfied with the attack then he may move on to another attack.
                                The attacker may adjust his attacks at will returning to earlier attacks if he desires.
                                Once he is satisfied with all attacks he moves on to resolving all attacks in any order that he pleases applying
                                the results of each attack before moving on to the next. Once all attacks have been completed the Melee combat phase is
                                concluded and the player moves on to the Rally Phase.
                            </p>

                        </li>
                        <li><span class="big">Attacking Multiple Defenders</span>
                            <p>
                            An attacking unit may melee attack up to 3 defending units in its Zones Of Control.
                                Such an attack may not be combined with attacks by other friendly units.
                                Should we come up with something that allows a flanker to reduce targets strength without joining attack?
                                See Rear attack Bonus above.
                            </p>
                        </li>
                        <li><span class="big">Advance after Melee Combat</span>
                            <p>
                                If the defending units hex is vacated for any reason after an attack is resolved.
                                Then an attacking unit must advance into that hex. Which of all the attacking units advance is at the attacking player’s
                                discretion (Should this always be that unit with the strongest Melee factor or fastest movement?)
                                (See Advanced Unit Status some Statuses prohibit advance after combat.)
                            </p>
                        </li>
                    </ol>
                </li>
                <li id="rallyRules"><span class="big">RALLY</span>
                    <p>
                        During the Rally phase units that have been disordered for one full turn rally to Battle Ready.
                        This is automatic. If using the Demoralized command rule Panicked units that are eligible to rally will rally as well.
                        Do we want units that are eligible to rally to hi-light and make the player click them so he is aware that it has or has not happened?
                    </p>
                </li>
                <li class="exclusive" id="victoryConditions">
                    @section('victoryConditions')
                        @include('wargame::Medieval.victoryConditions')
                    @show
                </li>
                <li id="designCredits">
                    @section('creditDesign')
                        @include('wargame::Medieval.credit')
                    @show
                </li>
            </ol>
        </div>
    </div>
</div>

