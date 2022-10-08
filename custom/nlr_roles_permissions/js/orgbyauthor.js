(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.orgbyauthor = {
      attach: function (context, settings) {
        var org_len = $('.form-item-field-org-org-info .form-radio').length;
        var event_org_lent = $('.form-item-field-author-org .form-radio').length;
        var award_org_lent = $('.form-item-field-award-firm .form-radio').length;
        if (org_len == 1) {
          $( "input[name=field_org_org_info]" ).trigger( "change");
        }
        if (event_org_lent == 1) {
          $( "input[name=field_author_org]" ).trigger( "change");
        }
        if (award_org_lent == 1) {
          $( "input[name=field_award_firm]" ).trigger( "change");
        }
      }
    };
  })(jQuery, Drupal, drupalSettings);
