$(document).ready(function () {
    $('.bet_type button').on('click', function(){
        $('.bet_type button.active').removeClass('active');
        $(this).addClass('active');
        if($(this).data('toggle') == 'm_bet'){
            $('#m_bet').css('display', 'block');
            $('#b_bet').css('display', 'none');
            $('#roll_btn').html('Roll dice').attr('onclick', "singleRoll();");
        }
        else{
            $('#b_bet').css('display', 'block');
            $('#m_bet').css('display', 'none');
            $('#roll_btn').html('Start rolling').attr('onclick', 'startAutomat();');
        }
    });
    $('#betTb_multiplier').change(function () {
        recountChance();
    });
    $('#betTb_chance').change(function () {
        recountPayout();
    });
    $('#bt_profit').change(function () {
        recountProfit();
    });
});

function singleRoll(){
    place($('#bt_wager').val(), $('#betTb_multiplier').val(), false);
}

var house_edge = 1;

var under_over=0;
function inverse() {
    if (under_over==0) {
        $("#under_over_txt").html('Roll over');
        under_over=1;
        recountUnderOver();
    }
    else {
        $("#under_over_txt").html('Roll under');
        under_over=0;
        recountUnderOver();
    }
}
function clickdouble() {
    $("#bt_wager").val((parseFloat($("#bt_wager").val())*2).toFixed(8)).change();
}
function clickmax() {
    $("#bt_wager").val($(".balance").html()).change();
}
function maxProfit() {
    var newval = parseFloat($("#bt_wager").val())*(10000*(1-(1/100)));
    $("#bt_profit").val(newval).change();
}

function recountProfit() {
    var payout=parseFloat($("#betTb_multiplier").val());
    var wager=parseFloat($("#bt_wager").val());
    $("#bt_profit").val(parseFloat((Math.round(((wager*payout)-wager)*1000000000)/1000000000).toFixed(9).toString().match(/^\d+(?:\.\d{0,8})?/)).toFixed(8));
}
function recountPayout() {
    var chance=parseFloat($("#betTb_chance").val()).toFixed(2);
    var payout=(1/(chance/100)*((100-house_edge)/100));
    $("#betTb_multiplier").val(parseFloat(payout.toString().match(/^\d+(?:\.\d{0,2})?/)).toFixed(2));
    recountUnderOver();
}
function recountChance() {
    var payout=parseFloat($("#betTb_multiplier").val());
    var chance=(1/(payout/100)*((100-house_edge)/100));
    $("#betTb_chance").val(parseFloat((Math.round(chance*1000000000)/1000000000).toString().match(/^\d+(?:\.\d{0,2})?/)).toFixed(2));
    recountUnderOver();
}
function recountUnderOver() {
    if (under_over==true) var chance_=100-parseFloat($("#betTb_chance").val()).toFixed(2);
    else var chance_=parseFloat($("#betTb_chance").val()).toFixed(2);
    $("#under_over_num").html(parseFloat(chance_.toString().match(/^\d+(?:\.\d{0,2})?/)).toFixed(2));
}
ajaxBetLock=false;
var lastBet=(Date.now()-500);
function place(wager,multiplier,bot) {
    if ((ajaxBetLock==false && (Date.now())>(lastBet+500)) || bot==true) {
        ajaxBetLock=true;
        lastBet=Date.now();
        $("#betBtn").html('Rolling');
        $.ajax({
            'url': './content/ajax/place.php?w='+wager+'&m='+multiplier+'&hl='+under_over+'&_unique='+unique(),
            'dataType': "json",
            'success': function(data) {
                if (data['error']=='yes') {
                    if (data['data']=='too_small') alert('Error: Your bet is too small.');
                    if (data['data']=='invalid_bet') alert('Error: Your balance is too small for this bet.');
                    if (data['data']=='invalid_m') alert('Error: Invalid multiplier.');
                    if (data['data']=='invalid_hl') alert('Error: Invalid under/over specifier.');
                    if (data['data']=='too_big_bet') alert('Error: Your bet is too big. At this time we only accept bets which are not bigger than '+data['under']+cursig());
                }
                else {
                    var result=data['result'];
                    var win_lose=data['win_lose'];
                    fairUpdate(data['fair']);
                }
                $("#betBtn").html('Roll dice');
                ajaxBetLock=false;

                if (bot==true && data['error']=='no') {
                    setTimeout(function(){
                        bB_profit-=wager;
                        if (win_lose==1) bB_profit+=(wager*multiplier);
                        bB_profit=Math.round(bB_profit*1000000000)/1000000000;
                        placed(win_lose);
                    },500);
                    if (operateMode==0) {
                        operateNum--;
                        $("#botBtn").html('Rolls left: '+operateNum);
                    }
                }
                if (bot==true && data['error']=='yes') {
                    startAutomat();
                }
            }
        });
    }
}

var bB_active=false;
var operateMode;
var operateNum;
var bB_profit=0;

function startAutomat() {
    if (bB_active==false) {
        bB_active=true;
        operateNum=parseInt($("#bt_rolls_bB").val());
        operateMode=$("#bB_operate:checked").val();
        _interval=setInterval(function(){
            if (operateNum!=0){
                operateNum--;
                place($('#bt_wager').val(),$('#betTb_multiplier').val(),true);
                if (operateMode=='seconds') {
                    $("#roll_btn").html('Time left: '+operateNum+'s');
                }
                else $("#roll_btn").html('Rolls: '+operateNum);
            }
            else {
                bB_active = false;
                $("#roll_btn").html('Canceling');
                placed(0);
            }
        },1000);
    }
}
function placed(win_or_lose) {
    if (bB_active==false || operateNum<1 || ($("#bB_max_loss").prop('checked')==true && bB_profit<=(0-parseFloat($("#bB_max_loss_val").val()))) || ($("#bB_max_win").prop('checked')==true && bB_profit>=parseFloat($("#bB_max_win_val").val()))) {
        if (typeof(_interval)!='undefined') clearInterval(_interval);
        bB_active=false;
        $("#roll_btn").html('Start rolling');
        bB_profit=0;
    }
    else {
        var onWin = $('#bB_win:checked').val();
        var onLoss = $('#bB_loss:checked').val();
        if (win_or_lose==1) {     // WIN
            if (onWin=='return') $("#bt_wager").val(parseFloat($("#bt_wager_bB").val())).change();
            else $("#bt_wager").val((parseFloat($("#bt_wager").val())+((parseFloat($("#bt_wager").val())/100)*parseFloat($("#bB_win_increase_by").val())))).change();
        }
        else {                    // LOSS
            if (onLoss=='return') $("#bt_wager").val(parseFloat($("#bt_wager_bB").val())).change();
            else $("#bt_wager").val((parseFloat($("#bt_wager").val())+((parseFloat($("#bt_wager").val())/100)*parseFloat($("#bB_loss_increase_by").val())))).change();
        }
        place($('#bt_wager').val(),$('#betTb_multiplier').val(),true);
    }
}