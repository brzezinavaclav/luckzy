<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/

header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/inc/start.php';
?>
  <!DOCTYPE html>
  <html>
  <head>
    <title><?php echo $settings['title'].' - '.$settings['description']; ?></title>
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="./styles/jquery-ui.min.css">
    <link type="text/css" rel="stylesheet" href="./styles/bootstrap-coingames.css">
    <link type="text/css" rel="stylesheet" href="./styles/mcs.css">
    <link type="text/css" rel="stylesheet" href="./styles/main.css">
    <link type="text/css" rel="stylesheet" href="./styles/themes/Basic/style.css" class="themeLinker">
    <link type="text/css" rel="stylesheet" href="./styles/items.css">
    <link rel="icon" href="./styles/imgs/favicon.ico" type="image/x-icon">
    <script type="text/javascript" src="./scripts/jquery.js"></script>
    <script type="text/javascript" src="./scripts/jquery.cookie.js"></script>
    <script type="text/javascript" src="./scripts/jquery-ui.min.js"></script>
    <script type="text/javascript" src="./scripts/bootstrap.js"></script>
    <script type="text/javascript" src="./scripts/qrlib.js"></script>
    <script type="text/javascript" src="./scripts/mcs.min.js"></script>
    <script type="text/javascript" src="./scripts/sha256.js"></script>
    <script type="text/javascript" src="./scripts/main.js"></script>
    <script type="text/javascript">
      function unique() {
        return '<?php echo $unique; ?>';
      }
      function cursig() {
        return '<?php echo $settings['currency_sign']; ?>';
      }
      function giveaway_freq() {
        return '<?php echo $settings['giveaway_freq']; ?>';
      }
      function min_inv() {
        return '<?php echo $settings['inv_min']; ?>';
      }
      function default_theme() {
        return '<?php echo $settings['active_theme']; ?>';
      }
      function get_active_game(){
        return '<?php echo $game; ?>';
      }
    </script>
  </head>
  <body>
  <div class="all c0">
    </div>

  <div class="navbar navbar-default navbar-fixed-top navbar-first">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand logo" href="./"><?php echo $settings['title']; ?></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <!--
        <ul class="nav navbar-form navbar-right">
          <button class="btn btn-primary" id="withdraw_btn">Withdraw</button>
        </ul>
        -->
        <ul class="nav navbar-nav navbar-right">
              <div class="bal_status">Balance: <span class="balance"><?php echo n_num($player['balance'], true); ?></span> <?php echo $settings['currency_sign']; ?></div>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li class="<?php if($game == 'blackjack') echo 'active'; ?>"><a href="javascript:select_game('blackjack')">Blackjack</a></li>
          <li class="<?php if($game == 'slots') echo 'active'; ?>"><a href="javascript:select_game('slots')">Slots</a></li>
          <li class="<?php if($game == 'dice') echo 'active'; ?>"><a href="javascript:select_game('dice')">Dice</a></li>
          <li><a href="">Support</a></li>
          <li><a href="">More</a></li>
          <li><a href="">Account</a></li>
          <!--<li><a href="">Login</a></li>-->
        </ul>
      </div>
    </div>
  </div>

  <div class="navbar navbar-default navbar-fixed-top navbar-second">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="chat-icon" onclick="javascript:leftCon('chat');"><span class="glyphicon glyphicon-comment"></span></a>
        <a class="navbar-brand">You are playing: <?php echo $game; ?></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="navbar-form navbar-right">
          <button class="btn btn-primary" id="withdraw_btn">Withdraw</button>
          <button class="btn btn-primary" id="deposit_btn">Deposit</button>
          <?php if ($settings['giveaway']) { ?>
          <button class="btn btn-primary" id="faucet_btn">Faucet</button>
          <?php } ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a>Won last 24h: </a></li>
          <li><a>Biggest win: </a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="leftblock"></div>
  <div class="page">
      <a href="#" class="closeLeft" onclick="javascript:leftbox.toggle();return false;"><span class="glyphicon glyphicon-remove"></span></a>
      <div class="leftbuttons">
        <button data-toggle="tooltip" data-placement="right" title="My&nbsp;Account" onclick="javascript:leftCon('profile');"><span class="glyphicon glyphicon-user"></span></button>
        <button data-toggle="tooltip" data-placement="right" title="Provably&nbsp;Fair" onclick="javascript:leftCon('fair');"><span class="glyphicon glyphicon-ok"></span></button>
        <button data-toggle="tooltip" data-placement="right" title="Stats" onclick="javascript:leftCon('stats');"><span class="glyphicon glyphicon-stats"></span></button>
        <button data-toggle="tooltip" data-placement="right" title="News" onclick="javascript:leftCon('news');"><span class="glyphicon glyphicon-flag"></span></button>
        <?php if ($settings['chat_enable']) { ?>
          <!--<button data-toggle="tooltip" data-placement="right" title="Chat" onclick="javascript:leftCon('chat');"><span class="glyphicon glyphicon-comment"></span></button>-->
        <?php } ?>
        <?php if ($settings['inv_enable']) { ?>
          <button data-toggle="tooltip" data-placement="right" title="Invest" onclick="javascript:leftCon('invest');" class="last-child"><span class="glyphicon glyphicon-briefcase"></span></button>
        <?php } ?>
      </div>
    <div class="game" id="<?php echo $game; ?>">
      <?php include './games/'.$game.'/index.php'; ?>
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
            <td>SPIN</td>
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
  </div>

  <!--  BLOCKS HIDDEN BY DEFAULT  -->

  <div class="leftCon" id="lc-stats">

    <div class="heading"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;&nbsp;&nbsp;Stats</div>
    <div class="content">


      <div class="_heading _hfirst">Your Stats</div>
      <div class="form-group">
        <label>Total spins</label><br>
        <span class="statsData_y_spins"></span>
      </div>
      <div class="form-group">
        <label>Total wagered</label><br>
        <span class="statsData_y_wagered"></span>
      </div>


      <div class="_heading _hfirst">Global Stats</div>
      <div class="form-group">
        <label>Total spins</label><br>
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
          <input type="text" class="rightact" value="0.00000000" id="input-invest">
          <span class="input-group-btn">
            <a href="#" onclick="invest();" class="btn btn-sm btn-primary">Invest</a>
          </span>
        </div>
      </div>
      <div class="form-group">
        <label>Divest funds:</label><br>
        <div class="input-group">
        <input type="text" class="rightact" value="0.00000000" id="input-divest">
          <span class="input-group-btn">
          <a href="#" onclick="javascript:divest();return false;" class="btn btn-sm btn-primary">Divest</a>
          </span>
        </div>
      </div>

    </div>
    <div class="footer"></div>

  </div>
  <div class="leftCon" id="lc-profile">

    <div class="heading"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;&nbsp;&nbsp;My Account</div>
    <div class="content">
      <div class="form-group" style="margin-top: 10px;">
        <label>Alias</label>
        <div class="input-group">
          <input  id="input-alias" value="<?php echo $player['alias']; ?>" class="rightact" type="text">
            <span class="input-group-btn">
              <a href="#" onclick="saveAlias();" class="btn btn-sm btn-primary" style="padding: 9px 13px 8px 13px;">Save</a>
            </span>
        </div>
      <div class="form-group">
        <label>Password (<span class="pass-en_dis" style="font-weight: bold;"><?php echo ($player['password'] != '') ? 'Enabled' : 'Disabled'; ?></span>)</label>
          <div class="input-group">
            <input id="input-pass" class="rightact" type="password">
            <span class="input-group-btn">
              <a href="#" class="btn  btn-sm btn-primary" onclick="<?php if ($player['password']=='') echo 'enablePass'; else echo 'disablePass'; ?>();"><?php if ($player['password']=='') echo 'Enable'; else echo 'Disable'; ?></a>
            </span>
          </div>
        </div>
        </div>
      <div class="form-group">
        <label>Unique URL</label>
        <input type="text" id="input-unique" style="cursor:pointer;cursor:hand;width: 100%;" onclick="$(this).select();" value="<?php echo $settings['url'].'/?unique='.$player['hash']; ?>">
      </div>
      <?php if ($settings['usertheme']) { ?>
        <div class="form-group">
          <label>Change theme</label>
          <div>
            <?php
            $dir = scan_dir(__DIR__.'/styles/themes');
            foreach ($dir as $theme) {

              if ($theme == '..' || $theme == '.') continue;

              $themeDir = __DIR__.'/styles/themes/'.$theme;

              if (!is_dir($themeDir) || !file_exists($themeDir.'/bg.jpg') || !file_exists($themeDir.'/style.css') ) continue;

              echo '<a class="themeSwitch" href="#" data-theme="'.$theme.'"></a>' . "\r\n";

            }
            ?>
          </div>
        </div>
      <?php } ?>

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
        <input style="width: 100%;" type="text" id="_fair_server_seed" value="<?php echo hash('sha256',seedExport($player[$game.'_seed'])); ?>" disabled><br>
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
        <input style="width: 100%;" type="text" id="_fair_l_server_seed" value="<?php echo hash('sha256',seedExport($player['last_'.$game.'_seed'])); ?>" disabled><br>
      </div>
      <div class="form-group">
        <label>Server seed (plain):</label><br>
        <input style="width: 100%;" type="text" id="_fair_l_server_seed_p" value="<?php echo seedExport($player['last_'.$game.'_seed']); ?>" disabled><br>
      </div>
      <div class="form-group">
        <label>Client seed:</label><br>
        <input style="width: 100%;" type="text" id="_fair_l_client_seed" value="<?php echo $player['last_client_seed']; ?>" disabled><br>
      </div>
      <div class="form-group">
        <label>Result:</label><br>
        <input style="width: 100%;" type="text" id="_fair_l_result" value="<?php echo $player['last_'.$game.'_result']; ?>" disabled><br>
      </div>

    </div>
    <div class="footer"></div>

  </div>
  <div class="leftCon" id="lc-chat">

    <div class="heading"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;&nbsp;&nbsp;Chat</div>
    <div class="content"></div>
    <div class="footer">
      <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top" title="Press ENTER to send">
      <div style="height: 5px;"></div>
    </div>
  </div>


  <div class="modal fade" id="modals-deposit" aria-labelledby="mlabels-deposit" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="mlabels-deposit">Deposit Funds</h4>
        </div>
        <div class="modal-body" style="text-align: center;">
          <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">Bitcoin</a></li>
            <li role="presentation"><a href="#">Runescape 3</a></li>
            <li role="presentation"><a href="#">Oldschool runescape</a></li>
          </ul>
          Please send at least <b><?php echo n_num($settings['min_deposit']); ?></b> <?php echo $settings['currency_sign']; ?> to this address:
          <div class="addr-p" style="margin:15px;font-weight:bold;font-size:18px;"></div>
          <div class="addr-qr"></div>
          <div class="alert alert-infoo" style="margin: 15px;"><big><b><i>This address is only for a single use. If you want to deposit multiple times, you should generate new address.</i></b></big></div>
          <div style="margin-bottom:15px;">
            <a href="#" class="gray_a" onclick="javascript:_genNewAddress();return false;">New Address</a> <span class="color: lightgray">·</span> <a href="#" class="gray_a pendingbutton" cj-opened="no" onclick="javascript:clickPending();return false;">Show Pending</a>
          </div>
          <div class="pendingDeposits" style="display:none;"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modals-withdraw" aria-labelledby="mlabels-withdraw" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="mlabels-withdraw">Withdraw Funds</h4>
        </div>
        <div class="modal-body">
          <div class="m_alert"></div>
          <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#">Bitcoin</a></li>
            <li role="presentation"><a href="#">Runescape 3</a></li>
            <li role="presentation"><a href="#">Oldschool runescape</a></li>
          </ul>
          <div class="form-group">
            <label for="input-address">Enter valid <?php echo $settings['currency_sign']; ?> address:</label>
            <input type="text" class="form-control" id="input-address">
          </div>
          <div class="form-group">
            <label for="input-am">Enter amount (min. <?php echo n_num($settings['min_withdrawal']).' '.$settings['currency_sign']; ?>):</label>
            <input type="text" class="form-control" id="input-am" style="width:150px;">
            <small>
              Balance: <span class="balance" style="font-weight: bold;"><?php echo n_num($player['balance'],true); ?></span> <?php echo $settings['currency_sign']; ?>
            </small>
          </div>
          <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="javascript:_withdraw();">Withdraw</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modals-faucet" aria-labelledby="modals-faucet" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="mlabels-faucet">Get free coins</h4>
        </div>
        <div class="modal-body">
          <div class="m_alert"></div>
            <p>Giveaway Amount</p>
            <?php echo '<b>'.n_num($settings['giveaway_amount'],true).'</b> '.$settings['currency_sign']; ?>
          <div class="form-group">
            <label>Enter text from image</label><br>
            <a class="captchadiv" href="#" onclick="javascript:$(this).children().remove().clone().appendTo($(this));return false;" data-toggle="tooltip" data-placement="top" title="Click to refresh"><img src="./content/captcha/genImage.php"></a>
            <input type="text" class="captchaInput form-control"  id="input-captcha">
          </div>
          <a href="#" onclick="claim_bonus();" class="btn btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;">Claim</a>
        </div>
      </div>
    </div>
  </div>
  <!--  /BLOCKS HIDDEN BY DEFAULT  -->
  <!-- COINTOLI_ID_-CointoliID- -->


  </body>
  </html>
<?php include __DIR__.'/inc/end.php'; ?>