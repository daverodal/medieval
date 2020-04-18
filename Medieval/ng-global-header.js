import  {fixHeader} from "@markarian/wargame-helpers";
import initialize from "./wargame-helpers/imported/initialize";
import {DR} from '@markarian/wargame-helpers'

import ""
import 'jquery-ui-bundle';

DR.globalZoom = 1;
DR.playerNameMap = ["Zero", "One", "Two", "Three", "Four"];

DR.players = ["observer", DR.playerOne, DR.playerTwo, DR.playerThree, DR.playerFour];
DR.crtDetails = false;
DR.showArrows = false;
DR.$ = $;
document.addEventListener("DOMContentLoaded",function() {
    initialize();
    fixHeader();
});



