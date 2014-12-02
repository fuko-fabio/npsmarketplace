/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var CalendarTemplate = '\
    <div class="header row">\
        <div class="left col-xs-12 col-sm-6 col-md-6">\
            <a href="<%= page_url %>" class="title"><%= title %></a>\
        </div>\
        <div class="right col-xs-12 col-sm-6 col-md-6">\
            <div class="nav">\
                <button class="previous"><i class="icon-chevron-left"></i></button>\
                <button class="next"><i class="icon-chevron-right"></i></button>\
            </div>\
            <div class="info">\
                <span class="month"><%= month %></span>\
                <span class="year"><%= year %></span>\
            </div>\
        </div>\
    </div>\
    <div class="content row seven-col">\
        <% _.each(days, function(day) { %>\
            <div class="col-xs-12 col-sm-1 col-md-1">\
                <div class="day">\
                    <div class="top">\
                        <span class="number"><%= day.day %></span>\
                        <span class="name"><%= day.name %></span>\
                    </div>\
                    <div class="events">\
                        <% if (_.isEmpty(day.events)) { %>\
                            <div class="event">\
                                <p class="name"><%= no_events %></p>\
                            </div>\
                        <% } else { %>\
                            <% _.each(day.events, function(event) { %>\
                                <a href="<%= event.link %>">\
                                    <img class="image" src="<%= event.image %>" />\
                                    <div class="event">\
                                        <p class="name"><%= event.name %></p>\
                                        <p class="time"><%= event.time %></p>\
                                    </div>\
                                </a>\
                            <% }); %>\
                        <% } %>\
                    </div>\
                </div>\
            </div>\
        <% }); %>\
    </div>\
';