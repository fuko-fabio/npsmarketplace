/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var Calendar = Backbone.AssociatedModel.extend({

    url: function () {
        var params;
        if (calendarApiUrl.indexOf('?') > -1) {
            params = '&actionWeek=1&';
        } else {
            params = '?actionWeek=1&';
        }
        if (this.get('start_date') != null) {
            params += 'start_date=' + this.get('start_date');
            if (this.get('end_date') != null) {
                params += '&end_date=' + this.get('end_date');
            }
        } else if (this.get('end_date') != null) {
            params += 'end_date=' + this.get('end_date');
        }
        return calendarApiUrl + params;
    },

    relations: [
        {
            type: Backbone.Many,
            key: 'days',
            collectionType: Days,
            relatedModel: Day
        }
    ],

    defaults: {
        days       : [],
        start_date : null,
        end_date   : null,
        page_url   : calendarPageUrl,
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
    }
});