(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.hide = {
    attach: function (context, settings) {

      if ($('.cart-form').length) {
        $('.printfriendly').show();
        $('#print-this-area').show();
        $('#block-views-block-brochure-catagories-block-8').show()
      }
      else {
        $('.printfriendly').hide();
        $('#print-this-area').hide();
        $('#block-views-block-brochure-catagories-block-8').hide()
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
