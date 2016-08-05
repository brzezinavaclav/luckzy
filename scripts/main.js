var ajaxBetLock = false;

function spinLock() {
  this.spinning = 0;
  this.locked = false;

  this.finished = function () {

    if (!this.spinning) return;

    this.spinning -= 1;

    if (!this.spinning) {
      this.unlock();
    }
  };

  this.unlock = function () {
    this.locked = false;
    $('.st-stats table tbody tr[data-hidden=1]').each(function(){

      if ($(this).parent().hasClass('reversed')) $(this).parent().removeClass('reversed');
      else $(this).parent().addClass('reversed');
      $(this).slideDown(100,function(){stats.limit();}).attr('data-hidden',0).next('.removablePlaceholder').remove();

    });

    fairUpdate(this.fair);
    investUpdate();

    if (bot.on) bet();

  };

  this.started = function (spins) {
    if(!spins) spins = 1;
    this.spinning = spins;
    this.locked = true;
  };

  this.fair;

}

var lock = new spinLock();

$(document).ready(function (){
  var selected = false;
  $('.navbar-first .navbar-nav > li > a').each(function (){
    if(location.href.indexOf($(this).attr('href')) != -1){
      $(this).addClass('active');
      selected = true;
    }
  });
  if(!selected) $('.navbar-first .navbar-nav > li > a[href^="?blackjack"]').addClass('active');

  $('.leftbuttons button').each(function(){
    $(this).tooltip();
  });
  $('.tooltips').each(function(){
    $(this).tooltip();
  });

  $('.chat-input').keypress(function(e){
    if (e.which == 13) chatSend($(this).val());
  });

  $(".wager").click(function () {
    $(this).select();
  });

  chatUpdate();
  setInterval(function(){
    chatUpdate();
  },500);

  setLeftbarH();
  setInterval(function(){
    setLeftbarH();
  },100);

  setInterval(function(){
    $.ajax({'url':'./content/ajax/refreshSession.php'});
  },10000);
  imitateCRON();
  setInterval(function(){
    imitateCRON();
    balanceUpdate();
    won_last();
  },1000);


  $('.st-switches a').each(function(){
    $(this).click(function(e){

      if ($(this).hasClass('rulesB')) return;

      e.preventDefault();
      $('.st-switches a.active').removeClass('active');
      $(this).addClass('active');

      stats.go( $(this).attr('data-load') );

    });
  });
  $('.st-switches a').eq(1).click();



  $('.leftbuttons button').each(function(){
    $(this).click(function(e){
      e.preventDefault();
      $('.leftbuttons button.active').removeClass('active');
      $(this).addClass('active');
    });
  });


  $('.wager').change(function(){
    formatWager();
  });

  $('.clientseedsave').click(function(e){

    e.preventDefault();

    var input = $('#_fair_client_seed');

    $.ajax({
      'url': './content/ajax/saveClientSeed.php?_unique='+unique()+'&seed='+input.val(),
      'dataType': 'json',
      'success': function(data) {
        input.val(data['repaired']);
        alert(data['content']);
      }
    });

  });

  $('#modals-deposit').on('show.bs.modal',function(){
    _genNewAddress();
  });
  $('#faucet_btn').click(function(){
    $('#modals-faucet').modal();
  });

  $('.modal').on('show.bs.modal',function(){
    $('.m_alert').hide();
  });

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('.m_alert').hide();
  });

  $('.captchadiv').click();
});

function withdraw(currency) {
  var amount = $('#w_'+currency+'_amount').val();
  $.ajax({
    'url': './content/ajax/withdraw.php?_unique='+unique()+'&valid_addr='+$('#w_btc_address').val()+'&amount='+amount+'&c='+currency,
    'dataType': "json",
    'success': function (data) {
      if(currency == 'btc') {
        if (data['error']=='yes') {
          m_alert('danger','<b>Error!</b> '+data['message']);
        }
        else if (data['error'] == 'half') {
          m_alert('info', '<b>Withdrawal request has been placed.</b>');
        }
        else {
          m_alert('success', 'Processed. TXID: <b>' + data['txid'] + '</b>');
          balanceUpdate();
        }
      }
      else{
        if (data['error']=='yes') {
          m_alert('danger','<b>Error!</b> '+data['message']);
        }
        else {
          m_alert('info', 'Your withdraw ID: <b>' + data['id'] + '</b>');
          balanceUpdate();
        }
      }
      $('#w_'+currency+'_amount').val()
    }
  });
}

