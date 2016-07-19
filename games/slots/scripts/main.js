$(document).ready(function(){

    for (var i=1;i<4;i++) {

        var $wheel = $('.slot.slot'+i+' .slotCon');

        $resline = $('<div class="resline"></div>').prependTo($wheel.parent());

        $resline.css('margin-top', ( $wheel.parent().height()/2 - $resline.height()/2 ));

        for (var ii=0;ii<74;ii++) {
            if (ii < 2)         item_i = 1;
            else if (ii < 7)    item_i = 2;
            else if (ii < 16)   item_i = 3;
            else if (ii < 26)   item_i = 4;
            else if (ii < 41)   item_i = 5;
            else if (ii < 74)   item_i = 6;

            var $item = $('<div class="item item'+item_i+' img'+item_i+'"></div>');

            $item.appendTo($wheel);

        }
        $wheel.children().shuffle();
        $wheel.children('.item2').eq(0).detach().appendTo($wheel);
        $wheel.children('.item1').eq(0).detach().appendTo($wheel);
        $wheel.children('.item3').eq(0).detach().appendTo($wheel);
        for (var ii=0;ii<54;ii++) {
            var $item = $('<div class="item item0"></div>');

            if (ii % 10 === 0) {

                order = -0.2;
                plus = -4;
            }
            else {
                order = 2;
                plus = 0;
            }

            $item.insertAfter($wheel.children().eq( (ii*order)+plus ));
        }




        $wheel.css('margin-top', ($wheel.height() *-1) );

        var itemTop = $wheel.children('.item1').eq(-1).offset().top;
        var displayTop = $wheel.parent().offset().top;

        var posun = (displayTop + parseInt($wheel.parent().css('padding-top'))) - itemTop;

        var mtop = ( parseInt($wheel.css('margin-top')) + posun);
        $wheel.css('margin-top', mtop);


    }

});


function spinWheel(wheel, items, index, dur ) {

    var $wheel = $('.slot.slot'+wheel+' .slotCon');

    $wheel.children().addClass('oldItems');

    var oldHeight = $wheel.height();

    $.each(items, function(i, val){


        var $item = $('<div class="item item'+val+' img'+val+' newItems"></div>');

        $item.prependTo($wheel);

    });


    $wheel.children('.oldItems').eq(0).clone().prependTo($wheel).removeClass('oldItems');

    $wheel.css('margin-top',"-="+( $wheel.height()-oldHeight ) );

    var $item = $wheel.children('.newItems').eq((index*-1)-1);

    var itemTop = $item.offset().top;

    var displayTop = $wheel.parent().offset().top;

    var posun = (displayTop + parseInt($wheel.parent().css('padding-top'))) - itemTop;

    posun += 30;


    posun += ( $wheel.parent().height()/2 - $item.height()/2 );


    $wheel.children('.newItems').removeClass('newItems');

    $wheel.animate({'margin-top': "-="+30},500,'swing',function(){
        $wheel.animate({'margin-top': "+="+posun},dur,'easeOutCirc',function(){
            $wheel.children('.oldItems').remove();

            lock.finished();

        });

    });

}
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function spin(data) {

    var timing = [
        getRandomInt(2000,4000),
        getRandomInt(2000,4000),
        getRandomInt(2000,4000)
    ];
    //timing.sort( function(a,b) {return a-b} );       // uncomment for ordering

    lock.fair = data['fair'];
    lock.started();

    spinWheel(1,data['items']['wheel1'],data['index'],timing[0]);
    spinWheel(2,data['items']['wheel2'],data['index'],timing[1]);
    spinWheel(3,data['items']['wheel3'],data['index'],timing[2]);

}

function fairUpdate(data) {

    $('#_fair_server_seed').val(data['newSeed']);
    $('#_fair_client_seed').val(data['newCSeed']);
    $('#_fair_l_server_seed').val(data['lastSeed_sha256']);
    $('#_fair_l_server_seed_p').val(data['lastSeed']);
    $('#_fair_l_client_seed').val(data['lastCSeed']);
    $('#_fair_l_result').val(data['lastResult']);

}

(function($){

    $.fn.shuffle = function() {

        var allElems = this.get(),
            getRandom = function(max) {
                return Math.floor(Math.random() * max);
            },
            shuffled = $.map(allElems, function(){
                var random = getRandom(allElems.length),
                    randEl = $(allElems[random]).clone(true)[0];
                allElems.splice(random, 1);
                return randEl;
            });

        this.each(function(i){
            $(this).replaceWith($(shuffled[i]));
        });

        return $(shuffled);

    };

})(jQuery);



var ajaxBetLock = false;

function bet() {
    if (lock.locked || ajaxBetLock) return;

    ajaxBetLock = true;

    $.ajax({
        'url': "./content/ajax/spin.php?_unique="+unique()+"&w="+$('.wager').val(),
        'dataType': "json",
        'success': function (data) {

            ajaxBetLock = false;

            if (data['error'] == 'no') {
                spin(data);
            }
            else alert(data['error']);
        }
    });
}