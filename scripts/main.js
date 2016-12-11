var ajaxBetLock = false;
var loginModal;

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

  loginModal = $('#modals-login .modal-body').html();

  var selected = false;
  $('.navbar-first .navbar-nav > li > a').each(function (){
    if($(this).attr('href')=='/authentication' && location.href.indexOf('/account') != -1){
      $(this).addClass('active');
      selected = true;
    }
    else if(location.href.indexOf($(this).attr('href')) != -1){
      $(this).addClass('active');
      selected = true;
    }
  });
  if(!selected) $('.navbar-first .navbar-nav > li > a[href^="/blackjack"]').addClass('active');

  $('.leftbuttons button').each(function(){
    $(this).tooltip();
  });
  $('.tooltips').each(function(){
    $(this).tooltip();
  });

  $('.chat-input').keypress(function(e){
    if (e.which == 13) chatSend($(this).val());
  });

  $('.chat-send').click(function(){
    chatSend($(this).parent('div').parent('div').find('input').val());
  });
  $('#saveClientSeed').click(function () {
    var seed = $(this).parent('span').parent('div').find('input').val();
    $.ajax({
      'url': './content/ajax/saveClientSeed.php?seed='+seed,
      'dataType': "json",
      'success': function (data) {
        if(data['error']=='yes') alert(data['message']);
      }
    });
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
  //imitateCRON();
  setInterval(function(){
    //imitateCRON();
    balanceUpdate();
    won_last();
    get_transactions();
    get_friends();
    get_pms();
    online_count();
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
  $('.st-switches a').eq(2).click();



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
      'url': './content/ajax/saveClientSeed.php?_unique_unique='+unique()+'&seed='+input.val(),
      'dataType': 'json',
      'success': function(data) {
        input.val(data['repaired']);
        alert(data['content']);
      }
    });

  });

  $('#modals-deposit').on('show.bs.modal',function(){
    setTimeout(
        function() {
          _genNewAddress();
        }, 150);
  });
  $('#faucet_btn').click(function(){
    $('#modals-faucet').modal();
  });

  $('.modal').on('show.bs.modal',function(){
    $('.m_alert').hide();
  });

  $('#modals-login').on('show.bs.modal',function(){
    $('#modals-login .modal-body').html(loginModal);
  });

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('.m_alert').hide();
  });

  $('.captchadiv').click();
});

function withdraw(currency) {
  var amount = $('#w_amount_'+currency).val().trim();
  $.ajax({
    'url': './content/ajax/withdraw.php?valid_addr='+$('#w_btc_address').val()+'&amount='+amount+'&c='+currency,
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
          m_alert('info', '<h4>Trade instructions</h4><p>'+data['instructions']+'</p>Your withdraw ID: <b>' + data['id'] + '</b>');
          balanceUpdate();
        }
      }
      $('#w_'+currency+'_amount').val()
    }
  });
}

function _genNewAddress() {
  $.ajax({
    'url': './content/ajax/makeDeposit.php?c=btc',
    'dataType': "json",
    'success': function (data) {
      if(data['error'] == 'yes') m_alert('danger','<b>Error!</b> '+data['message']);
      else {
        $('.addr-p').html(data['confirmed']);
        $('.addr-qr').empty();
        $('.addr-qr').qrcode(data['confirmed']);
      }
    }
  });
}
function clickPending() {
  if ($('.pendingbutton').attr('cj-opened')=='yes') hidePending();
  else showPending();
}                    
function showPending() {
  $.ajax({
    'url': './content/ajax/getPending.php',
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
    'url': './content/ajax/getBalance.php',
    'dataType': "json",
    'success': function (data) {

      for (var selector in data){
        if (data.hasOwnProperty(selector)) {
          $('.'+selector).text(data[selector]);
        }
      }
    }
  });
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
    $(this).children('.content').css('height', parseInt($(window).height()) - 130 - 68         - footer );
  });
  
}

var chatReceiveUpdates = false;
var chatUpdating = false;


