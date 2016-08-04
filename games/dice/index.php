<link rel="stylesheet" href="./games/dice/styles/main.css">
<script type="text/javascript" src="./games/dice/scripts/main.js"></script>

<div id="content" class="centers">
    <div class="wrap">
        <div class="c_center">
            <div class="bet_type">
                <button class="btn btn-sm btn-default active" data-toggle="m_bet"><i class="glyphicon glyphicon-repeat"></i></button>
                <button class="btn btn-sm btn-default" data-toggle="b_bet"><i class="glyphicon glyphicon-refresh"></i></button>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label id="under_over_txt">Roll under to win</label>
                        <span id="under_over_num" class="under_over_num" onclick="javascript:inverse();">49.50</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Multiplier</label>
                        <div class="input-group">
                            <input type="text" id="betTb_multiplier" class="form-control" value="2.00">
                            <span class="input-group-addon" onclick="$('#betTb_multiplier').focus();">x</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Win chance</label>
                        <div class="input-group">
                            <input type="text" id="betTb_chance" class="form-control" value="49.50">
                            <span class="input-group-addon" onclick="$('#betTb_chance').focus();">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="m_bet">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Bet amount</label>
                        <div class="input-group">
                            <input type="text" id="bt_wager" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                            <span class="input-group-btn">
                                <a class="btn btn-primary" onclick="clickdouble();">2x</a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-primary" onclick="clickmax();">Max</a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Profit on win</label>
                        <div class="input-group">
                            <input type="text" id="bt_profit" class="form-control" value="0">
                            <span class="input-group-btn">
                                    <a class="btn btn-primary" onclick="maxProfit();">Max</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="b_bet">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Operate</label>
                        <div class="radio">
                            <label>
                                <input name="bB_operate" id="bB_operate" value="rolls" type="radio" checked>Rolls
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input name="bB_operate" id="bB_operate" value="seconds" type="radio">Seconds
                            </label>
                        </div>
                        <input id="bt_rolls_bB" type="text" class="form-control" value="100">
                            <label style="padding: 10px 0px 9px;">Base bet</label>
                            <input id="bt_wager_bB" type="text" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                        </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>On lose</label>
                        <div class="radio">
                            <label>
                                <input name="bB_on_loss" id="bB_loss" value="return" type="radio" checked>Retrun to base
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input name="bB_on_loss" id="bB_loss" value="increase" type="radio">Increase bet by
                            </label>
                        </div>
                        <input id="bB_loss_increase_by" type="text" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked>Max lose
                            </label>
                        </div>
                        <input id="bB_max_loss_val" type="text" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>On win</label>
                        <div class="radio">
                            <label>
                                <input name="bB_on_win" id="bB_win" value="return" type="radio">Retrun to base
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input name="bB_on_win" id="bB_win" value="increase" type="radio" checked>Increase bet by
                            </label>
                        </div>
                        <input id="bB_win_increase_by" type="text" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked>Max profit
                            </label>
                        </div>
                        <input id="bB_max_win_val" type="text" class="form-control" value="0" <?php if(!logged()) echo 'disabled'; ?>>
                    </div>
                </div>
            </div>
            <a onclick="singleRoll();" id="roll_btn" class="btn btn-primary btn-lg btn-block" style="margin-top: 20px;">Roll dice</a>
        </div>
</div>
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
                <td>RESULT</td>
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
