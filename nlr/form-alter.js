jQuery(document).ready(function() {
    var text_min = 160;
    var text_max = 275;
    var target   = '#edit-metatags-und .form-item-metatags-und-description-value .description';

    setTimeout(checkMetaDescLength(), 3000);

    jQuery('#edit-metatags-und-description-value').keyup(function() {

        checkMetaDescLength();

    });

    function checkMetaDescLength() {
        var text_length = jQuery('#edit-metatags-und-description-value').val().length;
        var color = 'color:red;';

        if (text_length >= text_min && text_length <= text_max) {
            color = 'color:green;';
        }

        jQuery(target).html(`<span style="font-weight: bold;${color}">${text_length} characters total</span>.  A brief and concise summary of the page's content, preferably between ${text_min} and ${text_max} characters in length. The description meta tag may be used by search engines to display a snippet about the page in search results.`);
    }
});