function chatSend(val) {
  var dataToSend = encodeURIComponent(val);
  $.ajax({
    'url': './content/ajax/chatSend.php?data='+dataToSend,
    'dataType': "json",
    'success': function(data) {
      if(data['error']=='yes') alert(data['message']);    
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
    'url': './content/ajax/chatUpdate.php?lastId='+lastID,
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
      $.cookie('chat', '', { expires: 1, path: '/' });

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
          $('.leftbuttons').css('left', self.$obj().outerWidth() + $('.lefbuttons').width() -2);
          if($( document ).width() > 1200)$('.page').css('padding-left', self.$obj().outerWidth() + $('.lefbuttons').width()-2);
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
          $('.leftbuttons').css('left',self.$obj().outerWidth() + $('.lefbuttons').width());
          if($( document ).width() > 1200)$('.page').css('padding-left', self.$obj().outerWidth() + $('.lefbuttons').width());
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
    $.cookie('chat', 'chat', { expires: 7, path: '/' });
    $('.chat-icon').attr('onclick', "leftCon('chat')");
  }
  if(con == 'chat-rooms'){
    $.cookie('chat', 'chat-rooms', { expires: 7, path: '/' });
    $('.chat-icon').attr('onclick', "leftCon('chat-rooms')");
    $('.chat-users-toggle').removeClass('active');
    if($('.chat-rooms-toggle').hasClass('active')) $('.chat-rooms-toggle').addClass('active');
    else $('.chat-rooms-toggle').removeClass('active');
  }
  if(con == 'chat-users'){
    $.cookie('chat', 'chat-users', { expires: 7, path: '/' });
    $('.chat-icon').attr('onclick', "leftCon('chat-users')");
    $('.chat-rooms-toggle').removeClass('active');
    if($('.chat-users-toggle').hasClass('active')) $('.chat-users-toggle').addClass('active');
    else $('.chat-users-toggle').removeClass('active');
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
      
      'url': './content/ajax/stats_load.php?last=' + last1 + ',' + last2 + ',' + last3 + '&game='+get_active_game(),
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
function claim_bonus() {
  var sol = $('#input-captcha').val();
  $('#input-captcha').val('');
  $.ajax({
    'url': './content/ajax/getBonus.php?sol='+sol,
    'dataType': "json",
    'success': function(data) {
      if (data['error']=='yes') {
        if (data['content']=='balance') m_alert('danger','Your balance must be 0 to proceed.');
        else if (data['content']=='captcha') m_alert('danger','Incorrect captcha solution!');
        else if (data['content']=='time') m_alert('danger','You must wait '+giveaway_freq()+' seconds.');
        else if (data['content']=='no_funds') m_alert('danger','We have currently no funds to giveaway.');
        else m_alert('danger',data['message']);
      }
      else {
        m_alert('success', 'You obtained a bonus');
        balanceUpdate();
      }
      var $img = $('.captchadiv img').eq(0);
      
      $('.captchadiv').empty().append($img);
      
    }
  });
}
function invest() {
  var amount = $('.leftblock #input-invest').val();
  $.ajax({
    'url': './content/ajax/inv_invest.php?amount='+amount,
    'dataType': "json",
    'success': function (data) {
      if(data['error'] == 'yes') p_alert('danger', data['message']);
      else{
        p_alert('success', 'You have invested '+amount+' Coins');
        $('.leftblock #input-invest').val(0);
        investUpdate();
      }

    }
  });
}
function divest() {
  var amount = $('.leftblock #input-divest').val();
  $.ajax({
    'url': './content/ajax/inv_divest.php?amount='+amount,
    'dataType': "json",
    'success': function (data) {
      if (data['error']=='yes') p_alert('danger', data['message']);
      if (data['error']=='no'){
        p_alert('success', 'You have divested '+amount+' Coins');
        $('.leftblock #input-divest').val(0);
        investUpdate();
      }
    }
  });    
}
function investUpdate() {
  if (ajaxBetLock) return;


  $.ajax({
    'url': './content/ajax/inv_getData.php',
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
  
    'url': './content/ajax/getStats.php',
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
    'data': {username: $('#modals-sign #username').val(), passwd: $('#modals-sign #passwd').val(), re_passwd: $('#modals-sign #re_passwd').val(), email: $('#modals-sign #email').val()},
    'success': function(data) {
        if(data['error'] == 'no') {
          m_alert('success', 'Verification link was sent to your email address (please check your spam folder)');
          $('#modals-sign #username').val('');
          $('#modals-sign #email').val('');
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
  var amount = $('#d_amount_'+currency).val().trim();
  $.ajax({
    'url': './content/ajax/makeDeposit.phpc='+currency+'&amount='+amount,
    'dataType': "json",
    'success': function(data) {
        if (data['error']=='yes') {
          m_alert('danger','<b>Error!</b> '+data['message']);
        }
        else {
          if(currency == 'btc') {
            m_alert('success', 'Your deposit ID: <b>' + data['confirmed'] + '</b>');
          }
          else {
            m_alert('info', '<h4>Trade instructions</h4><p>'+data['instructions']+'</p><p>Your deposit ID: <b>' + data['confirmed'] + '</b></p>');
          }
        }
      $('#d_amount_'+currency).val('');
      $('#d_amount_'+currency+'_coins').val('');
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

function get_transactions(){
  $.ajax({
    'url': "./content/ajax/get_transactions.php",
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('#deposits tbody').html(data['deposits']);
        $('#withdrawals tbody').html(data['withdrawals']);
      }
    }
  });
}

function select_room(id, pm){
  $.ajax({
    'url': "./content/ajax/select_room.php?id="+id+'&pm='+pm,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('.current_room').html(data['name']);
        leftCon('chat');
      }
    }
  });
}

function get_friends(){
  $.ajax({
    'url': "./content/ajax/get_friends.php",
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('.friend_count').html(data['friend_count']);
        $('.online_count').html(data['online_count']);
        $('.offline_count').html(data['offline_count']);
        $('.ignored_count').html(data['ignored_count']);
        $('.requests_count').html(data['requests_count']);
        $('.online_friends').html(data['online_friends']);
        $('.offline_friends').html(data['offline_friends']);
        $('.ignored_friends').html(data['ignored_friends']);
        $('.friend_requests').html(data['friend_requests']);
      }
    }
  });
}

function get_pms(){
  $.ajax({
    'url': "./content/ajax/getPms.php",
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('#pms').html(data['pms']);
      }
    }
  });
}

function online_count(){
  $.ajax({
    'url': "./content/ajax/getOnline.php",
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('.online-users').html(data['online_count']);
      }
    }
  });
}

function make_friend(){
  var friend = prompt('Username: ');
  $.ajax({
    'url': "./content/ajax/makeFriend.php?friend="+friend,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        $('.offline_friends').append(data['offline_friends']);
      }
    }
  });
}

