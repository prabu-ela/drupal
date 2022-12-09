(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.sellectAll = {
    attach: function (context, settings) {
      
      let allEleIds = {
        "event_title": "edit-event-title",
        "stock_location": "edit-stock-location",
        "vendor_title": "edit-vendor-title",
        "transaction_type": "edit-transaction-type"
      };

      $.each(allEleIds, function(name, id) {
        let eleId = '#' + id;
        if ($(eleId).length > 0) {

          $(eleId).attr('name', name + '[]');
          $(eleId).select2({
            multiple: true,
            tags: true,
          });
    
          var selected = [];
          i = 0;
          $(eleId + ' option[selected=selected]').once().each(function(){
            selected[i]= $(this).val();
            i++;
          });
    
          if (selected != '') {
          // Appending the seeached elements.
            if (selected != '') {
              $(eleId).val(selected);
              $(eleId).trigger('change');
            }
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);