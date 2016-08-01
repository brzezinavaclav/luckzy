<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/

if (!isset($init)) exit();

session_start();

function prot($hodnota, $max_delka = 0)
{
    $text = db_real_escape_string(strip_tags($hodnota));
    if ($max_delka != 0) $vystup = substr($text, 0, $max_delka);
    else  $vystup = $text;
    return $vystup;
}

function generateHash($length, $capt = false)
{
    if ($capt == true) $possibilities = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    else $possibilities = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $return = '';
    for ($i = 0; $i < $length; $i++) $return .= $possibilities[mt_rand(0, strlen($possibilities) - 1)];
    return $return;
}

function random_num($length)
{
    $possibilities = '1234567890';
    $return = '';
    for ($i = 0; $i < $length; $i++) $return .= $possibilities[mt_rand(0, strlen($possibilities) - 1)];
    return $return;
}

function generateSlotsSeed()
{

    $server_seed = array(
        'wheel1' => getInitial(),
        'wheel2' => getInitial(),
        'wheel3' => getInitial(),
        'seed_num' => random_num(8),
        'salt' => generateHash(36)
    );

    return $server_seed;
}

function slotsSeedExport($seed)
{
    if (!empty($seed)) {
        $seed = unserialize($seed);
        $return = 'wheel_1:[' . implode(',', $seed['wheel1']) . '],'
            . 'wheel_2:[' . implode(',', $seed['wheel2']) . '],'
            . 'wheel_3:[' . implode(',', $seed['wheel3']) . '],'
            . 'seed_num:[' . $seed['seed_num'] . '],'
            . 'salt:[' . $seed['salt'] . ']';
        return $return;
    } else return '';
}

function getInitial()
{

    $array = array();

    for ($i = 0, $spaces = 0; $i < 74; $i++) {

        if ($i < 2) $new = 1;
        else if ($i < 7) $new = 2;
        else if ($i < 16) $new = 3;
        else if ($i < 26) $new = 4;
        else if ($i < 41) $new = 5;
        else if ($i < 74) $new = 6;

        $array[] = $new;

        if ($spaces < 54) {
            $array[] = 0;
            $spaces++;
        }


    }

    shuffle($array);

    $newArray = array();

    for ($i = 0; $i < 128; $i++) {

        $index = rand(0, (127 - $i));
        $newArray[] = $array[$index];

        array_splice($array, $index, 1);

    }

    shuffle($newArray);

    return $newArray;

}

function newPlayer()
{
    do $hash = generateHash(32);
    while (db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='$hash' LIMIT 1")) != 0);

    $client_seed = random_num(8);
    db_query("INSERT INTO `players` (`hash`,`slots_seed`,`dice_seed`,`initial_shuffle`, `client_seed`) VALUES ('$hash','" . serialize(generateSlotsSeed()) . "','" . random_num(8) . "','" . generateInitialShuffle($client_seed) . "', '" . $client_seed . "')");

    setcookie('unique_S_', $hash, (time() + 60 * 60 * 24), '/');
    setcookie('unique_S_', $hash, (time() + 60 * 60 * 24), '/');
}

function zkrat($str, $max, $iflonger)
{
    if (strlen($str) > $max) {
        $str = substr($str, 0, $max) . $iflonger;
    }
    return $str;
}

function n_num($num, $showall = false)
{
    $r = sprintf("%.8f", $num);
    if ($showall == true) return $r;
    else return rtrim(rtrim($r, '0'), '.');
}

function logged()
{
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) return true;
    else return false;
}

function game()
{
    return $_COOKIE['game'];
}

function bbcode($str)
{

    $str = str_replace(array(
        '[B]', '[/B]', '[b]', '[/b]', '[i]', '[/i]', '[I]', '[/I]', '[U]', '[/U]', '[u]', '[/u]', '[br]', '[BR]'
    ), array(
        '<b>', '</b>', '<b>', '</b>', '<i>', '</i>', '<i>', '</i>', '<u>', '</u>', '<u>', '</u>', '<br>', '<br>'
    ), $str);

    return $str;
}

function getSpin($multip)
{

    switch ($multip) {

        case 0:
            return '-';
        case 1:
            return '[6]';
        case 2:
            return '[6] [6]';
        case 5:
            return '[6] [6] [6]';
        case 10:
            return '[5] [5] [5]';
        case 50:
            return '[4] [4] [4]';
        case 200:
            return '[3] [3] [3]';
        case 600:
            return '[2] [2] [2]';
        default:
            return '[1] [1] [1]';

    }

}

function profit($profit)
{

    $plus = '+';
    if ($profit < 0) {
        $class = 'loss';
        $plus = '';
    } else if ($profit > 0) $class = 'win';
    else $class = 'neutral';

    return '<span class="profit-' . $class . '"><span class="st-plus">' . $plus . '</span>' . sprintf("%.8f", $profit) . '</span>';

}

function house_edge()
{

    $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1"));

    $p_return = 3.57627869
        + 6.95228577
        + 2.38418579
        + 1.60932541
        + 8.56804848
        + 29.59871292
        + 42.60420799;

    $p_return += 0.00000381 * $settings['jackpot'] * 100;

    return 100 - $p_return;

}

