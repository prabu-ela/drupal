(function ($, Drupal, drupalSettings){
  Drupal.behaviors.hideimage  = {
    attach: function (context, settings) {
      $('.youtube-url',context).click(function() {
        document.querySelectorAll('iframe').forEach(v => { v.src = v.src });
        var href = $(this).attr('data-url');
        $(this).addClass('remove__icon');
        $(this).html('<iframe width="576" height="350" src="' + href +'?autoplay=1&rel=0" frameborder="0" allowfullscreen </iframe>');
      });

      $('.go__big__view',context).click(function() {
        document.querySelectorAll('iframe').forEach(v => { v.src = v.src });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
