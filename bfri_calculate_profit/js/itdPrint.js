(function($, Drupal, drupalSettings) {
	"use strict";
	Drupal.behaviors.itdprint = {
		attach: function(context, settings) {
		var options = {
			debug: false,
			doctypeString: '<!DOCTYPE html>',
			loadCSS: location.origin+drupalSettings.path.baseUrl+"/modules/custom/bfri_calculate_profit/css/printable.css",
			header: '<h1><img src='+location.origin+drupalSettings.path.baseUrl+'/themes/custom/bigfundraising/logo.png></h1><h2>'+drupalSettings.currentPageTitle+'</h2>',
			footer: null
			};
			$('#print-this-area', context).once().bind('click', function() {
				$('.layout-main-wrapper').printThis(options);
			});
		}
	};
})(jQuery, Drupal, drupalSettings);