function maintenance()
{

    $settings = db_fetch_array(db_query("SELECT `maintenance` FROM `system` LIMIT 1"));

    if ($settings['maintenance']) exit();

}

/*________________________JACK_____________________________*/
function card_value($card_val)
{
    if ($card_val == 'A') return 1;
    else if ($card_val == 'J' || $card_val == 'Q' || $card_val == 'K')
        return 10;
    else return $card_val;
}

function dealerPlays($dealer_deck, $final_shuffle, $used_cards)
{
    $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

    $threshold = 17; // under = HIT


    while (max(getSums($dealer_deck)) < $threshold) {
        $dealer_deck[] = $final_shuffle[$used_cards];
        $used_cards++;
    }
    if ($settings['hits_on_soft'] == 1 && max(getSums($dealer_deck)) == $threshold && count(getSums($dealer_deck)) == 2) {
        $dealer_deck[] = $final_shuffle[$used_cards];
        $used_cards++;

        while (min(getSums($dealer_deck)) < $threshold) {
            $dealer_deck[] = $final_shuffle[$used_cards];
            $used_cards++;
        }
    }

    return $dealer_deck;
}

function getSums($deck)
{
    $sum = 0;
    $card_vals = array();
    foreach ($deck as $cardStr) {
        $card = explode('_', $cardStr);
        $val = card_value($card[1]);

        $sum += $val;
        $card_vals[] = $val;
    }
    $sums = array($sum);
    if (in_array(1, $card_vals) && ($sum + 10) <= 21) $sums[] = ($sum + 10);

    return $sums;
}

function stringify_shuffle($shuffle)
{
    if(!empty($shuffle)) {
        $cards = unserialize($shuffle);
        return implode(';', $cards['initial_array']) . ';random-string-' . $cards['random_string'];
    }
}

function playerWon($player_id, $game_id, $wager, $d_deck, $regular_or_tie, $blackjack, $final_shuffle = '')
{
    $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

    $gameData = db_fetch_array(db_query("SELECT * FROM `games` WHERE `id`=$game_id LIMIT 1"));
    /*$mysqlerr=db_error();*/
    $player = db_fetch_array(db_query("SELECT `client_seed` FROM `users`  WHERE `id`=$player_id LIMIT 1"));
    $wager = $gameData['bet_amount'];

    $first_won_second_lose = false;

    if ($gameData['player_deck_2'] != '') {
        $first_won = true;
        $second_won = true;


        $dealer_max = max(getSums($d_deck));
        $player_max = max(getSums(unserialize($gameData['player_deck'])));
        $player_max_2 = max(getSums(unserialize($gameData['player_deck_2'])));

        if ($player_max > 21 || ($player_max <= $dealer_max && $dealer_max <= 21)) $first_won = false;
        if ($player_max_2 > 21 || ($player_max_2 <= $dealer_max && $dealer_max <= 21)) $second_won = false;

        if ($first_won != $second_won) $first_won_second_lose = true;

        //D/mysql_query("UPDATE `games` SET `note`='".$first_won.":".$second_won.":".$first_won_second_lose."|D-$dealer_max:P-$player_max:P2-$player_max_2 || ".$mysqlerr."' WHERE `id`=$gameData[id] LIMIT 1");
    }

    if ($settings['bj_pays'] == 0) $bj_pays = 2.5;
    else $bj_pays = 2.2;

    if ($blackjack == true) $regularWin = $bj_pays;
    else $regularWin = 2;
    if ($regular_or_tie == 'tie') $multip = ($settings['tie_dealerwon'] == 1) ? 0 : 1;
    else if ($regular_or_tie == 'lose') $multip = ($first_won_second_lose) ? 1 : 0;
    else if ($regular_or_tie == 'regular') $multip = ($first_won_second_lose) ? 1 : $regularWin;
    if ($first_won_second_lose) $multip = 1;
    if ($final_shuffle != '') {
        $endGame = ",`last_client_seed`=`client_seed`"
            . ",`last_final_shuffle`='$final_shuffle'"
            . ",`last_initial_shuffle`=`initial_shuffle`"
            . ",`initial_shuffle`='" . generateInitialShuffle($player['client_seed']) . "'";

        db_query("UPDATE `games` SET `multiplier`=$multip WHERE `id`=$game_id LIMIT 1");
    } else $endGame = "";

    if ($regular_or_tie == 'tie') {
        if ($settings['tie_dealerwon'] != 1) $multip = 1;
    }
    db_query("UPDATE `players` SET "

        . "`balance`=ROUND((`balance`+" . ($wager * $multip) . "),9)"
        . $endGame
        . " WHERE `id`=$player_id LIMIT 1");

    $t_wins = 0;
    $t_bets = 0;
    $t_wagered = 0;
    if ($regular_or_tie == 'regular') {
        $t_wins = 1;
    }
    if ($regular_or_tie == 'tie') {
        $t_bets -= 1;
        $t_wagered = $wager * -1;
    } else
        db_query("UPDATE `system` SET `jack_wagered`=`jack_wagered`+$t_wagered,`jack_bets`=`jack_bets`+$t_bets,`jack_wins`=`jack_wins`+$t_wins,`t_player_profit`=ROUND((`t_player_profit`+" . ($wager * $multip) . "),8) WHERE `id`=1 LIMIT 1");

}