function _genNewAddress() {
  $.ajax({
    'url': './content/ajax/makeDeposit.php?_unique='+unique()+'&c=btc',
    'dataType': "json",
    'success': function (data) {
      $('.addr-p').html(data['confirmed']);
      $('.addr-qr').empty();
      $('.addr-qr').qrcode(data['confirmed']);
    }
  });
}
function clickPending() {
  if ($('.pendingbutton').attr('cj-opened')=='yes') hidePending();
  else showPending();
}                    
function showPending() {
  $.ajax({
    'url': './content/ajax/getPending.php?_unique='+unique(),
    'dataType': "json",
    'success': function (data) {
      $('.pendingDeposits').html(data['content']);
      $('.pendingDeposits').slideDown();
      $('.pendingbutton').html('Hide Pending');
      $('.pendingbutton').attr('cj-opened','yes')
    }
  });
}
function hidePending() {
  $('.pendingDeposits').slideUp();
  $('.pendingbutton').html('Show Pending');
  $('.pendingbutton').attr('cj-opened','no');
}

function balanceUpdate() {
  
  if (lock.locked) return;
  
  $.ajax({
    'url': './content/ajax/getBalance.php?_unique='+unique(),
    'dataType': "json",
    'success': function (data) {
        
      $('.balance').each(function(){
        $(this).text(data['balance'] + ' Coins');
        $(this).text(data['balance'] + ' Coins');
      });
        
    }
  });
}


function wagerDiv() {
  var repaired=$(".wager").val();
  $(".wager").val(repaired);
}
function wagerMultip() {
  var repaired=$(".wager").val();
  $(".wager").val(repaired);
}
function wagerMax() {
  $(".wager").val($(".balance").html()).change();
}

function formatWager() {
  var repaired=$(".wager").val();
  $(".wager").val(repaired);
}




function imitateCRON() {
  $.ajax({
    'url': './content/ajax/getDeposits.php',
    'dataType': "json",
    'success': function (data) {
      if (data['maintenance'] == 'yes') location.reload();
    }  
  });
}


function setLeftbarH() {
  leftbox.$obj().height( $(window).height() - 130 );
  $('.leftCon').each(function(){
    var footer = $(this).children('.footer').outerHeight();
    $(this).children('.content').css('height', parseInt($(window).height()) - 130 - 41 - footer );
  });
  
}

var chatReceiveUpdates = false;
var chatUpdating = false;


function chatSend(val) {
  var dataToSend = encodeURIComponent(val);
  $.ajax({
    'url': './content/ajax/chatSend.php?_unique='+unique()+'&data='+dataToSend,
    'dataType': "json",
    'success': function(data) {
      if (data['error']=='yes' && data['content']=='max_in_row') alert('You can\'t post more than 10 messages in a row.');
      else {
        chatUpdate();
        $('.chat-input').val('');
      }
    }
  });
}