function approve_friend(friend){
  $.ajax({
    'url': "./content/ajax/approveFriend.php?friend="+friend,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){

      }
    }
  });
}

function ignore_friend(friend){
  $.ajax({
    'url': "./content/ajax/ignoreFriend.php?friend="+friend,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
      }
    }
  });
}

function remove_friend(friend){
  $.ajax({
    'url': "./content/ajax/removeFriend.php?friend="+friend,
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
      }
    }
  });
}

function chat_status(elem, status){
  $('.chat_status').removeClass('active');
  $(elem).addClass('active');
  $.ajax({
    'url': "./content/ajax/chat_status.php?status="+status,
    'dataType': "json"
  });
}
function forgot_password(){
  $('#modals-login .modal-body').html('<div class="m_alert"></div><div class="form-group"><label for="email">Email:</label><input type="email" id="email" class="form-control" onkeydown="if (event.keyCode == 13) reset_password();"></div><button class="btn  btn-primary" style="height: 39px;line-height:39px; padding: 0 20px;" onclick="reset_password();">Send reset link </button>');
}
function reset_password(){
  $.ajax({
    'url': "./content/ajax/reset_password.php?email="+$('#modals-login #email').val(),
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no'){
        m_alert('success', 'The password reset link has been sent to your email.');
      }
      else if(data['error'] == 'yes') m_alert('danger', data['message']);;
    }
  });
}
function save_password(player) {
  $.ajax({
    'url': './content/ajax/save_password.php',
    'dataType': "json",
    'method': 'POST',
    'data': {id: player, passwd: $('#modals-reset #passwd').val(), re_passwd: $('#modals-reset #re_passwd').val()},
    'success': function(data) {
      if(data['error'] == 'no') {
        m_alert('success', 'Password has been saved');
        $('#modals-reset #passwd').val('');
        $('#modals-reset #re_passwd').val('');
      }
      else m_alert('danger',data['message']);
    }
  });
}
function p_alert(status, message){
  $('#p_alert').html('<div class="alert p_alert alert-'+status+' alert-dismissable"><b>'+message+'</b><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
  $(".p_alert").fadeTo(10000,500).slideUp(500,function(){$(".p_alert").slideUp(500);});
}
function save_account_settings(){
  $.ajax({
    'url': './content/ajax/save_account_settings.php',
    'dataType': "json",
    'method': 'POST',
    'data': {username: $('#account_settings #username').val(),email: $('#account_settings #email').val(),  passwd: $('#account_settings #passwd').val(), re_passwd: $('#account_settings #re_passwd').val(), currency_preference: $('#account_settings #currency_preference').val()},
    'success': function(data) {
      if(data['error'] == 'no') {
        $('#account_settings #passwd').val('');
        $('#account_settings #re_passwd').val('');
        p_alert('success', 'Settings has been saved');
      }
      else p_alert('danger',data['message']);
    }
  });
}

function resend_activation(){
  $.ajax({
    'url': './content/ajax/resend_activation.php',
    'dataType': "json",
    'success': function(data) {
      if(data['error'] == 'no') {
        p_alert('success', 'Verification link was send to your email (please check your spam folder)');
      }
      else p_alert('danger',data['message']);
    }
  });
}