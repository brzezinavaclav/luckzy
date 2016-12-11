<?php
/*
 *  © Coinslots
 *  Demo: http://www.btcircle.com/Coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/

header('X-Frame-Options: DENY');

$init = true;
include __DIR__ . '/inc/start.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $settings['title'] . ' - ' . $settings['description']; ?></title>
    <meta charset="utf-8">
    <meta name="google-site-verification" content="YtW9dYTSdjAityBVUQn_7gc84QMkGA7l63cg7qeM-50" />
    <link type="text/css" rel="stylesheet" href="./styles/jquery-ui.min.css">
    <link type="text/css" rel="stylesheet" href="./styles/bootstrap-coingames.css">
    <link type="text/css" rel="stylesheet" href="./styles/font-awesome.min.css">
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
        function giveaway_freq() {
            return '<?php echo $settings['giveaway_freq']; ?>';
        }
        function min_inv() {
            return '<?php echo $settings['inv_min']; ?>';
        }
        function get_site_url() {
            return '<?php echo $settings['url']; ?>';
        }
        function dice_edge() {
            return '<?php echo $settings['house_edge']; ?>';
        }
        function get_active_game() {
            return '<?php echo $game; ?>';
        }
    </script>
</head>
<body>
<div id="p_alert">
<?php echo $p_alert; ?>
</div>
<div class="navbar navbar-default navbar-fixed-top navbar-first">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#primary-nav"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./"><img src="images/luckzy_logo_1c_by_irianwhitefox-danbh7a.png" class="logo" alt="<?php echo $settings['title']; ?>" /></a>
        </div>
        <?php if (logged()): ?>
        <ul class="nav navbar-right">
            <div class="bal_status">Balance: <span class="balance"><?php echo $player['balance']; ?></span> Coins</div>
        </ul>
        <?php else: ?>
            <ul class="nav navbar-right">
                <button class="btn btn-primary signin_btn" data-toggle="modal" data-target="#modals-sign"><b>New?</b> Sign up
                    to play!
                </button>
            </ul>
        <?php endif; ?>
        <div id="primary-nav" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="?p=blackjack">Blackjack</a></li>
                <li><a href="?p=slots">Slots</a></li>
                <li><a href="?p=dice">Dice</a></li>
                <li><a href="?p=support">Support</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="?p=more"><span class="glyphicon glyphicon-user nav-icon"></span> Hall of fame</a></li>
                        <li><a href="?p=more"><span class="glyphicon glyphicon-question-sign nav-icon"></span> FAQ</a></li>
                        <li><a href="?p=more"><span class="glyphicon glyphicon-eye-open nav-icon"></span> Verification</a></li>
                        <li><a href="?p=more"><span class="glyphicon glyphicon-piggy-bank nav-icon"></span> Affiliate</a></li>
                        <li><a href="?p=more"><span class="fa fa-facebook nav-icon"></span> Facebook</a></li>
                        <li><a href="?p=more"><span class="fa fa-twitter nav-icon"></span> Twitter</a></li>
                        <li><a href="?p=more"><span class="fa fa-reddit nav-icon"></span> Reddit</a></li>
                        <li><a href="?p=more"><span class="fa fa-bitcoin nav-icon"></span> Bitcoin talk</a></li>
                    </ul>
                </li>
                <?php if (logged()): ?>
                    <li><a href="?p=account">Account</a></li>
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
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#second-nav"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
                <a class="chat-icon" onclick="leftCon('<?php if($_COOKIE['chat'] != '') echo $_COOKIE['chat']; else echo 'chat' ?>');"><span class="glyphicon glyphicon-comment"></span></a>
            <?php if ($game): ?>
                <a class="navbar-brand">You are playing: <?php echo $game; ?></a>
            <?php endif; ?>
        </div>
        <div id="second-nav" class="navbar-collapse collapse">
            <?php if (logged()): ?>
            <ul class="navbar-form navbar-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modals-withdraw">Withdraw</button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modals-deposit">Deposit</button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modals-transactions">
                        Transactions
                    </button>
                    <?php if ($settings['giveaway']) : ?>
                        <button class="btn btn-primary" id="faucet_btn">Faucet</button>
                    <?php endif; ?>
            </ul>
            <?php endif; ?>
            <ul class="nav navbar-nav navbar-right">
                <li><a id="won_last">Won last 24h: <?php echo last_won('1 DAY'); ?> Coins</a></li>
                <li><a id="biggest">Biggest win: <?php echo biggest_win(); ?> Coins</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="page">
<?php
echo $_SESSION['user_id'];
if ($game != false) include 'game.php';
else include $page . '.php'; ?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <h1 class="title">Winter white</h1>
                <p><a href="#">About us</a></p>
                <p><a href="#">Meet the team</a></p>
                <p><a href="#">Our blog</a></p>
                <p><a href="#">Get in touch</a></p>
                <p><a href="#">Feedback</a></p>
            </div>
            <div class="col-xs-3">
                <h1 class="title">Support</h1>
                <p><a href="#">Need some help?</a></p>
                <p><a href="#">Call us</a></p>
                <p><a href="#">Live chat</a></p>
                <p><a href="#">Frequently asked questions</a></p>
                <p><a href="#">Get in touch</a></p>
            </div>
            <div class="col-xs-3">
                <h1 class="title">Store</h1>
                <p><a href="#">Shipping & prices</a></p>
                <p><a href="#">International shipping</a></p>
                <p><a href="#">Payment options</a></p>
                <p><a href="#">Credit cards</a></p>
                <p><a href="#">Privacy policy</a></p>
            </div>
            <div class="col-xs-3">
                <h1 class="title">Winter white</h1>
                <p><a href="#">About us</a></p>
                <p><a href="#">Meet the team</a></p>
                <p><a href="#">Our blog</a></p>
                <p><a href="#">Get in touch</a></p>
                <p><a href="#">Feedback</a></p>
            </div>
        </div>
        <div class="row">
            <div class=" col-md-12 text-center">
                <hr>
                &copy; <?php echo date("Y"); ?> Luckzy
            </div>
        </div>
    </div>
</footer>
</div>
<?php
include __DIR__ . '/inc/end.php';
if (logged()):
    ?>
    <div class="modal fade" id="modals-deposit" aria-labelledby="mlabels-deposit" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="mlabels-deposit">Deposit Funds</h4>
                </div>
                <div class="modal-body">
                    <div class="m_alert"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a aria-controls="d_btc" role="tab" href="#d_btc"
                                                                  data-toggle="tab">Bitcoin</a></li>
                        <?php
                        $query = db_query("SELECT * FROM `currencies`");
                        while ($row = db_fetch_array($query)) :
                            if ($row['enabled']):
                                ?>
                                <li role="presentation"><a aria-controls="d_<?php echo $row['id']; ?>"
                                                           href="#d_<?php echo $row['id']; ?>" role="tab"
                                                           data-toggle="tab"><?php echo $row['currency']; ?></a></li>
                            <?php endif;endwhile; ?>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="d_btc" style="text-align: center;">
                            Please send at least <b><?php echo n_num($settings['btc_min_deposit']); ?></b> BTC to this
                            address:
                            <br>
                            <small><i>(1BTC = <?php echo $settings['btc_rate']; ?> Coins)</i></small>
                            <div class="addr-p" style="margin:15px;font-weight:bold;font-size:18px;"></div>
                            <div class="addr-qr"></div>
                            <div class="alert alert-infoo" style="margin: 15px;"><big><b><i>This address is only for a
                                            single use. If you want to deposit multiple times, you should generate new
                                            address.</i></b></big></div>
                            <div style="margin-bottom:15px;">
                                <a href="#" class="gray_a" onclick="javascript:_genNewAddress();return false;">New
                                    Address</a> <span class="color: lightgray">·</span> <a href="#"
                                                                                           class="gray_a pendingbutton"
                                                                                           cj-opened="no"
                                                                                           onclick="javascript:clickPending();return false;">Show
                                    Pending</a>
                            </div>
                            <div class="pendingDeposits" style="display:none;"></div>
                        </div>
                        <?php
                        $query = db_query("SELECT * FROM `currencies`");
                        while ($row = db_fetch_array($query)) :
                            if ($row['enabled']):
                                ?>
                                <div role="tabpanel" class="tab-pane" id="d_<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label for="input-am">Enter the amount you want to deposit (min. <?php echo $row['min_deposit'] . ' ' . $row['currency']; ?>):</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b><?php echo $row['currency']; ?></b></label>
                                                <input type="text" class="form-control" id="d_amount_<?php echo $row['id']; ?>" onkeydown="if (event.keyCode == 13) deposit('<?php echo $row['id']; ?>');" onkeyup="$('#d_amount_<?php echo $row['id']; ?>_coins').val($(this).val()*<?php echo $row['rate']; ?>)">
                                            </div>
                                            <div class="col-md-6">
                                                <label><b>Luckzy coins</b></label>
                                                <b style="position: absolute;top: 32px;left: -3px;">=</b><input type="text" class="form-control" id="d_amount_<?php echo $row['id']; ?>_coins" onkeydown="if (event.keyCode == 13) deposit('<?php echo $row['id']; ?>');" onkeyup="$('#d_amount_<?php echo $row['id']; ?>').val($(this).val()/<?php echo $row['rate']; ?>)">
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="deposit('<?php echo $row['id']; ?>');">Deposit </button>
                                </div>
                            <?php endif;endwhile; ?>
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
                    <h4 class="modal-title" id="mlabels-withdraw">Withdraw Funds - Your Coin
                        Balance: <span class="balance"><?php echo $player['balance']; ?></span></h4>
                </div>
                <div class="modal-body">
                    <div class="m_alert"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a aria-controls="w_btc" role="tab" href="#w_btc"
                                                                  data-toggle="tab">Bitcoin</a></li>
                        <?php
                        $query = db_query("SELECT * FROM `currencies`");
                        while ($row = db_fetch_array($query)) :
                            if ($row['enabled']):
                                ?>
                                <li role="presentation"><a aria-controls="w_<?php echo $row['id']; ?>"
                                                           href="#w_<?php echo $row['id']; ?>" role="tab"
                                                           data-toggle="tab"><?php echo $row['currency']; ?></a></li>
                            <?php endif;endwhile; ?>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="w_btc">
                            <div><small><b>Your Bitcoin Balance:</b> <span class="btc_balance"><?php echo $player['btc_balance']; ?></span> <b>| Coin value: </b><span class="btc_value"><?php echo round(bcmul($player['btc_balance'],$settings['btc_rate']),2); ?></span> (<span class="btc_mod"><?php echo round(bcmul(bc_div(bcmul($player['btc_balance'],$settings['btc_rate']),$player['balance']),100),2); ?></span>% of your balance)</small></div>
                            <div class="form-group">
                                <label for="input-address">Enter valid BTC address:</label>
                                <input type="text" class="form-control" id="w_btc_address">
                            </div>
                            <div class="form-group">
                                <label for="input-am">Enter the amount of <b>coins</b> you want to withdraw
                                    (min. <?php echo n_num($settings['min_withdrawal']); ?> Coins):</label>
                                <input type="text" class="form-control" id="w_amount_btc" style="width:150px;"
                                       onkeydown="if (event.keyCode == 13) withdraw('btc');">
                            </div>
                            <button class="btn btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;"
                                    onclick="withdraw('btc');">Withdraw
                            </button>
                        </div>
                        <?php
                        $query = db_query("SELECT * FROM `currencies`");
                        while ($row = db_fetch_array($query)) :
                            if ($row['enabled']):
                                ?>
                                <div role="tabpanel" class="tab-pane" id="w_<?php echo $row['id']; ?>">
                                    <div><small><b>Your <?php echo $row['currency']; ?> Balance:</b> <span class="<?php echo $row['currency']; ?>_balance"><?php echo $player[$row['currency'].'_balance']; ?></span> <b>| Coin value: </b><span class="<?php echo $row['currency']; ?>_value"><?php echo bcmul($player[$row['currency'].'_balance'],$row['rate']); ?></span> (<span class="<?php echo $row['currency']; ?>_mod"><?php echo round(bc_div(bcmul($player[$row['currency'].'_balance'],$row['rate']),$player['balance'])*100,2); ?></span>% of your balance)</small></div>
                                    <div class="form-group">
                                        <label for="input-am">Enter the amount of <b>coins</b> you want to withdraw
                                            (min. <?php echo n_num($settings['min_withdrawal']); ?> Coins):</label>
                                        <input type="text" class="form-control" id="w_amount_<?php echo $row['id']; ?>"
                                               onkeydown="if (event.keyCode == 13) withdraw('<?php echo $row['id']; ?>');">
                                    </div>
                                    <button class="btn  btn-primary"
                                            style="height: 39px;line-height:39px; padding: 0 20px;"
                                            onclick="withdraw('<?php echo $row['id']; ?>');">Withdraw
                                    </button>
                                </div>
                            <?php endif;endwhile; ?>
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
                    <?php echo '<b>' . $settings['giveaway_amount'] . '</b> Coins'; ?>
                    <div class="form-group">
                        <label>Enter text from image</label><br>
                        <a class="captchadiv" href="#"
                           onclick="javascript:$(this).children().remove().clone().appendTo($(this));return false;"
                           data-toggle="tooltip" data-placement="top" title="Click to refresh"><img
                                src="./content/captcha/genImage.php"></a>
                        <input type="text" class="captchaInput form-control" id="input-captcha">
                    </div>
                    <a href="#" onclick="claim_bonus();" class="btn btn-primary"
                       style="height: 39px;line-height:39px; padding: 0 20px;">Claim</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modals-transactions" aria-labelledby="modals-transactions" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="mlabels-transactions">Transaction history</h4>
                </div>
                <div class="modal-body">
                    <div class="m_alert"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a aria-controls="deposits" role="tab" href="#deposits"
                                                                  data-toggle="tab">Deposits</a></li>
                        <li role="presentation"><a aria-controls="withdrawals" role="tab" href="#withdrawals"
                                                   data-toggle="tab">Withdrawals</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="deposits">
                            <table class="table table-stripped">
                                <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Coins</th>
                                    <th>Address/ID</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php echo get_deposits(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="withdrawals">
                            <table class="table table-stripped">
                                <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Coins</th>
                                    <th>Address/ID</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php echo get_withdrawals(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
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
                            <input type="password" id="passwd" class="form-control"
                                   onkeydown="if (event.keyCode == 13) login();">
                        </div>
                    </div>
                    <div class="form-group" id="2facode" style="display: none;">
                        <label for="input-username">Google authentication code:</label>
                        <input type="text" id="totp" class="form-control" onkeydown="if (event.keyCode == 13) login();">
                    </div>
                    <div style="margin-bottom: 10px"><a href="javascript:forgot_password();">Forgot password?</a></div>
                    <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;"
                            onclick="login();">Login
                    </button>
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
                        <label for="input-email">Email:</label>
                        <input id="email" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="input-password">Password</label>
                        <input id="passwd" type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="input-password">Re-type password</label>
                        <input id="re_passwd" type="password" class="form-control"
                               onkeydown="if (event.keyCode == 13) register();">
                    </div>
                    <button class="btn btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;"
                            onclick="register();">Sign up
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php if(isset($_GET['reset']) && db_num_rows(db_query("SELECT `id` FROM `players` WHERE `password_reset_hash`='".$_GET['teset']."' LIMIT 1")) != 0){
        $player = db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `password_reset_hash`='".$_GET['reset']."' LIMIT 1"));
     ?>
    <div class="modal fade" id="modals-reset" aria-labelledby="mlabels-reset" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="mlabels-teset">Reset password</h4>
                </div>
                <div class="modal-body">
                    <div class="m_alert"></div>
                    <div class="form-group">
                        <label for="input-password">Password</label>
                        <input id="passwd" type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="input-password">Re-type password</label>
                        <input id="re_passwd" type="password" class="form-control"
                               onkeydown="if (event.keyCode == 13) save_password(<?php echo $player['id'] ?>);">
                    </div>
                    <button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;"
                            onclick="save_password(<?php echo $player['id'] ?>);">Save password
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#modals-reset').modal('show');
    </script>
<?php } endif; ?>
<div class="leftblock">
    <?php if($_COOKIE['chat'] != ''): ?>
    <script>
       $(window).load(function(){
           leftCon('<?php echo $_COOKIE['chat']; ?>');
       });
    </script>
    <?php endif; ?>
</div>

<div class="leftCon lc-chat" id="lc-chat">

    <div class="heading">
        <a class="glyphicon glyphicon-align-left chat-rooms-toggle" href="javascript:leftCon('chat-rooms');"></a>
        <?php if(logged()): ?><a class="glyphicon glyphicon-user chat-users-toggle" style="padding: 0px 10px" href="javascript:leftCon('chat-users');"></a><?php endif; ?>
        <span class="current_room"><?php echo $chat_room; ?></span>
    </div>
    <div class="content"></div>
    <div class="footer">
        <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top"
               title="Press ENTER to send">
      <div style="padding: 15px 0px 0px;display: inline-block;width: 100%;">
        <div style="float: left; padding-top: 7px">Online users: <span class="online-users"><?php echo online_count(); ?></span>
            <?php if(logged()): ?><span style="padding-left: 5px"><a href="javascript:leftCon('chat-settings');" class="glyphicon glyphicon-cog"></a></span><?php endif; ?>
        </div>
        <button class="chat-send btn btn-primary btn-sm" style="float: right">Send</button>
      </div>
      </div>
    </div>
</div>

<div class="leftCon lc-chat" id="lc-chat-rooms">
    <div class="heading">
        <a class="glyphicon glyphicon-align-left chat-rooms-toggle" href="javascript:leftCon('chat');"></a>
        <?php if(logged()): ?><a class="glyphicon glyphicon-user chat-users-toggle" style="padding: 0px 10px" href="javascript:leftCon('chat-users');"></a><?php endif; ?>
        <span class="current_room"><?php echo $chat_room; ?></span>
    </div>
    <div class="content">
        <h5><b>Public chat rooms</b></h5>
        <div><a href="javascript:select_room(0,0)">Global</div>
        </a>
        <?php
        $query = db_query("SELECT * FROM `chat_rooms` WHERE `id` != 0");
        while ($row = db_fetch_array($query)):
            ?>
            <div><a href="javascript:select_room(<?php echo $row['id'] ?>,0)"><?php echo $row['name'] ?></div></a>
        <?php endwhile; ?>
        <?php if(logged()): ?>
        <h5><b>Private messages</b></h5>
        <div id="pms">
            <?php echo get_pms(); ?>
        </div>
        <?php endif; ?>
        </a>
    </div>
  <div class="footer">
    <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top"
           title="Press ENTER to send">
    <div style="padding: 10px 0px">
      <span style="position: relative; top: 7px;">Online users: <span class="online-users"><?php echo online_count(); ?></span>
      <?php if(logged()): ?><span style="padding-left: 5px"><a href="javascript:leftCon('chat-settings');" class="glyphicon glyphicon-cog"></a></span></span><?php endif; ?>
      <button class="chat-send btn btn-primary btn-sm" style="float: right">Send</button>
    </div>
  </div>
</div>

<div class="leftCon lc-chat" id="lc-chat-users">
    <div class="heading">
        <a class="glyphicon glyphicon-align-left chat-rooms-toggle" href="javascript:leftCon('chat-rooms');"></a>
        <?php if(logged()): ?><a class="glyphicon glyphicon-user chat-users-toggle" style="padding: 0px 10px" href="javascript:leftCon('chat');"></a><?php endif; ?>
        <span class="current_room"><?php echo $chat_room; ?></span></div>
    <div class="content">
        <div>
            <h5><b>My friends (<span class="friend_count"><?php echo count_friends(); ?></span>)</b> <a
                    href="javascript:make_friend()">+
                    <sapn class="glyphicon glyphicon-user"></sapn>
                </a></h5>
            <hr>
        </div>
        <div>
            <h5><b>Friend requests (<span class="requests_count"><?php echo count_friends(10); ?></span>)</b></h5>
            <div class="friend_requests"><?php echo get_friends(10); ?></div>
        </div>
        <div>
            <h5><b>Online (<span class="online_count"><?php echo count_friends(1); ?></span>)</b></h5>
            <div class="online_friends"><?php echo get_friends(1); ?></div>
        </div>
        <div>
            <h5><b>Offline (<span class="offline_count"><?php echo count_friends(0); ?></span>)</b></h5>
            <div class="offline_friends"><?php echo get_friends(0); ?></div>
        </div>
        <div>
            <h5><b>Ignored (<span class="ignored_count"><?php echo count_friends(-1); ?></span>)</b></h5>
            <div class="ignored_friends"><?php echo get_friends(-1); ?></div>
        </div>
    </div>
  <div class="footer">
    <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top"
           title="Press ENTER to send">
    <div style="padding: 10px 0px">
      <span style="position: relative; top: 7px;">Online users: <span class="online-users"><?php echo online_count(); ?></span>
      <?php if(logged()): ?><span style="padding-left: 5px"><a href="javascript:leftCon('chat-settings');" class="glyphicon glyphicon-cog"></a></span></span><?php endif; ?>
      <button class="chat-send btn btn-primary btn-sm" style="float: right">Send</button>
    </div>
  </div>
</div>
<?php if(logged()): ?>
<div class="leftCon lc-chat" id="lc-chat-settings">
  <div class="heading">
      <a class="glyphicon glyphicon-align-left chat-rooms-toggle" href="javascript:leftCon('chat-rooms');"></a>
      <a class="glyphicon glyphicon-user chat-users-toggle" style="padding: 0px 10px" href="javascript:leftCon('chat-users');"></a>
      <span class="current_room"><?php echo $chat_room; ?></span>
  </div>
  <div class="content">
    <h5><b><Settings></Settings></b></h5>
      <div class="row" style="padding: 5px 0px">
        <div class="col-md-8"><b>Status: </b></div>
        <div class="btn-group btn-group-xs col-md-4" role="group">
          <button type="button" class="btn btn-default chat_status <?php if($player['chat_status']) echo 'active'; ?>" onclick="chat_status(this, 1)">On</button>
          <button type="button" class="btn btn-default chat_status <?php if(!$player['chat_status']) echo 'active'; ?>" onclick="chat_status(this,0)">Off</button>
        </div>
      </div>
      <div class="row" style="padding: 5px 0px">
        <div class="col-md-12">
          <b><a href="#">Chat rules</a></b>
        </div>
      </div>
  </div>
  <div class="footer">
    <input type="text" class="chat-input" placeholder="Type your message" data-toggle="tooltip" data-placement="top" title="Press ENTER to send">
    <div style="padding: 10px 0px">
      <span style="position: relative; top: 7px;">Online users: <span class="online-users"><?php echo online_count(); ?></span>
      <span style="padding-left: 5px"><a href="javascript:leftCon('chat');" class="glyphicon glyphicon-cog"></a></span></span>
      <button class="chat-send btn btn-primary btn-sm" style="float: right">Send</button>
    </div>
  </div>
</div>
<?php endif; ?>
</body>
</html>