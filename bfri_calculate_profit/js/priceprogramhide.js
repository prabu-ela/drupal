(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.priceprogramhide = {
    attach: function (context, settings) {
      var hide_show = drupalSettings.bfri_calculate_profit.flag;
      if (hide_show == '1') {
        $('.block-views-blockprize-program-add-on-block-1').hide();
      }
      else {
        $('.block-views-blockprize-program-add-on-block-1').show();
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
