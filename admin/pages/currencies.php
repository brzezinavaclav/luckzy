<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/


if (!isset($init)) exit();

if (isset($_POST['currencies_form']))
    echo '<div class="zprava zpravagreen"><b>Success!</b> Data was successfuly saved.</div>';

?>
<h1>Currencies</h1>
<style>
    td:first-of-type{
        width: 219px;
    }
</style>
<form method="post" action="./?p=currencies">
<input type="hidden" name="currencies_form" value="1">
<fieldset>
    <legend>Bitcoin</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding-bottom: 10px;">
                <input type="checkbox" value="1" checked="checked" disabled id="btc_chckbx" name="btx_enable">
                <label for="btc_chckbx" class="chckbxLabel">Enable</label>
            </td>
        </tr>
        <tr>
            <td>Conversion rate:</td>
            <td><input type="text" name="btc_rate" value="<?php echo $settings['btc_rate']; ?>"> (1BTC = <?php echo $settings['btc_rate']; ?> Coins)</td>
        </tr>
        <tr>
            <td>Minimal deposit:</td>
            <td><input type="text" name="btc_min_deposit" value="<?php echo $settings['btc_min_deposit']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in <?php echo $settings['currency_sign']; ?>."><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
        <tr>
            <td>Required confirmations:</td>
            <td><input type="text" name="min_confirmations" value="<?php echo $settings['min_confirmations']; ?>"></td>
        </tr>
        <tr>
            <td>Withdraw approval:</td>
            <td>
                <select name="w_mode">
                    <option value="0"<?php if (!$settings['withdrawal_mode']) echo ' selected="selected"'; ?>>Automatic</option>
                    <option value="1"<?php if ($settings['withdrawal_mode']) echo ' selected="selected"'; ?>>Manual</option>
                </select>
                <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="When set to manual, each player's withdraw request must be approved in administration (Wallet section)."><span class="glyphicon glyphicon-question-sign"></span></a>
            </td>
        </tr>
        <tr>
            <td>Transaction fee:</td>
            <td><input type="text" name="txfee" value="<?php $infofee=walletRequest('getinfo'); echo $infofee['paytxfee']; ?>">  <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in <?php echo $settings['currency_sign']; ?>. Transaction fee to <?php echo $settings['currency']; ?> network. This is covered by casino for all withdrawals."><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
    </table>
</fieldset>
<fieldset style="margin-top: 10px">
    <legend>Runescape 3</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding-bottom: 10px;">
                <input type="checkbox" value="1"<?php if ($settings['rns3']==1) echo ' checked="checked"'; ?> id="rns3_chckbx" name="rns3_enable">
                <label for="rns3_chckbx" class="chckbxLabel">Enable</label>
            </td>
        </tr>
        <tr>
            <td>Conversion rate: </td>
            <td><input type="text" name="rns3_rate" value="<?php echo $settings['rns3_rate']; ?>"> (1 Runescape 3 = <?php echo $settings['rns3_rate']; ?> Coins)</td>
        </tr>
        <tr>
            <td>Minimal deposit:</td>
            <td><input type="text" name="rns3_min_deposit" value="<?php echo $settings['rns3_min_deposit']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in <?php echo $settings['currency_sign']; ?>."><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
    </table>
</fieldset>
<fieldset style="margin-top: 10px">
    <legend>Oldschool runescape</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding-bottom: 10px;">
                <input type="checkbox" value="1"<?php if ($settings['orns']==1) echo ' checked="checked"'; ?> id="orns_chckbx" name="orns_enable">
                <label for="orns_chckbx" class="chckbxLabel">Enable</label>
            </td>
        </tr>
        <tr>
            <td>Conversion rate:</td>
            <td><input type="text" name="orns_rate" value="<?php echo $settings['orns_rate']; ?>"> (1 Oldschool runescape = <?php echo $settings['orns_rate']; ?> Coins)</td>
        </tr>
        <tr>
            <td>Minimal deposit:</td>
            <td><input type="text" name="orns_min_deposit" value="<?php echo $settings['orns_min_deposit']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in <?php echo $settings['currency_sign']; ?>."><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
    </table>
</fieldset>
<input type="submit" value="Save" style="margin-top: 10px;margin-left: auto;margin-right: auto;">
</form>