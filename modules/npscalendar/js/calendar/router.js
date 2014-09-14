/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var CalendarRouter = Backbone.Router.extend({

    routes: {
        "" : "displayCalendar"
    },

    displayCalendar: function() {
        var model = new Calendar();
        model.fetch().done(function () {
            var calendar = new CalendarView({model: model});
            calendar.render();
        });
    }

});

var router = new CalendarRouter();

$(document).ready(function(){
    Backbone.history.start();
});