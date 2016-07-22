<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


error_reporting(0);
header('X-Frame-Options: DENY'); 

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

$game = $_COOKIE['game'];

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"))==0) exit();

$player=db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));

validateAccess($player['id']);

maintenance();


$settings = db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));


$limit = 30; // <- ADJUSTABLE



$lastIDs = explode( ',', $_GET['last'] );

$stats = array( 'my_bets' => array( 'lastid' => (int)$lastIDs[0] ),
                'all_bets' => array( 'lastid' => (int)$lastIDs[1] ),
                'high' => array( 'lastid' => (int)$lastIDs[2] )
              );

if($game == 'blackjack'){
  foreach ( $stats as $key => $load ) {

    $order = "time";
    $where = "WHERE `id` > $load[lastid]";
    if ($key == 'high') {
      $order = "multiplier";
      $where = "WHERE `multiplier` > $load[lastid]";
    }
    if ($key == 'my_bets')  $where .= " AND `player` = $player[id]";
    else                    $where .= " AND `bet_amount` != 0";

    $q = db_query("SELECT * FROM `games` $where ORDER BY `$order` DESC LIMIT $limit");


    $stats[$key]['contents'] = '';

    while ($row = db_fetch_array($q)) {

      $person_q = db_query("SELECT `alias` FROM `players` WHERE `id`=$row[player] LIMIT 1");
      if ( !db_num_rows($person_q) ) $alias = '[unknown]';
      else {
        $person = db_fetch_array($person_q);
        $alias = $person['alias'];
      }

      $isHidden = ($row['player'] == $player['id']) ? 1 : 0;

      $stats[$key]['contents'].= '<tr data-betid="'.$row['id'].'" data-hidden="'.$isHidden.'">';
      $stats[$key]['contents'].= '<td>'.$row['id'].'</td>';
      $stats[$key]['contents'].= '<td>'.$alias.'</td>';
      $stats[$key]['contents'].= '<td>//</td>';
      $stats[$key]['contents'].= '<td>'.sprintf("%.8f",$row['bet_amount']).'</td>';
      $stats[$key]['contents'].= '<td>'.$row['player_deck'].'</td>';
      $stats[$key]['contents'].= '<td>x'.$row['multiplier'].'</td>';
      $stats[$key]['contents'].= '<td>'.profit( $row['bet_amount']*-1 + ($row['bet_amount'] * $row['multiplier']) ).'</td>';
      $stats[$key]['contents'].= '</tr>';

    }

  }
  echo  json_encode( array( 'stats' => $stats ) );
  exit();
}

if($game == 'slots') {
  $dir = __DIR__ . '/../custom_images/item-0/';
  $iterator = new \FilesystemIterator($dir);
  $file = '';
  while ($iterator->valid()) {
    $file_ = $iterator->getFilename();
    $iterator->next();

    $lower = strtolower($file_);
    if (substr($lower, -4) == '.jpg' || substr($lower, -4) == '.png' || substr($lower, -5) == '.jpeg') {
      $file = $file_;
      if ($i == 0) $emptyIm = true;
      break;
    }
  }
}



foreach ( $stats as $key => $load ) {  
  
  $order = "time";
  $where = "WHERE `id` > $load[lastid] AND `game` = '$game'";
  if ($key == 'high') {
    $order = "multiplier";
    $where = "WHERE `multiplier` > $load[lastid] AND `game` = '$game'";
  }
  if ($key == 'my_bets')  $where .= " AND `player` = $player[id]";
  else                    $where .= " AND `bet_amount` != 0";

  $q = db_query("SELECT * FROM `spins` $where ORDER BY `$order` DESC LIMIT $limit");
  

  $stats[$key]['contents'] = '';
  
  while ($row = db_fetch_array($q)) {
    
    $person_q = db_query("SELECT `alias` FROM `players` WHERE `id`=$row[player] LIMIT 1");
    if ( !db_num_rows($person_q) ) $alias = '[unknown]';
    else {
      $person = db_fetch_array($person_q);
      $alias = $person['alias'];
    }
    
    $isHidden = ($row['player'] == $player['id']) ? 1 : 0;
    if($game == 'slots') {
      $results = explode(',', $row['result']);
      $spin = '[' . $results[0] . '] [' . $results[1] . '] [' . $results[2] . ']';

      if (!isset($emptyIm)) $spin = str_replace('0]', '0 _topNone">-</div>', $spin);

      $spin = str_replace(']', '"></div>', $spin);
      $spin = str_replace('[', '<div class="statsResIcon img', $spin);
    }
    else if($game == 'dice') $spin = $row['result'];
    $stats[$key]['contents'].= '<tr data-betid="'.$row['id'].'" data-hidden="'.$isHidden.'">';
    $stats[$key]['contents'].= '<td>'.$row['id'].'</td>';
    $stats[$key]['contents'].= '<td>'.$alias.'</td>';
    $stats[$key]['contents'].= '<td>'.date('H:i', strtotime($row['time'])).'</td>';
    $stats[$key]['contents'].= '<td>'.sprintf("%.8f",$row['bet_amount']).'</td>';
    $stats[$key]['contents'].= '<td>'.$spin.'</td>';
    $stats[$key]['contents'].= '<td>x'.$row['multiplier'].'</td>';
    $stats[$key]['contents'].= '<td>'.profit( $row['bet_amount']*-1 + ($row['bet_amount'] * $row['multiplier']) ).'</td>';
    $stats[$key]['contents'].= '</tr>';
  
  }
  
}



echo  json_encode( array( 'stats' => $stats ) );
?>