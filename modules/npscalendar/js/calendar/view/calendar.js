/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var CalendarView = Backbone.View.extend({

    el: '.nps-calendar',

    template: _.template(CalendarTemplate),

    events: {
        'click .previous' : 'previousEvt',
        'click .next'     : 'nextEvt',
    },

    render: function () {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    },

    previousEvt: function (evt) {
        evt.preventDefault();
        var self = this;
        this.model.fetchPrevious().done(function () {
            self.render();
        });
    },

    nextEvt: function (evt) {
        evt.preventDefault();
        var self = this;
        this.model.fetchNext().done(function () {
            self.render();
        });
    }
});