(function($) {
    Drupal.behaviors.resourcecenter = {
        attach: function(context, settings) {
            hideall();

            $('#edit-field-resource-type-1').once('resourcecenter').on('change', function(){
                 var select_type= $(this).val();
                 hideall();
                 if(select_type=='Videos'){ $('#edit-field-resource-videos-1-wrapper').show(); }
                 if(select_type=='Documents'){ $('#edit-field-resource-documents-1-wrapper').show(); }
            });

            $('#edit-field-resource-type-2').once('resourcecenter').on('change', function(){
                var select_type= $(this).val();
                hideall();
                if(select_type=='Videos'){ $('#edit-field-resource-videos-2-wrapper').show(); }
                if(select_type=='Documents'){ $('#edit-field-resource-documents-2-wrapper').show(); }
           });


           $('#edit-field-resource-type-3').once('resourcecenter').on('change', function(){
            var select_type= $(this).val();
            hideall();
            if(select_type=='Videos'){ $('#edit-field-resource-videos-3-wrapper').show(); }
            if(select_type=='Documents'){ $('#edit-field-resource-documents-3-wrapper').show(); }
          });

             $('#edit-field-resource-type-4').once('resourcecenter').on('change', function(){
            var select_type= $(this).val();
            hideall();
            if(select_type=='Videos'){ $('#edit-field-resource-videos-4-wrapper').show(); }
            if(select_type=='Documents'){ $('#edit-field-resource-documents-4-wrapper').show(); }
            });

            $('#edit-field-resource-type-5').once('resourcecenter').on('change', function(){
            var select_type= $(this).val();
            hideall();
            if(select_type=='Videos'){ $('#edit-field-resource-videos-5-wrapper').show(); }
            if(select_type=='Documents'){ $('#edit-field-resource-documents-5-wrapper').show(); }
            });

            $('#edit-field-resource-type-6').once('resourcecenter').on('change', function(){
                var select_type= $(this).val();
                hideall();
                if(select_type=='Videos'){ $('#edit-field-resource-videos-6-wrapper').show(); }
                if(select_type=='Documents'){ $('#edit-field-resource-documents-6-wrapper').show(); }
            });


            function hideall(){

                for (var i = 1; i <= 6; i++) {
                    $('#edit-field-resource-videos-'+i+'-wrapper').hide();
                    $('#edit-field-resource-documents-'+i+'-wrapper').hide();
                } 

            }
        }
    };
})(jQuery);
