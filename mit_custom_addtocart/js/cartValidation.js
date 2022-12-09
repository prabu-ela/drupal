(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.cartValidation = {
    attach: function (context, settings) {
      var value = drupalSettings.mit_custom_addtocart.result;
      $(':input[type="number"]').once().on('change keyup', function (event) {
        var tot = 0;
        $('.custom-add-to-cart-form :input[type="number"]').each(function(){
          var qty = $(this).val();
          var price = $(this).data('price');
          tot += (qty * price);
        });
        $('.tol').text("$ "+tot);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
