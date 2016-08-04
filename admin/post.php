<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/

if (isset($init) && $logged==true) {

  if (!empty($_POST['s_title']) && !empty($_POST['s_url']) && !empty($_POST['s_desc']) && isset($_POST['min_bet']) && is_numeric($_POST['min_bet']) && isset($_POST['min_withdrawal']) && is_numeric((double)$_POST['min_withdrawal']) && isset($_POST['bankroll_maxbet_ratio']) && is_numeric((double)$_POST['bankroll_maxbet_ratio']) && isset($_POST['bj_pays']) && isset($_POST['hits_on_soft']) && isset($_POST['number_of_decks']) && isset($_POST['tie_dealerwon']) && isset($_POST['insurance']) && isset($_POST['house_edge']) && isset($_POST['jackpot'])) {

	
    $j = min((int)$_POST['jackpot'], 12339);
    $j = max($j, 1);
    
    db_query("UPDATE `system` SET `title`='".prot($_POST['s_title'])."',`url`='".prot($_POST['s_url'])."',`min_bet`=".$_POST['min_bet'].",`min_withdrawal`=".(double)$_POST['min_withdrawal'].",`description`='".prot($_POST['s_desc'])."',`bankroll_maxbet_ratio`=".(double)$_POST['bankroll_maxbet_ratio'].",`bj_pays`=".$_POST['bj_pays'].",`hits_on_soft`=".$_POST['hits_on_soft'].",`number_of_decks`=".$_POST['number_of_decks'].",`tie_dealerwon`=".$_POST['tie_dealerwon'].",`insurance`=".$_POST['insurance'].",`house_edge`=".$_POST['house_edge'].", `jackpot`=$j WHERE `id`=1 LIMIT 1");
    $warnStatus='<div class="zprava zpravagreen"><b>Success!</b> Data was successfuly saved.</div>';
  }
  else if (isset($_POST['s_title'])) {
    $warnStatus='<div class="zprava zpravared"><b>Error!</b> One of fields is empty.</div>';
  }
  if (isset($_POST['addons_form'])) {
    $giveaway=(isset($_POST['giveaway']))?1:0;
    $chat_enable=(isset($_POST['chat_enable']))?1:0;
    $inv_enable=(isset($_POST['inv_enable']))?1:0;
    
    db_query("UPDATE `system` SET `giveaway`=$giveaway,`giveaway_amount`=".(double)$_POST['giveaway_amount'].",`giveaway_freq`=".(int)$_POST['giveaway_freq'].",`chat_enable`=$chat_enable,`inv_enable`=$inv_enable,`inv_min`=".max(0,(double)$_POST['inv_min']).",`inv_perc`=".max(0,min((int)$_POST['inv_perc'],100))." LIMIT 1");
  }
    if (isset($_POST['currencies_form'])) {
        $btc_rate=$_POST['btc_rate'];
        $rns3_rate=$_POST['rns3_rate'];
        $orns_rate=$_POST['orns_rate'];
        $orns_enable=(isset($_POST['orns_enable']))?1:0;
        $rns3_enable=(isset($_POST['rns3_enable']))?1:0;
        $w_mode = (isset($_POST['w_mode']) && $_POST['w_mode']) ? 1 : 0;
        $min_confirmations = $_POST['min_confirmations'];
        $btc_min_deposit = $_POST['btc_min_deposit'];
        $rns3_min_deposit = $_POST['rns3_min_deposit'];
        $orns_min_deposit = $_POST['orns_min_deposit'];

        walletRequest('settxfee',array(round((double)$_POST['txfee'],8)));
        db_query("UPDATE `system` SET `btc_rate`=$btc_rate,`rns3_rate`=$rns3_rate,`orns_rate`=$orns_rate,`orns`=$orns_enable,`rns3`=$rns3_enable,`min_confirmations`=$min_confirmations,`withdrawal_mode`=$w_mode,`btc_min_deposit`=$btc_min_deposit,`rns3_min_deposit`=$rns3_min_deposit,`orns_min_deposit`=$orns_min_deposit LIMIT 1");
    }

  if (isset($_POST['theme'])) {
    $theme=prot($_POST['theme']);
    $usertheme=(isset($_POST['usertheme']))?1:0;
    
    db_query("UPDATE `system` SET `usertheme`=$usertheme,`active_theme`='".$theme."' LIMIT 1");
  }

  if (isset($_GET['maintenance'])) {
    db_query("UPDATE `system` SET `maintenance`=1-`maintenance` LIMIT 1");    
    
    $maint = db_fetch_array(db_query("SELECT `maintenance` FROM `system` LIMIT 1"));
    
    if ($maint['maintenance']) $onoff = 'activated';
    else $onoff = 'deactivated';
    
    $warnStatus='<div class="zprava zpravagreen"><b>Success!</b> Maintenance mode was '.$onoff.'.</div>';    
  }
  
  if (isset($_GET['reinstall_4'])) {
    db_query("UPDATE `system` SET `installed`=0 LIMIT 1");
    
    header('Location: ../install/');
  }

}
?>