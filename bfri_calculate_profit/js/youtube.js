(function($, Drupal, drupalSettings){
  Drupal.behaviors.youtubedata = {
    attach: function(context, settings) {
      var values = drupalSettings.bfri_calculate_profit.youtube;
      $(".block-field-blocknodebig-eventsfield-video-source-code").html('<iframe width="100%" height="315" src="'+ values +'" frameborder="0" allowfullscreen=""></iframe>');
    }
  };
})(jQuery, Drupal, drupalSettings);
  