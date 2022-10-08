(function($, Drupal, drupalSettings){
  Drupal.behaviors.youtubedata = {
    attach: function(context, settings) {
      let webform_target = $(".views-field-field-resource-webform .field-content").text();
      webform_target = webform_target.replaceAll("_", " ");
      $(".views-field-field-resource-webform .field-content").text(webform_target);
    }
  };
})(jQuery, Drupal, drupalSettings);
  