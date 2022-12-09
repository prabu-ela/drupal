(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.ticketAdd = {
		attach: function (context, settings) {

      // Current date.
      var cDate = drupalSettings.currDate;
      $("#edit-title-0-value").prop('disabled', true);
      
      // Add auto title for the product.
      $("#edit-field-ticket").once().change(function (e){

        $("#edit-title-0-value").val('');
        var result = $("#edit-field-ticket option:selected").text()+" - "+cDate
        $("#edit-title-0-value").val(result);
        
      });

		}
	};
})(jQuery, Drupal, drupalSettings);
