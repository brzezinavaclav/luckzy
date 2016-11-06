<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


if (!isset($init)) exit();

if(isset($_GET['g']) && $_GET['g'] == 'blackjack'){
  $query_=db_query("SELECT * FROM `games` WHERE `bet_amount`!=0 ORDER BY `time` DESC");
  $pocet=db_num_rows(db_query("SELECT `id` FROM `games` WHERE `bet_amount`!=0"));
}
else if(isset($_GET['g'])) {
  $where_game = "AND `game`='".$_GET['g']."'";
  $query_=db_query("SELECT * FROM `spins` WHERE `bet_amount`!=0 ".$where_game." ORDER BY `time` DESC");
  $pocet=db_num_rows(db_query("SELECT `id` FROM `spins` WHERE `bet_amount`!=0 ".$where_game));
}
else{
  $query_=db_query("SELECT * FROM `games` WHERE `bet_amount`!=0 ORDER BY `time` DESC");
  $query__=db_query("SELECT * FROM `spins` WHERE `bet_amount`!=0 ORDER BY `time` DESC");
  $pocet=db_num_rows(db_query("SELECT `id` FROM `games` WHERE `bet_amount`!=0"));
  $pocet+=db_num_rows(db_query("SELECT `id` FROM `spins` WHERE `bet_amount`!=0 "));
}


?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>
<style>
  table.dataTable tbody tr {
    background-color: inherit;
  }
</style>
<script>
  $(document).ready(function(){
    $('#bets_table').DataTable();
  });
</script>
<h1>Bets</h1>
<div class="menu_ menu-horizontal">
  <ul>
    <li><a href="?p=bets" class="<?php if (!isset($_GET['g'])) echo 'active_'; ?>">All bets</a></li>
    <li><a href="?p=bets&g=dice" class="<?php if (isset($_GET['g']) && $_GET['g']=='dice') echo 'active_'; ?>">Dice</a></li>
    <li><a href="?p=bets&g=slots" class="<?php if (isset($_GET['g']) && $_GET['g']=='slots') echo 'active_'; ?>">Slots</a></li>
    <li><a href="?p=bets&g=blackjack" class="<?php if (isset($_GET['g']) && $_GET['g']=='blackjack') echo 'active_'; ?>">Blackjack</a></li>
  </ul>
</div>
<div class="zprava" style="margin-top: 20px">
<table class="vypis_table" id="bets_table">
  <thead>
  <tr class="vypis_table_head">
    <th>ID</th>
    <th>Player</th>
    <th>Time</th>
    <th>Bet</th>
    <th>Multiplier</th>
    <th>Player's Profit</th>
  </tr>
  </thead>
  <tbody>
  <?php
  while ($row=db_fetch_array($query_)) {
    if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$row[player] LIMIT 1"))!=0)
      $player=db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$row[player] LIMIT 1"));
    else $player['username']='[unknown]';
    

    echo '<tr class="vypis_table_obsah">';
    echo '<td><small>'.$row['id'].'</small></td>';
    echo '<td title="'.$player['username'].'"><small>'.zkrat($player['username'],10,'<b>...</b>').'</small></td>';
    echo '<td><small><small>'.str_replace(' ','<br>',$row['time']).'</small></small></td>';
    echo '<td><small><b>'.n_num($row['bet_amount']).'</b> Coins</small></td>';
    echo '<td><small>'.$row['multiplier'].'</small></td>';
    echo '<td><small>'.profit($row['multiplier']*$row['bet_amount']-$row['bet_amount']).'</small></td>';
    echo '</tr>'."\n";
  }
  while ($row=db_fetch_array($query__)) {
    if (db_num_rows(db_query("SELECT `username` FROM `players` WHERE `id`=$row[player] LIMIT 1"))!=0)
      $player=db_fetch_array(db_query("SELECT `username` FROM `players` WHERE `id`=$row[player] LIMIT 1"));
    else $player['username']='[unknown]';


    echo '<tr class="vypis_table_obsah">';
    echo '<td><small>'.$row['id'].'</small></td>';
    echo '<td title="'.$player['username'].'"><small>'.zkrat($player['username'],10,'<b>...</b>').'</small></td>';
    echo '<td><small><small>'.str_replace(' ','<br>',$row['time']).'</small></small></td>';
    echo '<td><small><b>'.$row['bet_amount'].'</b> Coins</small></td>';
    echo '<td><small>'.$row['multiplier'].'</small></td>';
    echo '<td><small>'.profit($row['multiplier']*$row['bet_amount']-$row['bet_amount']).'</small></td>';
    echo '</tr>'."\n";
  }
    
  ?>
  </tbody>
</table>
</div>