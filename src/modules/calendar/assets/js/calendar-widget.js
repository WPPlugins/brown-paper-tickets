(function($) {
    'use strict';
    var BptCalendar;

    BptCalendar = function(calendarOptions) {
        var bptCalendar = this,
            events = [],
            currentEvents = [],
            calendar,
            calendarContainer = calendarOptions.calendarContainer,
            calendarTemplate = $('#bpt-calendar-widget-calendar-template').html(),
            eventListContainer = calendarOptions.eventListContainer,
            eventListTemplate = '#bpt-calendar-widget-event-view-template',
            showUpcoming = (calendarOptions.showUpcoming === 'true' ? true : false),
            eventList,
            getEvents,
            setEvents,
            displayEvents;

        this.events = events;
        this.currentEvents = currentEvents;

        Ractive.DEBUG = false;
        /**
         * This is the Ractive Template that displays the
         * event details.
         */
        eventList = new Ractive({
            el: eventListContainer,
            template: eventListTemplate,
            data: {
                showUpcoming: showUpcoming, // If show upcoming is enabled, show upcoming dates.
                currentEvents: bptCalendar.events,
                formatDate: function formatDate(newFormat, date) {
                    var singleDate = moment(date, 'YYYY-MM-DD');
                    return singleDate.format(newFormat);
                },
                formatTime: function formatTime(newFormat, time) {
                    var singleTime = moment(time, 'H:mm');
                    return singleTime.format(newFormat);
                },
                unescapeHTML: function unescapeHTML(html) {
                    return _.unescape(html);
                },
                sortEvents: function(events) {
                    events = events.slice();

                    events = events.sort(function(a, b) {

                        if (a.date > b.date) {
                            return 1;
                        }

                        if (a.date < b.date) {
                            return -1;
                        }

                        return 0;
                    });

                    return events;
                }
            }
        });

        /**
         * This is the calendar itself.
         */
        calendar = $(calendarOptions.calendarContainer).clndr({
            template: calendarTemplate,
            targets: {
                nextButton: 'bpt-calendar-widget-controls-next-button',
                previousButton: 'bpt-calendar-widget-controls-previous-button',
                nextYearButton: 'clndr-next-year-button',
                previousYearButton: 'clndr-previous-year-button',
                todayButton: 'clndr-today-button',
                day: 'day',
                empty: 'empty'
            },
            clickEvents: {
                click: function(target) {
                    displayEvents(target);
                },
                onMonthChange: function(month) {
                    displayEvents(month);
                }
            },
            ready: function() {

            }
        });

        this.getEvents = function() {

            var ajaxData = {
                action: 'bpt_get_calendar_events',
                nonce: calendarOptions.nonce,
                widgetID: calendarOptions.widgetID,
            };

            if (calendarOptions.clientID) {
                ajaxData.clientID = calendarOptions.clientID;
            }

            $.ajax(
            calendarOptions.ajaxurl,
            {
                type: 'GET',
                data: ajaxData,
                accepts: 'json',
                dataType: 'json'

            }).done(function(data) {
                setEvents(data);

                calendar.options.ready();

            }).fail(function(data) {

                setEvents(data);

            });
        };

        setEvents = function(events) {

            bptCalendar.events = events;
            calendar.setEvents(events);
            displayEvents();
        };

        displayEvents = function(target) {
            var dayEvents = [];

            if (target && target.element) {
                var container = document.querySelector(calendarContainer);
                var previousSelected =container.querySelectorAll('.bpt-calendar-selected-day');

                for (var i = 0; i < previousSelected.length; i++) {
                    previousSelected[i].classList.remove('bpt-calendar-selected-day');
                }

                target.element.classList.add('bpt-calendar-selected-day');
            }

            if (target && !target._isAMomentObject) {
                dayEvents = target.events;
            }

            if (showUpcoming && dayEvents.length === 0) {

                if (calendar.eventsThisMonth.length !== 0) {

                    eventList.set({
                        date: false,
                        eventsThisMonth: true,
                        showUpcoming: false,
                        currentEvents: calendar.eventsThisMonth,
                    });

                    return;

                } else {

                    eventList.set({
                        date: false,
                        eventsThisMonth: false,
                        showUpcoming: true,
                        currentEvents: bptCalendar.events.slice(0, 5),
                    });

                    return;
                }

            }

            if (dayEvents.length > 0) {

                eventList.set({
                    date: dayEvents[0].date,
                    eventsThisMonth: false,
                    showUpcoming: false,
                    currentEvents: dayEvents
                });

                return;
            }

            eventList.set({
                date: false,
                eventsThisMonth: false,
                showUpcoming: false,
                currentEvents: false
            });
        };
    };

    $(document).ready(function() {
        var bptCalendarWidgetShortcodeAjax = window.bptCalendarWidgetShortcodeAjax,
            bptCalendarWidgetAjax = window.bptCalendarWidgetAjax;

        if (bptCalendarWidgetShortcodeAjax) {

            var bptCalendarShortcode = new BptCalendar(bptCalendarWidgetShortcodeAjax);

            bptCalendarShortcode.getEvents();

        }

        if (bptCalendarWidgetAjax) {

            var bptCalendarWidget = new BptCalendar(bptCalendarWidgetAjax);

            bptCalendarWidget.getEvents();
        }
    });

})(jQuery);
