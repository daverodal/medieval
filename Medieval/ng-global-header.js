import { fixHeader, Sync} from "../../wargaming/Wargame/wargame-helpers/";
import initialize from "./wargame-helpers/ng-initialize";

import "./wargame-helpers/jquery.panzoom"
import 'jquery-ui-bundle';

DR.globalZoom = 1;
DR.playerNameMap = ["Zero", "One", "Two", "Three", "Four"];

DR.players = ["observer", DR.playerOne, DR.playerTwo, DR.playerThree, DR.playerFour];
DR.crtDetails = false;
DR.showArrows = false;

document.addEventListener("DOMContentLoaded",function() {
    initialize();
    x.fetch(0);
    fixHeader();
});



