$(document).ready(function () {
    decks = {};
    buttons = {
        disableAll: function () {
            $('.gameControllers').attr('disabled', true);
            $('.gameControllers').addClass('btn-disabled');
        },
        enableAll: function () {
            this.bet.enable();
            this.hit.enable();
            this.stand.enable();
            this.double.enable();
            this.split.enable();
        },
        bet: new _button($('.gameControllers.gC-5,.gameControllers.gC-6,.gameControllers.gC-7')),
        hit: new _button($('.gameControllers.gC-1')),
        stand: new _button($('.gameControllers.gC-2')),
        double: new _button($('.gameControllers.gC-3')),
        split: new _button($('.gameControllers.gC-4'))
    };

});


investUpdate();
statsUpdate();
setInterval(function(){
    investUpdate();
    statsUpdate();
}, 5000);

setInterval(function(){
    stats.update();
},500);
stats.update();


function tablePosition(){
    $('.gamblingTable').css('transform', 'translate(-'+$('.gamblingTable').width()/2+'px, -185px)');
}

function sumPosition(){
    if(decks.splitted){
        $('#player_checksum_2').css('left', $('.cj-playerTable').width()+10+'px');
    }
    else{
        $('#player_checksum').css('left', Number(($('.cj-playerTable').width() - $('.cj-playerTable .deck').width()) / 2 - $('#player_checksum').width()-20) + 'px');
    }
    $('#dealer_checksum').css('left', Number(($('.cj-dealerTable').width() - $('.cj-dealerTable .deck').width()) / 2 - $('#dealer_checksum').width()-20) + 'px');
}

function br_div() {
    var repaired=(parseFloat($(".betInput").val())/2).toFixed(8);
    if (isNaN(repaired)==true || parseFloat(repaired)<0) $(".betInput").val('0.00000000');
    else $(".betInput").val(repaired);
}
function br_multip() {
    var repaired=(parseFloat($(".betInput").val())*2).toFixed(8);
    if (isNaN(repaired)==true || parseFloat(repaired)<0) $(".betInput").val('0.00000000');
    else $(".betInput").val(repaired);
}
function _betChanged() {
    var repaired=parseFloat($(".betInput").val()).toFixed(8);
    if (isNaN(repaired)==true || parseFloat(repaired)<0) $(".betInput").val('0.00000000');
    else $(".betInput").val(repaired);
}

function bet_error(con) {
    alert(con);
}

function gameAction(action) {
    buttons.disableAll();
    $.ajax({
        'url': './content/ajax/gameAction.php?_unique='+unique()+'&action='+action,
        'dataType': "json",
        'success': function (data) {
            if (data['error']=='balance') {
                alert('You have insufficient funds. Please deposit.');
                buttonsAccess(data['accessable']);
                return;
            }
            if (data['data']['candouble'] == 0) buttons.double.disablePerm();
            if (data['split']=='true') splitPlayerDecks(function(){
                gameUpdate(data['data']);
                if (data['data']['mark']==1) decks.player.setMark();
                if (data['data']['mark']==2) decks.player_2.setMark();
            });
            else gameUpdate(data['data']);
            fairUpdate(data['data']['fair']);
        }
    });
}

