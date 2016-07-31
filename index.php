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
  function get_site_url(){
    return '<?php echo $settings['url']; ?>';
  }
  function get_active_game(){
    return '<?php echo $_COOKIE['game']; ?>';
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
          <div class="bal_status">Balance: <span class="balance"><?php echo n_num($player['balance'], true); ?></span> <?php echo $settings['currency_sign']; ?></div>
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
      <?php if(game()): ?>
      <a class="navbar-brand">You are playing: <?php echo game(); ?></a>
      <?php endif; ?>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="navbar-form navbar-right">
        <?php if(logged()): ?>
        <button class="btn btn-primary" id="withdraw_btn">Withdraw</button>
        <button class="btn btn-primary" id="deposit_btn">Deposit</button>
        <?php if ($settings['giveaway']) : ?>
          <button class="btn btn-primary" id="faucet_btn">Faucet</button>
        <?php endif;endif; ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a>Won last 24h: </a></li>
        <li><a>Biggest win: </a></li>
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
    <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top" title="Press ENTER to send">
    <div style="height: 5px;"></div>
  </div>
</div>
</body>
</html>