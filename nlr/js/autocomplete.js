(function ($) {

    /**
     * Attaches the autocomplete behavior to all required fields.
     */
    Drupal.behaviors.autocomplete = {
      attach: function (context, settings) {
        var acdb = [];
        jQuery('input.autocomplete', context).once('autocomplete', function () {
          var uri = this.value;
          if (!acdb[uri]) {
            acdb[uri] = new Drupal.ACDB(uri);
          }
          var $input = jQuery('#' + this.id.substr(0, this.id.length - 13))
            .attr('autocomplete', 'OFF')
            .attr('aria-autocomplete', 'list');
          jQuery($input[0].form).submit(Drupal.autocompleteSubmit);
          $input.parent()
            .attr('role', 'application')
            .append(jQuery('<span class="element-invisible" aria-live="assertive"></span>')
              .attr('id', $input.attr('id') + '-autocomplete-aria-live')
            );
          new Drupal.jsAC($input, acdb[uri]);
        });
      }
    };
    
    /**
     * Prevents the form from submitting if the suggestions popup is open
     * and closes the suggestions popup when doing so.
     */
    Drupal.autocompleteSubmit = function () {
      return jQuery('#autocomplete').each(function () {
        this.owner.hidePopup();
      }).length == 0;
    };
    
    /**
     * An AutoComplete object.
     */
    Drupal.jsAC = function ($input, db) {
      var ac = this;
      this.input = $input[0];
      this.ariaLive = jQuery('#' + this.input.id + '-autocomplete-aria-live');
      this.db = db;
    
      $input
        .keydown(function (event) { return ac.onkeydown(this, event); })
        .keyup(function (event) { ac.onkeyup(this, event); })
        .blur(function () { ac.hidePopup(); ac.db.cancel(); });
    
    };
    
    /**
     * Handler for the "keydown" event.
     */
    Drupal.jsAC.prototype.onkeydown = function (input, e) {
      if (!e) {
        e = window.event;
      }
      switch (e.keyCode) {
        case 40: // down arrow.
          this.selectDown();
          return false;
        case 38: // up arrow.
          this.selectUp();
          return false;
        default: // All other keys.
          return true;
      }
    };
    
    /**
     * Handler for the "keyup" event.
     */
    Drupal.jsAC.prototype.onkeyup = function (input, e) {
      if (!e) {
        e = window.event;
      }
      switch (e.keyCode) {
        case 16: // Shift.
        case 17: // Ctrl.
        case 18: // Alt.
        case 20: // Caps lock.
        case 33: // Page up.
        case 34: // Page down.
        case 35: // End.
        case 36: // Home.
        case 37: // Left arrow.
        case 38: // Up arrow.
        case 39: // Right arrow.
        case 40: // Down arrow.
          return true;
    
        case 9:  // Tab.
        case 13: // Enter.
        case 27: // Esc.
          this.hidePopup(e.keyCode);
          return true;
    
        default: // All other keys.
          if (input.value.length > 0 && !input.readOnly) {
            this.populatePopup();
          }
          else {
            this.hidePopup(e.keyCode);
          }
          return true;
      }
    };
    
    /**
     * Puts the currently highlighted suggestion into the autocomplete field.
     */
    Drupal.jsAC.prototype.select = function (node) {
      this.input.value = jQuery(node).data('autocompleteValue');
      jQuery(this.input).trigger('autocompleteSelect', [node]);
    };
    
    /**
     * Highlights the next suggestion.
     */
    Drupal.jsAC.prototype.selectDown = function () {
      if (this.selected && this.selected.nextSibling) {
        this.highlight(this.selected.nextSibling);
      }
      else if (this.popup) {
        var lis = jQuery('li', this.popup);
        if (lis.length > 0) {
          this.highlight(lis.get(0));
        }
      }
    };
    
    /**
     * Highlights the previous suggestion.
     */
    Drupal.jsAC.prototype.selectUp = function () {
      if (this.selected && this.selected.previousSibling) {
        this.highlight(this.selected.previousSibling);
      }
    };
    
    /**
     * Highlights a suggestion.
     */
    Drupal.jsAC.prototype.highlight = function (node) {
      if (this.selected) {
        jQuery(this.selected).removeClass('selected');
      }
      jQuery(node).addClass('selected');
      this.selected = node;
      jQuery(this.ariaLive).html(jQuery(this.selected).html());
    };
    
    /**
     * Unhighlights a suggestion.
     */
    Drupal.jsAC.prototype.unhighlight = function (node) {
      jQuery(node).removeClass('selected');
      this.selected = false;
      jQuery(this.ariaLive).empty();
    };
    
    /**
     * Hides the autocomplete suggestions.
     */
    Drupal.jsAC.prototype.hidePopup = function (keycode) {
      // Select item if the right key or mousebutton was pressed.
      if (this.selected && ((keycode && keycode != 46 && keycode != 8 && keycode != 27) || !keycode)) {
        this.select(this.selected);
      }
      // Hide popup.
      var popup = this.popup;
      if (popup) {
        this.popup = null;
        jQuery(popup).fadeOut('fast', function () { jQuery(popup).remove(); });
      }
      this.selected = false;
      jQuery(this.ariaLive).empty();
    };
    
    /**
     * Positions the suggestions popup and starts a search.
     */
    Drupal.jsAC.prototype.populatePopup = function () {
      var $input = jQuery(this.input);
      var position = $input.position();
      // Show popup.
      if (this.popup) {
        jQuery(this.popup).remove();
      }
      this.selected = false;
      this.popup = jQuery('<div id="autocomplete"></div>')[0];
      this.popup.owner = this;
      jQuery(this.popup).css({
        top: parseInt(position.top + this.input.offsetHeight, 10) + 'px',
        left: parseInt(position.left, 10) + 'px',
        width: $input.innerWidth() + 'px',
        display: 'none'
      });
      $input.before(this.popup);
    
      // Do search.
      this.db.owner = this;
      this.db.search(this.input.value);
    };
    
    /**
     * Fills the suggestion popup with any matches received.
     */
    Drupal.jsAC.prototype.found = function (matches) {
      // If no value in the textfield, do not show the popup.
      if (!this.input.value.length) {
        return false;
      }
    
      // Prepare matches.
      var ul = jQuery('<ul></ul>');
      var ac = this;
      for (key in matches) {
        jQuery('<li></li>')
          .html(jQuery('<div></div>').html(matches[key]))
          .mousedown(function () { ac.hidePopup(this); })
          .mouseover(function () { ac.highlight(this); })
          .mouseout(function () { ac.unhighlight(this); })
          .data('autocompleteValue', key)
          .appendTo(ul);
      }
    
      // Show popup with matches, if any.
      if (this.popup) {
        if (ul.children().length) {
          jQuery(this.popup).empty().append(ul).show();
          jQuery(this.ariaLive).html(Drupal.t('Autocomplete popup'));
        }
        else {
          jQuery(this.popup).css({ visibility: 'hidden' });
          this.hidePopup();
        }
      }
    };
    
    Drupal.jsAC.prototype.setStatus = function (status) {
      switch (status) {
        case 'begin':
          jQuery(this.input).addClass('throbbing');
          jQuery(this.ariaLive).html(Drupal.t('Searching for matches...'));
          break;
        case 'cancel':
        case 'error':
        case 'found':
          jQuery(this.input).removeClass('throbbing');
          break;
      }
    };
    
    /**
     * An AutoComplete DataBase object.
     */
    Drupal.ACDB = function (uri) {
      this.uri = uri;
      this.delay = 300;
      this.cache = {};
    };
    
    /**
     * Performs a cached and delayed search.
     */
    Drupal.ACDB.prototype.search = function (searchString) {
      var db = this;
      this.searchString = searchString;
    
      // See if this string needs to be searched for anyway. The pattern ../ is
      // stripped since it may be misinterpreted by the browser.
      searchString = searchString.replace(/^\s+|\.{2,}\/|\s+$/g, '');
      // Skip empty search strings, or search strings ending with a comma, since
      // that is the separator between search terms.
      if (searchString.length <= 2 ||
        searchString.charAt(searchString.length - 1) == ',') {
        return;
      }
    
      // See if this key has been searched for before.
      if (this.cache[searchString]) {
        return this.owner.found(this.cache[searchString]);
      }
    
      // Initiate delayed search.
      if (this.timer) {
        clearTimeout(this.timer);
      }
      this.timer = setTimeout(function () {
        db.owner.setStatus('begin');
    
        // Ajax GET request for autocompletion. We use Drupal.encodePath instead of
        // encodeURIComponent to allow autocomplete search terms to contain slashes.
        $.ajax({
          type: 'GET',
          url: db.uri + '/' + Drupal.encodePath(searchString),
          dataType: 'json',
          success: function (matches) {
            if (typeof matches.status == 'undefined' || matches.status != 0) {
              db.cache[searchString] = matches;
              // Verify if these are still the matches the user wants to see.
              if (db.searchString == searchString) {
                db.owner.found(matches);
              }
              db.owner.setStatus('found');
            }
          },
          error: function (xmlhttp) {
            Drupal.displayAjaxError(Drupal.ajaxError(xmlhttp, db.uri));
          }
        });
      }, this.delay);
    };
    
    /**
     * Cancels the current autocomplete request.
     */
    Drupal.ACDB.prototype.cancel = function () {
      if (this.owner) this.owner.setStatus('cancel');
      if (this.timer) clearTimeout(this.timer);
      this.searchString = '';
    };
    
})(jQuery);
    