function gameUpdate(data) {
    if (typeof data['splitted_cards']!='undefined') {
        decks.player.addCard(
            data['splitted_cards']['card-1'][0],
            data['splitted_cards']['card-1'][1],
            data['splitted_cards']['card-1'][2]
        );
        tablePosition();
        decks.player_2.addCard(
            data['splitted_cards']['card-2'][0],
            data['splitted_cards']['card-2'][1],
            data['splitted_cards']['card-2'][2]
            ,function(){
                buttonsAccess(data['accessable']);
                decks.player.setSum(data['splitted_cards']['deck-1-value']);
                decks.player_2.setSum(data['splitted_cards']['deck-2-value']);

                if (data['winner']!='-' && data['winner']!='tie') ceremonial(true,'<b>WON</b>');
                else if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
            });
        tablePosition();
    }
    if (typeof data['hitted_card-player_deck']!='undefined') {
        decks.player.addCard(
            data['hitted_card-player_deck'][0],
            data['hitted_card-player_deck'][1],
            data['hitted_card-player_deck'][2]
            ,function(){
                decks.player.setSum(data['hitted_sum']);
                buttonsAccess(data['accessable']);
                if (data['dealer_new']!='-') decks.dealer.revealCards(data['dealer_new']);
                if (data['dealer_sum']!='-') decks.dealer.setSum(data['dealer_sum']);
                if (data['winner']=='player') {

                    $pldeck=decks.player.$deckObj;
                    card1=$pldeck.children().eq(0).find('.value').html();
                    card2=$pldeck.children().eq(1).find('.value').html();

                    if ((card1=='A' && (card2=='10' || card2=='J' || card2=='K' || card2=='Q')) || ((card1=='10' || card1=='J' || card1=='K' || card1=='Q') && card2=='A')) blackjacked=true;
                    else blackjacked=false;

                    if (blackjacked) {ceremonial(true,'<b>BLACKJACK</b>')}
                    else ceremonial(true,'<b>WON</b>');
                }
                if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
                if (data['winner']=='dealer') ceremonial(false,'<b>LOSE</b>');

                if (typeof data['re-stand']!='undefined' && data['winner']=='-') gameAction('stand');
            });
        tablePosition();
    }
    if (typeof data['nextDeck']!='undefined' && data['nextDeck']=='yes') {
        if (typeof decks.player!='undefined') decks.player.removeMark();

        if (typeof decks.player_2!='undefined')  decks.player_2.setMark();
    }
    if (typeof data['hitted_card-player_deck_2']!='undefined') {
        decks.player_2.addCard(
            data['hitted_card-player_deck_2'][0],
            data['hitted_card-player_deck_2'][1],
            data['hitted_card-player_deck_2'][2]
            ,function(){
                decks.player_2.setSum(data['hitted_sum']);
                buttonsAccess(data['accessable']);
                if (data['dealer_new']!='-') decks.dealer.revealCards(data['dealer_new']);
                if (data['dealer_sum']!='-') decks.dealer.setSum(data['dealer_sum']);
                if (data['winner']=='player') ceremonial(true,'<b>WON</b>');
                if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
                if (data['winner']=='dealer') ceremonial(false,'<b>LOSE</b>');

                if (typeof data['re-stand']!='undefined' && data['winner']=='-') gameAction('stand');



            });
        tablePosition();
    }
    if (typeof data['standed']!='undefined') {
        buttonsAccess(data['accessable']);
        if (data['dealer_new']!='-') decks.dealer.revealCards(data['dealer_new']);
        if (data['dealer_sum']!='-') decks.dealer.setSum(data['dealer_sum']);
        if (data['winner']=='player') ceremonial(true,'<b>WON</b>');
        if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
        if (data['winner']=='dealer') ceremonial(false,'<b>LOSE</b>');

    }
}

