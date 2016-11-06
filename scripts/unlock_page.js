$(document).ready(function(){
  _ready();
});
function _ready() {
  $div=$('.loginDiv');
  placeElems();
  $(window).resize(function(){
    rePlaceElems();
  });
  
  $('input').keypress(function(e){
    if (e.which == 13) $('a').click();
  });



  var cookieTheme = readCookie('theme');
  if (cookieTheme != null) {
    setTheme(cookieTheme);
  }

  var defTheme = default_theme();
  if (defTheme != '1' && cookieTheme == null) {
    setTheme(defTheme);
  }

}


function setTheme(id) {
    $('.themeLinker').attr('href','./styles/themes/'+id+'/style.css');
    $('body').css('background-image',"url('./styles/themes/"+id+"/bg.jpg')");
    
}

function placeElems() {
  rePlaceElems();
  
  var $clone = $div.clone().appendTo('body');
  var divOffset = $div.offset();
  $div.css('visibility','hidden');
  $clone
    .css('position','absolute')
    .css('margin-top',($clone.height()*-1))
  .animate({
    'top': (divOffset.top+$clone.height())
  },function(){
    $clone.remove();
    $div.css('visibility','visible');          
  });
  
}

function rePlaceElems() {
  $div.css('margin-top',margin($div.height(),$(window).height()));          
  $div.css('margin-left',margin($div.width(),$(window).width()));
}

function unlock() {
  $.ajax({
    'url': './content/ajax/unlockAccount.php?_unique='+unique()+'&pass='+CryptoJS.SHA256($('input').val()),
    'dataType': "json",
    'success': function (data) {
      if (data['error']=='yes') alert('Incorrect password.');
      else location.reload();
    }
  });
}

function margin(elem,wrap) {
  return ((wrap-elem)/2);
}
function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}