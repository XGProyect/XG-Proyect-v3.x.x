/*==================================================*
 $Id: filterlist.js,v 1.3 2003/10/08 17:13:49 pat Exp $
 Copyright 2003 Patrick Fitzgerald
 http://www.barelyfitz.com/webdesign/articles/filterlist/

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *==================================================*/

function filterlist(selectobj) {

  //==================================================
  // PARAMETERS
  //==================================================

  // HTML SELECT object
  // For example, set this to document.myform.myselect
  this.selectobj = selectobj;

  // Flags for regexp matching.
  // "i" = ignore case; "" = do not ignore case
  // You can use the set_ignore_case() method to set this
  this.flags = 'i';

  // Which parts of the select list do you want to match?
  this.match_text = true;
  this.match_value = false;

  // You can set the hook variable to a function that
  // is called whenever the select list is filtered.
  // For example:
  // myfilterlist.hook = function() { }

  // Flag for debug alerts
  // Set to true if you are having problems.
  this.show_debug = false;

  //==================================================
  // METHODS
  //==================================================

  //--------------------------------------------------
  this.init = function() {
    // This method initilizes the object.
    // This method is called automatically when you create the object.
    // You should call this again if you alter the selectobj parameter.

    if (!this.selectobj) return this.debug('selectobj not defined');
    if (!this.selectobj.options) return this.debug('selectobj.options not defined');

    // Make a copy of the select list options array
    this.optionscopy = new Array();
    if (this.selectobj && this.selectobj.options) {
      for (var i=0; i < this.selectobj.options.length; i++) {

        // Create a new Option
        this.optionscopy[i] = new Option();

        // Set the text for the Option
        this.optionscopy[i].text = selectobj.options[i].text;

        // Set the value for the Option.
        // If the value wasn't set in the original select list,
        // then use the text.
        if (selectobj.options[i].value) {
          this.optionscopy[i].value = selectobj.options[i].value;
        } else {
          this.optionscopy[i].value = selectobj.options[i].text;
        }

      }
    }
  }

  //--------------------------------------------------
  this.reset = function() {
    // This method resets the select list to the original state.
    // It also unselects all of the options.

    this.set('');
  }


  //--------------------------------------------------
  this.set = function(pattern) {
    // This method removes all of the options from the select list,
    // then adds only the options that match the pattern regexp.
    // It also unselects all of the options.

    var loop=0, index=0, regexp, e;

    if (!this.selectobj) return this.debug('selectobj not defined');
    if (!this.selectobj.options) return this.debug('selectobj.options not defined');

    // Clear the select list so nothing is displayed
    this.selectobj.options.length = 0;

    // Set up the regular expression.
    // If there is an error in the regexp,
    // then return without selecting any items.
    try {

      // Initialize the regexp
      regexp = new RegExp(pattern, this.flags);

    } catch(e) {

      // There was an error creating the regexp.

      // If the user specified a function hook,
      // call it now, then return
      if (typeof this.hook == 'function') {
        this.hook();
      }

      return;
    }

    // Loop through the entire select list and
    // add the matching items to the select list
    for (loop=0; loop < this.optionscopy.length; loop++) {

      // This is the option that we're currently testing
      var option = this.optionscopy[loop];

      // Check if we have a match
      if ((this.match_text && regexp.test(option.text)) ||
          (this.match_value && regexp.test(option.value))) {

        // We have a match, so add this option to the select list
        // and increment the index

        this.selectobj.options[index++] =
          new Option(option.text, option.value, false);

      }
    }

    // If the user specified a function hook,
    // call it now
    if (typeof this.hook == 'function') {
      this.hook();
    }

  }


  //--------------------------------------------------
  this.set_ignore_case = function(value) {
    // This method sets the regexp flags.
    // If value is true, sets the flags to "i".
    // If value is false, sets the flags to "".

    if (value) {
      this.flags = 'i';
    } else {
      this.flags = '';
    }
  }


  //--------------------------------------------------
  this.debug = function(msg) {
    if (this.show_debug) {
      alert('FilterList: ' + msg);
    }
  }


  //==================================================
  // Initialize the object
  //==================================================
  this.init();

}