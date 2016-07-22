<?php
generateInitialShuffle();
function random_num($length) {
    $possibilities='1234567890';
    $return='';
    for ($i=0;$i<$length;$i++)  $return.=$possibilities[mt_rand(0,strlen($possibilities)-1)];
    return $return;
}

function generateHash($length,$capt=false) {
    if ($capt==true) $possibilities='123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    else $possibilities='abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $return='';
    for ($i=0;$i<$length;$i++)  $return.=$possibilities[mt_rand(0,strlen($possibilities)-1)];
    return $return;
}

function generateInitialShuffle() {
    $initial_shuffle=array();
    for ($i=0;$i<4;$i++) {
        shuffle($initial_shuffle);
        $newDeck=listDeck();
        shuffle($newDeck);
        $initial_shuffle=array_merge($initial_shuffle,listDeck());
        shuffle($initial_shuffle);
    }
    shuffle($initial_shuffle);
    $initial_shuffle=cs_shuffle(random_num(8),$initial_shuffle);
    echo serialize(array('initial_array'=>$initial_shuffle,'random_string'=>generateHash(32)));
}
function listDeck() {
    $blacks=array(
        '♠_A_black',  '♥_A_red',  '♦_A_red',  '♣_A_black',
        '♠_2_black',  '♥_2_red',  '♦_2_red',  '♣_2_black',
        '♠_3_black',  '♥_3_red',  '♦_3_red',  '♣_3_black',
        '♠_4_black',  '♥_4_red',  '♦_4_red',  '♣_4_black',
        '♠_5_black',  '♥_5_red',  '♦_5_red',  '♣_5_black',
        '♠_6_black',  '♥_6_red',  '♦_6_red',  '♣_6_black',
        '♠_7_black',  '♥_7_red',  '♦_7_red',  '♣_7_black',
        '♠_8_black',  '♥_8_red',  '♦_8_red',  '♣_8_black',
        '♠_9_black',  '♥_9_red',  '♦_9_red',  '♣_9_black',
        '♠_10_black', '♥_10_red', '♦_10_red', '♣_10_black',
        '♠_J_black',  '♥_J_red',  '♦_J_red',  '♣_J_black',
        '♠_Q_black',  '♥_Q_red',  '♦_Q_red',  '♣_Q_black',
        '♠_K_black',  '♥_K_red',  '♦_K_red',  '♣_K_black',
    );

    $return=array();

    foreach ($blacks as $black) {
        $return[]=$black;
    }
    return $return;
}

function cs_shuffle($client_seed,$deck) {

    $final_deck=$deck; // copy deck to final_deck

    srand((int)$client_seed);

    foreach ($final_deck as $key => $final_card) {
        do {
            $deck_index = rand(0,count($deck)-1);
        } while ($deck[$deck_index]===null);

        $final_deck[$key]=$deck[$deck_index];

        $deck[$deck_index]=null;
    }

    srand(mt_rand());  //D/$final_deck[]='♠_8_black';$final_deck[]='♠_8_black';$final_deck[]='♠_J_black';$final_deck[]='♠_2_black';$final_deck[]='♠_3_black';$final_deck[]='♠_A_black';$final_deck[]='♠_4_black';$final_deck=array_reverse($final_deck);

    return $final_deck;
}