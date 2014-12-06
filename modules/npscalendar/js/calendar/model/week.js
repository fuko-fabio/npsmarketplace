/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var Week = Backbone.AssociatedModel.extend({
    relations: [
        {
            type: Backbone.Many,
            key: 'days',
            collectionType: Days,
            relatedModel: Day
        }
    ],

    defaults: {
        nr : null,
        days : []
    }
});