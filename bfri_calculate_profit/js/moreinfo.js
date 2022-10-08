(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.moreinfo = {
    attach: function (context, settings) {
      var page_type = drupalSettings.bfri_calculate_profit.page_type;
      $(context).find('.school__fund__sponsor').once('moreinfo').prepend("<div class='more__info-wrap'><p class='more__info'></p></div>");
      
      if($('body').find('.more__info-wrap').length) {
        $('div.block[class*="top-block-title"]').insertAfter('.more__info-wrap');
      }

      if (page_type != 1) {
         // Working with Resource page.
        $('.fundraiser__sponsor__left > h2').insertBefore('.more__info-wrap');

        $('.more__info-wrap', context).once().bind('click', function() {
          $(this).next().toggleClass('show');
          $(this).next().next().toggleClass('show');
        });
      }

      $('.more__info-wrap', context).once().bind('click', function() {
        $(this).toggleClass('rotate');
        $(this).next().next().toggleClass('show');
        $(this).next().next().next().toggleClass('show');
      });

      // Image Zoom on mouse over.
      $('figure.zoom').mousemove(function(e) {
        var zoomer = e.currentTarget;
        e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX;
        e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX;
        x = offsetX/zoomer.offsetWidth*100;
        y = offsetY/zoomer.offsetHeight*100;
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
