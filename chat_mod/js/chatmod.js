jQuery.noConflict();
(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.orgbyauthor = {
      attach: function (context, settings) {
        var user_id_values = drupalSettings.u_chat_id;
        var user_name_values = drupalSettings.u_chat_name;
        // Profile edit page upload error start
        $('#edit-user-picture-0--label').once('orgbyauthor').next().append('<div id="palceinlineerror"></div>');
        $( document ).once('orgbyauthor').ajaxSuccess(function( event, xhr, settings ) {
          var h_data =$('#edit-user-picture-wrapper #ajax-wrapper div:first-child').html();
          $('#palceinlineerror').html('<div data-drupal-messages="" style="">'+ h_data +'</div>');
          $('#edit-user-picture-wrapper #ajax-wrapper div:first-child').css('display','none');
          $('#edit-user-picture-wrapper #ajax-wrapper div:first-child').css('display','none');
          $('#edit-user-picture-wrapper #ajax-wrapper .image-widget-data').css('display','block');
          $('#edit-user-picture-wrapper #ajax-wrapper #palceinlineerror div:first-child').css('display','block'); 
        });
        // Profile edit page upload error end

        if(jQuery('body').hasClass('toolbar-horizontal')){
          jQuery('body').find('.form-autocomplete').on('autocompleteclose', function() {
            jQuery('body').find('.form-autocomplete').each(function (event, node) {
              var val = $(this).val();
            });
          });
        }
        else{
        jQuery('body').find('.form-autocomplete').on('autocompleteclose', function() {
          jQuery('body').find('.form-autocomplete').each(function (event, node) {
            var val = $(this).val();
            var match = val.match(/\s\(.*?\)/g);
            if (match) {
            $(this).data('real-value', val);
            $(this).val(val.replace(/\s\(.*?\)/g, '' ));
            }
          });
        });
        }

        // chat open hide start
        $('.chat-block').hide();
        $("#openchat").attr('class','closechat');
        $("#openchat").click(function(){ 
        if($('#openchat').hasClass('open')){
          $("#openchat").attr('class','closechat');
          $("#openchat a").text('Open Chat');
          $('.chat-block').show();
        }
        else{
          $("#openchat").attr('class','open');
          $('.chat-block').show();
        }
        return false;
        });

        $('#lounge-chat-close').click(function(){ $('.chat-block').hide(); });
        //chat open hide end

        // Interval for hide default chat start
        let interval = setInterval(function(){ 
          $('.iflychat-popup div').each(function(){
           
            if($(this).hasClass('ifc-chat-list-container')){
              if($('body').hasClass('toolbar-horizontal')){
               
                $(this).show(); // admin side show condition
                clearInterval(interval);  //clearInterval function stop the interval after get class
              }
              else{
                $('.ifc-chat-list-roster .ifc-chat-list-roster-room').hide();
               
                clearInterval(interval);  //clearInterval function stop the interval after get class

              }
            
            }
          });
        },1000);
        // Interval for hide default chat end
      $(document).ready(function(){     
        $('#openchat').show();
      });

    /*  $(document).ready(function(){
        if( $('body').hasClass('page-view-my-lounge')) {
          let intervaliflyy =  setInterval(function () {
          var lenn =  $('#lounge-chat-body .iflychat-embed div').length;
          if(lenn > 0){
          $('#openchat').show();
          clearInterval(intervaliflyy);
          }
          else{
          location.reload();
          }
          }, 4000);
        }
      });  */
       

        var width = $(window).width();
        if (width <= 1200){
        $('.desktop__booth #podium-button-2 #leave-resume').attr('href','javascript:void(0)');
        }
      }
    };
  })(jQuery, Drupal, drupalSettings);