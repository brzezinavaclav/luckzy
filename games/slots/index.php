<link rel="stylesheet" href="./games/slots/styles/main.css">
<script type="text/javascript" src="./games/slots/scripts/main.js"></script>
    <div class="slots">
        <div class="slot slot1"><div class="slotCon"></div></div>
        <div class="slot slot2"><div class="slotCon"></div></div>
        <div class="slot slot3"><div class="slotCon"></div></div>
    </div>
    <div class="control">
        <div class="bRegul">
            <button onclick="wagerDiv();">½</button><!--
           --><button onclick="wagerMultip();">x2</button><!--
           --><button onclick="wagerMax();">MAX</button><!--
           --><button onclick="bot.toggle();" data-toggle="tooltip" data-placement="top" title="AutoSpin" class="tooltips autoBotButton"><div class="autoBotCheck"><span class="glyphicon glyphicon-ok"></span></div></button>
        </div>
        <div class="ybtext">Your Bet:</div>
        <input type="text" class="wager" value="0.00000000">
        <a class="spinbtn btn btn-primary" onclick="javascript:bet();return false;">SPIN</a>
    </div>

<div class="modal fade" id="modals-rules" aria-labelledby="mlabels-rules" aria-hidden="true">
    <div class="modal-dialog" style="width: 350px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="mlabels-rules">Game Rules</h4>
            </div>
            <div class="modal-body">
                <div class="m_alert"></div>

                <table class="table">
                    <tr>
                        <th colspan="2" style="border-top: none;">Payout Table</th>
                    </tr>
                    <tr style="display: none;">
                        <td><div style="float:left;height:30px;line-height:30px;width:20px;">3x</div><div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img1"></div></td>
                        <td>x<?php echo $settings['jackpot']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img1"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img1"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img1"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x<?php echo $settings['jackpot']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img2"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img2"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img2"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x600</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img3"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img3"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img3"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x200</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img4"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img4"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img4"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x50</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img5"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img5"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img5"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x10</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x5</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x2</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float:left;background-size:100%;width: 30px; height: 30px;" class="img6"></div>
                        </td>
                        <td style="line-height: 30px;font-weight:bold;">x1</td>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr>
                        <td>Expexted return to player</td>
                        <th><?php echo round(100-house_edge(), 2); ?> %</th>
                    </tr>
                </table>


            </div>
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