function generateInitialShuffle($client_seed)
{
    $settings = db_fetch_array(db_query("SELECT `number_of_decks` FROM `system` LIMIT 1"));
    $initial_shuffle = array();
    for ($i = 0; $i < $settings['number_of_decks']; $i++) {
        shuffle($initial_shuffle);
        $newDeck = listDeck();
        shuffle($newDeck);
        $initial_shuffle = array_merge($initial_shuffle, listDeck());
        shuffle($initial_shuffle);
    }
    shuffle($initial_shuffle);
    $initial_shuffle = cs_shuffle($client_seed, $initial_shuffle);
    return serialize(array('initial_array' => $initial_shuffle, 'random_string' => generateHash(32)));
}

function listDeck()
{
    $blacks = array(
        '♠_A_black', '♥_A_red', '♦_A_red', '♣_A_black',
        '♠_2_black', '♥_2_red', '♦_2_red', '♣_2_black',
        '♠_3_black', '♥_3_red', '♦_3_red', '♣_3_black',
        '♠_4_black', '♥_4_red', '♦_4_red', '♣_4_black',
        '♠_5_black', '♥_5_red', '♦_5_red', '♣_5_black',
        '♠_6_black', '♥_6_red', '♦_6_red', '♣_6_black',
        '♠_7_black', '♥_7_red', '♦_7_red', '♣_7_black',
        '♠_8_black', '♥_8_red', '♦_8_red', '♣_8_black',
        '♠_9_black', '♥_9_red', '♦_9_red', '♣_9_black',
        '♠_10_black', '♥_10_red', '♦_10_red', '♣_10_black',
        '♠_J_black', '♥_J_red', '♦_J_red', '♣_J_black',
        '♠_Q_black', '♥_Q_red', '♦_Q_red', '♣_Q_black',
        '♠_K_black', '♥_K_red', '♦_K_red', '♣_K_black',
    );

    $return = array();

    foreach ($blacks as $black) {
        $return[] = $black;
    }
    return $return;
}

function cs_shuffle($client_seed, $deck)
{

    $final_deck = $deck; // copy deck to final_deck

    srand((int)$client_seed);

    foreach ($final_deck as $key => $final_card) {
        do {
            $deck_index = rand(0, count($deck) - 1);
        } while ($deck[$deck_index] === null);

        $final_deck[$key] = $deck[$deck_index];

        $deck[$deck_index] = null;
    }

    srand(mt_rand());  //D/$final_deck[]='♠_8_black';$final_deck[]='♠_8_black';$final_deck[]='♠_J_black';$final_deck[]='♠_2_black';$final_deck[]='♠_3_black';$final_deck[]='♠_A_black';$final_deck[]='♠_4_black';$final_deck=array_reverse($final_deck);

    return $final_deck;
}




function get_count($player = '', $filter = '')
{
    $where = '';
    if($player != '') $where .= "WHERE `player`='$player'";
    if($player == '' && $filter != '') $where .= "WHERE ";
    else $where .= " AND ";
    if($filter == 'wins') $where .= "`multiplier` > 1";
    else if($filter == 'losses') $where .= "`multiplier` < 1";
    else if($filter == 'ties') $where.= "`multiplier` = 1";
    $pocet = 0;
    if (isset($_GET['g']) && $_GET['g'] == 'blackjack') {
        $pocet += db_num_rows(db_query("SELECT `id` FROM `games` $where"));
    } else if (isset($_GET['g'])) {
        if($where != '') $where .= " AND `game`='" . $_GET['g'] . "'";
        else $where = "WHERE `game`='" . $_GET['g'] . "'";
        $pocet += db_num_rows(db_query("SELECT `id` FROM `spins` $where"));
    } else {
        $pocet += db_num_rows(db_query("SELECT `id` FROM `spins` $where"));
        $pocet += db_num_rows(db_query("SELECT `id` FROM `games` $where"));
    }
    return $pocet;
}
function get_wagered($player = ''){
    if($player != '') $where = "WHERE `player`='$player'";
    $soucet = 0;
    if (isset($_GET['g']) && $_GET['g'] == 'blackjack') {
        $q = db_query("SELECT `bet_amount` FROM `games` $where");
        while($row = db_fetch_array($q)){
            $soucet += $row['bet_amount'];
        }
    }
    else if (isset($_GET['g'])) {
        $q = db_query("SELECT `bet_amount` FROM `spins` $where");
        while($row = db_fetch_array($q)){
            $soucet += $row['bet_amount'];
        }
    }
    else {
        $q = db_query("SELECT `bet_amount` FROM `spins` $where");
        while($row = db_fetch_array($q)){
            $soucet += $row['bet_amount'];
        }
        $q = db_query("SELECT `bet_amount` FROM `games` $where");
        while($row = db_fetch_array($q)){
            $soucet += $row['bet_amount'];
        }
    }
    return $soucet;
}