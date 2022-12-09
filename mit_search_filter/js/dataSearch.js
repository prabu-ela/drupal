(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dataSearch = {
    attach: function (context, settings) {

      // Changing the submit attr. to button.
      $('#edit-submit-event-listing').removeAttr("type").attr("type", "button");
      $('.form-item-date-less').hide();
      $('#edit-data-rang-filter-wrapper').hide();
      $('.date-min-max').hide();
      $('.hide_show').hide();

      // Dropdown filter.
      $('#edit-filter').on('change', function() {
        var data = this.value;
        if (data == 'range') {
          dateSearchFilter(data);
        }
        else {
          $("#edit-data-rang-filter-min").val('');
          $("#edit-data-rang-filter-max").val('');
        }
      });

      // Redirecting result to another view.
      $('#edit-submit-event-listing').on('click', function() {

        var title = $('#edit-title').val();
        var dates = $('#edit-date-text').val();
        var filter = $('#edit-filter').val();
        var dateValue = dates.split(" - ");
        if (filter == 'all') {
          dateValue[0] = '';
          dateValue[1] = '';
        }
        $(location).attr("href","/event?title="+title+"&data-rang-filter[min]="+dateValue[0]+"&data-rang-filter[max]="+dateValue[1]+"&filter="+filter);
      });

      // Setting default filters.
      var curent_select = $('#edit-filter').val();
      if (curent_select == 'all') {
        $('.date-min-max').hide();
        $("#edit-data-rang-filter-min").val('');
        $("#edit-data-rang-filter-max").val('');
      }
      else {
        dateSearchFilter(curent_select);
      }

      // X mark for hide and show the drop down and date range.
      $('.hide_show').on('click', function() {
        $('.hide_show').hide();
        $('#edit-filter').show();
        $('#edit-date-text').hide();
        $('#edit-filter').val("all");
        $("#edit-data-rang-filter-min").val('');
        $("#edit-data-rang-filter-mix").val('');
      });

      // Avoiding string in daterang picker.
      $('#edit-date-text').on('keyup keydown', function() {
        return false;
      });
    }
  };
})(jQuery, Drupal, drupalSettings);

/*
 *  Function for date sort.
 */
function dateSearchFilter(param) {
  if (param == 'all') {
    return false;
  }

  var $ = jQuery;
  $('.date-min-max').show();
  $('.date-min-max').daterangepicker({
    minDate: moment()
  });
  $('#edit-date, .hide_show').show();
  $('#edit-filter').hide();
  $('.hide_show').on('click', function (){
    $('#edit-filter').val("all");
  });

  // Passing min and max date to the date filed.
  $("#edit-submit-event-listing").on('click', function () {
      if ($("#edit-filter").val() == 'range') {
        $('#edit-date, .hide_show, .date-min-max').show();
        var selectedDate = "";
        selectedDate = $('.date-min-max').val();
        var dateValue = selectedDate.split(" - ");
        $("#edit-data-rang-filter-min").val(dateValue[0]);
        $("#edit-data-rang-filter-max").val(dateValue[1]);
      }
  });
}