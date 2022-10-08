(function($, Drupal, drupalSettings) {
  Drupal.behaviors.entityqueue = {
    attach: function(context, settings) {
      var base_url = drupalSettings.nlr_moderator_entity_queue.base_url;
      $(".queue-mgnt").once('entityqueue').on('click', function() {
        $queue_url = '/manage_enitity_queue/'+ $(this).attr('data-url');
        imag_ulr = '/themes/custom/dark_blood/images/loader.gif';
        $('.queue-mgnt img:last-child').remove();
        $(this).append('<img src="'+base_url + imag_ulr+'" style="width:22%" />');
        console.log(base_url);
        $.ajax({
          url: base_url + $queue_url,
          type: 'POST',
          success: (response) =>{
            if (response.response == true && response.flag == 0) {
              $(this).css({
                'background-color':'#194c81',
                'color' : 'white'
              }
              );
            }
            else{
              $(this).css({
                'background-color':'',
                'color' : 'black'
              }
              );
            }
            $('.queue-mgnt img:last-child').remove();
          }
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);