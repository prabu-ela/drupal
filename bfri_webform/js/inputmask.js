(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.showhide = {
      attach: function (context, settings) {
        $('.phone_mask').each(function(){
             $(this).inputmask("999-999-9999");
          });
      }
    };
  })(jQuery, Drupal, drupalSettings);
  