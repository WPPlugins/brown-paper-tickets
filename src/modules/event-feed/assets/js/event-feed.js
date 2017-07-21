(function($) {
    'use strict';

    var eventFeed = new Ractive({
        el: bptEventFeed.containerId,
        template: bptEventFeed.templateId,
        data: {
            title: bptEventFeed.title,
            loading: true,
            defaultImage: bptEventFeed.defaultImage,
        }
    });

    $(document).ready(function() {
        $.ajax({
            method: 'GET',
            url: bptEventFeed.url,
            data: {
                nonce: bptEventFeed.nonce,
                action: 'bpt_get_feed_events',
                id: bptEventFeed.feedId
            }
        }).done(function (response, status) {
            response = JSON.parse(response);

            for (var i = 0; i < response.length; i++) {
                if (response[i].contactEmail === 'cat@bpt.com') {
                    response.splice(i, 1);
                    continue;
                }

                if (response[i].contactEmail === 'graeme@brownpapertickets.com') {
                    response.splice(i, 1);
                    continue;
                }
            }
            eventFeed.set('events', response);
        }).always(function () {
            eventFeed.set('loading', false);
        });
    });
})(jQuery);