function chatUpdate(first) {

  if (!leftbox.$obj().children('#lc-chat').length) return;
  if (chatUpdating) return;
  if (!chatReceiveUpdates) return;
  
  var lastID = 0;
  if ($('.chat-message').length)
    lastID = $('.chat-message').last().attr('data-messid');
  
  

  chatUpdating = true;  

  $.ajax({
    'url': './content/ajax/chatUpdate.php?_unique='+unique()+'&lastId='+lastID,
    'dataType': "json",
    'success': function(data) {
      
      var $messages = $(data['content']);

      var $existingMessages = leftbox.$obj().find('.content .mCSB_container');

      $messages.each(function(){
        var $message = $(this).remove();
        var messid = $message.attr('data-messid');
        
      });
      
      $existingMessages.append($messages);

      if (!leftbox.scrolled) {

        if (leftbox.first)
          setTimeout(function(){
            leftbox.$obj().find('.content').mCustomScrollbar('scrollTo','last',{scrollInertia:100});
            setTimeout(function(){ leftbox.$obj().find('.content').mCustomScrollbar('scrollTo','last',{scrollInertia:50}); },110)
          },100);
        else if ($messages.length) leftbox.$obj().find('.content').mCustomScrollbar('scrollTo','bottom',{scrollInertia:100,callbacks:true});

      }
            
      chatUpdating = false;
    }
  });
}
function leftbox() {

  var self = this;

  self.opened = false;
  self.lock = false;

  self.toggle = function () {

    if (self.lock) return;
    self.lock = true;

    if (self.opened) {

      self.con = '';

      self.$obj().animate({
        'width': 0,
        'padding-left': 0,
        'padding-right': 0
      },{
        'duration': 300,
        'done': function() {
          self.opened = false;
          self.$obj().hide();
          self.lock = false;
          $('.closeLeft').hide();
          $('.leftbuttons button.active').removeClass('active');

        },
        'progress': function() {
          $('.page').width($(window).width() - ( (self.$obj().outerWidth() -2 )  ));
          $('.page').css('margin-left',self.$obj().width() + parseFloat(self.$obj().css('padding-left')) + parseFloat(self.$obj().css('padding-right')) );

        }
      });
      $('.st-stats table').animate({
        'width': 948
      },300);

    }
    else {

      self.$obj().show();
      $('.closeLeft').show();
      self.$obj().animate({
        'width': self.width,
        'padding-left': 10,
        'padding-right': 10
      },{
        'duration': 300,
        'done': function() {
          self.opened = true;
          self.lock = false;
          self.scrollbar();
        },
        'progress': function() {
          $('.page').width($(window).width() - ( self.$obj().outerWidth() + $('.lefbuttons').width() ));
          $('.page').css('margin-left',self.$obj().outerWidth() + $('.lefbuttons').width());
        }
      });

    }
  };

  self.width = 260;
  self.$obj = function() {
    return $('.leftblock');
  };
  self.scrollbar = function() {

    chatUpdate();
    self.first = true;

    var $scrollArea = self.$obj().children().children('.content');


    if ($scrollArea.parent().children('.footer').length) ifFooter = $scrollArea.parent().children('.footer').outerHeight();
    else ifFooter = 0;
    $scrollArea
    .height( parseInt($scrollArea.height()) - ifFooter )
    .mCustomScrollbar({
      theme: 'dark',
      scrollInertia: 0,
      alwaysShowScrollbar: 0,
      autoHideScrollbar: 1,
      scrollbarPosition: "outside",
      mouseWheel: {
        enable: true,
        scrollAmount: 30
      },
      setWidth: '100%',
      advanced: {
        updateOnContentResize: true
      },
      callbacks: {
        onTotalScroll: function() {
          self.scrolled = false;
          self.first = false;
        },
        onScrollStart: function() {
          self.scrolled = true;
          self.first = false;
        }
      }
    });

  };

  self.scrolled = false;
  self.first = true;
  self.con = '';


}
var leftbox = new leftbox();

function leftCon(con) {

  chatReceiveUpdates = false;
  $('.chat-input').tooltip('destroy');

  leftbox.$obj().children().children('.content').mCustomScrollbar("destroy");
  leftbox.$obj().empty();

  if (con == leftbox.con) {
    leftbox.toggle();
    return;
  }

  $newObj = $('#lc-'+con).clone(true,true).width(leftbox.width-22).show().appendTo(leftbox.$obj());


  leftbox.scrolled = false;
  if (con == 'chat') {
    chatReceiveUpdates = true;
    $('.chat-input').tooltip();
  }

  leftbox.con = con;

  if (!leftbox.opened) leftbox.toggle();
  else leftbox.scrollbar();



}

function stats( which ) {

  var self = this;
  
  self.on = false;
  
  self.$obj = function() {
    return $('.stats-' + which);
  }
  
  
  
}


