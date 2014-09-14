/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var CalendarTemplate = '\
    <div class="header row">\
        <div class="left col-md-6">\
            <p class="title"><%= title %></p>\
        </div>\
        <div class="right col-md-6">\
            <span class="month"><%= month %></span>\
            <span class="year"><%= year %></span>\
            <button class="previous"><%= previous %></button>\
            <button class="next"><%= next %></button>\
        </div>\
    </div>\
    <div class="content row seven-col">\
        <% _.each(days, function(day) { %>\
            <div class="col-md-1">\
                <div class="day">\
                    <div class="top">\
                        <p class="number"><%= day.day %></p>\
                        <p class="name"><%= day.name %></p>\
                    </div>\
                    <div class="events">\
                        <% _.each(day.events, function(event) { %>\
                            <div class="event">\
                                <p class="name"><%= event.name %></p>\
                                <p class="time"><%= event.time %></p>\
                            </div>\
                        <% }); %>\
                    </div>\
                </div>\
            </div>\
        <% }); %>\
    </div>\
';