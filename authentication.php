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
            <a href="/account" style="margin-right: 15px;">Account settings</a>
            <a href="/authentication">Authentication settings</a>
            <h1>Two factor authentication setup</h1>
            <?php
            if ($player['ga_token'] == '') {
                $newtoken = Google2FA::generate_secret_key();
                ?>
                Scan this QR code to your authenticator:
                <div class="tqrcode"></div>
                <script type="text/javascript">
                    $(".tqrcode").qrcode("otpauth://totp/<?php echo $player['username'] . '@' . $settings['url'] . '?secret=' . $newtoken; ?>");
                </script>
                or write the secret key manualy:<br>
                <b><?php echo $newtoken; ?></b>
            <br>
            <br>
                Enter one-time password generated by your authenticator here:
            <br>
                <div class="form-inline" style="padding: 10px 0px">
                    <input type="text" id="totp" class="form-control">
                    <button onclick="pair('<?php echo $newtoken; ?>', '<?php echo $player['id']; ?>');"
                            class="btn btn-primary">Authenticate
                    </button>
                </div>
            <?php } else { ?>
            <br>Two factor authentication is <b>active</b> for this account. <a href="./?p=set_ga&rem">Disable</a>
            <?php } ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/inc/end.php'; ?>