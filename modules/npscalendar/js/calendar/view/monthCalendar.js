/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var MonthCalendarView = Backbone.View.extend({

    el: '.nps-calendar-big',

    template: _.template(MonthCalendarTemplate),

    events: {
        'click .previous' : 'previousEvt',
        'click .next'     : 'nextEvt',
    },

    render: function () {
        $(this.el).html(this.template(this.model.toJSON()));
        this.handleScroll();
        var self = this;
        window.onresize = function() {
            self.handleScroll();
        };
        return this;
    },

    handleScroll: function (evt) {
        var $calendar = $(this.el);
        if (window.outerWidth > 992) {
             $(window).scroll(function(){
                var scrl = $(window).scrollTop();
                var productsHeight = $('.product_list').height();
                if (scrl > 250 && scrl < productsHeight) {
                    $calendar.stop().animate({"marginTop": ($(window).scrollTop() -250) + "px"}, "slow" );
                } else if (scrl < productsHeight) {
                    $calendar.stop().animate({"marginTop": "0px"}, "slow" );
                }
            });
            display('list', false);
        } else {
            $(window).scroll(function(){
                $calendar.stop();
            });
            $calendar.stop().animate({"marginTop": "0px"});
            display('grid', false);
        }
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