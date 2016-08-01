<?php
$playingGame=false;
$endedOnInit=false;
if (db_num_rows(db_query("SELECT `id` FROM `games` WHERE `ended`=0 AND `player`=$player[id] LIMIT 1"))!=0)
    $playingGame=true;
if (db_num_rows(db_query("SELECT `id` FROM `games` WHERE `ended`=1 AND `player`=$player[id] AND `insurance_process`=1 LIMIT 1"))!=0)
    $endedOnInit=true;
?>
<link rel="stylesheet" href="./games/blackjack/styles/main.css">
<link rel="stylesheet" href="./games/blackjack/styles/cards.css">
<script type="text/javascript" src="./games/blackjack/scripts/main.js"></script>
<script>
    $(document).ready(function(){
        <?php if ($playingGame) echo 'playingOnInit();'; ?>
        <?php if ($endedOnInit) echo 'endedOnInit();'; ?>
    });
</script>
<div class="jack_table">
    <div class="cj-table">
        <div class="gamblingTable">
            <div class="cj-rivalTables">
                <div class="cj-dealerTable"></div>
                <div class="cj-playerTable"></div>
            </div>
        </div>
    </div>
    <div class="bjinfo">
        <div class="bjinfo-image"><img src="./games/blackjack/images/26.png"></div>
        <div class="bjinfo-text">BLACKJACK PAYS 3 TO 2</div>
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

<div class="stats">
    <div class="st-switches">
        <a href="#" onclick="javascript:rules();return false;" class="rulesB tooltips" data-toggle="tooltip" data-placement="right" title="Game Rules"><span class="glyphicon glyphicon-info-sign"></span></a>
        <a data-load="my_bets" href="#">MY BETS</a>
        <a data-load="all_bets" href="#">ALL BETS</a>
        <a data-load="high" href="#">HIGHEST WINS</a>
    </div>
    <div class="st-switchline"></div>
    <div class="st-stats">
        <table>
            <thead>
            <tr>
                <td>BET ID</td>
                <td>PLAYER</td>
                <td>TIME</td>
                <td>BET</td>
                <td>PLAYER DECK</td>
                <td>DEALER DECK</td>
                <td>PAYOUT</td>
                <td>PROFIT</td>
            </tr>
            </thead>
            <tbody class="stats-my_bets"></tbody>
            <tbody class="stats-all_bets"></tbody>
            <tbody class="stats-high"></tbody>
        </table>
    </div>
</div>


<footer>
</footer>
