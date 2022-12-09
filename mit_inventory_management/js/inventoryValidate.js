(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.inventoryValidate = {
    attach: function (context, settings) {

      // Getting base path from form.
      var data = drupalSettings.mit_inventory_management.data;
      $(".trg-error").hide();

      // Move Stock on change of transaction type.
       $('select[name="transaction_type"',context).once('#edit-transaction-type').on('change', function() {
        $(".trg-error").hide();

        $("#edit-source-location option").attr("disabled","disabled");

        var transact_type = $('#edit-transaction-type').val();
        
        if (transact_type != 'receiveStock') {

          // Restricting submit on null qty.
          if (data == null) {
            var errormsg = $(".invtory-msg" ).length;

            // Displaying error messages.
            if (errormsg == 0) {
              $("#edit-source").append("<div class=invtory-msg style=color:red;> There is no inventory found for the Variation</div>")
              $('#edit-submit').hide();
            }
          }
          else {
            $(".invtory-msg").remove();
            $('#edit-submit').show();
          }

          // Looing through all the option of locations.
          $('#edit-source-location').children('option').each(function() {
            var j = 0;
            $.each(data, function(i, values) {

              // Making first enabled item to be selected.
              if (j == 0){
                $("#edit-source-location option[value='"+i+"']").attr('selected', true);
                $('#edit-transaction-qty').prop('max',data[i].inventory);
              }
              var qtylocation = $("#edit-source-location option[value='"+i+"'] .qty").length;
              if (qtylocation == 0) {
                $("#edit-source-location option[value='"+i+"']").append("  <span class=qty>("+data[i].inventory+")<span>");
              }
              $("#edit-source-location option[value='"+i+"']").removeAttr("disabled");
            });
          });

          // Setting Max value for the location.
          $('#edit-source-location').on('change', function() {
            var locaiton_max_id = $(this).val();
            $('#edit-transaction-qty').prop('max',data[locaiton_max_id].inventory);
          });

          // Hiding Error message on target location change.
          $('#edit-target-location').on('change', function() {
            $(".trg-error").hide();
          });

          // Preventing submit if have the same location.
          $("input", context).once('#edit-submit').on('click', function( event ) {

            if ($('#edit-source-location').val() == $('#edit-target-location').val()) {
              event.preventDefault();

              $(".trg-error").show();
              if ($('.trg-error').length == 0) {
                $(".form-item--target-location").append("  <span class=trg-error style=color:red; >Your are not allowed to move the inventory for the same location<span>");
              }              
              // alert ('Your are not allowed to move the inventory for the same location');
              return false;
            }
            else {
            event.stopPropagation();
            $(".trg-error").hide();
            return true;
            }
          });
        }
        else {
          $(".invtory-msg" ).remove();
          $(".qty").remove();
          $('#edit-submit').show();
          $("#edit-source-location option").removeAttr("disabled");
        }
      });

      // Qty validation not allow dot(.).
      $('#edit-transaction-qty').attr('step' , 1);
      $('#edit-transaction-qty').attr('min', 1);
      $('#edit-unit-price').attr('min', 0);
      $('#edit-additional-fee').attr('min', 0);      
    }
  };
})(jQuery, Drupal, drupalSettings);