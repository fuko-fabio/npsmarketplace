/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var WeekCalendarView = Backbone.View.extend({

    el: '.nps-calendar',

    template: _.template(WeekCalendarTemplate),

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
        $.fancybox.showLoading();
        var self = this;
        this.model.fetchPrevious().done(function () {
            self.render();
            $.fancybox.hideLoading();
        });
    },

    nextEvt: function (evt) {
        evt.preventDefault();
        $.fancybox.showLoading();
        var self = this;
        this.model.fetchNext().done(function () {
            self.render();
            $.fancybox.hideLoading();
        });
    }
});