function deck(_deckObj) {

    this.$deckObj = _deckObj;
    if(this.$deckObj.hasClass('deck-2')){
        this.$sumObj = $('<div id="player_checksum_2" class="deckSum"></div>')
            .appendTo('.cj-playerTable');
        this.$markObj = $('<div class="deck_mark mark-2"><span class="glyphicon glyphicon-chevron-down"></span></div>')
            .appendTo('.cj-playerTable');
    }
    else if (this.$deckObj.hasClass('player-deck')) {
        this.$sumObj = $('<div id="player_checksum" class="deckSum"></div>')
            .appendTo('.cj-playerTable');
        this.$markObj = $('<div class="deck_mark"><span class="glyphicon glyphicon-chevron-down"></span></div>')
            .appendTo('.cj-playerTable');
    }
    else{
        this.$sumObj = $('<div id="dealer_checksum" class="deckSum"></div>')
            .appendTo('.cj-dealerTable');
    }

    this.addCard = function (suit,val,colour,done,quick) {

        var $deck=this.$deckObj;

        // init card
        var $emptyOuter=$('<div class="cardOuter"></div>').appendTo($deck);
        var $newCard=$('<div class="card"><div class="value">'+val+'</div><div class="suit">'+suit+'</div></div>');

        if (suit=='-') $newCard.prepend('<div class="back"></div>');

        if (colour=='red') $newCard.addClass('red');

        var $temp=$emptyOuter.clone().appendTo('body');
        $temp.addClass('clone');
        $temp.css('left',$emptyOuter.offset().left);
        $temp.append($newCard);

        $emptyOuter.append($newCard.clone().css('visibility','hidden'));

        sumPosition();
        if (quick == 'quick') {
            $temp.css('top', $emptyOuter.offset().top);
            $emptyOuter.children().css('visibility','visible');
            $temp.remove();
            if (done!=null) done();
        }

        $temp.animate({'top':$emptyOuter.offset().top},600,'linear',function(){
            $emptyOuter.children().css('visibility','visible');
            $temp.remove();
            if (done!=null) done();
        });

    };
    this.revealCards = function (cards) {
        var $deck=this.$deckObj;

        $deck.empty();

        for (var i=0;i<cards.length;i++) {
            var $emptyOuter=$('<div class="cardOuter"></div>').appendTo($deck);
            var $newCard=$('<div class="card"><div class="value">'+cards[i][1]+'</div><div class="suit">'+cards[i][0]+'</div></div>');

            if (cards[i][2]=='red') $newCard.addClass('red');

            $emptyOuter.append($newCard);
            tablePosition();
            sumPosition();

        }

    };

    this.setSum = function (newsum) {
        var $sum=this.$sumObj;
        $sum.html(newsum);
        $sum.css('display','block');
        sumPosition();
    };

    this.setMark = function () {
        var $mark=this.$markObj;
        $mark.css({
            display: 'block'
        });
        $('.deck_mark:not(.mark-2)').css('left', $('.player-deck').width()/2+'px');
        $('.deck_mark.mark-2').css('left', ($('.cj-playerTable').width() - $('.deck-2').width()/2)+'px');
    };
    this.removeMark = function () {
        var $mark=this.$markObj;

        $mark.css({
            display: 'none'
        });
    }
}

function bet() {
    //ajax call (success: initGame())
    buttons.disableAll();
    $.ajax({
        'url': './content/ajax/gameCreate.php?wager='+$('.wager').val()+'&_unique='+unique(),
        'dataType': "json",
        'success': function (data) {
            if (data['error']=='yes') {
                buttons.bet.enable();
                if (data['content']=='balance') alert('Your balance is too small. Please deposit.');
                if (data['content']=='too_big') alert('We can not currently operate such big bets.');
                if (data['playing']) location.reload();
            }
            else {
                initGame();
                $('.gamblingTable').css('transform', 'translate(-72.5px, -185px)');
                decks.player.addCard(
                    data['content']['player-1'][0],
                    data['content']['player-1'][1],
                    data['content']['player-1'][2]
                    ,function(){
                        decks.dealer.addCard(
                            data['content']['dealer-1'][0],
                            data['content']['dealer-1'][1],
                            data['content']['dealer-1'][2]
                            ,function(){
                                decks.player.addCard(
                                    data['content']['player-2'][0],
                                    data['content']['player-2'][1],
                                    data['content']['player-2'][2]
                                    ,function(){
                                        if (data['insured']!='-') {
                                            decks.dealer.addCard(
                                                data['content']['dealer-2'][0],
                                                data['content']['dealer-2'][1],
                                                data['content']['dealer-2'][2]
                                                ,function(){
                                                    buttonsAccess(data['accessable']);
                                                    decks.player.setSum(data['sums']['player']);
                                                    if (data['sums']['dealer']!='-') decks.dealer.setSum(data['sums']['dealer']);

                                                    if (data['winner']=='player') {
                                                        if ((data['content']['player-1'][1]=='A' && (data['content']['player-2'][1]=='10' || data['content']['player-2'][1]=='J' || data['content']['player-2'][1]=='K' || data['content']['player-2'][1]=='Q')) || (data['content']['player-2'][1]=='A' && (data['content']['player-1'][1]=='10' || data['content']['player-1'][1]=='J' || data['content']['player-1'][1]=='K' || data['content']['player-1'][1]=='Q'))) ceremonial(true,'<b>BLACKJACK</b>')
                                                        else ceremonial(true,'<b>WON</b>');
                                                    }
                                                    if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
                                                    if (data['winner']=='dealer') ceremonial(false,'<b>LOSE</b>');
                                                });
                                        }
                                        else {
                                            buttonsAccess(data['accessable']);
                                            decks.player.setSum(data['sums']['player']);
                                            decks.dealer.setSum(data['sums']['dealer']);
                                            insuranceQ();
                                        }
                                    });
                            });
                    });

            }
        }
    });
}

