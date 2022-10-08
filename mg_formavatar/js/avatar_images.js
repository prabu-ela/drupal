(function($) {
    Drupal.behaviors.avatar_images = {
        attach: function(context, settings) {
            var i=0;
            $('.form-radio').each(function(){
                var image_name = $(this).val();
                var img = '<img src="/sites/default/files/images/avatars/thumbs/'+image_name+'">';
               var labelid =  i++; if(labelid==0){ labelname='N/A'; }else{ labelname='Avatar '+labelid;}
                $(this).closest('div').find('label').html(img+' '+labelname);
            })
        }
    };
})(jQuery);