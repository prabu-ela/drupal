(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.phoneValidation = {
    attach: function (context, settings) {
      $("#edit-field-phone-number-0-value").inputmask("(999)-999-9999");
    }
  };
})(jQuery, Drupal, drupalSettings);