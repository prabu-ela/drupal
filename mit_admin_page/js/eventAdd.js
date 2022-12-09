(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.eventAdd = {
		attach: function (context, settings) {

			// Selecting auto populate the existing fields.
			$("#edit-field-categories").once().change(function (e){
				$("input[name*='field_additional_categories']").prop('checked', false);
				$("#edit-field-additional-categories-"+$(this).val()).prop('checked', true);
			});

			// Hiding promotion on the event add page.
			$("#edit-options").remove();

      // Vendor popup.
      $('.js-append-vendor').insertAfter('#edit-field-event-vendor-0-target-id');
		}
	};
})(jQuery, Drupal, drupalSettings);