<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/


if (!isset($init)) exit();

if (isset($_POST['currencies_form']) || isset($_POST['new_currency']))
    echo '<div class="zprava zpravagreen"><b>Success!</b> Data was successfuly saved.</div>';

?>
<script>
    function delete_currency(id){
        if (confirm('Do you really want to delete this currency?')) {
            $.ajax({
                'url': 'ajax/delete_currency.php?currency='+id,
                'dataType': "json",
                'success': function() {
                    location.reload();
                }
            });
        }
    }
</script>
<h1>Currencies</h1>
<a href="./?p=add_currency">Add new</a>
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
            <td><input type="text" name="btc_min_deposit" value="<?php echo $settings['btc_min_deposit']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in Coins"><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
        <tr>
            <td>Required confirmations:</td>
            <td><input type="text" name="min_confirmations" value="<?php echo $settings['min_confirmations']; ?>"> <a href="#" style="color: #4F556C;" title="Minimum number of bitcoin transaction confirmations"><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
        <tr>
            <td>Withdraw approval:</td>
            <td>
                <select name="w_mode">
                    <option value="0"<?php if (!$settings['withdrawal_mode']) echo ' selected="selected"'; ?>>Automatic</option>
                    <option value="1"<?php if ($settings['withdrawal_mode']) echo ' selected="selected"'; ?>>Manual</option>
                </select>
                <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="When set to manual, each player's withdraw request must be approved in administration"><span class="glyphicon glyphicon-question-sign"></span></a>
            </td>
        </tr>
        <tr>
            <td>Transaction fee:</td>
            <td><input type="text" name="txfee" value="<?php $infofee=walletRequest('getinfo'); echo $infofee['paytxfee']; ?>">  <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in <?php echo $settings['currency_sign']; ?>. Transaction fee to <?php echo $settings['currency']; ?> network. This is covered by casino for all withdrawals."><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
    </table>
</fieldset>

<?php
    $query=db_query("SELECT * FROM `currencies`");
    while ($row=db_fetch_array($query)) {
        ?>
            <fieldset style="margin-top: 10px">
                <legend><?php echo $row['currency']; ?></legend>
                <table style="border: 0; border-collapse: collapse;">
                    <tr>
                        <td style="padding-bottom: 10px;">
                            <input type="checkbox" value="1"<?php if ($row['enabled'] == 1) echo ' checked="checked"'; ?> name="<?php echo $row['id']; ?>_enabled" id="<?php echo $row['id']; ?>_enabled">
                            <label for="<?php echo $row['id']; ?>_enabled" class="chckbxLabel">Enable</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Name: </td>
                        <td><input name="<?php echo $row['id']; ?>_name" type="text"  value="<?php echo $row['currency']; ?>"></td>
                    </tr>
                    <tr>
                        <td>Conversion rate: </td>
                        <td><input name="<?php echo $row['id']; ?>_rate" type="text"  value="<?php echo $row['rate']; ?>"> <a href="#" style="color: #4F556C;" title="(1 Runescape 3 = <?php echo $row['rate']; ?> Coins)"><span class="glyphicon glyphicon-question-sign"></span></a></td>
                    </tr>
                    <tr>
                        <td>Minimal deposit:</td>
                        <td><input name="<?php echo $row['id']; ?>_min_deposit" type="text"  value="<?php echo $row['min_deposit']; ?>"> <a href="#" style="color: #4F556C;" title="Amount in Coins"><span class="glyphicon glyphicon-question-sign"></span></a></td>
                    </tr>
                    <tr>
                        <td>Instructions:</td>
                        <td style="position: relative">
                            <textarea style="margin-right: 5px" name="<?php echo $row['id']; ?>_instructions" rows="10"><?php echo $row['instructions']; ?></textarea> <a href="#" style="color: #4F556C; position: absolute;  top: 2px;" title="Instructions for for finishing transaction"><span class="glyphicon glyphicon-question-sign"></span></a>
                        </td>
                    </tr>
                    <tr><td colspan="2"><a href="javascript:delete_currency(<?php echo $row['id'];?>);">Delete</a></td></tr>
                </table>
            </fieldset>
        <?php
    }
?>
<input type="submit" value="Save" style="margin-top: 10px;margin-left: auto;margin-right: auto;">
</form>