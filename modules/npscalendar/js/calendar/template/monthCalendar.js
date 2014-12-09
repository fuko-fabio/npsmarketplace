/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

var MonthCalendarTemplate = '\
    <h4 class="title_block"><%= title %></h4>\
    <div class="header row">\
        <div class="left col-xs-12 col-sm-6 col-md-6">\
            <div class="info">\
                <span class="month"><%= month %></span>\
            </div>\
        </div>\
        <div class="right col-xs-12 col-sm-6 col-md-6">\
            <div class="nav">\
                <button class="previous"><i class="icon-chevron-left"></i></button>\
                <button class="next"><i class="icon-chevron-right"></i></button>\
            </div>\
        </div>\
    </div>\
    <div class="row seven-col week-days">\
    <% _.each(week_days, function(week_day) { %>\
        <div class="week-day-item col-xs-1 col-sm-1 col-md-1">\
            <div class="week-day"><%= week_day %></div>\
        </div>\
    <% }); %>\
    </div>\
    <% _.each(weeks, function(week) { %>\
        <div class="row seven-col">\
            <% _.each(week.days, function(day) { %>\
                <% if(day.day != null) { %>\
                    <a class="item col-xs-1 col-sm-1 col-md-1 <% if(day.selected) { %>selected<% } %>" href="<%= day.url %>" onclick="$.fancybox.showLoading();">\
                        <div class="item-content">\
                            <span class="number"><%= day.day %></span><br />\
                            <span class="items <% if(day.events_count > 0) { %>available<% } %>"><%= day.events_count %></span>\
                        </div>\
                    </a>\
                <% } else { %>\
                    <div class="col-xs-1 col-sm-1 col-md-1"></div>\
                <% } %>\
            <% }); %>\
        </div>\
    <% }); %>\
    <div class="footer row">\
        <div class="left col-xs-12 col-sm-6 col-md-6">\
            <div class="info">\
                <span class="year"><%= year %></span>\
            </div>\
        </div>\
    </div>\
';