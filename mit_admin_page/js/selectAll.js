(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.sellectAll = {
    attach: function (context, settings) { 

      $("#edit-title").attr('name','title[]');
      $("#edit-field-categories-target-id").attr('name','field_categories_target_id[]');
      $("#edit-title").select2({
        multiple: true,
        tags: true,
      });

      $("#edit-field-categories-target-id").select2({
        multiple: true,
        tags: true,
      });
      

      var selected = [];
      i=0;
      $('#edit-title option[selected=selected]').once().each(function(){
        selected[i]= $(this).val();
        i++;
      });

      if (selected != '') {
      // Appending the seeached elements.
        if (selected != '') {
          $('#edit-title').val(selected);
          $('#edit-title').trigger('change');
        }
      }

      var cat = [];
      i=0;
      $('#edit-field-categories-target-id option[selected=selected]').once().each(function(){
        cat[i]= $(this).val();
        i++;
      });

      if (cat != '') {
      // Appending the seeached elements.
        if (cat != '') {
          $('#edit-field-categories-target-id').val(cat);
          $('#edit-field-categories-target-id').trigger('change');
        }
      }

      // Avoiding string in daterang picker.
      $('#edit-created-min').prop("readonly", true);
      selected_date = drupalSettings.date_array;

      // Date range picker.
      $('.form-item-created-max').hide();
      $('.form-item-created-min > label').hide();
      if (selected_date) {
        var start = selected_date[0];
        var end = selected_date[1];
      }
      else {
        var start = moment().subtract(3, 'months');
        var end = moment();
      }

      // Chaning date picker to range picker.
      $('.bef-datepicker').daterangepicker({
        startDate: start,
        endDate: end,
      });

      // Hiding unwanter column.
      $(".form-item-views-fields-on-off-form-field-dateless-event").hide();
      $(".form-item-views-fields-on-off-form-operations").hide();

      // Submiting form show dateless event.
      $('#edit-field-dateless-event-value').on('click', function (event) {
        $("#views-exposed-form-mitac-manage-content-page-1").submit();
      });

      // Custimized column filter.
      $('.applybtn').on('click', function (event) {
        $("#views-exposed-form-mitac-manage-content-page-1").submit();
        $("#views-exposed-form-mitac-people-management-page-1").submit();
      });

      // Pagger on change submit.
      $("#edit-items-per-page").change(function (e){

        // Event page filter.
        if ($("#views-exposed-form-mitac-manage-content-page-1").length) {
          $("#views-exposed-form-mitac-manage-content-page-1").submit();
        }

        // Customer page filter.
        if ($("#views-exposed-form-mitac-people-management-page-1").length) {
          $("#views-exposed-form-mitac-people-management-page-1").submit();
        }
      });

     
      $("#edit-field-mit-user-type-value").select2({
        multiple: true,
        tags: true,
      });

      // Customer filter.
      $("#edit-field-mit-user-type-value").attr('name','field_mit_user_type_value[]');
      var selectedValue = [];
      i=0;
      $('#edit-field-mit-user-type-value option[selected=selected]').once().each(function(){
        selectedValue[i]= $(this).val();
        console.log($(this).val());
        i++;
      });

      if (selectedValue != '') {
      // Appending the seeached elements.
        if (selectedValue != '') {
          $('#edit-field-mit-user-type-value').val(selectedValue);
          $('#edit-field-mit-user-type-value').trigger('change');
        }
      }
    }
  };
})(jQuery, Drupal, drupalSettings);