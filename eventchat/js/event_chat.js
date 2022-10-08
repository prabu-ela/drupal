(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.eventchat = {
    attach: function (context, settings) {
      var user_id_values = drupalSettings.c_u_id;
      var user_name_values = drupalSettings.c_u_name;
      var user_in_route = drupalSettings.route_name;
      var user_in_roles = drupalSettings.roles;
     //Booth page pop enter queue start
     if ($(window).width() > 991) {
      $('#chat-with-recruiter').click(function(){
        $(".modal-body-chat .popup-mobile").removeClass("hide");
        $(".modal-body-chat .popup-mobile").addClass("show");
        $('#myModalChat textarea').val('');
        $('#myModalChat').modal('show');
        
       });
     }
     else {
      $('.chat-recruiter #chat-with-recruiter').click(function(){
        $(".modal-body-chat .popup-mobile").removeClass("hide");
        $(".modal-body-chat .popup-mobile").addClass("show");
        $('#myModalChat textarea').val('');
        $('#myModalChat').modal('show');
        $('#myModal').modal('hide');
        
       });
     }
     // Booth page pop enter queue end.

     $(document).on('click', '.visible_chater_assign', function(e) {
      if ($(this).hasClass("active")) {
        $(".visible_chater_assign").removeClass("active");
      }
      else {
        $(".visible_chater_assign").removeClass("active");
        $(this).addClass("active");
      }
    });

      $(document).on('click', '.visible_chater_assign', function(e) {
        $('.visible_chater_assign').next('.sub_btn').removeClass('show').css('display', 'none');
        $(this).next('.sub_btn').removeClass('show').css('display', 'block');  
      });

      $(".close__btn").click(function(){
        $("#myModaldoc22").hide();
      });

      $(document).ready(function() {
        $(".close__btn").click(function(){
          $(".chat-rep-waiting").hide();
        });

        $(".close__btn").click(function(){
          $(".chat-view-history").hide();
        });
      });

      $(document).ready(function() {
        $(".close_history").click(function() {
          $(".chat-sidebar-right").hide("slide", {direction: "left"}, 1000);
        });
      });      

      // Event Chat

      $(document).on('click','.openbtn',function(){
        $('#mySidebar').css({'width':'330px'});
        $('#main').css({'marginLeft' : '330px'});
      });

      $(document).on('click','.closebtn',function(){
        $('#mySidebar').css({'width':'0'});
        $('#main').css({'marginLeft' : '0'});
      });

      /* To get data for assigned and unassigned chat data */
      $('#select_type,#select_type_repo').change(function(){
        var type_que = $(this).val();
        /* To get assigned data*/
        $.ajax({
          url:"/eventchat/getdata",
          type: "Post",
          data:{
            queue_type : type_que
          },
          success:function(response){
            $('#placing_data_assign').html(response);
          }
        });

        /* To get unassigned data*/
        $.ajax({
          url:"/eventchat/getdataunassign",
          type: "Post",
          data:{
            queue_type : type_que
          },
          success:function(response){
            $('#placing_data_unassign').html(response);
          }
        });

      });

      /* function for the assign user pop up. */
      $(document).on('click','.assign_process',function(){ 
        var qid = $(this).attr('data');
        var user_name = $(this).attr('data-user');
        $('#myModaldoc22 .nameshow_user_assign').text(user_name);
        $("#queue_id").attr('data',qid);
       // $('#myModaldoc22').modal('show');
       $('#myModaldoc22').attr('style','display:block');
      });

      /* function for the assign to me. */
      $(document).once('eventchat').on('click','.assign_process_me',function(){ 
        var sid = $(this).attr('data-id');
        var qid = $(this).attr('data');
        var sname = $(this).attr('data-user');
        var chatsid = $(this).attr('data-userid');
        start_chatfly(chatsid,sname,qid);
        assign_process_call(qid,sid,sname);
      });

      /* function for the submit assign user pop-up. */
      $('#submit_assign').once('eventchat').click(function(){ 
        var sid = $('#list_repo :selected').val();
        var sname = $('#list_repo :selected').text();
        var qid = $('#queue_id').attr('data');
        if(sid == 0){
          $('#error_repo').html('Please select the user');
        }
        else{
          assign_process_call(qid,sid,sname);
      }
        
      });

      /*  function for the mark as complete process */
      $(document).on('click','.unassign_process',function(){ 
        var qid = $(this).attr('data');
        $.ajax({
          url:"/eventchat/markcomplete",
          type: "Post",
          data:{
            queue_id : qid
          },success:function(response){
            if(response == 200){ 
              $('.chat-rep-waiting').hide();
              to_call_after_reprocess();
            }
          }
        });
      });

      /* assigning process for chat. */
      function assign_process_call(qid,sid,sname){
        $.ajax({
          url:"/eventchat/assign",
          type: "Post",
          data:{
            queue_id : qid,
            user_id : sid,
            username : sname
          },success:function(response){
            if(response == 200){ 
              //$('#myModaldoc22').modal('hide'); 
              $('#myModaldoc22').hide(); 
              $('.chat-rep-waiting').hide();
              to_call_after_reprocess();
          }
          else{ 
            $('#submit_assign').next().html('<a>Error</a>');}
          }
        });
      }

      

      /* To reload the assigned and unassigned data. */
      function to_call_after_reprocess(){
        var type_que = $('#select_type :selected').val();
        $.ajax({
          url:"/eventchat/getdata",
          type: "Post",
          data:{
            queue_type : type_que
          },
          success:function(response){
            $('#placing_data_assign').html(response);
          }
        });

        $.ajax({
          url:"/eventchat/getdataunassign",
          type: "Post",
          data:{
            queue_type : type_que
          },
          success:function(response){
            $('#placing_data_unassign').html(response);
          }
        });
      }

     

      /* function for the view history. */
      $(document).on('click','.view_chat_history',function(){ 
        var id = $(this).attr('data');
        $.ajax({
          url:"/eventchat/getthreads",
          type: "Post",
          data:{
            queue_id : id
          },
          success:function(response){
            $('#chat_history .chat_history_data').html(response);
            $('.chat-sidebar-right').show();
            $('#myModaldoc22').hide(); 
            $('.chat-view-history .show-viewhistory').html(response);
            $('.chat-view-history').show();
          }
        });
      });

      

      /* show chat enable option */
      $(document).on('change','#list_repo',function(){
        var sname = $('#list_repo :selected').text();
        $('#chat_assign').text('Chat with '+sname);
        $(this).siblings('#error_repo').text('');
        $('#chat_assign').show();
      });

      $(document).on('click','.assign_process',function(){
        var user_quee = $(this).attr('data-user');
        $('.nameshow_user_assign').text(user_quee);
      });

      

      $(document).on('click','#chat_assign',function(){
        var sid = $('#list_repo :selected').val();
        var sname = $('#list_repo :selected').text();
        var qid = $('#queue_id').attr('data');
        //$('#myModaldoc22').hide(); 
        start_chatfly(sid,sname,qid);
        
      });

      

      /* Start the chat to triggered user */
      $(document).on('dblclick','#chat_process,.user_clickchat',function(){
          var sid = $(this).attr('data-id');
          var sname = $(this).attr('data');
          var qid = $(this).attr('data-queue');
          var repo_id = $(this).attr('data-repo'); 
          if($(this).hasClass('unassigned_terms')){
            assign_process_call(qid,repo_id,'');
          }
          start_chatfly(sid,sname,qid);
      });

       /* To trigger the user for the  chat. */
      function start_chatfly(sid,sname,qid){

        iflychat.startChat({
          id: sid, /* id of the user or room with whom to start chat */
          name: sname, /*name of the user or room with whom to start chat */
          state: 'open' /* defines if chat needs to be started in open state or minimised state*/
        });
        
        iflychat.on("winOpen", function (e) {
          // keeps window from opening
          var htmlContent = '<span class="prevHistoryBox">';
          htmlContent += "<a  data='"+ qid +"' class='view_chat_history'>View Chat History</a>";
          htmlContent += '</span>';
          iflychat.renderLabelInChatWindow({ 
          id: e.uid2,
          position: 'above-chat-content', 
          content: htmlContent 
          });
        });
        
      }
      
      /* User to enter the queue submit. */
      $('#myModalChat .enter_queue').once('eventchat').click(function(){
        var qdata  = $('#myModalChat textarea').val();
        var qtype = $('#myModalChat #type').val();
        // Check wheter empty or not.
        if(qdata =="" || typeof qdata == "undefined" || typeof qdata == "null"){
          return false;
        }
        else{
        $.ajax({
          url:"/eventchat/saveqdata",
          type: "Post",
          data:{
            queue_data : qdata,
            queue_type : qtype
          },success:function(response){
            if(response != null) { 
            $('#myModalChat').modal('hide') ; 
            $('.chat-rep-waiting').show();
            let interval = setInterval(function(){ 
              $.ajax({
               url:"/eventchat/checkrepoget",
               type: "Post",
               data:{
                queue_id : response,
               },success:function(response){
                 if(response == 200){
                   $('.chat-rep-waiting').hide();
                   clearInterval(interval);
                 }
               }
              });
             },1000);

             //To check Repo placed as mark as complete.

             let interval2 = setInterval(function(){ 
              $.ajax({
               url:"/eventchat/checkusersidemarkcomplte",
               type: "Post",
               data:{
                queue_id : response,
               },success:function(response1){
                 if(response1 == 200){
                   $('#myModalChatcomplete').modal('show');
                   setTimeout($('#myModalChatcomplete').modal('hide'),1000);
                   clearInterval(interval2);
                   close_chat_fly(user_id_values,user_name_values,response); 
                   
                 }
               }
              });
             },1000);

             //To get assign Repo id and name.

             let interval3 = setInterval(function(){ 
              $.ajax({
                url:"/eventchat/getuser_to_closechat",
                type: "Post",
                data:{
                 queue_id : response,
                },
                success:function(response_data1){
                  if(response_data1 != 400) { 
                    var json = $.parseJSON(response_data1);
                    var id_user_chat = json.id;
                    var name_user_chat = json.name;
                   if(id_user_chat != null){ 
                   var width = $(window).width();
                    if (width <= 1200){


                      iflychat.init({
  
                        userlist: {
                          visible: false
                        } 
                      
                      });

                        iflychat.startChat({
                        id: id_user_chat, /* id of the user or room with whom to start chat */
                        name: name_user_chat, /*name of the user or room with whom to start chat */
                        state: 'open' /* defines if chat needs to be started in open state or minimised state*/
                        });

                        var htmlContent = '<span class="prevHistoryBox">';
                        htmlContent += "<a  data='"+ response +"' class='view_chat_history'>View Chat History</a>";
                        htmlContent += '</span>';
                        iflychat.renderLabelInChatWindow({ 
                        id: id_user_chat,
                        position: 'above-chat-content', 
                        content: htmlContent 
                        });

                        var htmlContent = '<span class="defaultchat-header">';
                        htmlContent += '</span>';
                        iflychat.renderLabelInChatWindow({ 
                        id: 3,
                        position: 'above-chat-content', 
                        content: htmlContent 
                        });

                        $('#ifc-app .ifc-chat-list-container').addClass('container-to-hide');
                     
                        clearInterval(interval3);
                    }
                    else{
                      iflychat.startChat({
                      id: id_user_chat, /* id of the user or room with whom to start chat */
                      name: name_user_chat, /*name of the user or room with whom to start chat */
                      state: 'open' /* defines if chat needs to be started in open state or minimised state*/
                      });
                      var htmlContent = '<span class="prevHistoryBox">';
                      htmlContent += "<a  data='"+ response +"' class='view_chat_history'>View Chat History</a>";
                      htmlContent += '</span>';
                      iflychat.renderLabelInChatWindow({ 
                      id: id_user_chat,
                      position: 'above-chat-content', 
                      content: htmlContent 
                      });

                      clearInterval(interval3);
                    }

                    

                  }
                    
                  }
                }
              });
             },1000);


            }else{ 
            $('#myModalChat .enter_queue').next().html('<a>'+response+'</a>');
            }
          }
        });
      }
      });
      
      /* To close the chat. */
      function close_chat_fly(sid,sname,response)
      {
        $('#myModalChatcomplete').removeClass('show');
        $('#myModalChatcomplete').attr('style','display:none');
        $('body').removeClass('modal-open');
        $('body').attr('style','');
        $('.modal-backdrop').removeClass('show');
        $('#myModalChatcomplete').hide();

        $.ajax({
          url:"/eventchat/getuser_to_closechat",
          type: "Post",
          data:{
           queue_id : response,
          },
          success:function(response_data){
            if(response_data != 400) { 
              var json = $.parseJSON(response_data);
              var id_user_chat = json.id;
              var name_user_chat = json.name;
              iflychat.closeChat({
              id: id_user_chat, /* id of the user or room with whom to close chat */
              name: name_user_chat /*name of the user or room with whom to close chat */
              });
              
            }
          }
        });
        
      }

      
      $(document).on('click','.ifc-chat-window-header-close-icon',function(){
       if ($.inArray('administrator', user_in_roles) != 1){
       if(user_in_route != 'view.my_lounge.page_1'){
       $.getScript("../modules/custom/drupalchat/js/drupalchat-bundle.js?v=1.x");
       $.getScript("//cdn.iflychat.com/js/iflychat-v2.min.js?app_id=8c9b5343-d766-41a4-8397-9005f6cd121a");
       $(".iflychat-popup").load(location.href + ".iflychat-popup");
       }
      }
      });


      /* To update the queue data in the sidebar. */
      let interval = setInterval(function(){ 
        var type_que = $('#select_type :selected').val();
          if(type_que != "" || typeof value != "undefined"){
            if( type_que !=0){
              $.ajax({
              url:"/eventchat/getdata",
              type: "Post",
              data:{
              queue_type : type_que
              },
              success:function(response){
              $('#placing_data_assign').html(response);
              }
              });

              $.ajax({
              url:"/eventchat/getdataunassign",
              type: "Post",
              data:{
              queue_type : type_que
              },
              success:function(response){
              $('#placing_data_unassign').html(response);
              }
              });

            }
          }
       },180000); //3 minutes to refresh the data.

       // Trigger unassigned users.
       function refereshUnassignedUser() {
        let interval = setInterval(function() { 
          var type_que = $('#select_type :selected').val();
          console.log("dddddddd");
            if(type_que != "" || typeof value != "undefined"){
              if( type_que !=0){
                $.ajax({
                url:"/eventchat/getdata",
                type: "Post",
                data:{
                queue_type : type_que
                },
                success:function(response){
                $('#placing_data_assign').html(response);
                }
                });
  
                $.ajax({
                url:"/eventchat/getdataunassign",
                type: "Post",
                data:{
                queue_type : type_que
                },
                success:function(response){
                $('#placing_data_unassign').html(response);
                }
                });
              }
            }
         },180000); 
       }

       /* To update the queue data in the sidebar. */
     /* let intervalupdate = setInterval(function(){ 
        var type_que = $('#select_type :selected').val();
        var arr = []
          if(type_que != "" || typeof value != "undefined"){
            if( type_que !=0){
              $('#unassignsub .chat_profile').each(function(){
                arr.push($(this).attr('data-chatqid'));
              });

            var arr2 =   $.unique(arr);
            console.log(arr2);

              $.ajax({
                url:"/eventchat/getnewunassigneddata",
                type: "Post",
                data:{
                queue_type : type_que,
                queue_id : arr2
                },
                success:function(response){
               // console.log(response);
                 if(response != 400){
                   $('#placing_data_unassign #unassignsub').append(response);
                  }
                
                }
                });

            }
          }
       },2500); */ //2.5 second to refresh the data.

       

    }

      
      

  };
})(jQuery, Drupal, drupalSettings);

