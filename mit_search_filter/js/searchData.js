(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.searchData = {
    attach: function (context, settings) {

      // Changing the submit attr. to button.
      $('#edit-submit-event-listing').removeAttr("type").attr("type", "button");

      // Getting base path from form.
      var path = drupalSettings.mit_search_filter.path;

      // Redirecting result to another view.
      $('#edit-submit-event-listing').on('click', function() {

        // Getting base path from form.
        // var path = drupalSettings.mit_search_filter.path;

        var title = $('#edit-title').val();
        var dates = $('#edit-date').val();
        var filter = $('.form-select').val();
        var dateValue = dates.split(" - ");
        $(location).attr("href", path +"/event?title="+title+"&data-rang-filter[min]="+dateValue[0]+"&data-rang-filter[max]="+dateValue[1]+"&filter="+filter);
      });

      $('.hide_show').on('click', function() {
        $('#edit-date, .hide_show').hide();
        $(".form-select").show();
        $(".form-select").val("all");
      });

      var curent_select = $('.form-select').val();
      if (curent_select == 'all') {
        $('#edit-date, .hide_show').hide();
      }
      else {
        $('#edit-date, .hide_show').show();
      }

      $('.form-select').on('change', function() {
        var data = this.value;
        if (data == 'range') {
          $('#edit-date').daterangepicker({
            minDate: moment()
          });
          $('#edit-date, .hide_show').show();
          $('.form-select').hide();
        }
      });

      // Avoiding string in daterang picker.
      $('#edit-date').on('keyup keydown', function() {
        return false;
      });
      
    }
  };
})(jQuery, Drupal, drupalSettings);