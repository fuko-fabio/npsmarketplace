/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var CalendarView = Backbone.View.extend({
 
    el: '.nps-calendar',

    template: _.template(CalendarTemplate),
 
    render: function () {
        $(this.el).html(this.template(this.model.toJSON()));
        return this;
    }
});