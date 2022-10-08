(function($, Drupal, drupalSettings){
  Drupal.behaviors.calculateprofit = {
    attach: function(context, settings) {
      var values = drupalSettings.bfri_calculate_profit.calcualte;
      var type = drupalSettings.bfri_calculate_profit.type;
      var product = drupalSettings.bfri_calculate_profit.product;
      var profitPercentage = drupalSettings.bfri_calculate_profit.profit;
      var minArray = drupalSettings.bfri_calculate_profit.min;
      var minValue = Math.min.apply(Math,minArray);
      var showHide = drupalSettings.bfri_calculate_profit.show_hide;
      var brochuresProfit = drupalSettings.bfri_calculate_profit.brochure_profit;
      var brochuresAvgPrice = drupalSettings.bfri_calculate_profit.avg_price;
      

      $('#edit-quantity-0-value').val(minValue);
      $('#edit-quantity-0-value').attr('min', minValue);

      // Quanity and addtocart show or hide.
      if (showHide == 0) {
        $('.add__cart').hide();
        $('.views-field-nothing-1').show();
        $('.go-purchase-order').hide();
      }
      else {
        $('.add__cart').show();
        $('.views-field-nothing-1').hide();
        $('.go-purchase-order').show();
      }

      $("#edit-participant-goal,#edit-group-size").once('calculateprofit').on('change', function() {
        var gropSize = parseInt($('#edit-group-size').val());
        var participant = parseInt($('#edit-participant-goal').val());

         // Chekcing NaN.
         if (isNaN(gropSize)) {
          gropSize = '';
        }

        if (isNaN(participant)) {
          participant = '';
        }

        // Brouchers Content type.
        if (type == 'brochures' || type == 'sales_incentive' || type == 'big_events' || type == 'mini_resource') {

          if (brochuresProfit != undefined) {
            var casesProfit = gropSize * participant;

            $.each(brochuresProfit, function (i) {
              $.each(brochuresProfit[i], function(key, val) {
                if (brochuresProfit[i].min <= casesProfit && brochuresProfit[i].max >= casesProfit && brochuresProfit[i].max != 0) {
                  profit = brochuresProfit[i].profit;
                }

                if (brochuresProfit[i].min <= casesProfit && brochuresProfit[i].max == 0 || brochuresProfit[i].max == '') {
                  profit = brochuresProfit[i].profit;
                }
              });
            });
            var result = (gropSize * participant) * (profit/100) * brochuresAvgPrice;
          }
          else {
            // Calculation.
            var result = gropSize * participant * values;
            if (isNaN(result)) {
              result = '';
            }
          }

          if (isNaN(result)) {
            result = '';
          }
          else {
            $('#edit-profit').val('$'+parseFloat(result,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
          }
        }

        // Product Content type.
        if (type == 'product_display') {
          var packaging = product.packaging;
          var profit = '';
          var sellingPrince = product.selling_prince;

          // Calculation.
          var caseNeeded = (gropSize * participant) / packaging;

          if (isNaN(caseNeeded)) {
            caseNeeded = '';
          }
          if (caseNeeded >= minValue ) {
            $('#edit-casses-needed').val(Math.round(caseNeeded));

            // Looing for profit calculations.
            if (profitPercentage != '') {
              var flag = 0;
              var profit = '';

              cases = Math.round(caseNeeded);

              $.each(profitPercentage, function (i) {
                $.each(profitPercentage[i], function(key, val) {
                  if (profitPercentage[i].min <= cases && profitPercentage[i].max >= cases && profitPercentage[i].max != 0) {
                    profit = profitPercentage[i].profit;
                  }

                  if (profitPercentage[i].min <= cases && profitPercentage[i].max == 0 || profitPercentage[i].max == '') {
                    profit = profitPercentage[i].profit;
                  }
                });
              });
            }
          }
          else {
            if (participant != '' && gropSize != ''){
              alert('You need to increase quantity');
            }
            $('#edit-casses-needed').val('');
          }

          if (profit == '' && cases != '') {
            // alert('You need to increase quantity');
            $('#edit-casses-needed').val('');
            $('#edit-profit').val('');
          }
          else {
            result = gropSize * participant * (profit/100) * sellingPrince;
            $('#edit-profit').val('$'+ parseFloat(result,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
