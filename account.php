<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/
if (!logged()) header("Location: ./");
include __DIR__ . '/inc/ga_class.php';
?>
    <div class="container content">
        <div class="row">
            <div class="col-md-12">
                <a href="/account" style="margin-right: 15px;" class="active">Account settings</a>
                <a href="/authentication">Authentication settings</a>
                <?php if(!$player['state']): ?>
                <h1>Activation</h1>
                <p>Please activate your account to make any transactions</p>
                <button class="btn btn-primary" onclick="resend_activation()">Resend activation email</button>
                <?php endif; ?>
                <h1>Account settings</h1>
                <div class="col-md-4">
                    <form class="form-horizontal" id="account_settings" action="javascript: save_account_settings();">
                        <div class="form-group">
                            <label>Username*</label>
                            <input type="text" id="username" class="form-control" value="<?php echo $player['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email*</label>
                            <input type="email" id="email" class="form-control" value="<?php echo $player['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>New password</label> 
                            <input type="password" id="passwd" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Retype password</label>
                            <input type="password" id="re_passwd" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Currency settings</label>
                            <select class="form-control" id="currency_preference">
                                <option <?php if($player['currency_preference'] == 0) echo 'selected'; ?> value="0">Take from other currencies balance first over Bitcoin</option>
                                <option <?php if($player['currency_preference'] == 1) echo 'selected'; ?> value="1">Take from Bitcoin balance first over other currencies</option>
                                <option <?php if($player['currency_preference'] == 2) echo 'selected'; ?> value="2">Take from any currency at random</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/inc/end.php'; ?>