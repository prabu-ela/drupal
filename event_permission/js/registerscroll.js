(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.scroll = {
      attach: function (context, settings) {
        // scroll top to pages while success and error show Start
        if($('body').hasClass()){
          return false;
        }else{
        $("input:text:visible:first").focus();
        }
      
      // scroll top to pages while success and error show End
      }
    };
  })(jQuery, Drupal, drupalSettings);