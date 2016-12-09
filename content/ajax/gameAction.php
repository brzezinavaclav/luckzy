<?php
/*
 *  © CoinJack 
 *  Demo: http://www.btcircle.com/coinjack
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


error_reporting(0);
header('X-Frame-Options: DENY'); 

$init=true;
include '../../inc/db-conf.php';
include '../../inc/functions.php';
include __DIR__.'/../../inc/db_functions.php';

db_query('START TRANSACTION');

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1 FOR UPDATE"));



$gD_q=db_query("SELECT * FROM `games` WHERE `ended`=0 AND `player`=$player[id] LIMIT 1");
if (db_num_rows($gD_q)==0) exit();

$gameData=db_fetch_array($gD_q);
$gameData['canhit']=($gameData['accessable_actions']>=1)?1:0;

if ($gameData['ended']) exit();

$split='false';

$data=array();





switch ($_GET['action']) {
  case 'stand':

    $final_shuffle=unserialize($gameData['final_shuffle']);
    $final_shuffle=$final_shuffle['initial_array'];
  
    if ($gameData['player_deck_2']=='' || $gameData['player_deck_stand']==0) {
      $whichDeck='player_deck';
      $secondDeck='player_deck_2';
      $data['nextDeck']='yes';
      $deck=unserialize($gameData['player_deck']);
    }
    else {
      $whichDeck='player_deck_2';
      $secondDeck='player_deck';
      $data['nextDeck']='no';
      $deck=unserialize($gameData['player_deck_2']);
    }
    
    $deckSums=getSums($deck);


    $dealer_deck_old=unserialize($gameData['dealer_deck']);
    
    // defaults
    $stand=0;
    $used_cards=$gameData['used_cards'];
    $ended=0;
    $winner='';
    $data['winner']='-';
    $dealer_deck_new=$dealer_deck_old;
    $dealer_used_cards=0;
    $data['accessable']=1;
    $data['dealer_new']='-';
    $data['dealer_sum']='-';
    $data['standed']='yes';
    // /defaults
    
    if (true) {
      $stand=1;
      if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']==1) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1))
        $data['nextDeck']='yes';
      if (($whichDeck=='player_deck' && ($gameData['player_deck_2']=='' || $gameData['player_deck_2_stand']==1)) || $whichDeck=='player_deck_2') {         // game ended
        $ended=1;
        $dealer_deck_new=dealerPlays($dealer_deck_old,$final_shuffle,$used_cards);
        $dealer_used_cards=(count($dealer_deck_new)-count($dealer_deck_old));
        $data['dealer_new']=$dealer_deck_new;
        $data['dealer_sum']=implode(',',getSums($dealer_deck_new));
        
        $data['accessable']=0;

        if (max(getSums($dealer_deck_new))==max($deckSums)) {
          $winner='tie';
          $data['winner']='tie';


          if (false) {
            $bet_amount=$gameData['bet_amount'];
            $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
            if (max(getSums($dealer_deck_new))==max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
            else if (max(getSums($dealer_deck_new))>max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false);
            else
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'lose',false);
          }
          else {
            $bet_amount=$gameData['bet_amount'];
          }
          playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',true,$gameData['final_shuffle']);

          
        }
        else if (max(getSums($dealer_deck_new))>max($deckSums) && max(getSums($dealer_deck_new))<22) {
          $winner='dealer';
          $data['winner']='dealer';


          if (false) {
            $bet_amount=$gameData['bet_amount'];
            $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
            if (max(getSums($dealer_deck_new))==max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
            else if (max(getSums($dealer_deck_new))>max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false);
            else
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'lose',false);
          }
          else {
            $bet_amount=$gameData['bet_amount'];
          }

          playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'lose',false,$gameData['final_shuffle']);
        
        }
        else {
          $winner='player';
          $data['winner']='player';


          if (false) {
            $bet_amount=($gameData['bet_amount']);
            $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
            if (max(getSums($dealer_deck_new))==max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
            else if (max(getSums($dealer_deck_new))>max($secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false);
            else
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'lose',false);
          }
          else {
            $bet_amount=$gameData['bet_amount'];
          }

          playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false,$gameData['final_shuffle']);
        }
        $fair=db_fetch_array(db_query("SELECT `client_seed`, `last_client_seed`, `initial_shuffle`, `last_initial_shuffle`, `last_final_shuffle` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
        $data['fair'] = array(
            'newSeed'           => hash( 'sha256', stringify_shuffle($fair['initial_shuffle']) ),
            'newCSeed'          => $fair['client_seed'],
            'lastSeed_sha256'   => hash( 'sha256', stringify_shuffle($fair['last_initial_shuffle'] )),
            'lastSeed'          => stringify_shuffle($fair['last_initial_shuffle']),
            'lastCSeed'         => $fair['last_client_seed'],
            'lastResult'        => stringify_shuffle($fair['last_final_shuffle'])
        );
      }
    }
    
    db_query("UPDATE `games` SET `$whichDeck`='".serialize($deck)."',`".$whichDeck."_stand`=$stand,`dealer_deck`='".serialize($dealer_deck_new)."',`ended`=$ended,`winner`='$winner',`used_cards`=$used_cards,`accessable_actions`=$data[accessable] WHERE `id`=$gameData[id] LIMIT 1");
  

  break;
  
  case 'double':
    
    if ($player['balance']<$gameData['bet_amount']) {
      echo json_encode(array('error'=>'balance','accessable'=>$gameData['accessable_actions']));
      exit();
    }
    if ($gameData['player_deck_2']!='') exit();
    
    db_query("UPDATE `players` SET `balance`=TRUNCATE(ROUND((`balance`-$gameData[bet_amount]),9),8) WHERE `id`=$player[id] LIMIT 1");
    $gameData['bet_amount']*=2;
    db_query("UPDATE `games` SET `bet_amount`=TRUNCATE(ROUND((`bet_amount`*2),9),8),`canhit`=0 WHERE `id`=$gameData[id] LIMIT 1");
    db_query("UPDATE `system` SET `t_wagered`=ROUND((`t_wagered`+($gameData[bet_amount]/2)),8) WHERE `id`=1 LIMIT 1");
    
    $data['re-stand']='yes';
    
  case 'hit':
  
    if ($gameData['canhit']==0) return;
  
    $final_shuffle=unserialize($gameData['final_shuffle']);
    $final_shuffle=$final_shuffle['initial_array'];
  
    if ($gameData['player_deck_2']=='' || $gameData['player_deck_stand']==0) {
      $whichDeck='player_deck';
      $secondDeck='player_deck_2';
      $data['nextDeck']='yes';
    }
    else {
      $whichDeck='player_deck_2';
      $secondDeck='player_deck';
      $data['nextDeck']='no';
    }
    $deck=unserialize($gameData[$whichDeck]);
    
    $newCard=$final_shuffle[($gameData['used_cards'])];
    
    $deck[]=$newCard;
    db_query("UPDATE `games` SET `$whichDeck`='".serialize($deck)."',`used_cards`=(`used_cards`+1) WHERE `id`=$gameData[id] LIMIT 1");
    $used_cards=($gameData['used_cards']+1);
    
    $deckSums=getSums($deck);


    $dealer_deck_old=unserialize($gameData['dealer_deck']);
    
    // defaults
    $stand=0;
    $used_cards=$used_cards;
    $ended=0;
    $winner='';
    $data['winner']='-';
    $dealer_deck_new=$dealer_deck_old;
    $dealer_used_cards=0;
    $data['accessable']=1;
    $data['dealer_new']='-';
    $data['dealer_sum']='-';
    $data['nextDeck']='no';
    // /defaults
    
    if (in_array(21,$deckSums)) {
      $stand=1;
      if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']==0) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1))
        $data['nextDeck']='yes';
      if (($whichDeck=='player_deck' && ($gameData['player_deck_2']=='' || $gameData['player_deck_2_stand']==1)) || $whichDeck=='player_deck_2') {         // game ended
        $ended=1;
        $dealer_deck_new=dealerPlays($dealer_deck_old,$final_shuffle,$used_cards);
        $dealer_used_cards=(count($dealer_deck_new)-count($dealer_deck_old));
        $data['dealer_new']=$dealer_deck_new;
        $data['dealer_sum']=implode(',',getSums($dealer_deck_new));
        
        $data['accessable']=0;

        if (in_array(21,getSums($dealer_deck_new))) {
          $winner='tie';
          $data['winner']='tie';


          if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']==1) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1)) {
            $bet_amount=$gameData['bet_amount']/2;
            $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
            if (in_array(21,$secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
          }
          else {
            $bet_amount=$gameData['bet_amount'];
          }
          playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false,$gameData['final_shuffle']);

          
        }
        else {
          $winner='player';
          $data['winner']='player';


          if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']==1) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1)) {
            $bet_amount=($gameData['bet_amount']/2);
            $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
            if (in_array(21,$secondDeckSum))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
          }
          else {
            $bet_amount=$gameData['bet_amount'];
          }

          playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false,$gameData['final_shuffle']);
        }
      }
    }
    else if (count($deckSums)==1 && $deckSums[0]>21) {
      $stand=1;
      if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']!=1) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1))
        $data['nextDeck']='yes';
      if (($whichDeck=='player_deck' && $gameData['player_deck_2']=='') || $whichDeck=='player_deck_2') {         // game ended
        $winner='dealer';
        $data['winner']='dealer';
  
        $ended=1;
        $dealer_deck_new=($gameData['player_deck_2']=='')?$dealer_deck_old:dealerPlays($dealer_deck_old,$final_shuffle,$used_cards);
        $dealer_used_cards=(count($dealer_deck_new)-count($dealer_deck_old));
        $data['dealer_new']=$dealer_deck_new;
        $data['dealer_sum']=implode(',',getSums($dealer_deck_new));
          
        $data['accessable']=0;


        if (($whichDeck=='player_deck' && $gameData['player_deck_2_stand']==1) || ($whichDeck=='player_deck_2' && $gameData['player_deck_stand']==1)) {
          $bet_amount=$gameData['bet_amount']/2;
          $secondDeckSum=getSums(unserialize($gameData[$secondDeck]));
          if (in_array(21,$secondDeckSum)) {
            if (in_array(21,getSums($dealer_deck_new)))
              playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'tie',false);
            else playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'regular',false);
          }
        }
        else {
          $bet_amount=$gameData['bet_amount'];
        }
        playerWon($player['id'],$gameData['id'],$bet_amount,$dealer_deck_new,'lose',false,$gameData['final_shuffle']);
      }
      $fair=db_fetch_array(db_query("SELECT `client_seed`, `last_client_seed`, `initial_shuffle`, `last_initial_shuffle`, `last_final_shuffle` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
      $data['fair'] = array(
          'newSeed'           => hash( 'sha256', stringify_shuffle($fair['initial_shuffle']) ),
          'newCSeed'          => $fair['client_seed'],
          'lastSeed_sha256'   => hash( 'sha256', stringify_shuffle($fair['last_initial_shuffle']) ),
          'lastSeed'          => stringify_shuffle($fair['last_initial_shuffle']),
          'lastCSeed'         => $fair['last_client_seed'],
          'lastResult'        => stringify_shuffle($fair['last_final_shuffle'])
      );
    }
    
    db_query("UPDATE `games` SET `$whichDeck`='".serialize($deck)."',`".$whichDeck."_stand`=$stand,`dealer_deck`='".serialize($dealer_deck_new)."',`ended`=$ended,`winner`='$winner',`used_cards`=$used_cards,`accessable_actions`=$data[accessable] WHERE `id`=$gameData[id] LIMIT 1");
  
    $data['hitted_card-'.$whichDeck]=explode('_',$newCard);
    $data['hitted_sum']=implode(',',$deckSums);
    
  break;
  
  case 'split':
    if ($player['balance']<$gameData['bet_amount']) {
      echo json_encode(array('error'=>'balance','accessable'=>$gameData['accessable_actions']));
      exit();
    }
    if ($gameData['player_deck_2']!='' || $gameData['accessable_actions']!=2) break;
    
    db_query("UPDATE `players` SET `balance`=TRUNCATE(ROUND((`balance`-$gameData[bet_amount]),9),8) WHERE `id`=$player[id] LIMIT 1");
    $gameData['bet_amount']*=2;
    db_query("UPDATE `games` SET `bet_amount`=$gameData[bet_amount] WHERE `id`=$gameData[id] LIMIT 1");
    
    $split='true';
    
    $final_shuffle=unserialize($gameData['final_shuffle']);
    $final_shuffle=$final_shuffle['initial_array'];
    
    $newcard1=$final_shuffle[4];
    $newcard2=$final_shuffle[5];
    
    $player_deck_before=unserialize($gameData['player_deck']);

    $player_deck=array($player_deck_before[0],$newcard1);
    $player_deck_2=array($player_deck_before[1],$newcard2);

    $dealer_deck_old=unserialize($gameData['dealer_deck']);


    // defaults
    $stand1=0;
    $stand2=0;
    $ended=0;
    $data['accessable']=1;
    $winner='';
    $data['winner']='-';
    $dealer_deck_new=$dealer_deck_old;
    $dealer_used_cards=0;
    $data['dealer_new']='-';
    $data['dealer_sum']='-';
    $data['mark']=1;
    // /defaults
    
    if (in_array(21,getSums($player_deck))) {
      $stand1=1;
      $data['mark']=2;
    }
    if (in_array(21,getSums($player_deck_2))) {
      $stand2=1;
      $data['mark']='-';
      
      if ($stand1==1) {      // both stands ( = ended)
        
        $dealer_deck_new=dealerPlays($dealer_deck_old,$final_shuffle,6);
        $dealer_used_cards=(count($dealer_deck_new)-2);
        $data['dealer_new']=$dealer_deck_new;
        $data['dealer_sum']=implode(',',getSums($dealer_deck_new));
        
        $ended=1;
        $data['accessable']=0;
        if (in_array(21,getSums($dealer_deck_new))) {
          $winner='tie';
          $data['winner']='tie';
          playerWon($player['id'],$gameData['id'],$gameData['bet_amount'],$dealer_deck_new,'tie',true,$gameData['final_shuffle']);
        }
        else {
          $winner='player';
          $data['winner']=$gameData['bet_amount'];
          playerWon($player['id'],$gameData['id'],$gameData['bet_amount'],$dealer_deck_new,'regular',true,$gameData['final_shuffle']);
        }
        $fair=db_fetch_array(db_query("SELECT `client_seed`, `last_client_seed`, `initial_shuffle`, `last_initial_shuffle`, `last_final_shuffle` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
        $data['fair'] = array(
            'newSeed'           => hash( 'sha256', stringify_shuffle($fair['initial_shuffle']) ),
            'newCSeed'          => $fair['client_seed'],
            'lastSeed_sha256'   => hash( 'sha256', stringify_shuffle($fair['last_initial_shuffle']) ),
            'lastSeed'          => stringify_shuffle($fair['last_initial_shuffle']),
            'lastCSeed'         => $fair['last_client_seed'],
            'lastResult'        => stringify_shuffle($fair['last_final_shuffle'])
        );
      }
    }

        
    db_query("UPDATE `games` SET `player_deck`='".serialize($player_deck)."',`player_deck_2`='".serialize($player_deck_2)."',`used_cards`=(6+$dealer_used_cards),`accessable_actions`=$data[accessable],`ended`=$ended,`player_deck_stand`=$stand1,`player_deck_2_stand`=$stand2,`winner`='$winner',`dealer_deck`='".serialize($dealer_deck_new)."' WHERE `id`=$gameData[id] LIMIT 1");
    
    
    $data['splitted_cards']['card-1']=explode('_',$newcard1);
    $data['splitted_cards']['card-2']=explode('_',$newcard2);
    $data['splitted_cards']['deck-1-value']=implode(',',getSums($player_deck));
    $data['splitted_cards']['deck-2-value']=implode(',',getSums($player_deck_2));
    

  break;
  
  default:
    exit();
  break;
}

if ($data['dealer_new']!='-') {
  $newd=array();
  foreach ($data['dealer_new'] as $card) {
    $newd[]=explode('_',$card);
  }
  $data['dealer_new']=$newd;
}



$gData = db_fetch_array(db_query("SELECT * FROM `games` WHERE `id`=$gameData[id] LIMIT 1"));
if (count(unserialize($gData['player_deck'])) <= 2 && $gData['player_deck_2'] == '') {
  $data['candouble'] = 1;
}
else $data['candouble'] = 0;


echo json_encode(array('error'=>'no','split'=>$split,'data'=>$data));


db_query('COMMIT');

?>