function insuranceQ() {
    $('.g_insurance a').each(function(){
        $(this).removeAttr('disabled');
        $(this).removeClass('btn-disabled');
    });
    $('.g_controls').css('display','none');
    $('.g_insurance').css('display','inline-block');
}

function insure(a) {
    $.ajax({
        'url': './content/ajax/insure.php?_unique='+unique()+'&ans='+a,
        'dataType': 'json',
        'success': function (data) {
            location.reload();
        }
    });
}

function ceremonial(won,content) {
    enablePerms();
    $cermess=$('<div class="ceremonial"></div>').appendTo('.cj-rivalTables');

    $cermess.html(content);

    $cermess.css({
        'display': 'block',
        'transform': 'translate(-'+$cermess.width()/2+'px, -15px)'
    });
}

function _button(_obj) {

    this.$obj = _obj;
    this.disabledPerm = false;

    this.disable = function() {
        var $obj=this.$obj;
        $obj.attr('disabled',true);
        $obj.addClass('btn-disabled');
    };

    this.enable = function() {
        if (this.disabledPerm != true) {
            var $obj=this.$obj;
            $obj.removeAttr("disabled");
            $obj.removeClass('btn-disabled');
        }
    };

    this.disablePerm = function() {
        this.disable();
        this.disabledPerm = true;
    };
    this.enablePerm = function() {
        this.disabledPerm = false;
    }

}

function initGame() {
    $('.cj-rivalTables').children().empty();
    $('.deckSum').remove();
    $('.ceremonial').remove();
    $('.deck_mark').remove();
    decks = {
        dealer: new deck($('<div class="deck"></div>').appendTo('.cj-dealerTable')),
        player: new deck($('<div class="deck player-deck"></div>').appendTo('.cj-playerTable'))
    };
    enablePerms();
}

function enablePerms() {
    buttons.split.enablePerm();
    buttons.double.enablePerm();
    buttons.stand.enablePerm();
    buttons.hit.enablePerm();
    buttons.bet.enablePerm();
}

function playingOnInit() {
    buttons.disableAll();
    $.ajax({
        'url': './content/ajax/gameContinue.php?_unique='+unique(),
        'dataType': "json",
        'success': function (data) {
            $('.betAmount .betInput').val((parseFloat(data['bet_amount'])*2));br_div();
            initGame();

            for (var i=0;i<data['dealer']['cards'].length;i++) {

                decks.dealer.addCard(
                    data['dealer']['cards'][i][0],
                    data['dealer']['cards'][i][1],
                    data['dealer']['cards'][i][2]
                    ,function(){
                        buttonsAccess(data['accessable']);
                        //decks.dealer.setSum(data['sums']['dealer']);
                        decks.player.setSum(data['sums']['player']);
                    }, 'quick');
                tablePosition();


            }
            for (var i=0;i<data['player']['cards'].length;i++) {

                decks.player.addCard(
                    data['player']['cards'][i][0],
                    data['player']['cards'][i][1],
                    data['player']['cards'][i][2], function(){}, 'quick'
                );
                tablePosition();

                if (data['player']['cards2'] && i==1) splitPlayerDecks(function(){});


            }
            if (data['player']['cards2']) {
                for (var i=0;i<data['player']['cards2'].length;i++) {

                    decks.player_2.addCard(
                        data['player']['cards2'][i][0],
                        data['player']['cards2'][i][1],
                        data['player']['cards2'][i][2], function(){}, 'quick'
                    );
                    tablePosition();
                }
                if (data['data']['mark']==1) decks.player.setMark();
                if (data['data']['mark']==2) decks.player_2.setMark();
                if (data['sums']['player2']!='-') decks.player_2.setSum(data['sums']['player2']);

            }
        }
    });

}

