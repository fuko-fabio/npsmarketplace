/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var MonthCalendarRouter = Backbone.Router.extend({

    routes: {
        "" : "displayCalendar"
    },

    displayCalendar: function() {
        var model = new Month();
        model.fetch().done(function () {
            var calendar = new MonthCalendarView({model: model});
            calendar.render();
        });
    }

});

var router = new MonthCalendarRouter();

$(document).ready(function(){
    Backbone.history.start();
});