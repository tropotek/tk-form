/**
 * This is here fo reference mainly. Can be used for non tk-base dependant sites
 *
 * For tk-base derived sites use the core.js project_core.initTkFormTabs();
 *
 *
 * @author Tropotek <info@tropotek.com>
 * @created: 28/07/18
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Tropotek
 */

jQuery(function() {

  // create bootstrap tab elements around a tabbed form
  $('.formTabs').each(function(id, tabContainer) {
    var ul = $('<ul class="nav nav-tabs"></ul>');
    var errorSet = false;

    $(tabContainer).find('.tab-pane').each(function (i, tbox) {
      var name = $(tbox).attr('data-name');
      var li = $('<li class="nav-item"></li>');
      var a = $('<a class="nav-link"></a>');
      a.attr('href', '#'+tbox.id);
      a.attr('data-toggle', 'tab');
      a.text(name);
      li.append(a);

      // Check for errors
      if ($(tbox).find('.has-error').length) {
        li.addClass('has-error');
      }
      if (i === 0) {
        $(tbox).addClass('active');
        li.addClass('active');
        a.addClass('active');
      }
      ul.append(li);
    });
    $(tabContainer).prepend(ul);
    $(tabContainer).find('li.has-error a');

    //$(tabContainer).find('li.has-error a').tab('show'); // shows last error tab
    $(tabContainer).find('li.has-error a').first().tab('show');   // shows first error tab
  });

  // Deselect tab
  $('.formTabs li a').on('click', function (e) { $(this).trigger('blur'); });

});



