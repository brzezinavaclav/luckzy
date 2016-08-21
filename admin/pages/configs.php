<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

if (!empty($warnStatus)) {
  echo $warnStatus;
}

?>

<h1>Configuration</h1>

<style>
  td:first-of-type{
    width: 219px;
  }
</style>
<form action="./?p=configs" method="post">
<fieldset>
  <legend>Basic Settings</legend>

    <table>
      <tr>
        <td>Site Title:</td>
        <td><input type="text" name="s_title" value="<?php echo $settings['title']; ?>"></td>
      </tr>
      <tr>
        <td>Site URL:</td>
        <td><input type="text" name="s_url" value="<?php echo $settings['url']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Without http://"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td>Site Description:</td>
        <td><input type="text" name="s_desc" value="<?php echo $settings['description']; ?>"></td>
      </tr>
      <tr>
        <td>Minimal bet:</td>
        <td><input type="text" name="min_bet" value="<?php echo $settings['min_bet']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in Coins"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td>Minimal withdrawal:</td>
        <td><input type="text" name="min_withdrawal" value="<?php echo $settings['min_withdrawal']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Amount in Coins"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td>Bankroll/max bet ratio</td>
        <td><input type="text" name="bankroll_maxbet_ratio" value="<?php echo $settings['bankroll_maxbet_ratio']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="The default ratio between amount in wallet and max available bet is set to 25. So for example if you want to allow players to bet 1 <?php echo $settings['currency_sign']; ?>, you have to have 25 <?php echo $settings['currency_sign']; ?> in server bankroll."><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
    </table>
  
  <style type="text/css">
    form table tr td a {
      font-size: 14px;  
    }
  </style>

</fieldset>

  <fieldset style="margin-top: 10px">
    <legend>Email settings</legend>
    <table>
      <tr>
        <td>Email:</td>
        <td><input type="text" name="email" value="<?php echo $settings['email']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Your email"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td style="padding-bottom: 10px;">
          <input type="checkbox" value="1" name="smtp_enabled" id="smtp_enabled" <?php if ($settings['smtp_enabled']==1) echo ' checked="checked"'; ?>>
          <label for="smtp_enabled" class="chckbxLabel">Use SMTP</label> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Ensures more reaible mail delivery"><span class="glyphicon glyphicon-question-sign"></span></a>
        </td>
      </tr>
      <tr>
        <td>SMTP server:</td>
        <td><input type="text" name="smtp_server" value="<?php echo $settings['smtp_server']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="SMTP server"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td>SMTP password:</td>
        <td><input type="password" name="smtp_password" value="<?php echo $settings['smtp_password']; ?>"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="Your email password"><span class="glyphicon glyphicon-question-sign"></span></a></td>
      </tr>
      <tr>
        <td>Encryption:</td>
        <td>
          <select name="smtp_encryption">
            <option value="0"<?php if ($settings['smtp_encryption']==0) echo ' selected="selected"'; ?>> TLS
            <option value="1"<?php if ($settings['smtp_encryption']==1) echo ' selected="selected"'; ?>> SSL
          </select>
        </td>
      </tr>
      <tr>
        <td style="padding-bottom: 10px;">
          <input type="checkbox" value="1" name="smtp_auth" id="smtp_auth" <?php if ($settings['smtp_auth']==1) echo ' checked="checked"'; ?>>
          <label for="smtp_auth" class="chckbxLabel">SMTP Authentication</label>
        </td>
      </tr>
    </table>

  </fieldset>

