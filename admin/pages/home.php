<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();
?>
<h1>Stats</h1>
<style>
  td:first-of-type{
    width: 219px;
  }
</style>
<div class="menu_ menu-horizontal">
  <ul>
    <li><a href="./" class="<?php if (!isset($_GET['g'])) echo 'active_'; ?>">All bets</a></li>
    <li><a href="?g=dice" class="<?php if (isset($_GET['g']) && $_GET['g']=='dice') echo 'active_'; ?>">Dice</a></li>
    <li><a href="?g=slots" class="<?php if (isset($_GET['g']) && $_GET['g']=='slots') echo 'active_'; ?>">Slots</a></li>
    <li><a href="?g=blackjack" class="<?php if (isset($_GET['g']) && $_GET['g']=='blackjack') echo 'active_'; ?>">Blackjack</a></li>
  </ul>
</div>
<fieldset>
<legend>Beting Stats</legend>
<table class="vypis_table">
  <tr class="vypis_table_obsah">
    <td>Number of bets:</td>
    <td><b><?php echo get_count(); ?></b></td>
  </tr>
  <tr class="vypis_table_obsah">
    <td>Total wagered:</td>
    <td><b><?php echo get_wagered(); ?></b> Coins</td>
  </tr>
  <tr class="vypis_table_obsah">
    <td style="color: green;">Wins:</td>
    <td style="color: green;"><b><?php echo get_count('','wins'); ?></b></td>
  </tr>
  <tr class="vypis_table_obsah">
    <td>Ties:</td>
    <td><b><?php echo get_count('','ties'); ?></b></td>
  </tr>
  <tr class="vypis_table_obsah">
    <td style="color: #d10000;">Losses:</td>
    <td style="color: #d10000;"><b><?php echo get_count('','losses'); ?></b></td>
  </tr>
  <tr class="vypis_table_obsah">
    <td style="color: #a06d00;">W/L ratio:</td>
    <td style="color: #a06d00;"><b><?php echo get_count('','wins')/ get_count('','losses') ?></b></td>
  </tr>
</table>
</fieldset>
  <fieldset style="margin-top: 10px;">
    <legend>House edge</legend>
<table class="vypis_table">
  <tr class="vypis_table_head">
    <th>Period</th>
    <th>Real house edge</th>
    <th>Profit</th>
  </tr>
  <tr>
    <td>Last hour</td>
    <?php real_edge("1 HOUR"); ?>
  </tr>
  <tr>
    <td>Last 24h</td>
    <?php real_edge("24 HOUR"); ?>
  </tr>
  <tr>
    <td>Last 7d</td>
    <?php real_edge("7 DAY"); ?>
  </tr>
  <tr>
    <td>Last 30d</td>
    <?php real_edge("30 DAY"); ?>
  </tr>
  <tr>
    <td>Last 6m</td>
    <?php real_edge("6 MONTH"); ?>
  </tr>
  <tr>
    <td>Last 12m</td>
    <?php real_edge("12 MONTH"); ?>
  </tr>
  <tr>
    <td>Since start</td>
    <?php real_edge(); ?>
  </tr>
</table>
</fieldset>
<?php if ($settings['inv_enable']==1) { ?>
  <fieldset style="margin-top: 10px;">
    <legend>BTC Invest Stats</legend>
    <table class="vypis_table" style="width: 50%;">
      <tr class="vypis_table_obsah">
        <td>Total Investors:</td>
        <td><b><?php echo db_num_rows(db_query("SELECT `id` FROM `investors` WHERE `amount`!=0")); ?></b></td>
      </tr>
      <tr class="vypis_table_obsah">
        <td title="Total invested by investors">Total Invested:</td>
        <td><b><?php $tsum=db_fetch_array(db_query("SELECT SUM(`amount`) AS `am` FROM `investors` WHERE `amount`!=0")); echo sprintf("%.8f",$tsum['am']); ?></b> <?php echo $settings['currency_sign']; ?></td>
      </tr>
      <tr class="vypis_table_obsah">
        <td title="= free balance">House Investment:</td>
        <td><b>
        <?php
            $usersinv_=db_fetch_array(db_query("SELECT SUM(`amount`) AS `sum` FROM `investors` WHERE `amount`!=0"));
            $usersinv_['sum']=($settings['inv_enable']==1)?(0+(double)$usersinv_['sum']):0;
            $usersdeps_=db_fetch_array(db_query("SELECT SUM(`amount`) AS `sum` FROM `deposits` WHERE `currency`='btc'"));
            $usersdeps_['sum']=(0+(double)$usersdeps_['sum']);
            $usersbal_=db_fetch_array(db_query("SELECT SUM(`btc_balance`) AS `sum` FROM `players`"));
            $usersbal_['sum']=(0+(double)$usersbal_['sum']);
            echo sprintf("%.8f",walletRequest('getbalance')-$usersbal_['sum']-$usersdeps_['sum']-$usersinv_['sum']);
            ?>
          </b><?php echo $settings['currency_sign']; ?></td>
      </tr>
      <tr class="vypis_table_obsah">
        <td>Total Investor's Profit:</td>
        <td><b><?php $tsum=db_fetch_array(db_query("SELECT SUM(`profit`) AS `am` FROM `investors` WHERE `profit`!=0")); if ($tsum['am']<0) echo '<span style="color: #d10000;">'.sprintf("%.8f",$tsum['am']).'</span>'; else echo '<span style="color: green;">'.sprintf("%.8f",$tsum['am']).'</span>'; ?></b> <?php echo $settings['currency_sign']; ?></td>
      </tr>
      <tr class="vypis_table_obsah">
        <td title="Commision + investement from Free balance">Total house profit:</td>
        <td><b><?php if ($settings['inv_casprofit']<0) echo '<span style="color: #d10000;">'.sprintf("%.8f",$settings['inv_casprofit']).'</span>'; else echo '<span style="color: green;">'.sprintf("%.8f",$settings['inv_casprofit']).'</span>'; ?></b> <?php echo $settings['currency_sign']; ?></td>
      </tr>
    </table>
  </fieldset>
<?php } ?>