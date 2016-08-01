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
<form method="post" action="./?p=currencies">
<input type="hidden" name="currencies_form" value="1">
<fieldset>
    <legend>Bitcoin</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">
                <input type="checkbox" value="1" checked="checked" disabled id="btc_chckbx" name="btx_enable">
                <label for="btc_chckbx" class="chckbxLabel">Enable</label>
            </td>
            <td>
                Conversion rate: <input type="text" name="btc_rate" value="<?php echo $settings['btc_rate']; ?>"> (1BTC = <?php echo $settings['btc_rate']; ?> Coins)
            </td>
        </tr>
    </table>
</fieldset>
<fieldset style="margin-top: 10px">
    <legend>Runescape 3</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">
                <input type="checkbox" value="1"<?php if ($settings['rns3']==1) echo ' checked="checked"'; ?> id="rns3_chckbx" name="rns3_enable">
                <label for="rns3_chckbx" class="chckbxLabel">Enable</label>
            </td>
            <td>
                Conversion rate: <input type="text" name="rns3_rate" value="<?php echo $settings['rns3_rate']; ?>"> (1 Runescape 3 = <?php echo $settings['rns3_rate']; ?> Coins)
            </td>
        </tr>
    </table>
</fieldset>
<fieldset style="margin-top: 10px">
    <legend>Oldschool runescape</legend>
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 0;">
                <input type="checkbox" value="1"<?php if ($settings['orns']==1) echo ' checked="checked"'; ?> id="orns_chckbx" name="orns_enable">
                <label for="orns_chckbx" class="chckbxLabel">Enable</label>
            </td>
            <td>
                Conversion rate: <input type="text" name="orns_rate" value="<?php echo $settings['orns_rate']; ?>"> (1 Oldschool runescape = <?php echo $settings['orns_rate']; ?> Coins)
            </td>
        </tr>
    </table>
</fieldset>
<input type="submit" value="Save" style="margin-top: 10px;margin-left: auto;margin-right: auto;">
</form>