<fieldset style="margin-top: 10px">
  <legend>Blackjack</legend>
  <table>
    <tr>
      <td>Blackjack pays:</td>
      <td>
        <select name="bj_pays">
          <option value="0"<?php if ($settings['bj_pays']==0) echo ' selected="selected"'; ?>>3 to 2 (lower house edge)
          <option value="1"<?php if ($settings['bj_pays']==1) echo ' selected="selected"'; ?>>6 to 5 (higher house edge)
        </select>
      </td>
      <td><small></small></td>
    </tr>
    <tr>
      <td>Dealer hits on soft 17:</td>
      <td>
        <select name="hits_on_soft">
          <option value="1"<?php if ($settings['hits_on_soft']==1) echo ' selected="selected"'; ?>>yes (higher house edge)
          <option value="0"<?php if ($settings['hits_on_soft']==0) echo ' selected="selected"'; ?>>no (lower house edge)
        </select>
      </td>
      <td><small></small></td>
    </tr>
    <tr>
      <td>Number of decks:</td>
      <td>
        <select name="number_of_decks">
          <option value="1"<?php if ($settings['number_of_decks']==1) echo ' selected="selected"'; ?>>1 (lowest house edge)
          <option value="2"<?php if ($settings['number_of_decks']==2) echo ' selected="selected"'; ?>>2
          <option value="4"<?php if ($settings['number_of_decks']==4) echo ' selected="selected"'; ?>>4
          <option value="6"<?php if ($settings['number_of_decks']==6) echo ' selected="selected"'; ?>>6
          <option value="8"<?php if ($settings['number_of_decks']==8) echo ' selected="selected"'; ?>>8 (highest house edge)
        </select>
      </td>
      <td><small></small></td>
    </tr>
    <tr>
      <td>On tie:</td>
      <td>
        <select name="tie_dealerwon">
          <option value="1"<?php if ($settings['tie_dealerwon']==1) echo ' selected="selected"'; ?>>Dealer won (higher house edge)
          <option value="0"<?php if ($settings['tie_dealerwon']==0) echo ' selected="selected"'; ?>>Bet is returned (lower house edge)
        </select>
      </td>
      <td><small></small></td>
    </tr>
    <tr>
      <td>Insurance:</td>
      <td>
        <select name="insurance">
          <option value="1"<?php if ($settings['insurance']==1) echo ' selected="selected"'; ?>>Enabled (lower house edge)
          <option value="0"<?php if ($settings['insurance']==0) echo ' selected="selected"'; ?>>Disabled (higher house edge)
        </select>
      </td>
      <td><small></small></td>
    </tr>
  </table>
</fieldset>

<fieldset style="margin-top: 10px">
  <legend>Slots</legend>
  <form action="./?p=configs" method="post">
  <table>
  <tr>
    <td>Jackpot multiplier</td>
    <td><input type="number" name="jackpot" value="<?php echo $settings['jackpot']; ?>" max="12339" min="0"> <a href="#" style="color: #4F556C;" onclick="javascript:return false;" title="The lower this amount is, the higher is the house edge."><span class="glyphicon glyphicon-question-sign"></span></a></td>
    <td style="padding-top: 3px;"><small><i>Current expected house edge: <b><?php echo round(house_edge(), 4); ?></b> %</i></small></td>
  </tr>
  </table>
</fieldset>

<fieldset style="margin-top: 10px">
  <legend>Dice</legend>
  <table>
    <tr>
      <td>House edge:</td>
      <td><input type="text" name="house_edge" value="<?php echo $settings['house_edge']; ?>"> %</td>
      <td><small><i>Your approximate profit from total wagered amount.</i></small></td>
    </tr>
  </table>
</fieldset>


<fieldset style="margin-top: 10px;">
  <legend>System Maintenance</legend>
  <table>

    <?php
    if (!$settings['maintenance']) $onoff = 'Activate';
    else $onoff = 'Deactivate';
    ?>

    <tr>
      <td style="padding-top: 5px;">Maintenance Mode:</td>
      <td><button style="padding: 4px;" onclick="javascript:location.href='./?p=configs&maintenance';"><?php echo $onoff; ?></button></td>
    </tr>
    <tr>
      <td style="padding-top: 5px;">Reinstall CoinSlots:</td>
      <td><button style="padding: 4px;" onclick="javascript:if(confirm('WARNING! This will enable anyone to access the script installer until the installation is done. Do you want to proceed?')){location.href='./?p=configs&reinstall_4';}">Reinstall</button></td>
    </tr>
  </table>

</fieldset>

<input type="submit" value="Save" style="margin-top: 10px">
</form>