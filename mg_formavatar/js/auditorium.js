(function($) {
    Drupal.behaviors.auditorium = {
        attach: function(context, settings) {
            var i=0;
            $('.form-radio').each(function(){
                var image_name = $(this).val();
                var namesimage_name = Array("Angelica","Anthony","Beau","Brittany","Caitlin","Candice","Carolyn","Chrissy","Don","Erica","Hakim","Hayden","Jamie","Jenny","Kelsey","Lamont","Laura","Lauretta","Liza","Marcian","Mark","Martie","Molly","Moriah","Nabil","Nic","Patricia","Regina","Sarah","Sonia","Terri");
                var img = '<img src="/sites/default/files/images/avatars/thumbs/'+image_name+'">';
                $(this).closest('div').find('label').html(img+' '+namesimage_name[i]);
                var labelid =  i++; 
            })
        }
    };
})(jQuery);


