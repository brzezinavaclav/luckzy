<link rel="stylesheet" href="./games/blackjack/styles/main.css">
<script type="text/javascript" src="./games/blackjack/scripts/main.js"></script>
<div class="jack_table">
    <div class="cj-table">
        <div class="gamblingTable">
            <div class="cj-rivalTables">
                <div class="cj-dealerTable"></div>
                <div class="cj-playerTable"></div>
            </div>
            <div class="bjinfo">
                <div class="bjinfo-image"><img src="./games/blackjack/images/26.png"></div>
                <div class="bjinfo-text">BLACKJACK PAYS </div>
            </div>
        </div>
    </div>
</div>

<div class="control">
    <div style="float: left">
        <a class="btn btn-primary gameControllers gC-4 btn-disabled" onclick="gameAction('split');" disabled>Split</a>
        <a class="btn btn-primary gameControllers gC-3 btn-disabled" onclick="gameAction('double');" disabled>Double</a>
        <a class="btn btn-primary gameControllers gC-2 btn-disabled" onclick="gameAction('stand');" disabled>Stand</a>
        <a class="btn btn-primary gameControllers gC-1 btn-disabled" onclick="gameAction('hit');" disabled>Hit</a>
    </div>
    <div class="bRegul">
           <button class="gameControllers gC-5" onclick="br_multip();">x2</button>
           <button class="gameControllers gC-6" onclick="br_div();">/2</button>
    </div>
    <div class="ybtext">Your Bet:</div>
    <a class="gameControllers gC-7 spinbtn btn btn-primary" onclick="bet();">BET</a>
    <input class="wager" value="0.00000000" onchange="_betChanged();" type="text">
</div>

<!--

    <div class="cj-table-control">
        <div class="cj-leftMargin"></div>
        <div class="cj-table-actions">
            <div class="g_controls">
                <a href="#" class="btn btn-main gameControllers gC-4 btn-disabled" onclick="javascript:gameAction('split');return false;" disabled>SPLIT</a>
                <a href="#" class="btn btn-main gameControllers gC-3 btn-disabled" onclick="javascript:gameAction('double');return false;" disabled>DOUBLE</a>
                <a href="#" class="btn btn-main gameControllers gC-2 btn-disabled" onclick="javascript:gameAction('stand');return false;" disabled>STAND</a>
                <a href="#" class="btn btn-main gameControllers gC-1 btn-disabled" onclick="javascript:gameAction('hit');return false;" disabled>HIT</a>
            </div>
            <div class="g_insurance">
                <div class="g_ins_question">Insure?</div>
                <a href="#" class="btn btn-main gameControllers first__" onclick="javascript:insure(1);return false;">YES</a>
                <a href="#" class="btn btn-main gameControllers" onclick="javascript:insure(0);return false;">NO</a>
            </div>
            <div class="betRegulators">
                <a href="#" class="btn btn-main gameControllers gC-5" onclick="javascript:br_multip();return false;"><small><small><b>x2</b></small></small></a>
                <a href="#" class="btn btn-main gameControllers gC-6" onclick="javascript:br_div();return false;" style="border-top: none;height:26px;"><small><small><b>/2</b></small></small></a>
            </div><div class="betAmount">
    <input type="text" onchange="javascript:_betChanged();" class="betInput" value="0.00000000">
    <div class="betUpdown">
        <a href="#" onclick="javascript:_betValUp();return false;"><span class="glyphicon glyphicon-chevron-up"></span></a>
        <a class="valdown" href="#" onclick="javascript:_betValDown();return false;"><span class="glyphicon glyphicon-chevron-down"></span></a>
    </div>
</div><a href="#" class="btn btn-main betButton gameControllers gC-7" onclick="javascript:bet();return false;">BET</a>
</div>
</div>
-->