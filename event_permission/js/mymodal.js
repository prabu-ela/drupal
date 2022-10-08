(function ($, Drupal) {
    'use strict';
  
    Drupal.behaviors.myModal = {
      attach: function(context) {
        $(document).ready(function(){
            var link = $('.video_youtube_url a, .video-play a').attr('href');
            $('.video_youtube_url a, .video-play a').attr('data',link);
            $('.video_youtube_url a, .video-play a').removeAttr('href');
            $(".playicon").remove();
            $('.video_youtube_url a, .video-play a').append('<div class="playicon"></div>');
        });
        $('.video_youtube_url, .node--type-video .video-play, .node--type-presentation .video-play').click(function() {
            var data_link = $('.video_youtube_url a , .node--type-video .video-play a, .node--type-presentation .video-play a').attr('data');
            $('#event-video .video-play, .node--type-video .video-play a,.node--type-presentation .video-play a').addClass('iframe-video');
            var width = $(window).width();
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
            const match = data_link.match(regExp);
            var url_you =  (match && match[2].length === 11)? match[2]: null;
            
            if (width >= 1024){
              var full_url = "https://www.youtube.com/embed/"+url_you;
              $('.video_youtube_url, .node--type-video .video-play,.node--type-presentation .video-play').html('<iframe width="100%" height="270" src="'+ full_url +'?autoplay=1&rel=0" allow="autoplay" frameborder="0" allowfullscreen="" ></iframe>');
             
              var befor_play = $('.video_youtube_url, .node--type-video .video-play,.node--type-presentation .video-play').html();
              
               $('iframe').contents().find("body").on('click', function(event) {
                $('.video_youtube_url').html(befor_play); 
              });
            }
            else{
              var ajaxSettings = {
                url: '/modals/my-modal?link='+data_link
                };
                var myAjaxObject = Drupal.ajax(ajaxSettings);
                myAjaxObject.execute();
           }   
        });
         
        $('.videoAttach').click(function() {
            $('.video_youtube_url').empty();
            var attach_link = $(this).attr('data');
            var attach_image = $(this).attr('image');
            $('.video_youtube_url').append('<a data=""></a>');
            $('.video_youtube_url a, .video-play a').append('<div class="playicon"></div>');
             $('.video_youtube_url a, .video-play a').attr('data',attach_link);
             var s_plit = attach_link.split('=');
             $('.video-image-attach').attr('style','background-image: url(https://img.youtube.com/vi/'+ s_plit[1] +'/maxresdefault.jpg)');
            // $('.video-image-attach').css('background-image', 'url(' + attach_image + ')');
        });
        
      }
    };
  
  })(jQuery, Drupal);