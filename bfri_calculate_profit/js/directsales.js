(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.directsales = {
    attach: function (context, settings) {
      $(document).ready(function () {
        setTimeout(function() {
          window.scrollTo(window.scrollX, window.scrollY - 100);
        },
      2000);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