var stats = {
  
  st : {
    my_bets : new stats('my_bets'),
    all_bets : new stats('all_bets'),
    high : new stats('high')
  },
  
  go : function (load) {
    
    if (stats.st.my_bets.on) {
      stats.st.my_bets.on = false;
      stats.st.my_bets.$obj().hide();
    }
    if (stats.st.all_bets.on) {
      stats.st.all_bets.on = false;
      stats.st.all_bets.$obj().hide();
    }
    if (stats.st.high.on) {
      stats.st.high.on = false;
      stats.st.high.$obj().hide();
    }

    stats.st[load].$obj().show();
    stats.st[load].on = true;    

    
  },
  
  update : function() {
  
    if (stats.updating) return;
    stats.updating = true;
  
    var last1 = parseInt( stats.st['my_bets'].$obj().children().eq(0).attr('data-betid') );
    var last2 = parseInt( stats.st['all_bets'].$obj().children().eq(0).attr('data-betid') );
    var last3 = parseInt( stats.st['high'].$obj().children().eq(0).attr('data-betid') );
  
    $.ajax({
      
      'url': './content/ajax/stats_load.php?_unique='+unique()+'&last=' + last1 + ',' + last2 + ',' + last3 + '&game='+get_active_game(),
      'dataType': "json",
      'success': function (data) {
        
        $.each(data['stats'],function(name,val){

          if (!$(val['contents']).length && !stats.st[name] .$obj().children().length)
            stats.st[name] .$obj() .prepend( '<tr class="noBetsMessage"><td colspan="8">We are sorry, but there are currently no bets to show.</td></tr>' );
          else {
            
            $($(val['contents']).get().reverse()).each(function(){
              
              $(this).hide();
              stats.st[name] .$obj() .prepend( $(this) );
              
              $(this).parent().children('.noBetsMessage').remove();
              
                          
              if (!lock.locked && !ajaxBetLock) {
                $(this).attr('data-hidden',0);
              }              
              if ($(this).attr('data-hidden') == 0) {                
                if ($(this).parent().hasClass('reversed')) $(this).parent().removeClass('reversed');
                else $(this).parent().addClass('reversed');
                $(this).slideDown(100,function(){stats.limit();});
              }                                
              else {
                $(this).after($('<tr class="removablePlaceholder" style="display: none;"></tr>'));               
              }
              

            });
            
              
          }
            
           
        });
        stats.updating = false;
      }
    });
  
  },
  
  updating : false,
  
  limit : function() {
    stats.st['my_bets'] .$obj() .children() .slice(20).remove();
    stats.st['all_bets'] .$obj() .children() .slice(20).remove();
    stats.st['high'] .$obj() .children() .slice(20).remove();
  }  
  
};

function saveAlias() {
  $.ajax({
    'url': './content/ajax/saveAlias.php?_unique='+unique()+'&alias='+$('#input-alias').val(),
    'dataType': "json",
    'success': function(data) {
      alert(data['content']);
      if (data['repaired']!=null) $('#input-alias').val(data['repaired']);      
    }
  });
}
function enablePass() {
  var pass = CryptoJS.SHA256($('#input-pass').val());
  $.ajax({
    'url': './content/ajax/enablePassword.php?_unique='+unique()+'&pass='+pass,
    'dataType': "json",
    'success': function(data) {
      alert(data['content']);
      if (data['color']=='green') location.reload();
    }
  });
}
function disablePass() {
  var pass = CryptoJS.SHA256($('#input-pass').val());
  $.ajax({
    'url': './content/ajax/disablePassword.php?_unique='+unique()+'&pass='+pass,
    'dataType': "json",
    'success': function(data) {
      alert(data['content']);
      if (data['color']=='green') {
        $('.pass-en_dis').html('Disabled');
        $('.savePass').attr('onclick',"javascript:enablePass();return false;");
        $('.savePass').html('Enable');
        $('#input-pass').val('');
      }
    }
  });
}
function claim_bonus() {
  var sol = $('#input-captcha').val();
  $('#input-captcha').val('');
  $.ajax({
    'url': './content/ajax/getBonus.php?_unique='+unique()+'&sol='+sol,
    'dataType': "json",
    'success': function(data) {
      if (data['error']=='yes') {
        var m_alert = "";
        if (data['content']=='balance') m_alert='Your balance must be 0 to proceed.';
        else if (data['content']=='captcha') m_alert='Incorrect captcha solution!';
        else if (data['content']=='time') m_alert='You must wait '+giveaway_freq()+' seconds.';
        else if (data['content']=='no_funds') m_alert='We have currently no funds to giveaway.';
        $('#modals-faucet .m_alert').fadeIn().html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + m_alert + '</div>');
      }
      else {
        balanceUpdate();
      }
      var $img = $('.captchadiv img').eq(0);
      
      $('.captchadiv').empty().append($img);
      
    }
  });
}
function invest() {
  alert($('.leftblock #lc-invest.leftCon #input-invest').val());
  var amount = $('.leftblock #input-invest').val();
  $.ajax({
    'url': './content/ajax/inv_invest.php?_unique='+unique()+'&amount='+amount,
    'dataType': "json",
    'success': function (data) {
      if (data['error']=='yes') alert('Invalid amount!');
      if (data['error']=='min') alert('Minimum amount is '+min_inv()+' '+cursig());
      if (data['error']=='no') investUpdate();
    }
  });
}
function divest() {
  var amount = $('.leftblock #input-divest').val();
  $.ajax({
    'url': './content/ajax/inv_divest.php?_unique='+unique()+'&amount='+amount,
    'dataType': "json",
    'success': function (data) {
      if (data['error']=='yes') alert('Invalid amount!');
      if (data['error']=='no') investUpdate();        
    }
  });    
}
function investUpdate() {
  if (ajaxBetLock) return;


  $.ajax({
    'url': './content/ajax/inv_getData.php?_unique='+unique(),
    'dataType': "json",
    'success': function(data) {
      
      $('.invData_caninvest').html(data['canInv']);
      $('.invData_invested').html(data['invested']);
      $('.invData_share').html(data['share']);
      
    }
  });

}