function endedOnInit() {
    $.ajax({
        'url': './content/ajax/showEnded.php?_unique='+unique(),
        'dataType': "json",
        'success': function (data) {
            $('.betAmount .betInput').val((parseFloat(data['bet_amount'])*2));br_div();
            initGame();
            buttons.disableAll();
            buttons.bet.enable();

            for (var i=0;i<data['dealer']['cards'].length;i++) {

                decks.dealer.addCard(
                    data['dealer']['cards'][i][0],
                    data['dealer']['cards'][i][1],
                    data['dealer']['cards'][i][2]
                    ,function(){
                        decks.dealer.setSum(data['sums']['dealer']);
                        decks.player.setSum(data['sums']['player']);
                        if (data['sums']['player2']!='-') decks.player_2.setSum(data['sums']['player2']);
                        if (data['winner']=='player') ceremonial(true,'<b>WON</b>');
                        if (data['winner']=='tie') ceremonial(true,'<b>TIE</b>');
                        if (data['winner']=='dealer') ceremonial(false,'<b>LOSE</b>');
                    }, 'quick');
                tablePosition();


            }
            for (var i=0;i<data['player']['cards'].length;i++) {

                decks.player.addCard(
                    data['player']['cards'][i][0],
                    data['player']['cards'][i][1],
                    data['player']['cards'][i][2], function(){}, 'quick'
                );
                tablePosition();

                if (data['player']['cards2'] && i==1) splitPlayerDecks(function(){});


            }
            if (data['player']['cards2']) {
                for (var i=0;i<data['player']['cards2'].length;i++) {

                    decks.player_2.addCard(
                        data['player']['cards2'][i][0],
                        data['player']['cards2'][i][1],
                        data['player']['cards2'][i][2], function(){}, 'quick'
                    );
                    tablePosition();
                }

            }

        }
    });
}

function buttonsAccess(v) {
    if (v==0) {
        buttons.disableAll();
        buttons.bet.enable();
    }
    else if (v==1) {
        buttons.enableAll();
        buttons.bet.disable();
        buttons.split.disable();
    }
    else if (v==2) {
        buttons.enableAll();
        buttons.bet.disable();
    }
}

function splitPlayerDecks(done_fc) {
    var $secDeck = $('<div class="deck deck-2"></div>');
    decks.player_2 = new deck($secDeck);

    var $firstDeck = decks.player.$deckObj;

    var firstCard_offset_1 = $firstDeck
        .children().eq(-1)
        .offset();
    var firstCard_clone = $firstDeck
        .children().eq(-1)
        .clone().appendTo('body')
        .css('z-index','30')
        .css('position','absolute')
        .css('left',firstCard_offset_1.left)
        .css('top',firstCard_offset_1.top)
        .addClass('clone');
    var secCard_offset_1 = $firstDeck
        .children().eq(0)
        .offset();
    var secCard_clone = $firstDeck
        .children().eq(0)
        .clone().appendTo('body')
        .css('position','absolute')
        .css('left',secCard_offset_1.left)
        .css('top',secCard_offset_1.top)
        .addClass('clone');

    $firstDeck
        .children().eq(-1)
        .remove()
        .clone()
        .appendTo($secDeck);


    $firstDeck
        .wrap($('<table></table>').addClass('splitted'))
        .wrap('<tr></tr>')
        .parent().append($secDeck).children()
        .children().children().css('visibility','hidden').parent().parent()
        .wrap('<td></td>');

    decks.splitted = true;

    var firstCard_offset_2 = $secDeck
        .children().eq(0)
        .offset();
    var secCard_offset_2 = $firstDeck
        .children().eq(0)
        .offset();

    firstCard_clone.animate({
        'top': firstCard_offset_2.top,
        'left': firstCard_offset_2.left
    },100,'linear',function(){
        firstCard_clone.remove();
        $firstDeck.children().children().css('visibility','visible');
        done_fc();
    });
    secCard_clone.animate({
        'top': secCard_offset_2.top,
        'left': secCard_offset_2.left
    },100,'linear',function(){
        secCard_clone.remove();
        $secDeck.children().children().css('visibility','visible');
    });
    tablePosition();

}
