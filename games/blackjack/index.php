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


<div class="leftCon" id="lc-fair">

    <div class="heading"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;&nbsp;&nbsp;Provably Fair</div>
    <div class="content">

        <div class="_heading _hfirst">Next Shuffle</div>
        <div class="form-group">
            <label>Server seed (Sha256):</label><br>
            <input style="width: 100%;" type="text" id="_fair_server_seed" value="<?php echo hash('sha256',stringify_shuffle($player['initial_shuffle'])); ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Client seed:</label><br>
            <div class="input-group">
                <input class="rightact" type="text" id="_fair_client_seed" value="<?php echo $player['client_seed']; ?>">
          <span class="input-group-btn">
          <a href="#" class="btn btn-sm btn-primary" style="padding: 9px 13px 8px 13px">Save</a><br>
          </span>
            </div>
        </div>

        <div class="_heading">Last Shuffle</div>
        <div class="form-group">
            <label>Server seed (Sha256):</label><br>
            <input style="width: 100%;" type="text" id="_fair_l_server_seed" value="<?php echo hash('sha256',stringify_shuffle($player['last_initial_shuffle'])); ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Server seed (plain):</label><br>
            <input style="width: 100%;" type="text" id="_fair_l_server_seed_p" value="<?php echo stringify_shuffle($player['last_initial_shuffle']); ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Client seed:</label><br>
            <input style="width: 100%;" type="text" id="_fair_l_client_seed" value="<?php echo $player['last_client_seed']; ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Result:</label><br>
            <input style="width: 100%;" type="text" id="_fair_l_result" value="<?php echo stringify_shuffle($player['last_final_shuffle']); ?>" disabled><br>
        </div>

    </div>
    <div class="footer"></div>

</div>