function statsUpdate() {
  $.ajax({
  
    'url': './content/ajax/getStats.php?_unique='+unique(),
    'dataType': "json",
    'success': function (data) {
    
      $('.statsData_y_spins').html(data['player']['spins']);
      $('.statsData_g_spins').html(data['global']['spins']);
      $('.statsData_y_wagered').html(data['player']['wagered']);
      $('.statsData_g_wagered').html(data['global']['wagered']);
    
    }
  
  });
}

var bot = {
  
  on : false,
  
  toggle : function() {
    
    if (bot.on) {
      bot.on = false;      
      $('.autoBotCheck').removeClass('bot_on');
      
    }
    else {
      bot.on = true;      
      $('.autoBotCheck').addClass('bot_on');
      
    }
    
  }
  
};

function rules() {
  $('#modals-rules').modal('show');
}

function login() {
  $.ajax({
    'url': './content/ajax/login.php',
    'dataType': "json",
    'method': 'POST',
    'data': {username: $('#modals-login #username').val(), passwd: $('#modals-login #passwd').val(), totp: $('#modals-login #totp').val()},
    'success': function(data) {
      if(data['error'] == 'no') {
        if(data['2f_1']){
          $('#2facode').fadeIn();
        }
        else window.location = "./";
      }
      else {
        if (data['message'] == 'Wrong verification key') $('#totp').val('');
        m_alert('danger',data['message']);
      }
    }
  });
}

$('#modals-login').on('show.bs.modal',function(){
  $('#2facode').hide();
});

function register() {
  $.ajax({
    'url': './content/ajax/register.php',
    'dataType': "json",
    'method': 'POST',
    'data': {username: $('#modals-sign #username').val(), passwd: $('#modals-sign #passwd').val(), re_passwd: $('#modals-sign #re_passwd').val()},
    'success': function(data) {
        if(data['error'] == 'no') {
          m_alert('success', 'You\'ve been signed up successfully');
          $('#modals-sign #username').val('');
          $('#modals-sign #passwd').val('');
          $('#modals-sign #re_passwd').val('');
        }
        else m_alert('danger',data['message']);
    }
  });
}

function logout(){
  $.ajax({
    'url': './content/ajax/login.php?logout',
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no') window.location = "./";
    }
  });
}

function pair(token, user_id) {
  $.ajax({
    'url': "./content/ajax/pair.php?newtoken="+token+"&totp="+$("#totp").val()+"&id="+user_id,
    'dataType': "json",
    'success': function(data) {
      if (data['success']=='no') alert("One-time GA code check wasn't successful. Please, try again.");
      else window.location='./account.php';
    }
  });
}

function m_alert(status, message){
  $('.modal.in .m_alert').html('<div class="alert alert-dismissable alert-'+status+'"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+message+'</div>').fadeIn();
}

function fairUpdate(data) {
  $('._fair_server_seed').val(data['newSeed']);
  $('._fair_client_seed').val(data['newCSeed']);
  $('._fair_l_server_seed').val(data['lastSeed_sha256']);
  $('._fair_l_server_seed_p').val(data['lastSeed']);
  $('._fair_l_client_seed').val(data['lastCSeed']);
  $('._fair_l_result').val(data['lastResult']);
}

function deposit(currency){
  var amount = $('#d_'+currency+'_amount').val();
  $.ajax({
    'url': './content/ajax/makeDeposit.php?_unique='+unique()+'&c='+currency+'&amount='+amount,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'yes') m_alert('danger', data['message']);
      else m_alert('info','Your deposit ID: <b>'+ data['confirmed'] + '</b>');
      $('#d_'+currency+'_amount').val('');
    }
  });
}

function won_last() {
  $.ajax({
    'url': "./content/ajax/won_last.php",
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
       $('#won_last').html('Won last 24h: '+data['won_last']+' Coins');
       $('#biggest').html('Biggest win: '+data['biggest']+' Coins');
      }
    }
  });
}