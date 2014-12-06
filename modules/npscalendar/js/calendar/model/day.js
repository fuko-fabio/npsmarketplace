/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var Day = Backbone.AssociatedModel.extend({
    relations: [
        {
            type: Backbone.Many,
            key: 'events',
            collectionType: Events,
            relatedModel: Event
        }
    ],

    defaults: {
        day : null,
        name : null,
        eventsCount: null,
        events : []
    }
});