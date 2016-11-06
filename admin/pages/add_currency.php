<h1>Add currency</h1>
    <form method="post" action="./?p=currencies">
    <input type="hidden" name="new_currency" value="1">
    <table style="border: 0; border-collapse: collapse;">
        <tr>
            <td style="padding-bottom: 10px;">
                <input type="checkbox" value="1" name="enabled" id="enabled">
                <label for="enabled" class="chckbxLabel">Enable</label>
            </td>
        </tr>
        <tr>
            <td>Name: </td>
            <td><input name="currency" type="text"></td>
        </tr>
        <tr>
            <td>Conversion rate: </td>
            <td><input name="rate" type="text"> <a href="#" style="color: #4F556C;" title="(Enter amount of how many coins user get for 1 unit)"><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
        <tr>
            <td>Minimal deposit:</td>
            <td><input name="min_deposit" type="text"> <a href="#" style="color: #4F556C;" title="Amount in coins"><span class="glyphicon glyphicon-question-sign"></span></a></td>
        </tr>
        <tr>
            <td>Instructions:</td>
            <td>
                <textarea name="instructions" rows="10"></textarea>
            </td>
        </tr>
    </table>
        <input type="submit" value="Save" style="margin-top: 10px;margin-left: auto;margin-right: auto;">
        </form>