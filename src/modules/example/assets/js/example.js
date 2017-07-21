(function() {
'use strict';

var $ = jQuery,
    testAccount,
    resetValues,
    $loading = $('.loading'),
    $events = $('.events'),
    $name = $('.name'),
    $error = $('.error'),
    $devId = $('#dev-id'),
    $clientId = $('#client-id'),
    $message = $('.message');

resetValues = function resetValues() {
    $loading.fadeOut();
    $events.text('');
    $name.text('');
    $error.text('');
    $message.text('');
};

testAccount = function testAccount(event) {
    event.preventDefault();

    resetValues();

    if (!$devId.val()) {
        $error.text('You must enter in a developer ID.');
        return;
    }

    if (!$clientId.val()) {
        $error.text('You must enter in your client ID.');
        return;
    }

    $loading.fadeIn();

    $.ajax(
        bptAccount.ajaxurl,
        {
            type: 'POST',
            data: {
                // wp ajax action
                action : 'bpt_test_account',
                // vars
                devID : $devId.val(),
                clientID : $clientId.val(),
                // send the nonce along with the request
                nonce: bptAccount.nonce,
            },
            accepts: 'json',
            dataType: 'json'
        }
    )
    .always(function(res) {
        var events,
            name;

        $loading.hide();


        if (res.account) {
            name = '<h3>Account</h3>' + res.account.firstName + ' ' + res.account.lastName;
        }

        if (res.events.length) {
            var eventNames = [];

            res.events.forEach(function(element, array) {
                eventNames.push(element.title);
            });

            events = '<h3>Events</h3> ' + eventNames.join(', ');
        }

        if (name && events) {
            $name.html(name);
            $events.html(events);
            $message.html('<h1>Does this look OK?</h1>');
            return;
        }
        $message.html('<h1>Sorry, that developer/client ID combination did not work.</h1>');
        $error.text('Double check those fields for accuracy. If you\'re still having issues, check the help section on the top right of the page for some tips.');
    })
    .fail(function(data) {
        $error.text('An unknown error occurred.');
    });
};

$('#test-account').click(testAccount);
})(jQuery);
