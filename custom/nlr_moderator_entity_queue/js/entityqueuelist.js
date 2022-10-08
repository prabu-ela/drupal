(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.enityqueuelist = {
    attach: function (context, settings) {
      var queueData = drupalSettings.nlr_moderator_entity_queue.queue;
      $.each(queueData, function(index, value) {
        var arr2 = [];
        arr2 = value.toString().split(',');
       $.each(arr2, function(i, val){
          $('.'+val+'-'+index).css({'background-color':'#194c81', 'color':'white'});
        }); 
      });
    }
  };
})(jQuery, Drupal, drupalSettings);