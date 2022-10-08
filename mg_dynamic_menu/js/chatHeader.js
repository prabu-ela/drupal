(function($, Drupal, drupalSettings){
  Drupal.behaviors.chatHeader = {

    // JS for chaging the chat header.
    attach: function(context, settings) {
      $('.chat_drupal_open #openchat',context).click(function() {
        setTimeout(function() {
     //     $(".chat-block .ifc-chat-window-header-title,.chat-block .ifc-chat-button-with-tooltip,.chat-block .ifc-chat-window-header-top").html("");
        },
      1500);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
