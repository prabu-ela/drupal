(function ($, Drupal, drupalSettings){
  Drupal.behaviors.review_hide = {
    attach: function (context, settings) {
      $('.webform-submission-form fieldset .form-textarea-wrapper textarea').focus(function() {
        $(this).parent().siblings('label.form-required').hide();
      });

      $('.webform-submission-form fieldset .form-textarea-wrapper textarea').blur(function() {
        var $this = $(this);
        if ($this.val().length == 0)
          $(this).parent().siblings('label.form-required').show();
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
