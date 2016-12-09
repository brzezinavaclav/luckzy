<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


header('X-Frame-Options: DENY'); 

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

$game = $_GET['game'];

if (empty($_COOKIE['unique_S_']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();

$player=db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));

maintenance();


$settings = db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));


$limit = 30; // <- ADJUSTABLE



$lastIDs = explode( ',', $_GET['last'] );

$stats = array( 'my_bets' => array( 'lastid' => (int)$lastIDs[0] ),
                'all_bets' => array( 'lastid' => (int)$lastIDs[1] ),
                'high' => array( 'lastid' => (int)$lastIDs[2] )
              );


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
  
  $order = "`time` DESC";
  if($game == 'blackjack') $where = "WHERE `id` > $load[lastid] AND `ended`=1";
  else $where = "WHERE `id` > $load[lastid] AND `game` = '$game'";
  if ($key == 'high') {
    $order = "`multiplier` DESC, `bet_amount` DESC";
      if($game == 'blackjack') $where = "WHERE `multiplier` >= $load[lastid]";
      else $where = "WHERE `multiplier` > $load[lastid] AND `game` = '$game'";
  }
  if ($key == 'my_bets')  $where .= " AND `player` = $player[id]";
  else                    $where .= " AND `bet_amount` != 0";

  if($game == 'blackjack')  $q = db_query("SELECT * FROM `games` $where ORDER BY $order LIMIT $limit");
  else $q = db_query("SELECT * FROM `spins` $where ORDER BY $order LIMIT $limit");
    

  $stats[$key]['contents'] = '';
  
  while ($row = db_fetch_array($q)) {
    
    $person_q = db_query("SELECT `username` FROM `players` WHERE `id`=$row[player] LIMIT 1");
    $person = db_fetch_array($person_q);
    $username = $person['username'];
    if ($username == '') $username = '[unregistered]';
    
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
    $stats[$key]['contents'].= '<td>'.$username.'</td>';
    $stats[$key]['contents'].= '<td>'.date('H:i', strtotime($row['time'])).'</td>';
    $stats[$key]['contents'].= '<td>'.$row['bet_amount'].' Coins</td>';
    if($game == 'blackjack') $stats[$key]['contents'].=  '<td>'.get_cards($row['player_deck']);
    if($game == 'blackjack' && $row['player_deck_2'] != '') $stats[$key]['contents'].=  '<span style="margin-left: 20px">'.get_cards($row['player_deck_2']).'</span>';
    if($game == 'blackjack') $stats[$key]['contents'] .= '</td><td>'.get_cards($row['dealer_deck']).'</td>';
    else $stats[$key]['contents'].= '<td>'.$spin.'</td>';
    $stats[$key]['contents'].= '<td>x'.$row['multiplier'].'</td>';
    $stats[$key]['contents'].= '<td>'.profit( $row['bet_amount']*-1 + ($row['bet_amount'] * $row['multiplier']) ).'</td>';
    $stats[$key]['contents'].= '</tr>';
  
  }
  
}

function get_cards($deck){
    $cards = unserialize($deck);
    $html = array();
    for($i = 0; $i< count($cards); $i++){
        $card = explode("_", $cards[$i]);
        array_push($html, '<div class="cardOuter small"><div class="card '.$card[2].'"><div class="value">'.$card[1].'</div><div class="suit">'.$card[0] . '</div></div></div>');
    }
    return implode(" ", $html);
}

echo  json_encode( array( 'stats' => $stats ) );
?>