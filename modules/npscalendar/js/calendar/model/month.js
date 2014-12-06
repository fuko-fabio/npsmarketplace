/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var Month = Backbone.AssociatedModel.extend({

    url: function () {
        var params;
        if (calendarApiUrl.indexOf('?') > -1) {
            params = '&actionMonth=1&';
        } else {
            params = '?actionMonth=1&';
        }
        if (this.get('start_date') != null) {
            params += 'start_date=' + this.get('start_date');
            if (this.get('end_date') != null) {
                params += '&end_date=' + this.get('end_date');
            }
        } else if (this.get('end_date') != null) {
            params += 'end_date=' + this.get('end_date');
        }
        params += '&selected_date=' + calendarCurrentDate;
        return calendarApiUrl + params;
    },

    relations: [
        {
            type: Backbone.Many,
            key: 'weeks',
            collectionType: Weeks,
            relatedModel: Week
        }
    ],

    defaults: {
        year : null,
        name : null,
        weeks : []
    },

    fetchPrevious: function() {
        this.set({
            'end_date'   : this.get('start_date'),
            'start_date' : null
        });
        return this.fetch();
    },

    fetchNext: function() {
        this.set({
            'start_date': this.get('end_date'),
            'end_date'  : null
        });
        return this.fetch();
    },

    fetch: function(options) {
        $.fancybox.showLoading();
        var promise = Backbone.Model.prototype.fetch.apply(this, options);
        var self = this;
        promise.done(function () {
            self.fillFirstWeek();
            $.fancybox.hideLoading();
        });
        return promise;
    },

    fillFirstWeek: function() {
        var firstWeek = this.get('weeks').first();
        var size = firstWeek.get('days').size();
        while (size < 7) {
            firstWeek.get('days').add(new Day(), {at: 0});
            size = firstWeek.get('days').size();
        }
    }
});