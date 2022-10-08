(function ($, Drupal, drupalSettings) {
  "use strict";
  Drupal.behaviors.purchase_order = {
    attach: function(context, settings) {
     var base_path = drupalSettings.path.baseUrl;

     // Avoiding right click.
     $(document).bind("contextmenu",function(e){
      return false;
     }) 
     
      $('#edit-1st-product, #edit-2nd-product, #edit-3rd-product', context).on('change', function() {
        var product = $(this).val();
        var product_id = $(this).attr('id');

        // Ajax call for getting product details.
        $.ajax ({
          url: base_path + 'purchase_order_calculation/' + product,
          dataType: "json",
          type: 'POST',
          success: function (response) {
            $.each(response, function(index, value) {

              // Webform 1st Select box.
              if (product_id == 'edit-1st-product') {
                $('#edit-1st-product').closest('div').find('.prodcut-qty').attr('min', value.min);
                $("#edit-quantity-for-1st-product--description").text("Please enter at least " + value.min);

                // Setting empty once product drop-down changed.
                $('#edit-quantity-for-1st-product').val('');
                $('#edit-1st-product-total').val(0);
                finalTotal();

                $('#edit-quantity-for-1st-product', context).on('keyup change', function() {

                  var qty = parseInt($('#edit-quantity-for-1st-product').val());
                  if(isNaN(qty)){ qty=0; }
                  var price = priceCalculation(qty, value)
                  var tot = qty * price;
                  $('#edit-1st-product-total').val(parseFloat(tot,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
                  finalTotal();
                });
              }

              // Webform 2nd Select box.
              if (product_id == 'edit-2nd-product') {
                $('#edit-2nd-product').closest('div').find('.prodcut-qty').attr('min', value.min);
                $("#edit-quantity-for-2nd-product--description").text("Please enter at least " + value.min);

                // Setting empty once product drop-down changed.
                $('#edit-quantity-for-2nd-product').val('');
                $('#edit-2nd-product-total').val(0);
                finalTotal();

                $('#edit-quantity-for-2nd-product', context).on('keyup change', function() {
                  var qty = parseInt($('#edit-quantity-for-2nd-product').val());                  
                  if(isNaN(qty)){ qty=0; }
                  var price = priceCalculation(qty, value)
                  var tot = qty * price;
                  $('#edit-2nd-product-total').val(parseFloat(tot,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
                  finalTotal();
                });
              }

                // Webform 3rd Select box.
              if (product_id == 'edit-3rd-product') {
                $('#edit-3rd-product').closest('div').find('.prodcut-qty').attr('min', value.min);
                $("#edit-quantity-for-3rd-product--description").text("Please enter at least " + value.min);

                // Setting empty once product drop-down changed.
                $('#edit-quantity-for-3rd-product').val('');
                $('#edit-3rd-product-total').val(0);
                finalTotal();

                $('#edit-quantity-for-3rd-product', context).on('keyup change', function() {
                  var qty = parseInt($('#edit-quantity-for-3rd-product').val());
                  if(isNaN(qty)){ qty=0; }
                  var price = priceCalculation(qty, value)
                  var tot = qty * price;
                  $('#edit-3rd-product-total').val(parseFloat(tot,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
                  finalTotal();
                });
              }
            });
          }
        });
      });

      
    }
  };
})(jQuery, Drupal, drupalSettings)

// Find the price of the product.
function priceCalculation(qty, value) {
  var price = 0;
  jQuery.each(value, function(ind, val) {
    
    if (val.max_qty != 'undefined' || val.max_qty != 'undefined') {     
      
      if (val.min_qty <=  qty && val.max_qty >= qty) {
        price = val.amount;
        return false; 
      }

      if (val.max_qty < 1) {
        price = val.amount;
        return false;
      }
    }
  });
  return price;
}

// Calculating the final total.
function finalTotal() {
  var subTotal = tot1 = tot2 = tot3 = 0;
  var tot1 =  parseFloat(jQuery("#edit-1st-product-total").val().replace(",", ""));
  if(isNaN(tot1)){ tot1=0; }
  var tot2 =  parseFloat(jQuery("#edit-2nd-product-total").val().replace(",", ""));
  if(isNaN(tot2)){ tot2=0; }
  var tot3 =  parseFloat(jQuery("#edit-3rd-product-total").val().replace(",", ""));
  if(isNaN(tot3)){ tot3=0; }
  subTotal = tot1 + tot2 + tot3;
  jQuery("#edit-total-amount").val(parseFloat(subTotal,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
  return true;
}
