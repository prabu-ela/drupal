(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.inventory = {
    attach: function (context, settings) {

      // Getting default select box value.
      var transact_type = $('#edit-transaction-type').val();
      $('.form-item-source-zone, .js-form-item-source-zone, .js-form-item-target-zone, .form-item-target-zone').hide();

      // Getting changed select box value.
      $('#edit-transaction-type').on('change', function() {
        transact_type = $(this).val();
        if (transact_type != 'receiveStock') {
          // Hiding unwanted fields.
          $('.js-form-item-unit-price, .js-form-item-additional-fee, .js-form-item-purchased-date, .js-form-item-expiry-date, #edit-payment-type--wrapper').hide();
        }
        else {
          $('.js-form-item-unit-price, .js-form-item-additional-fee, .js-form-item-purchased-date, .js-form-item-expiry-date, #edit-payment-type--wrapper').show();
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
  