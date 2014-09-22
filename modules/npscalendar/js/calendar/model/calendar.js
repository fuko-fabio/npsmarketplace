/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var Calendar = Backbone.AssociatedModel.extend({

    url: '/modules/npscalendar/api/calendar.php',

    relations: [
        {
            type: Backbone.Many,
            key: 'days',
            collectionType: Days,
            relatedModel: Day
        }
    ],

    defaults: {
        days    : []
    }
});