(function() {
'use strict';

var $ = jQuery,
    deleteCache,
    deleteCacheButton = $('#delete-cache'),
    messageSpan = $('#message'),
    originalText = messageSpan.text();

deleteCache = function deleteCache(event) {
    event.preventDefault();
    messageSpan.text('Deleting');
    $.ajax(
        bptCache.ajaxurl,
        {
            type: 'POST',
            data: {
                // wp ajax action
                action : 'bpt_delete_cache',
                // vars
                // send the nonce along with the request
                bptNonce : bptCache.nonce,
            },
            accepts: 'json',
            dataType: 'json'

        }
    )
    .always(function(res) {
        var text = 'Deleted'

        if (!res.success) {
            text = res.message;
        }

        messageSpan.text(text);

        window.setTimeout(function() {
            messageSpan.text(originalText);
        }, 2000);

    });
};

deleteCacheButton.click(deleteCache);

})(jQuery);
