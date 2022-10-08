(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.selectform = {
    attach: function (context, settings) {
      // Document on change.
      $('#edit-state-select').on('change', function() {
        $('.select-state-values option:contains("-- Choose a County --")').text('-- Choose a state --');
      });

      $(document).ready(function() {
        $('.select-state-values option:contains("-- Choose a County --")').text('-- Choose a state --');
      });
    }
  };
})(jQuery, Drupal, drupalSettings);