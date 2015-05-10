/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*/

$(document).ready(function(){
    $('.export-list-btn').fancybox({
        hideOnContentClick : false,
        helpers: {
        overlay: {
          locked: false
        }
      }
    });
    $('[name=name]').change(function() {
        populateDates(this.value);
        $.uniform.update();
    });
    populateEvents();
    $("form select").uniform({selectAutoWidth : false});
});

function populateEvents() {
    $('[name=name]').html('');
    $.each(exportEentsList, function(index, event) {
      $('[name=name]').append(new Option(event.name, event.name));
    });
    $('[name=name]').trigger('change');
}

function populateDates(name) {
    $('[name=date]').html('');
    $('[name=date]').append(new Option(dictAll, 0));
    $.each(exportEentsList, function(index, event) {
      if (event.name == name) {
          $.each(event.terms, function(index, term) {
              var name = term;
              if (term == '0000-00-00 00:00:00') {
                  name = dictCarnet;
              }
            $('[name=date]').append(new Option(name, term));
          });
      }
    });
}