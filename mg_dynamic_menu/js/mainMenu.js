(function($, Drupal, drupalSettings){
  Drupal.behaviors.mainMenu = {

    // Appedning dynamic menus.
    attach: function(context, settings) {
      var values = drupalSettings.mg_dynamic_menu.menu;

      $(".exhibithall-class").parent().addClass("menu-item--expanded dropdown");
      $(".exhibithall-class").addClass("dropdown-toggle ");
      if($( ".main-menu" ).hasClass( "dropdown-menu" )){

      }else{
      let childName = $(".exhibithall-class").parent().append('<div class="dropdown-menu main-menu"></div>');
        $(values).each(function(ind,val){
          if (val != 'undefined') {
            $(".main-menu").append('<li class="dropdown-item"><a href="/node/'+ val.nid+'" class="nav-link--marcomgroup-web-booth-engineering" data-drupal-link-system-path="node/'+val.nid+'">'+val.title+'</a></li>');
          }
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
