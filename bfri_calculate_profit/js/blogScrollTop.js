(function ($, Drupal, drupalSettings){
  Drupal.behaviors.blogScrollTop  = {
    attach: function (context, settings) {
      $('.page-item a').click(function() {
        $(document).ajaxComplete(function(event, xhr, settings) {
          $(window).scrollTop(parseInt($('.views-row:first').offset().top) - parseInt($('.sticky__header__space').height()));
          // $(".view-blogs-for-group-pages").animate({scrollTop: $(window).scrollTop(450)});
          // $(".view-resource-category-list").animate({scrollTop: $(window).scrollTop(450)});
  
          // Blog Page hide and show.
          $('.more__info-wrap').click(function() {
            // $('.school__fund__sponsor > .view-content.row').next().toggleClass('show');
            $('.school__fund__sponsor > .view-content.row').toggleClass('show');
          });
        });
      });

      // Blog Page hide and show.
      // $('.more__info-wrap').click(function() {
      //   // $('.school__fund__sponsor > .view-content.row').next().toggleClass('show');
      //   $('.school__fund__sponsor > .view-content.row').toggleClass('show 1');
      // });
    }
  };
})(jQuery, Drupal, drupalSettings);
