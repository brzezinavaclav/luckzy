<div class="leftblock"></div>
<div class="page">
    <a href="#" class="closeLeft" onclick="javascript:leftbox.toggle();return false;"><span class="glyphicon glyphicon-remove"></span></a>
    <div class="leftbuttons">
        <!--<button data-toggle="tooltip" data-placement="right" title="My&nbsp;Account" onclick="javascript:leftCon('profile');"><span class="glyphicon glyphicon-user"></span></button>-->
        <button data-toggle="tooltip" data-placement="right" title="Provably&nbsp;Fair" onclick="javascript:leftCon('fair');"><span class="glyphicon glyphicon-ok"></span></button>
        <button data-toggle="tooltip" data-placement="right" title="Stats" onclick="javascript:leftCon('stats');"><span class="glyphicon glyphicon-stats"></span></button>
        <button data-toggle="tooltip" data-placement="right" title="News" onclick="javascript:leftCon('news');"><span class="glyphicon glyphicon-flag"></span></button>
        <?php if ($settings['chat_enable']) { ?>
            <!--<button data-toggle="tooltip" data-placement="right" title="Chat" onclick="javascript:leftCon('chat');"><span class="glyphicon glyphicon-comment"></span></button>-->
        <?php } ?>
        <?php if ($settings['inv_enable'] && logged()) { ?>
            <button data-toggle="tooltip" data-placement="right" title="Invest" onclick="javascript:leftCon('invest');" class="last-child"><span class="glyphicon glyphicon-briefcase"></span></button>
        <?php } ?>
    </div>
    <div class="game" id="<?php echo $page; ?>">
        <?php include './games/'.$page.'/index.php'; ?>
    </div>
</div>

<!--  BLOCKS HIDDEN BY DEFAULT  -->
<div class="leftCon" id="lc-stats">
    <div class="heading"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;&nbsp;&nbsp;Stats</div>
    <div class="content">
        <div class="_heading _hfirst">Your Stats</div>
        <div class="form-group">
            <label>Total bets</label><br>
            <span class="statsData_y_spins"></span>
        </div>
        <div class="form-group">
            <label>Total wagered</label><br>
            <span class="statsData_y_wagered"></span>
        </div>
        <div class="_heading _hfirst">Global Stats</div>
        <div class="form-group">
            <label>Total bets</label><br>
            <span class="statsData_g_spins"></span>
        </div>
        <div class="form-group">
            <label>Total wagered</label><br>
            <span class="statsData_g_wagered"></span>
        </div>
    </div>
    <div class="footer"></div>
</div>
<div class="leftCon" id="lc-invest">
    <div class="heading"><span class="glyphicon glyphicon-briefcase"></span>&nbsp;&nbsp;&nbsp;&nbsp;Invest</div>
    <div class="content">
        <div class="form-group" style="margin-top: 10px;">
            <label>You can invest:</label><br>
            <span class="invData_caninvest"></span>
        </div>
        <div class="form-group">
            <label>Invested:</label><br>
            <span class="invData_invested"></span>
        </div>
        <div class="form-group">
            <label>Bankroll share:</label><br>
            <span class="invData_share"></span>
        </div>
        <div class="form-group">
            <label>Invest funds:</label><br>
            <div class="input-group">
                <input type="text" class="rightact" value="0" id="input-invest">
          <span class="input-group-btn">
            <a href="#" onclick="invest();" class="btn btn-sm btn-primary">Invest</a>
          </span>
            </div>
        </div>
        <div class="form-group">
            <label>Divest funds:</label><br>
            <div class="input-group">
                <input type="text" class="rightact" value="0" id="input-divest">
          <span class="input-group-btn">
          <a href="#" onclick="javascript:divest();return false;" class="btn btn-sm btn-primary">Divest</a>
          </span>
            </div>
        </div>
    </div>
    <div class="footer"></div>
</div>
<div class="leftCon" id="lc-news">
    <div class="heading"><span class="glyphicon glyphicon-flag"></span>&nbsp;&nbsp;&nbsp;&nbsp;News</div>
    <div class="content">
        <?php
        $news = db_query("SELECT * FROM `news` ORDER BY `time` DESC");
        while ($new = db_fetch_array($news)) {
            echo '<div class="well" style="overflow:hidden;margin-bottom: 0;margin-top:10px;"><div style="width:100%;text-align:justify;">'.bbcode($new['content']).'</div><div style="width:100%;text-align:right;"><small><i>'.date('Y-m-d',strtotime($new['time'])).'</i></small></div></div>';
        }
        ?>
    </div>
    <div class="footer"></div>
</div>
<div class="leftCon" id="lc-fair">
    <div class="heading"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;&nbsp;&nbsp;Provably Fair</div>
    <div class="content">
        <div class="_heading _hfirst">Next Shuffle</div>
        <div class="form-group">
            <label>Server seed (Sha256):</label><br>
            <input style="width: 100%;" type="text" class="_fair_server_seed" value="<?php echo hash('sha256',$server_seed); ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Client seed:</label><br>
            <div class="input-group">
                <input type="text" class="_fair_client_seed rightact" value="<?php echo $client_seed; ?>">
          <span class="input-group-btn">
          <a href="#" class="btn btn-sm btn-primary" style="padding: 9px 13px 8px 13px">Save</a><br>
          </span>
            </div>
        </div>
        <div class="_heading">Last Shuffle</div>
        <div class="form-group">
            <label>Server seed (Sha256):</label><br>
            <input style="width: 100%;" type="text" class="_fair_l_server_seed" value="<?php if(!empty($last_server_seed)) echo hash('sha256',$last_server_seed); ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Server seed (plain):</label><br>
            <input style="width: 100%;" type="text" class="_fair_l_server_seed_p" value="<?php echo $last_server_seed; ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Client seed:</label><br>
            <input style="width: 100%;" type="text" class="_fair_l_client_seed" value="<?php echo $last_client_seed; ?>" disabled><br>
        </div>
        <div class="form-group">
            <label>Result:</label><br>
            <input style="width: 100%;" type="text" class="_fair_l_result" value="<?php echo $last_result; ?>" disabled><br>
        </div>
    </div>
    <div class="footer"></div>
</div>