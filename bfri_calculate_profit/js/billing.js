(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.billing  = {
    attach: function (context, settings) {
      var valules = $('input[name="payment_information[payment_method]"]:checked').val();
      if (valules == 'new--credit_card--braintree_hosted_fields') {
        $(".credit-pop").show();
      }
      else {
        $(".credit-pop").hide();
      }

      // On change function call back.
      $('.form-item-payment-information-payment-method').on('change', function() {
        valules = $('input[name="payment_information[payment_method]"]:checked').val();
        if (valules == 'new--credit_card--braintree_hosted_fields') {
          $(".credit-pop").show();
        }
        else {
          $(".credit-pop").hide();
        }
      });

      //what is this link inserted before cvv field
      $('.privacy__link.what-is-this').insertAfter('.form-item-payment-information-add-payment-method-payment-details-cvv');
    }
  };
})(jQuery, Drupal, drupalSettings);
