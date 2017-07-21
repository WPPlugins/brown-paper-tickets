(function($) {
    var refreshButton = $('#refresh-feed'),
        eventsBox = $('.events-box'),
        spinner = $('#feed-spinner');

    refreshButton.click(function (event) {
        event.originalEvent.preventDefault();
        refreshButton.val('Refreshing Feed');
        refreshButton.attr('disabled', true);
        spinner.addClass('is-active');

        $.ajax({
            method: 'GET',
            url: brown_paper_tickets_event_feed_params.url,
            data: {
                nonce: brown_paper_tickets_event_feed_params.nonce,
                action: 'bpt_get_feed_events',
                id: $('#feed-id').val(),
                update: true
            }
        }).done(function (response, status) {
            eventsBox.text(JSON.stringify(response));
        }).always(function () {
            refreshButton.val('Refresh Feed');
            refreshButton.attr('disabled', false);
            spinner.removeClass('is-active');
        })
    });
})(jQuery);
