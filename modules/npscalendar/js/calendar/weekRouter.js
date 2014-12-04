/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var WeekCalendarRouter = Backbone.Router.extend({

    routes: {
        "" : "displayCalendar"
    },

    displayCalendar: function() {
        var model = new Calendar();
        model.fetch().done(function () {
            var calendar = new WeekCalendarView({model: model});
            calendar.render();
        });
    }

});

var router = new WeekCalendarRouter();

$(document).ready(function(){
    Backbone.history.start();
});