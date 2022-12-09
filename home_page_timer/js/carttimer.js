(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.carttimer = {
    attach: function (context, settings) {
      $('.custom-add-to-cart-form .form-submit').once().on('click', function (event) {
        var tol = $(".tol").text();
        if (tol == '$0.00' || tol == '$00.00') {
          if ($('.error-msg').length == 0) {
            $("<span class = error-msg style=color:red> Please add atleast 1 Quanity.</span>").insertBefore('.tolltxt');
          }
          return false;
        }
        $.ajax({
          
          url: '/addtocart/settimer',
          type:"GET",
          success: function(response) {
            sessionTimer("20:00");          
            event.stopPropagation();
            $('.custom-add-to-cart-form').submit();
          }
        });
      });

      // Calling counter one time.
      $(context).find(".countdown").once("carttimer").each(function () {
        var data = drupalSettings.data;

        if (data != "00:00" && data != null) {
          sessionTimer(data);
        }
      });      
    }
  };
})(jQuery, Drupal, drupalSettings);

function sessionTimer(timer2) {
  
  var $ = jQuery;
  var interval = setInterval(function() {
    
    var timer = timer2.split(':');
    //by parsing integer, I avoid all extra string processing
    var minutes = parseInt(timer[0], 10);
    var seconds = parseInt(timer[1], 10);
    console.log(seconds);
    if (isNaN(seconds)) {
      seconds = 0;
    }
    --seconds;
    minutes = (seconds < 0) ? --minutes : minutes;
    seconds = (seconds < 0) ? 59 : seconds;
    seconds = (seconds < 10) ? '0' + seconds : seconds;
    var dispaly_time  = minutes + ':' + seconds;
    $('.countdown').html(dispaly_time);
      if (minutes < 0) {
        $('.countdown').html("00:00");
        clearInterval(interval);
      }
      else{
      $('.countdown').html(minutes + ':' + seconds);
      timer2 = minutes + ':' + seconds;
    }
  }, 1000);
}
