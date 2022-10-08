(function($) {
    Drupal.behaviors.avatar_images = {
        attach: function(context, settings) {
            var i=0;
            $('.form-item-field-color-scheme').each(function(){
               var names =  Array("N/A","Yellow","Orange","Red","Purple","Blue","Green","Brown","White","Dark Grey");
               var namesimage_name =  Array("N/A","thumb-yellow.jpg","thumb-orange.jpg","thumb-red.jpg","thumb-purple.jpg","thumb-blue.jpg","thumb-green.jpg","thumb-brown.jpg","thumb-light-grey.jpg","thumb-dark-grey.jpg","thumb-cyan.jpg");
                var img = '<img src="/sites/default/files/images/booths/colors/'+namesimage_name[i]+'">';
                $(this).closest('div').find('label').html(img+' '+names[i]);
                i++;
            })
            
            $('input[name="field_booth_avatar"]').once().each(function(){
                var image_name = $(this).val();
                var img = '<img src="/sites/default/files/images/avatars/thumbs/'+image_name+'_aud.jpg">';
                var label_name = $(this).parent().find('label').text();
                $(this).closest('div').find('label').html(img+' '+label_name);
            })
        }
    };
})(jQuery);
