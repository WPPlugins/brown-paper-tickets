(function() {
'use strict';

var $ = jQuery,
    unhidePrice,
    customDateFormat,
    customTimeFormat;

unhidePrice = function unhidePrice(event) {
    event.preventDefault();

    var priceLink = $(event.target),
        price = {
            priceId: priceLink.data('price-id')
        };

    $.ajax(
        eventListAdmin.ajaxurl,
        {
            type: 'POST',
            data: {
                action: 'bpt_unhide_prices',
                nonce: eventListAdmin.nonce,
                admin: true,
                prices: [price]
            },
            accepts: 'json',
            dataType: 'json'
        }
    )
    .always(function() {

    })
    .done(function(data) {
        if (data.success) {
            priceLink.parent().parent().fadeOut();
        }
    })
    .fail(function(data) {

    });
};

customDateFormat = function() {

    var selectedDateFormat = $('select#date-format option').filter(':selected');

    if (selectedDateFormat.val() === 'custom') {
        $('input#custom-date-format-input').removeClass('hidden');
    } else {
        $('input#custom-date-format-input').addClass('hidden');
    }
};

customTimeFormat = function() {

    var selectedTimeFormat = $('select#time-format option').filter(':selected');

    if (selectedTimeFormat.val() === 'custom') {
        $('input#custom-time-format-input').removeClass('hidden');
    } else {
        $('input#custom-time-format-input').addClass('hidden');
    }
};


customDateFormat();
customTimeFormat();

$('.bpt-unhide-price').click(unhidePrice);
$('select#date-format').change(customDateFormat);
$('select#time-format').change(customTimeFormat);

})(jQuery);
