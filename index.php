<?php
/*
 *  © Coinslots
 *  Demo: http://www.btcircle.com/Coinslots
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
<script type="text/javascript" src="./scripts/bootstrap.min.js"></script>
<script type="text/javascript" src="./scripts/qrlib.js"></script>
<script type="text/javascript" src="./scripts/mcs.min.js"></script>
<script type="text/javascript" src="./scripts/sha256.js"></script>
<script type="text/javascript" src="./scripts/main.js"></script>
<script type="text/javascript">
  function unique() {
    return '<?php echo $unique; ?>';
  }
  function cursig() {
    return 'Coins';
  }
  function giveaway_freq() {
    return '<?php echo $settings['giveaway_freq']; ?>';
  }
  function min_inv() {
    return '<?php echo $settings['inv_min']; ?>';
  }
  function get_site_url(){
    return '<?php echo $settings['url']; ?>';
  }
  function dice_edge(){
    return '<?php echo $settings['house_edge']; ?>';
  }
  function get_active_game(){
    return '<?php echo $game; ?>';
  }
</script>
</head>
<body>
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
      <?php if(logged()): ?>
        <ul class="nav navbar-nav navbar-right">
          <div class="bal_status">Balance: <span class="balance"><?php echo $player['balance']; ?> Coins</span></div>
        </ul>
      <?php else: ?>
        <ul class="nav navbar-form navbar-right">
          <button class="btn btn-primary"  data-toggle="modal" data-target="#modals-sign"><b>New?</b> Sign up to play!</button>
        </ul>
      <?php endif; ?>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="?blackjack">Blackjack</a></li>
        <li><a href="?slots">Slots</a></li>
        <li><a href="?dice">Dice</a></li>
        <li><a href="?support">Support</a></li>
        <li><a href="?more">More</a></li>
        <?php if(logged()): ?>
          <li><a href="?account">Account</a></li>
          <li><a onclick="logout()">Sign out</a></li>
        <?php else: ?>
          <li><a data-toggle="modal" data-target="#modals-login">Login</a></li>
        <?php endif; ?>
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
      <?php if(logged()): ?>
        <a class="chat-icon" onclick="javascript:leftCon('chat');"><span class="glyphicon glyphicon-comment"></span></a>
      <?php endif; ?>
      <?php if($game): ?>
      <a class="navbar-brand">You are playing: <?php echo $game; ?></a>
      <?php endif; ?>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="navbar-form navbar-right">
        <?php if(logged()): ?>
          <button class="btn btn-primary" data-toggle="modal" data-target="#modals-withdraw">Withdraw</button>
          <button class="btn btn-primary" data-toggle="modal" data-target="#modals-deposit">Deposit</button>
        <?php if ($settings['giveaway']) : ?>
          <button class="btn btn-primary" id="faucet_btn">Faucet</button>
        <?php endif;endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a id="won_last">Won last 24h: <?php echo last_won('1 DAY'); ?> Coins</a></li>
        <li><a id="biggest">Biggest win: <?php echo biggest_win(); ?> Coins</a></li>
      </ul>
    </div>
  </div>
</div>
<?php
if($page == 'blackjack' || $page == 'slots' || $page == 'dice') include 'game.php';
else if($page == 'account'){
  if(logged()) include 'account.php';
  else header("Location: ./");
}
else include $page.'.php';
include __DIR__.'/inc/end.php';
?>
<div class="modal fade" id="modals-deposit" aria-labelledby="mlabels-deposit" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="mlabels-deposit">Deposit Funds</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a aria-controls="d_btc" role="tab" href="#d_btc" data-toggle="tab">Bitcoin</a></li>
          <?php if($settings['rns3']): ?>
            <li role="presentation"><a aria-controls="d_rns3" href="#d_rns3" role="tab" data-toggle="tab">Runescape 3</a></li>
          <?php endif; if($settings['orns']):?>
            <li role="presentation"><a aria-controls="d_orns" href="#d_orns" role="tab" data-toggle="tab">Oldschool runescape</a></li>
          <?php endif; ?>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="d_btc" style="text-align: center;">
            Please send at least <b><?php echo n_num($settings['btc_min_deposit']); ?></b> BTC to this address:
            <br><small><i>(1BTC = <?php echo $settings['btc_rate']; ?> Coins)</i></small>
            <div class="addr-p" style="margin:15px;font-weight:bold;font-size:18px;"></div>
            <div class="addr-qr"></div>
            <div class="alert alert-infoo" style="margin: 15px;"><big><b><i>This address is only for a single use. If you want to deposit multiple times, you should generate new address.</i></b></big></div>
            <div style="margin-bottom:15px;">
              <a href="#" class="gray_a" onclick="javascript:_genNewAddress();return false;">New Address</a> <span class="color: lightgray">·</span> <a href="#" class="gray_a pendingbutton" cj-opened="no" onclick="javascript:clickPending();return false;">Show Pending</a>
            </div>
            <div class="pendingDeposits" style="display:none;"></div>
          </div>
          <div role="tabpanel" class="tab-pane" id="d_rns3">
            <div class="m_alert"></div>
            <div class="form-group">
              <label for="input-am">Enter amount (min. <?php echo n_num($settings['rns3_min_deposit']); ?> Runescape 3):</label>
              <input type="text" class="form-control" id="d_rns3_amount" onkeydown="if (event.keyCode == 13) deposit('rns3');;">
              <small><i>(1 Runescape 3 = <?php echo $settings['rns3_rate']; ?> Coins)</i></small>
            </div>
            <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="deposit('rns3');">Deposit</button>
          </div>
          <div role="tabpanel" class="tab-pane" id="d_orns">
            <div class="m_alert"></div>
            <div class="form-group">
              <label for="input-am">Enter amount (min. <?php echo n_num($settings['orns_min_deposit']); ?> Oldschool runescape):</label>
              <input type="text" class="form-control" id="d_orns_amount" onkeydown="if (event.keyCode == 13) deposit('orns');">
              <small><i>(1 Oldschool runescape = <?php echo $settings['orns_rate']; ?> Coins)</i></small>
            </div>
            <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="deposit('orns');">Deposit</button>
          </div>
        </div>
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
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a aria-controls="w_btc" role="tab" href="#w_btc" data-toggle="tab">Bitcoin</a></li>
          <?php if($settings['rns3']): ?>
            <li role="presentation"><a aria-controls="w_rns3" href="#w_rns3" role="tab" data-toggle="tab">Runescape 3</a></li>
          <?php endif; if($settings['orns']):?>
            <li role="presentation"><a aria-controls="w_orns" href="#w_orns" role="tab" data-toggle="tab">Oldschool runescape</a></li>
          <?php endif; ?>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="w_btc">
        <div class="form-group">
          <label for="input-address">Enter valid BTC address:</label>
          <input type="text" class="form-control" id="w_btc_address">
        </div>
        <div class="form-group">
          <label for="input-am">Enter amount (min. <?php echo n_num($settings['min_withdrawal']); ?> Coins):</label>
          <input type="text" class="form-control" id="w_btc_amount" style="width:150px;" onkeydown="if (event.keyCode == 13) withdraw();">
          <small>
            Balance: <span class="balance" style="font-weight: bold;"><?php echo $player['balance']; ?> Coins</span>
          </small>
        </div>
        <button class="btn btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="withdraw('btc');">Withdraw</button>
          </div>
          <div role="tabpanel" class="tab-pane" id="w_rns3">
            <div class="form-group">
              <label for="input-am">Enter amount (min. <?php echo n_num($settings['min_withdrawal']); ?> Coins):</label>
              <input type="text" class="form-control" id="w_rns3_amount">
              <small><i>(1 Runescape 3 = <?php echo $settings['rns3_rate']; ?> Coins)</i></small>
            </div>
            <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="withdraw('rns3');">Withdraw</button>
          </div>
          <div role="tabpanel" class="tab-pane" id="w_orns">
            <div class="form-group">
              <label for="input-am">Enter amount (min. <?php echo n_num($settings['min_withdrawal']); ?> Coins):</label>
              <input type="text" class="form-control" id="w_orns_amount">
              <small><i>(1 Oldschool runescape = <?php echo $settings['orns_rate']; ?> Coins)</i></small>
            </div>
            <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="withdraw('orns');">Withdraw</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modals-faucet" aria-labelledby="modals-faucet" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="mlabels-faucet">Get free Coins</h4>
      </div>
      <div class="modal-body">
        <div class="m_alert"></div>
        <p>Giveaway Amount</p>
        <?php echo '<b>'.$settings['giveaway_amount'].'</b> Coins'; ?>
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
<div class="modal fade" id="modals-login" aria-labelledby="mlabels-login" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="mlabels-login">Login</h4>
      </div>
      <div class="modal-body">
        <div class="m_alert"></div>
        <div id="loginform">
          <div class="form-group">
            <label for="input-username">Username:</label>
            <input type="text" id="username" class="form-control">
          </div>
          <div class="form-group">
            <label for="input-password">Password</label>
            <input type="password" id="passwd" class="form-control" onkeydown="if (event.keyCode == 13) login();">
          </div>
        </div>
        <div class="form-group" id="2facode" style="display: none;">
          <label for="input-username">Google authentication code:</label>
          <input type="text" id="totp" class="form-control" onkeydown="if (event.keyCode == 13) login();">
        </div>
        <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="login();">Login</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modals-sign" aria-labelledby="mlabels-sign" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="mlabels-sign">Sign up</h4>
      </div>
      <div class="modal-body">
        <div class="m_alert"></div>
        <div class="form-group">
          <label for="input-username">Username:</label>
          <input id="username" type="text" class="form-control">
        </div>
        <div class="form-group">
          <label for="input-password">Password</label>
          <input id="passwd" type="password" class="form-control">
        </div>
        <div class="form-group">
          <label for="input-password">Re-type password</label>
          <input id="re_passwd" type="password" class="form-control">
        </div>
        <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="register();">Sign up</button>
      </div>
    </div>
  </div>
</div>
<div class="leftblock"></div>

<div class="leftCon" id="lc-chat">

  <div class="heading"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;&nbsp;&nbsp;Chat</div>
  <div class="content"></div>
  <div class="footer">
    <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top" title="Press ENTER to send" >
    <div style="height: 5px;"></div>
  </div>
</div>
</body>
</html>