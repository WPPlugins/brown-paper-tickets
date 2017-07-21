(function() {
    'use strict';
    var $ = jQuery,
        attendeeView,
        getEventsOptions = {
            type: 'GET',
            data: {
                action: 'bpt_attendee_list_get_events',
                nonce: bptAttendeeList.nonce
            }
        },
        getAttendeesOptions = {
            type: 'GET',
            data: {
                action: 'bpt_attendee_list_get_attendees',
                nonce: bptAttendeeList.nonce
            }
        },
        loadEvents,
        getAttendees,
        eventSelectView,
        dateSelectView,
        attendeeListView,
        resetState,
        formatTime,
        formatDate;

    formatTime = function formatTime(time) {
        return moment(time, 'H:mm').format(bptAttendeeList.timeFormat);
    }

    formatDate = function formatTime(date) {
        return moment(date, 'YYYY-MM-DD').format(bptAttendeeList.dateFormat);
    }

    loadEvents = function() {
        eventSelectView.set('loading', true);
        $.ajax(bptAttendeeList.ajaxurl, getEventsOptions)
        .done(function(data) {
            if (data.events.length) {
                eventSelectView.set('events', data.events);
                eventSelectView.set('selected', data.events[0]);
                eventSelectView.set('loading', false);
                eventSelectView.fire('select-event');
                return;
            }

            eventSelectView.set('error', 'Could not load events.');
        });
    };

    getAttendees = function() {
        var event = eventSelectView.get('selected');

        if (!event) {
            return false;
        }

        attendeeListView.set('loading', true);
        getAttendeesOptions.data.event = event.id;

        $.ajax(bptAttendeeList.ajaxurl, getAttendeesOptions)
        .fail(function(data) {

        })
        .done(function(data) {
            attendeeListView.set('loading', false);
            if (data.attendees.length) {
                attendeeListView.set('attendees', data.attendees);
                return;
            }
        });
    };

    resetState = function() {
        eventSelectView.set('selected', eventSelectView.get('events')[0]);
        eventSelectView.set('error', null);
        dateSelectView.set('dates', eventSelectView.get('selected').dates);
        dateSelectView.set('selected', []);
        attendeeListView.set('attendees', []);

        loadEvents();
    };

    Ractive.DEBUG = false;

    /**
     * The three Ractive views.
     */
    eventSelectView = new Ractive({
        el: '#event-select',
        template: $('#event-select-template').html(),
        data: {
            events: [],
            selected: null,
            error: null
        }
    });

    eventSelectView.getPriceObject = function getPriceObject(id) {
        var events = this.get('events');

        for (var i = 0; i < events.length; i++) {
            for (var i2 = 0; i2 < events[i].dates.length; i2++) {
                for (var i3 = 0; i3 < events[i].dates[i2].prices.length; i3++) {
                    if (events[i].dates[i2].prices[i3].id === id) {
                        return events[i].dates[i2].prices[i3];
                    }
                }
            }
        }

        return false;
    };

    dateSelectView = new Ractive({
        el: '#date-select',
        template: $('#date-select-template').html(),
        data: {
            dates: [],
            selected: [],
            formatTime: formatTime,
            formatDate: formatDate,
        }
    });

    /**
     * This allows you to get a specific date object. For some reason the
     * template is rendering the selected objects as a string when using
     * value="{{.}}" so the selected objects are being stored as
     * "[Object object]" rather than the actual object.
     */
    dateSelectView.getDate = function(id) {
        var dates = this.get('dates');

        for (var i = 0; i < dates.length; i++) {
            // Again because for some reason the template renders the value
            // as a string rather than an integer when switching between
            // selected events I'm casting them to a string rather than use
            // the "==" operator.
            if (id.toString() === dates[i].id.toString()) {
                return dates[i];
            }
        }
    };

    attendeeListView = new Ractive({
        el: '#attendees',
        template: $('#attendee-list-template').html(),
        data: {
            attendees: [],
            dates: [],
            inDate: function(attendee, date) {

                if (attendee.dateID === date.id) {
                    return true;
                }

                return false;
            },
            formatTime: formatTime,
            formatDate: formatDate,
            priceName: function(id) {
                var price = eventSelectView.getPriceObject(id);
                return price.name;
            }
        }
    });

    /**
     * Event Select View event listeners.
     */

    /**
     * When an event is selected, set the event's dates to the dates on the
     * Date Select View.
     */
    eventSelectView.on('select-event', function(event) {
        // If this was trigged by a JS event, prevent the default.
        if (event) {
            event.original.preventDefault();
        }

        dateSelectView.set('dates', this.get('selected').dates);
        getAttendees();
    });

    eventSelectView.on('refresh-events', function(event) {
        if (event) {
            event.original.preventDefault();
        }

        loadEvents();
    });

    /**
     * When an event is selected, get rid of the selected dates and attendees.
     */
    eventSelectView.observe('selected', function(event) {
        dateSelectView.set('selected', []);
        attendeeListView.set('attendees', []);
    });

    /**
     * Date Select View event listeners.
     */

    dateSelectView.on('get-attendees', function(event) {
        if (event) {
            event.original.preventDefault();
        }
    });


    /**
     * When a date is selected, get the attendees for the dates.
     */
    dateSelectView.observe('selected', function() {
        var self = this;
        var selected = this.get('selected').map(function(id) {
            return self.getDate(id);
        });

        if (selected) {
            attendeeListView.set('dates', selected);
            return;
        }

        attendeeListView.set('attendees', []);
    });

    /**
     * Event Select View event listeners.
     */

    attendeeListView.observe('attendees', function(attendees) {

    });

    $(document).ready(function() {
        loadEvents();
    });

})(jQuery);
