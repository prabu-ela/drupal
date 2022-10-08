(function ($, Drupal, drupalSettings){
  Drupal.behaviors.pop_up_top  = {
    attach: function (context, settings) {
      $('.use-ajax',context).click(function() {
        setTimeout(function() {
          $(".ui-widget-content").scrollTop(0);
        },
      2000);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
