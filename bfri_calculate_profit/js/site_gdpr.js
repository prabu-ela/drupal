(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.site_gdpr = {
    attach: function (context, settings) {

      $(window).on('load',function() {
        if ($("#sliding-popup").hasClass("sliding-popup-bottom")) {
          var styleAttrVal = $('.sliding-popup-bottom').attr('style');
          var bottomVal =  styleAttrVal.substr(styleAttrVal.search('bottom'),20).split('px')[0].replace('bottom:','').trim();
          if (bottomVal <= 0 ) {
            $('.block__sticky').removeClass('block__sticky__top');
          }
          else {
            
            $('.block__sticky').addClass('block__sticky__top');
          }
        }
        else {
          $('.block__sticky').removeClass('block__sticky__top');
        }

        $('.agree-button, .decline-button').on('click',function() {
          $('.block__sticky').removeClass('block__sticky__top');
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
